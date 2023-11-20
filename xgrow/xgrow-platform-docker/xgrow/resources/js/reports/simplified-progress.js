import Pagination from '../components/Datatables/Pagination.vue';
import Table from '../components/Datatables/Table.vue';
import StatusModalComponent from "../components/StatusModalComponent";
import moment from "moment";

import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";

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

          /** Pagination */
          pagination: {
            totalPages: 1,
            totalResults: 0,
            currentPage: 1,
            limit: 25
          },

          /** Filter */
          filter: {
            searchValue: '',
            course: null,
            courseOptions: [],
          },

          /** Status Modal */
          statusLoading: true,
          status: 'loading',

          /** Screen pages */
          activeScreen: "progress.all",

          /** Subscriber's Details */
          details: {
            subscriber: {},
            accessData: {},
            courseProgress: []
          }
        };
    },
    methods: {
      /** Change screen by value */
      changePage: function (screen) {
        this.activeScreen = screen.toString();
      },
      /** On change page */
      onPageChange: async function (page) {
        this.getAPIData({ page });
      },
      /** Limit by size itens */
      onLimitChange: async function (value) {
        this.getAPIData({ offset: parseInt(value) });
      },
      /** Gets all API data and change the loading status */
      getAPIData: async function (options = {}) {
        this.statusLoading = true;

        const { pagination, results } = await this.getSubscribersData(
          options.hasOwnProperty('offset') ? options.offset : this.pagination.limit,
          options.hasOwnProperty('term') ? options.term : this.filter.searchValue,
          options.hasOwnProperty('course') ? options.course : this.filter.course,
          options.hasOwnProperty('page') ? options.page : null,
        );

        this.results = results;
        this.pagination = pagination;
        this.statusLoading = false;
      },
      /** Return subscriber list based on the term, course and pagination data */
      getSubscribersData: async function (offset = 25, term = null, course = null, page = null) {
        const res = await axios.get(getSubscribersRoute, {
          params: { offset, term, course, page }
        });

        const data = res.data.response[0];
        const pagination = {
          currentPage: data.current_page,
          limit: data.per_page,
          totalPages: data.last_page,
          totalResults: data.total
        }

        return { pagination, results: data.data };
      },
      /** Used for search with timer */
      search: async function () {
        const term = this.filter.searchValue;
        setTimeout(async () => {
          if (term === this.filter.searchValue) {
            this.getAPIData();
          }
        }, 1000);
      },
      /** Return progress details from a single subscriber in a specific course */
      getSubscriberDetails: async function (subscriberId) {
        if (this.filter.course === null || this.filter.course === -1) {
          errorToast("Algum erro aconteceu!", "Selecione um curso em Filtros Avançados");
          return;
        }

        this.statusLoading = true;

        const res = await axios.get(getSubscriberSimplifiedProgress, {
          params: { subscriber: subscriberId, course: this.filter.course }
        });

        const data = res.data.response.data;

        if (data === undefined || data.length < 1) {
          alertToast("Progresso não encontrado!", "Não foi encontrado progresso do aluno no curso selecionado.");
          this.statusLoading = false;
          return;
        }

        const subscriber = {
          username: data[0].userName,
          email: data[0].userEmail
        };
        const accessData = {
          course: data[0].courses[0].courseName,
          percentage: data[0].courses[0].percentCourseCompleted,
          firstAccess: data[0].courses[0].firstAccess,
          lastAccess: data[0].courses[0].lastAccess
        };
        const courseProgress = data[0].courses[0].modules;

        this.details = { subscriber, accessData, courseProgress };

        this.statusLoading = false;
        this.changePage('progress.summary');
      },
      /** Change to the primary page */
      backToAll: function () {
        this.changePage('progress.all');
      },
      /** Lists all courses in the platform */
      getCourses: async function () {
        const res = await axios.get(getCoursesRoute);
        const data = res.data.response.data;
        return data || null;
      }
    },
    watch: {
      /** Trigger search when term and/or course is changed */
      "filter.searchValue": function () {
        this.search();
      },
      "filter.course": function (newValue) {
        this.search();
      }
    },
    async created() {
      const courses = await this.getCourses();
      this.filter.courseOptions = courses;
      this.getAPIData();
    },
});

app.component("status-modal-component", StatusModalComponent);
app.component("xgrow-pagination-component", Pagination);
app.component("xgrow-table-component", Table);
app.component("multiselect-component", Multiselect);

app.config.globalProperties.$util = {
  formatDateTimeBR(value) {
    return moment(value).format("DD/MM/YYYY HH:mm:ss");
  },
  formatDateBR(value) {
    return moment(value).format("DD/MM/YYYY");
  }
};

app.use(ApmVuePlugin, apmConfig);

app.mount("#simplifiedProgress");
