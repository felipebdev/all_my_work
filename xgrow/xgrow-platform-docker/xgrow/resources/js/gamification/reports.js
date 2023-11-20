import StatusModalComponent from '../components/StatusModalComponent.vue';
import Pagination from '../components/Datatables/Pagination.vue';
import Table from '../components/Datatables/Table.vue';
import Modal from '../components/ModalComponent.vue';

import Multiselect from '@vueform/multiselect'
import "@vueform/multiselect/themes/default.css";

import DatePicker from 'vue-datepicker-next';
import 'vue-datepicker-next/index.css';
import 'vue-datepicker-next/locale/pt-br';

import moment from 'moment';
import axios from "axios"

import { ApmVuePlugin } from '@elastic/apm-rum-vue';
import apmConfig from '../config/elk-apm';

const vue = require('vue');

const app = vue.createApp({
    delimiters: ["[[", "]]"],
    data() {
        return {
            /** Loading */
            statusLoading: false,
            status: 'loading',
            /** Pagination */
            paginationCurrentPage: 1, // Current Page
            paginationLimit: 25, // Limit by page
            paginationTotal: 0, // Total Results
            paginationTotalResults: 0, //Total Pages
            /** Challenge Data */
            results: [
                {
                    _id: 1,
                    name: 'Fernando Martins',
                    action: 'Concluiu o curso “Seja produtivo”',
                    createdAt: '04/11/21 às 15:22h',
                    level: 'Raiz',
                    score: '328',
                },
                {
                    _id: 2,
                    name: 'Dan Felix',
                    action: 'Concluiu o curso “Seja produtivo”',
                    createdAt: '23/03/21 às 14:56',
                    level: 'Floresta',
                    score: '586',
                },
                {
                    _id: 3,
                    name: 'John Foe',
                    action: 'Concluiu o curso “Seja produtivo”',
                    createdAt: '08/10/21 às 05:28h',
                    level: 'Árvore',
                    score: '1',
                },
            ],
            /** Filters */
            filter: {
                searchValue: '',
                subscriberName: '',
                dateRangeValue: null,
                dateRangeFormat: null,
                actionValue: '',
                actionOptions: [
                    { id: 1, name: 'Concluiu o curso' },
                    { id: 2, name: 'Não concluído' },
                ],
                levelValue: '',
                levelOptions: [
                    { id: 1, name: 'Solo' },
                    { id: 2, name: 'Semente' },
                    { id: 3, name: 'Raiz' },
                    { id: 4, name: 'Planta' },
                    { id: 5, name: 'Árvore' },
                    { id: 6, name: 'Floresta' },
                ],
                challengeValue: '',
                challengeOptions: [
                    { id: 1, name: 'Preparando o solo' },
                    { id: 2, name: 'Escolhendo a semente' },
                    { id: 3, name: 'Primeiras raízes' },
                ],
            }
        }
    },
    methods: {
        /** Get Total de Pages */
        totalPages: function () {
            const qty = Math.ceil(this.paginationTotal / this.paginationLimit);
            this.paginationTotalResults = (qty <= 1) ? 1 : qty;
        },
        /** On change page */
        onPageChange: async function (page) {
            this.paginationCurrentPage = page;
            // await this.getData();
        },
        /** Limit by size itens */
        onLimitChange: async function (value) {
            this.paginationLimit = parseInt(value);
            // await this.getData();
        },
        /** Start Period component */
        startPeriod: function () {
            const endDate = new Date();
            const startDate = new Date(new Date().setDate(endDate.getDate() - 30));
            this.filter.dateRangeValue = [startDate, endDate];
            this.filter.dateRangeFormat = moment(startDate).format('DD/MM/YYYY') + ' - ' + moment(endDate).format('DD/MM/YYYY');
        },
        /** Change period */
        changePeriodFilter: async function () {
            this.filter.dateRangeFormat = moment(this.filter.dateRangeValue[0]).format('DD/MM/YYYY') + ' - ' + moment(this.filter.dateRangeValue[1]).format('DD/MM/YYYY');
            // await this.getData();
        },
    },
    async created() {
        this.statusLoading = true;
        await new Promise(resolve => setTimeout(resolve, 1000));
        this.statusLoading = false;
        this.totalPages();
        this.startPeriod();
    },
});

app.component("status-modal-component", StatusModalComponent);
app.component("xgrow-pagination-component", Pagination);
app.component("xgrow-table-component", Table);
app.component("xgrow-modal-component", Modal);
app.component("multiselect-component", Multiselect);
app.component("xgrow-daterange-component", DatePicker);

/** Filter user for datetime format */
app.config.globalProperties.$filters = {
    formateDateBR(value) {
        return moment(value).format('DD/MM/YYYY HH:mm:ss')
    }
}

app.use(ApmVuePlugin, apmConfig);

app.mount("#reports");
