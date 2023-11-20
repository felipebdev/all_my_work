import StatusModalComponent from "../components/StatusModalComponent";
import PlatformCardComponent from "../../views/platforms/components/PlatformCard.vue";
import PlatformListItemComponent from "../../views/platforms/components/PlatformListItem.vue";
import HeaderComponent from "../components/HeaderComponent.vue";
import Table from "../components/Datatables/Table";
import Pagination from "../components/Datatables/Pagination.vue";
import XgrowBreadcrumb from "../components/XgrowDesignSystem/Breadcrumb/XgrowBreadcrumb.vue";
import XgrowTabNav from "../components/XgrowDesignSystem/Tab/XgrowTabNav.vue";
import XgrowTab from "../components/XgrowDesignSystem/Tab/XgrowTab.vue";
import XgrowTabContent from "../components/XgrowDesignSystem/Tab/XgrowTabContent.vue";
import Input from "../components/XgrowDesignSystem/Input";
import ModalComponent from "../components/ModalComponent";
import UploadImage from "../components/UploadImage";
import VerifyDocument from "../components/XgrowDesignSystem/Alert/VerifyDocument";
import { ApmVuePlugin } from '@elastic/apm-rum-vue';
import apmConfig from '../config/elk-apm';

import axios from "axios";

const vue = require("vue");

