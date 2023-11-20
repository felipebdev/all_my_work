import StatusModalComponent from "../components/StatusModalComponent";
import moment from "moment";
import { ApmVuePlugin } from '@elastic/apm-rum-vue';
import apmConfig from './config/elk-apm';

const vue = require("vue");

const app = vue.createApp({
    delimiters: ["[[", "]]"],
    data() {
        return {
            method: "create",
            activeScreen: "active.screen",
            /** If has loading */
            loading: {
                active: true,
                status: "loading"
            },
            /** If has search bar */
            filter: {
                valueA: null,
                valueB: null
            },
            /** If has Breadcrumbs */
            breadcrumbs: [
                {title: "Xgrow", link: "/"},
                {title: "Bread 2", link: false}
            ],
            /** If has Tabs */
            tabs: {
                items: [
                    {title: "Title Tab 1", screen: "screen.name.one"},
                    {title: "Title Tab 2", screen: "screen.name.two"}
                ]
            },
            /** If has Datatables and Pagination */
            results: [],
            pagination: {
                totalPages: 1,
                totalResults: 0,
                currentPage: 1,
                limit: 25
            }
        };
    },
    methods: {
        /** Async function Id */
        asyncFunction: async function (id) {
        },
        /** No async function */
        noAsyncFunction: function (status) {
        }
    },

    /** Created lifecycle */
    async created() {
    }
});

// app.component("status-modal-component", StatusModalComponent);

/** Filter user for datetime format */
app.config.globalProperties.$filters = {
    formatDateTimeBR(value) {
        return moment(value).format("DD/MM/YYYY HH:mm:ss");
    },
    formatDateBR(value) {
        return moment(value).format("DD/MM/YYYY");
    },
    formatBRLCurrency: function (val) {
        return new Intl.NumberFormat("pt-BR", {style: "currency", currency: "BRL"}).format(val);
    }
};

app.use(ApmVuePlugin, apmConfig);

app.mount("#containerId");
