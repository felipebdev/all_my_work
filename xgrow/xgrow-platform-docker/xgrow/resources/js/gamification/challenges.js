import StatusModalComponent from "../components/StatusModalComponent.vue";
import Pagination from "../components/Datatables/Pagination.vue";
import Table from "../components/Datatables/Table.vue";
import Modal from "../components/ModalComponent.vue";
import ExportLabel from "../components/XgrowDesignSystem/Utils/ExportLabel";
import IconButton from "../components/XgrowDesignSystem/Buttons/IconButton";
import Input from "../components/XgrowDesignSystem/Input.vue";
import FilterButton from "../components/XgrowDesignSystem/Buttons/FilterButton";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";

import DatePicker from "vue-datepicker-next";
import "vue-datepicker-next/index.css";
import "vue-datepicker-next/locale/pt-br";

import XGrowTour from "../functions/tour";

import moment from "moment";
import axios from "axios";

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
            activeScreen: "challenges.challenges",
            activeContentScreen: "",
            /** Modal */
            showReplyModal: false,
            /** Challenge Data */
            challenges: [],
            challengeReply: {
                _id: 1,
                userName: "Fernando Martins",
                challenge: "Nome do primeiro desafio",
                reply: "Lorem ipsum dolor sit amet consectetur adipisicing elit. Eos autem molestias eaque quiquod reiciendis",
                createdAt: "04/11/21 às 13:22h",
                link: "Link da resposta",
                linkUrl: "#"
            },
            currentId: 0,
            challenge: null,
            typesOfMultimedia: [
                {id: "", type: ""},
                {id: "video", type: "Vídeo"},
                {id: "audio", type: "Audio"}
            ],
            typesOfAnswer: [
                {id: "", type: ""},
                {id: "singleOption", type: "Opções Simples"},
                {id: "text", type: "Somente Texto"},
                {id: "onlyView", type: "Somente Visualização"}
            ],
            /** Configuration Data */
            configuration: {
                _id: 0,
                enableChallenges: false,
                formDelivery: "",
                deliveryFrequency: 1,
                frequencyFormat: "day",
                startFrom: null
            },
            typesOfFormDelivery: [
                {id: "", type: ""},
                {id: "sequential", type: "Sequencial (1 desafio por dia)"},
                {id: "programmed", type: "Programada"}
            ],
            typesOfFrequencyFormat: [
                {id: "", type: ""},
                {id: "day", type: "Dia(s)"},
                {id: "week", type: "Semana(s)"},
                {id: "month", type: "Mês(s)"}
            ],

            /** Pagination */
            paginationCurrentPage: 1, // Current Page
            paginationLimit: 25, // Limit by page
            paginationTotal: 0, // Total Results
            paginationTotalResults: 0, //Total Pages

            /** Challenge Filter */
            challengeOptions: [
                "Desafio 1",
                "Desafio 2",
                "Desafio 3"
            ],
            challengeValue: null,
            dateRangeValue: null,
            dateRangeFormat: null,
            search: "",

            /** Delete Modal */
            showDeleteModal: false,
            deleteModalChallenge: "",
            deleteModalConfirmation: () => {}
        };
    },
    watch: {
        search: function () {
            this.searchTerm();
        }
    },
    methods: {
        /** Change screen by value */
        changePage: function (screen) {
            this.activeScreen = screen.toString();
        },

        /** Get Total de Pages */
        totalPages: function () {
            const qty = Math.ceil(this.paginationTotal / this.paginationLimit);
            this.paginationTotalResults = (qty <= 1) ? 1 : qty;
        },

        /** On change page */
        onPageChange: async function (page) {
            this.paginationCurrentPage = page;
            await this.getData();
        },

        /** Limit by size itens */
        onLimitChange: async function (value) {
            this.paginationLimit = parseInt(value);
            await this.getData();
        },

        /** Get all data **/
        getData: async function () {
            this.statusLoading = true;
            const res = await axios.get(getChallengesURL, {
                params: {
                    page: this.paginationCurrentPage,
                    offset: this.paginationLimit
                }
            });
            this.challenges = res.data.response.data.data;
            this.paginationTotal = res.data.response.data.total;
            this.totalPages();
            this.statusLoading = false;
        },

        /** Used for search with timer */
        searchTerm: async function () {
            let term = this.search;
            setTimeout(() => {
                if (term == this.search) {
                    this.statusLoading = true;
                    axios.get(getChallengesURL, {
                        params: {
                            page: this.paginationCurrentPage,
                            offset: this.paginationLimit,
                            search: this.search
                        }
                    })
                        .then((res) => {
                            this.challenges = res.data.response.data.data;
                            this.paginationTotal = res.data.response.data.total;
                            this.totalPages();
                            this.statusLoading = false;
                        }).catch((err) => console.log(err));
                }
            }, 1000);
        },

        /** Start Period component */
        startPeriod: function () {
            const endDate = new Date();
            const startDate = new Date(new Date().setDate(endDate.getDate() - 30));
            this.dateRangeValue = [startDate, endDate];
            this.dateRangeFormat = moment(startDate).format("DD/MM/YYYY") + " - " + moment(endDate).format("DD/MM/YYYY");
        },

        /** Open Show Reply modal */
        openShowReplyModal: function (id) {
            this.showReplyModal = true;
            this.currentId = id;
        },

        /** Change period */
        changePeriodFilter: async function () {
            this.dateRangeFormat = moment(this.dateRangeValue[0]).format("DD/MM/YYYY") + " - " + moment(this.dateRangeValue[1]).format("DD/MM/YYYY");
            // await this.getData();
        },

        /** ----------------- Settings ----------------- */
        /** Load settings */
        loadChallengeSettings: async function () {
            const req = await axios.get(getChallengeSettingsURL);
            if (req.data.response.data.length > 0 && req.data.response.data[0].hasOwnProperty("_id")) {
                this.configuration = {...req.data.response.data[0]};
                this.configuration.startFrom = this.$filters.formatToDate(this.configuration.startFrom);
            }
        },

        /** Save Settings Challenge */
        saveChallengeSettings: async function () {
            this.statusLoading = true;
            try {
                const validations = [];
                validations.push(
                    [
                        !(this.configuration.enableChallenges === true && this.configuration.formDelivery === null)
                        ,
                        "Selecione a forma de entrega"
                    ],
                    [
                        !(this.configuration.enableChallenges === true && !moment(this.configuration.startFrom).isValid()),
                        "Escolha uma data para a entrega do desafio"
                    ]
                );

                const validation = this.checkValidations(validations);
                //validation ok
                if (validation.status) {
                    //Save challenge settings
                    const res = await axios.post(saveChallengeSettingsURL, this.configuration);
                    successToast("Ação realizada com sucesso", res.data.message.toString());
                    this.configuration._id = res.data.response[0].data._id;
                } else {
                    errorToast("Falha ao realizar ação", validation.message);
                }
                this.statusLoading = false;
            } catch (e) {
                this.statusLoading = false;
                errorToast("Falha ao realizar ação", e.response.data.message.toString());
            }
        },

        /** Crud Challenge */
        /** Create Challenge */
        createChallenge: function () {
            this.resetChallengeData();
            this.currentId = 0;
            this.activeScreen = "challenges.new";
        },

        /** Remove Challenge */
        removeChallenge: async function (id) {
            this.showDeleteModal = false;
            this.statusLoading = true;
            try {
                const deleteUrl = deleteChallengeURL.replace(/:id/g, id);
                const res = await axios.delete(deleteUrl);
                successToast("Ação realizada com sucesso", res.data.message.toString());
                await this.getData();
                this.statusLoading = false;
            } catch (e) {
                this.statusLoading = false;
                errorToast("Falha ao realizar ação", e.response.data.message.toString());
            }
        },

        /** Save challenge */
        saveChallenge: async function () {
            this.statusLoading = true;
            this.status = "saving";

            try {
                const validations = [];
                validations.push([this.challenge.title, "Informe o título"]);
                validations.push([this.challenge.message, "Informe o conteúdo"]);
                validations.push([this.challenge.answerType, "Selecione uma opção"]);
                validations.push([this.challenge.multimediaType, "Informe o tipo de multimídia"]);
                validations.push([this.challenge.multimediaUrl, "Informe o link"]);
                validations.push([this.challenge.order, "Informe a ordem"]);
                validations.push([this.challenge.reward, "Informe a recompensa"]);
                const validation = this.checkValidations(validations, { checkOptionsList: true });
                //validation ok
                if (validation.status) {
                    if (this.currentId !== 0) {
                        //Edit Challenge
                        const putChallengeURL = updateChallengeURL.replace(/:id/g, this.currentId);
                        await axios.put(putChallengeURL, this.challenge);
                        successToast("Ação realizada com sucesso", "Desafio atualizado com sucesso");
                    } else {
                        //Create Challenge
                        await axios.post(saveChallengeURL, this.challenge);
                        successToast("Ação realizada com sucesso", "Desafio salvo com sucesso");
                    }
                    await this.getData();

                    this.changePage("challenges.challenges");
                    this.validations = [];
                } else {
                    errorToast("Falha ao realizar ação", validation.message);
                }
                this.statusLoading = false;

            } catch (e) {
                this.statusLoading = false;
                errorToast("Falha ao realizar ação", e.response.data.message.toString());
            }


        },

        /** Edit challenge */
        editChallenge: async function (id) {
            this.currentId = id;
            this.activeScreen = "challenges.new";
            this.challenge = this.challenges.filter(item => item._id === id)[0];
        },

        /** set data of challenge to default values **/
        resetChallengeData: function () {
            this.challenge = {
                title: "",
                message: "",
                multimediaType: "",
                multimediaUrl: "",
                answerType: "",
                showOnLogin: false,
                order: "",
                reward: "",
                optionsList: []
            };
        },

        /** check validations **/
        checkValidations: function (validations, options = {}) {
            let status = true;
            let message = null;
            let error = validations.find(condition => !condition[0]);

            if (options.hasOwnProperty('checkOptionsList') && this.checkForErrorOptionsList()) {
                error =  [false, 'Preencha todas as opções ou remova as vazias'];
            }

            /** error found **/
            if (error !== undefined) {
                status = false;
                message = error[1];
            }
            return {status, message};
        },
        checkForErrorOptionsList: function () {
            if (this.challenge.answerType !== 'singleOption') { return false }
            if (this.challenge.optionsList.length < 1) { return true }

            const error = this.challenge.optionsList.find(option => option.message == '' || option.message == null || option.message.length == 0);
            if (error !== undefined) {
                return true;
            }

            return false;
        },
        callDeleteModal: async function (id) {
            const challenge = this.challenges.find(challenge => challenge._id === id);
            this.deleteModalChallenge = challenge.title;
            this.deleteModalConfirmation = () => this.removeChallenge(id);
            this.showDeleteModal = true;
        },
        closeDeleteModal: function () {
            this.showDeleteModal = false;
        },

        /** optionsList functions */
        addOptionInList: function () {
            if (!this.challenge.optionsList) { this.challenge.optionsList = [] }

            this.challenge.optionsList.push({
                id: this.challenge.optionsList.length + 1,
                message: ""
            });
        },
        removeOptionFromList: function (idx) {
            this.challenge.optionsList.splice(idx, 1);
            this.fixOptionId();
        },
        fixOptionId: function () {
            for (let i = 0; i < this.challenge.optionsList.length; i++) {
                this.challenge.optionsList[i].id = (i+1);
            }
        }
    },
    async created() {
        this.resetChallengeData();
        await this.getData();
        await this.loadChallengeSettings();

        XGrowTour.initialize(
            "challengesTour",
            [
                {
                    elementId: "#challenge-content",
                    title: "Listagem de desafios",
                    description: "Nesta área, você pode ter uma visão geral e acessar todos os desafios que você criou para seus alunos.",
                    position: "bottom"
                },
                {
                    elementId: "#nav-configurations-score-tab",
                    title: "Desafios - configurações",
                    description: "Aqui você pode configurar a forma de entrega dos seus desafios e se quer habilitá-los ou não.",
                    position: "right"
                }
            ]
        );
    }
});

app.component("status-modal-component", StatusModalComponent);
app.component("xgrow-pagination-component", Pagination);
app.component("xgrow-table-component", Table);
app.component("xgrow-modal-component", Modal);
app.component("multiselect-component", Multiselect);
app.component("xgrow-daterange-component", DatePicker);
app.component("export-label", ExportLabel);
app.component("icon-button", IconButton);
app.component("xgrow-input", Input);
app.component("filter-button", FilterButton);

/** Filter user for datetime format */
app.config.globalProperties.$filters = {
    formateDateBR(value) {
        return moment(value).format("DD/MM/YYYY HH:mm:ss");
    },
    formatToDate(value) {
        return moment(value).toDate()
    }
};

app.use(ApmVuePlugin, apmConfig);

app.mount("#challenges");