const app = vue.createApp({
    delimiters: ["[[", "]]"],
    data() {
        return {
            verifyDocument,
            recipientStatusMessage,
            /** Loading Modal */
            loading: {
                active: false,
                status: "loading"
            },
            editModal: {
                isOpen: false
            },
            /** Tabs */
            activeScreen: "platforms.own",

            /** Toggle View Mode */
            viewMode: "grid", // grid|list

            /** Platforms Data */
            platforms: [],
            collaborations: [],

            /** Pagination */
            pagination: {
                platforms: {
                    totalPages: 1,
                    totalResults: 0,
                    currentPage: 1,
                    limit: 25
                },
                collaborations: {
                    totalPages: 1,
                    totalResults: 0,
                    currentPage: 1,
                    limit: 25
                }
            },

            /** Breadcrumbs */
            breadcrumbs: [
                { title: "Plataformas", link: "#" }
            ],

            /** Tabs */
            tabs: {
                items: [
                    { title: "Plataformas", screen: "platforms.own" },
                    { title: "Colaborações", screen: "platforms.collaboration" }
                ]
            },
            /** If has search bar */
            filter: {
                platforms: {
                    search: '',
                },
                collaborations: {
                    search: '',
                }
            },
            iconUrl: "https://las.xgrow.com/background-default.png",
            platformId: null,
        };
    },
    watch: {
        "filter.collaborations.search": function () {
            this.searchCollaboration();
        },
        "filter.platforms.search": function () {
            this.searchOwner();
        }
    },
    methods: {
        /** Change screen by value */
        changePage: function (screen) {
            this.activeScreen = screen.toString();
        },
        /** Change view mode */
        changeViewMode: function (mode) {
            this.viewMode = mode;
        },
        /** OWNER */
        onPageChange: async function (page) {
            this.pagination.platforms.currentPage = page;
            await this.getPlatforms();
        },
        onLimitChange: async function (value) {
            this.pagination.platforms.limit = parseInt(value);
            this.pagination.platforms.currentPage = 1;
            await this.getPlatforms();
        },
        /** Used for owner search with timer */
        searchOwner: async function () {
            let term = this.filter.platforms.search;
            setTimeout(() => {
                if (term === this.filter.platforms.search) {
                    this.loading.active = true;
                    axios.post(searchPlatformUrl, {
                        offset: this.pagination.platforms.limit,
                        search: this.filter.platforms.search,
                        page: this.pagination.platforms.currentPage ?? 1
                    }).then((res) => {
                        const data = res.data.response.ownerPlatforms;
                        this.platforms = data.data;
                        this.pagination.platforms.currentPage = data.current_page;
                        this.pagination.platforms.totalPages = data.last_page;
                        this.pagination.platforms.totalResults = data.total;
                        this.loading.active = false;
                    }).catch((err) => console.log(err));
                }
            }, 1000);
        },
        getPlatforms: async function () {
            this.loading.active = true;
            try {
                const res = await axios.post(searchPlatformUrl, {
                    offset: this.pagination.platforms.limit,
                    search: this.filter.platforms.search,
                    page: this.pagination.platforms.currentPage ?? 1
                });
                const data = res.data.response.ownerPlatforms;
                this.platforms = data.data;
                this.pagination.platforms.results = data.total;
                this.pagination.platforms.currentPage = data.current_page;
                this.pagination.platforms.totalPages = data.last_page;
                this.pagination.platforms.totalResults = data.total;
                this.loading.active = false;
            } catch (e) {
                this.loading.active = false;
            }
        },

        /** COLLABORATION */
        onPageChangeCollaborations: async function (page) {
            this.pagination.collaborations.currentPage = page;
            await this.getCollaborationPlatforms();
        },
        onLimitChangeCollaborations: async function (value) {
            this.pagination.collaborations.limit = parseInt(value);
            this.pagination.collaborations.currentPage = 1;
            await this.getCollaborationPlatforms();
        },
        /** Used for search with timer */
        searchCollaboration: async function () {
            let term = this.filter.collaborations.search;
            setTimeout(() => {
                if (term === this.filter.collaborations.search) {
                    this.loading.active = true;
                    axios.post(searchPlatformUrl, {
                        offset: this.pagination.collaborations.limit,
                        search: this.filter.collaborations.search,
                        page: this.pagination.collaborations.currentPage ?? 1
                    }).then((res) => {
                        const data = res.data.response.platforms;
                        this.collaborations = data.data;
                        this.pagination.collaborations.currentPage = data.current_page;
                        this.pagination.collaborations.totalPages = data.last_page;
                        this.pagination.collaborations.totalResults = data.total;
                        this.loading.active = false;
                    }).catch((err) => console.log(err));
                }
            }, 1000);
        },
        getCollaborationPlatforms: async function () {
            this.loading.active = true;
            try {
                const res = await axios.post(searchPlatformUrl, {
                    offset: this.pagination.collaborations.limit,
                    search: this.filter.collaborations.search,
                    page: this.pagination.collaborations.currentPage ?? 1
                });
                const data = res.data.response.platforms;
                this.collaborations = data.data;
                this.pagination.collaborations.results = data.total;
                this.pagination.collaborations.currentPage = data.current_page;
                this.pagination.collaborations.totalPages = data.last_page;
                this.pagination.collaborations.totalResults = data.total;
                this.loading.active = false;
            } catch (e) {
                this.loading.active = false;
            }
        },
        receiveImage(obj) {
            this.iconUrl = obj.file.files[0];
        },
        modalEditPlatform: function (id) {
            this.platformId = id;
            this.editModal.isOpen = true;
        },
        saveThumb: async function () {
            try {
                this.loading.active = true;
                const headers = {
                    headers: { "Content-Type": "multipart/form-data" }
                };
                const formData = new FormData();
                formData.append("platformId", this.platformId);
                formData.append("image", this.iconUrl);
                formData.append("_method", "put");
                const res = await axios.post(changePlatformThumbUrl, formData, headers);
                await this.getPlatforms();
                await this.getCollaborationPlatforms();
                this.loading.active = false;

                this.editModal.isOpen = false;
                this.iconUrl = "https://las.xgrow.com/background-default.png";
                this.platformId = null;
                this.$refs.iconUrl.reset();

                successToast("Ação realizada com sucesso", res.data.message.toString())
            } catch (e) {
                this.loading.active = false;
                errorToast("Falha ao realizar ação", e.response.data.message.toString());
            }
        },
    },
    /** Created lifecycle */
    async created() {
        await this.getPlatforms();
        await this.getCollaborationPlatforms();
    }
});

app.component("xgrow-tab-nav", XgrowTabNav);
app.component("xgrow-tab", XgrowTab);
app.component("xgrow-tab-content", XgrowTabContent);
app.component("xgrow-breadcrumb", XgrowBreadcrumb);
app.component("status-modal-component", StatusModalComponent);
app.component("platform-card-component", PlatformCardComponent);
app.component("platform-list-item-component", PlatformListItemComponent);
app.component("header-component", HeaderComponent);
app.component("xgrow-table-component", Table);
app.component("xgrow-pagination-component", Pagination);
app.component("xgrow-input-component", Input);
app.component("xgrow-modal-component", ModalComponent);
app.component("upload-image", UploadImage);
app.component("verify-document", VerifyDocument)

app.use(ApmVuePlugin, apmConfig);

app.mount("#platforms");
