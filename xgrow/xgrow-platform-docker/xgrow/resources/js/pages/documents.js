import * as VueRouter from "vue-router";
import routes from "../routes/documents";
import { createStore } from "vuex";
import StatusModalComponent from "../components/StatusModalComponent";
import VerifyDocument from "../components/XgrowDesignSystem/Alert/VerifyDocument";
import axios from "axios";
import { ApmVuePlugin } from '@elastic/apm-rum-vue';
import apmConfig from '../config/elk-apm';

const vue = require("vue");

const router = VueRouter.createRouter({
    history: VueRouter.createWebHistory(),
    routes,
});

const app = vue.createApp({
    data() {
        return {
            identity: {},
            verifyDocument,
        };
    },
    async created() {
        await this.getInfo();
    },
    methods: {
        setTab(value) {
            if (!this.$store.state.edit) return;
            this.$store.commit("setTab", value);
        },
        async getInfo() {
            this.$store.commit("setLoading", true);
            await axios
                .get(getIdentity)
                .then(({ data }) => {
                    if (data.data.verified) {
                        this.$store.commit("isEdit", true);
                        this.$store.commit("setTab", "bank-data");
                    } else {
                        this.$store.commit("setTab", "validate");
                    }
                })
                .catch((e) => console.log(e));
            this.$store.commit("setLoading", false);
        },
    },
});

const store = createStore({
    state() {
        return {
            tab: "",
            loading: false,
            edit: false,
            documentType: "",
            hasErrors: false,
            errorMessage: "",
            two_factor_code: "",
            identity: {
                name: "",
                document: "",
            },
        };
    },
    mutations: {
        setTab(state, tab) {
            state.tab = tab;
        },
        setLoading(state, loading) {
            state.loading = loading;
        },
        isEdit(state, edit) {
            state.edit = edit;
        },
        setDocumentType(state, documentType) {
            state.documentType = documentType;
        },
        setErrorAlert(state, hasErrors) {
            state.hasErrors = hasErrors;
        },
        setTokenCode(state, token) {
            state.two_factor_code = token;
        },
        setIdentity(state, identity) {
            state.identity = identity;
        },
        setErrorMessage(state, message) {
            state.errorMessage = message;
        }
    },
});

app.use(router);
app.use(store);
app.component("status-modal-component", StatusModalComponent);
app.component("verify-document", VerifyDocument)
app.use(ApmVuePlugin, {
    router,
    config: apmConfig.config
});
app.mount("#documentsApp");
