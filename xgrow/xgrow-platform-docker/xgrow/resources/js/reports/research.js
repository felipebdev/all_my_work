import Pagination from '../components/Datatables/Pagination.vue';
import Table from '../components/Datatables/Table.vue';
import StatusModalComponent from '../components/StatusModalComponent.vue';

import Multiselect from '@vueform/multiselect'
import "@vueform/multiselect/themes/default.css";

import DatePicker from 'vue-datepicker-next';
import 'vue-datepicker-next/index.css';
import 'vue-datepicker-next/locale/pt-br';

import { ApmVuePlugin } from '@elastic/apm-rum-vue';
import apmConfig from '../config/elk-apm';

import axios from "axios";
const vue = require('vue');

const app = vue.createApp({
    delimiters: ["[[", "]]"],
    data() {
        return {
            /** Result Total */
            results: [],

            /** Content Filter */
            contents: [],
            content: null,

            /** Loading */
            statusLoading: false,
            status: 'loading',

            /** Pagination */
            currentPage: 1, // Current Page
            limit: 25, // Limit by page
            total: 0, // Total Results
            totalResults: 0, //Total Pages

            /** Search Filter */
            search: '',

            /** DateRange Picker */
            dateRange: null,
            dateRangeFormat: null,
        };
    },
    watch: {
        search: function () {
            this.searchTerm();
        }
    },
    methods: {
        /** Get all data */
        getData: async function () {
            this.statusLoading = true;
            const res = await axios.get(getAPIResult, { params: { page: this.currentPage, offset: this.limit, filter: this.search, content: this.content, period: this.dateRangeFormat } });
            this.results = res.data.response.data.data;
            this.contents = res.data.response.contents;
            this.total = res.data.response.data.total;
            this.totalPages();
            this.statusLoading = false;
        },
        /** Get Total de Pages */
        totalPages: function () {
            const qty = Math.ceil(this.total / this.limit);
            this.totalResults = (qty <= 1) ? 1 : qty;
        },
        /** On change page */
        onPageChange: async function (page) {
            this.currentPage = page;
            await this.getData();
        },
        /** Limit by size itens */
        onLimitChange: async function (value) {
            this.limit = parseInt(value);
            await this.getData();
        },
        /** Used for search with timer */
        searchTerm: async function () {
            let term = this.search;
            setTimeout(() => {
                if (term == this.search) {
                    this.statusLoading = true;
                    axios.get(getAPIResult, { params: { page: this.currentPage, offset: this.limit, filter: this.search, content: this.content, period: this.dateRangeFormat } })
                        .then((res) => {
                            this.results = res.data.response.data.data;
                            this.total = res.data.response.data.total;
                            this.totalPages();
                            this.statusLoading = false;
                        }).catch((err) => console.log(err));
                }
            }, 1000);
        },
        /** Select by content */
        selectByContent: async function () {
            await this.getData();
        },
        convertDateTime: async function () {
            this.dateRangeFormat = moment(this.dateRange[0]).format('DD/MM/YYYY') + ' - ' + moment(this.dateRange[1]).format('DD/MM/YYYY');
            await this.getData();
        },
        // Start date on create
        startDate: function () {
            const endDate = new Date();
            const startDate = new Date(new Date().setDate(endDate.getDate() - 30));
            this.dateRange = [startDate, endDate];
            this.dateRangeFormat = moment(startDate).format('DD/MM/YYYY') + ' - ' + moment(endDate).format('DD/MM/YYYY');
        },
        /** Clear content data */
        clearContent: async function () {
            this.content = null;
            await this.getData();
        },
    },
    async created() {
        this.startDate();
        await this.getData();
    },
});

app.component("status-modal-component", StatusModalComponent);
app.component("xgrow-pagination-component", Pagination);
app.component("xgrow-table-component", Table);
app.component("multiselect-component", Multiselect);
app.component("xgrow-daterange-component", DatePicker);

/** Filter user for datetime format */
app.config.globalProperties.$filters = {
    formateDateBR(value) {
        return moment(value).format('DD/MM/YYYY HH:mm:ss')
    }
}
app.use(ApmVuePlugin, apmConfig);

app.mount("#researchReport");
