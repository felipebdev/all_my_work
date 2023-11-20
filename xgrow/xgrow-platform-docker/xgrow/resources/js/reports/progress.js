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

            /** Course Filter */
            courses: [],
            course: null,

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

            /** Date Access */
            firstAccessFilter: null,
            firstAccessFormat: '',

            lastAccessFilter: null,
            lastAccessFormat: '',
        };
    },
    watch: {
        search: function () {
            this.searchTerm();
        }
    },
    methods: {
        /** Get courses by platform */
        getCourses: async function () {
            const res = await axios.get(getAPICourses);
            this.courses = res.data.response.data;
        },
        /** Get all data */
        getData: async function () {
            this.statusLoading = true;
            const res = await axios.get(getAPIResult, {
                params: {
                    page: this.currentPage,
                    offset: this.limit,
                    search: this.search,
                    course: this.course,
                    firstAccess: this.firstAccessFormat,
                    lastAccess: this.lastAccessFormat,
                }
            });
            this.results = res.data.response.data.data;
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
                    axios.get(getAPIResult, {
                        params: {
                            page: this.currentPage,
                            offset: this.limit,
                            search: this.search,
                            course: this.course,
                            firstAccess: this.firstAccessFormat,
                            lastAccess: this.lastAccessFormat,
                        }
                    }).then((res) => {
                        this.results = res.data.response.data.data;
                        this.total = res.data.response.data.total;
                        this.totalPages();
                        this.statusLoading = false;
                    }).catch((err) => console.log(err));
                }
            }, 1000);
        },
        /** Search by firstAccess */
        firstAccessConvert: async function () {
            if (this.firstAccessFilter[0] !== null || this.firstAccessFilter[1] !== null) {
                this.firstAccessFormat = moment(this.firstAccessFilter[0]).format('DD/MM/YYYY') + ' - ' + moment(this.firstAccessFilter[1]).format('DD/MM/YYYY');
                await this.getData();
            } else {
                this.firstAccessFilter = null;
                this.firstAccessFormat = '';
                await this.getData();
            }
        },
        /** Search by lastAccess */
        lastAccessConvert: async function () {
            if (this.lastAccessFilter[0] !== null || this.lastAccessFilter[1] !== null) {
                this.lastAccessFormat = moment(this.lastAccessFilter[0]).format('DD/MM/YYYY') + ' - ' + moment(this.lastAccessFilter[1]).format('DD/MM/YYYY');
                await this.getData();
            } else {
                this.lastAccessFilter = null;
                this.lastAccessFormat = '';
                await this.getData();
            }
        },
        /** Clear course data */
        clearCourse: async function () {
            this.course = null;
            await this.getData();
        },
    },
    async created() {
        await this.getCourses();
        this.course = Object.keys(this.courses)[0];
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

app.use(ApmVuePlugin, apmConfig)

app.mount("#progressReport");
