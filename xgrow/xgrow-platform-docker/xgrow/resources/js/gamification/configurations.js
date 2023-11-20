import StatusModalComponent from "../components/StatusModalComponent.vue";
import UploadImage from "../components/UploadImage.vue";
import Table from "../components/Datatables/Table.vue";
import Modal from "../components/ModalComponent.vue";
import LevelCard from "../../views/gamification/components/LevelCard.vue";

import axios from "axios";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";

import { ApmVuePlugin } from '@elastic/apm-rum-vue';
import apmConfig from '../config/elk-apm';

const vue = require("vue");

const app = vue.createApp({
    delimiters: ["[[", "]]"],
    data() {
        return {
            /** Loading */
            statusLoading: false,
            status: "loading",
            /** Page controller */
            activeScreen: "configurations.config",
            activeContentScreen: "",
            /** Method used on form create, update */
            method: "create",
            currentId: 0,
            deleteModal: false,
            levelModal: false,
            /** Configuration Data */
            configuration: {
                isEnabled: false,
                showBestPlayersRanking: false,
                showWorsePlayersRanking: false,
                showPoints: false,
                showPhases: false,
                showChallengesReward: false
            },
            /** Score Data */
            score: {
                actions: []
            },
            /** Level Data */
            level: {
                _id: null,
                order: 0,
                name: "",
                requiredPoints: 0,
                color: "#FFFFFF",
                iconUrl: "/xgrow-vendor/assets/img/gamification/blank.svg"
            },
            /** List of Levels */
            levels: []
        };
    },
    methods: {
        /** Change screen by value */
        changePage: function (screen, contentScreen) {
            this.activeScreen = screen.toString();
            this.activeContentScreen = contentScreen.toString();
        },
        /** ----------------- Settings ----------------- */
        /** Load settings */
        loadSettings: async function () {
            const req = await axios.get(getSettingsURL);
            if (req.data.response.data.hasOwnProperty("_id")) {
                const {
                    isEnabled,
                    showBestPlayersRanking,
                    showWorsePlayersRanking,
                    showPoints,
                    showPhases,
                    showChallengesReward
                } = req.data.response.data;
                this.configuration.isEnabled = isEnabled;
                this.configuration.showBestPlayersRanking = showBestPlayersRanking;
                this.configuration.showWorsePlayersRanking = showWorsePlayersRanking;
                this.configuration.showPoints = showPoints;
                this.configuration.showPhases = showPhases;
                this.configuration.showChallengesReward = showChallengesReward;
            }
        },
        /** Save settings */
        saveSettings: async function () {
            this.statusLoading = true;
            this.status = "saving";

            const formData = new FormData();
            formData.append("isEnabled", this.configuration.isEnabled);
            formData.append("showBestPlayersRanking", this.configuration.showBestPlayersRanking);
            formData.append("showWorsePlayersRanking", this.configuration.showWorsePlayersRanking);
            formData.append("showPoints", this.configuration.showPoints);
            formData.append("showPhases", this.configuration.showPhases);
            formData.append("showChallengesReward", this.configuration.showChallengesReward);

            try {
                const res = await axios.post(saveSettingsURL, formData);
                this.statusLoading = false;
                successToast("Ação realizada com sucesso", res.data.message.toString());
                return true;
            } catch (e) {
                this.statusLoading = false;
                errorToast("Falha ao realizar ação", e.response.data.message.toString());
            }
        },
        /** ----------------- Score ----------------- */
        /** Save score */
        loadScore: async function () {
            const pluck = (arr, key) => arr.map(i => i[key]);
            const req = await axios.get(getScoreURL);
            const data = req.data.response.data;

            // Check if request data has ACTIONS and SUGGESTIONS defined
            if (!data.hasOwnProperty('actions') && !data.hasOwnProperty('suggestions')) return;

            const actions = data.actions.data[0]?.actionList;
            this.score.actions = actions != undefined ? [...actions] : [];
            const actionsId = pluck(this.score.actions, 'actionId');

            let suggestions = data.suggestions.data;
            suggestions = suggestions.filter((e) => !actionsId.includes(e.actionId));

            suggestions.forEach(suggestion => {
                this.score.actions.push({
                    actionId: suggestion.actionId,
                    actionName: suggestion.actionName,
                    actionValue: suggestion.defaultValue,
                    isEnabled: false, // From API, this value is coming true
                    isLimited: false, // From API, this value is coming true
                    limitQuantity: suggestion.defaultLimitQuantity,
                    limitType: suggestion.defaultLimitType,
                })
            });
        },
        saveScore: async function () {
            this.statusLoading = true;
            const actions = this.score.actions.filter((e) => e.isEnabled);

            const formData = new FormData();
            formData.append("actionList", JSON.stringify(actions));

            try {
                const res = await axios.post(saveScoreURL, formData);
                successToast("Pontuação atualizada!", res.data.message.toString());
                await this.loadScore();
                this.statusLoading = false;
                return true;
            } catch (e) {
                this.statusLoading = false;
                errorToast("Falha ao realizar ação", e.response.data.message.toString());
            }

            // successToast("Pontuação atualizada!", "Sua nova configuração de pontuação foi salva.");
            // await new Promise(resolve => setTimeout(resolve, 6000));
            // errorToast("Erro ao salvar pontuação!", "Ocorreu algum erro ao salvar sua nova configuração de pontuação, tente novamente mais tarde.");
        },
        /** ----------------- Phases ----------------- */
        /** Load phases */
        loadPhases: async function () {
            const req = await axios.get(getPhasesURL);
            this.levels = [];
            if (req.data.response.data.length > 0) {
                req.data.response.data.forEach(level => {
                    const data = {
                        _id: level._id,
                        order: level.order,
                        name: level.name,
                        requiredPoints: level.requiredPoints,
                        color: level.color,
                        iconUrl: level.iconUrl
                    };
                    this.levels.push(data);
                });

                this.levels.sort(function (a, b) {
                    if (a.order > b.order) return 1;
                    if (a.order < b.order) return -1;
                    return 0;
                });
            }

        },
        /** Open Level Modal */
        openLevelModal: function () {
            this.clearLevelFields();
            this.method = "create";
            this.level.order = this.levels.length + 1;
            this.levelModal = true;
        },
        /** Save level */
        saveLevel: async function () {
            if (this.hasErrors()) return true;
            this.statusLoading = true;
            this.status = "saving";

            const headers = {
                headers: {"Content-Type": "multipart/form-data"}
            };
            const formData = new FormData();
            formData.append("order", this.level.order);
            formData.append("name", this.level.name);
            formData.append("requiredPoints", this.level.requiredPoints);
            formData.append("color", this.level.color);
            formData.append("iconUrl", this.level.iconUrl);

            if (this.method === "create") {
                try {
                    const res = await axios.post(savePhasesURL, formData, headers);
                    this.statusLoading = false;
                    successToast("Ação realizada com sucesso", res.data.message.toString());
                    this.clearLevelFields();
                    await this.loadPhases();
                    return true;
                } catch (e) {
                    this.statusLoading = false;
                    errorToast("Falha ao realizar ação", e.response.data.message.toString());
                }
            }
            if (this.method === "edit") {
                try {
                    formData.append("_method", "put");
                    const updateURL = updatePhaseURL.replace(/:id/g, this.currentId);
                    const res = await axios.post(updateURL, formData, headers);
                    this.statusLoading = false;
                    successToast("Ação realizada com sucesso", res.data.message.toString());
                    this.clearLevelFields();
                    await this.loadPhases();
                    return true;
                } catch (e) {
                    this.statusLoading = false;
                    errorToast("Falha ao realizar ação", e.response.data.message.toString());
                }
            }
        },
        /** Edit level */
        editLevel: function (id) {
            this.statusLoading = true;
            this.currentId = id;
            const level = this.levels.filter(item => item._id === id)[0];
            this.method = "edit";
            this.level._id = level._id;
            this.level.order = level.order;
            this.level.name = level.name;
            this.level.requiredPoints = level.requiredPoints;
            this.level.color = level.color;
            if (level.iconUrl !== null || level.iconUrl !== undefined) {
                this.$refs.iconUrl.src(level.iconUrl);
                this.level.iconUrl = level.iconUrl;
            }
            const nameInput = document.querySelector("#levelTitle");
            nameInput.classList.remove("mui--is-empty");
            nameInput.classList.add("mui--is-not-empty");
            this.levelModal = true;
            this.statusLoading = false;
        },
        /** Open delete modal */
        openLevelDeleteModal: function (id) {
            this.currentId = id;
            this.deleteModal = true;
        },
        /** Delete level */
        removeLevel: async function () {
            this.statusLoading = true;
            this.levels = this.levels.filter(item => item._id !== this.currentId);
            this.status = "loading";
            try {
                const deleteUrl = deletePhaseURL.replace(/:id/g, this.currentId);
                const res = await axios.delete(deleteUrl);
                successToast("Ação realizada com sucesso", res.data.message.toString());
                await this.loadPhases();
                this.deleteModal = this.statusLoading = false;
                return true;
            } catch (e) {
                this.statusLoading = false;
                errorToast("Falha ao realizar ação", e.response.data.message.toString());
                this.deleteModal = false;
            }
        },
        /** Verify all fields */
        hasErrors() {
            if (this.level.name.trim() === "") {
                errorToast("Algum erro aconteceu!", "O nome da fase não pode ficar em branco.");
                return true;
            }

            if (this.level.requiredPoints < 1) {
                errorToast("Algum erro aconteceu!", "A pontuação deve ser igual ou superior a 1.");
                return true;
            }

            if (this.level.iconUrl === null) {
                errorToast("Algum erro aconteceu!", "Você precisa adicionar um ícone para a fase criada.");
                return true;
            }
        },
        /** ----------------- Misc ----------------- */
        /** Receive image for UploadImage Plugin */
        receiveImage(obj) {
            this.level.iconUrl = obj.file.files[0];
        },
        /** Clear all fields */
        clearLevelFields: function () {
            this.level.requiredPoints = 0;
            this.level._id = null;
            this.level.name = "";
            const nameInput = document.querySelector("#levelTitle");
            nameInput.classList.remove("mui--is-not-empty");
            nameInput.classList.add("mui--is-empty");
            this.level.color = "#FFFFFF";
            this.level.iconUrl = null;
            this.levelModal = false;
            this.$refs.iconUrl.reset();
        },
        // Resume the description for short sentence
        resumeDetail: function (resume, length = 14) {
            const short = resume.substring(0, length);
            return resume.length > short.length ? short + "..." : resume;
        }
    },
    async created() {
        this.statusLoading = true;
        await this.loadSettings();
        await this.loadScore();
        await this.loadPhases();
        this.statusLoading = false;
    }
});

app.component("status-modal-component", StatusModalComponent);
app.component("xgrow-table-component", Table);
app.component("xgrow-level-card-component", LevelCard);
app.component("xgrow-modal-component", Modal);
app.component("upload-image", UploadImage);
app.component("multiselect", Multiselect);

/** Filter user for datetime format */
app.config.globalProperties.$filters = {
    formateDateBR(value) {
        return moment(value).format("DD/MM/YYYY HH:mm:ss");
    }
};

// await new Promise(resolve => setTimeout(resolve, 1000)); /** Only for delay */

app.use(ApmVuePlugin, apmConfig);

app.mount("#configurations");
