import * as VueRouter from 'vue-router';
import routes from "../routes/affiliates";
import { createStore } from 'vuex'
import StatusModalComponent from '../components/StatusModalComponent'
import axios from 'axios'
import { ApmVuePlugin } from '@elastic/apm-rum-vue';
import apmConfig from '../config/elk-apm';

const vue = require('vue');

const router = VueRouter.createRouter({
    history: VueRouter.createWebHistory(),
    routes
});

const app = vue.createApp({
    data() {
      return {
        tab: this.$store.state.tab,
        loading: this.$store.state.loading,
      }
    },
    async mounted() {
      let tabByUrl = window.location.pathname.replace('/affiliates','').replace('/', '');
      tabByUrl = tabByUrl === '' ? 'active' : tabByUrl;
      this.setTab(tabByUrl, false);
      await this.getAdvancedFilters()
    },
    methods: {
        setTab(value, redirect = true) {
          this.$store.commit('setTab', value)
          if (redirect) {
            value = value === 'active' ? '' : value
            this.$router.push(`/affiliates/${value}`)
          }
        },
        async getAdvancedFilters() {
          const { data } = await axios.get(affiliatesFilters)
          this.$store.commit('setAdvancedFilters', data)
        }
    }
});


const store = createStore({
    state () {
      return {
        tab: 'active',
        loading: false,
        advancedFilters: []
      }
    },
    mutations: {
      setTab (state, tab) {
        state.tab = tab
      },
      setLoading (state, loading) {
        state.loading = loading
      },
      setAdvancedFilters (state, filter) {
        state.advancedFilters = filter
      }
    }
})

app.use(store);
app.use(router);
app.component("status-modal-component", StatusModalComponent);
app.use(ApmVuePlugin, {
  router,
  config: apmConfig.config
});
app.mount('#affiliatesApp');
