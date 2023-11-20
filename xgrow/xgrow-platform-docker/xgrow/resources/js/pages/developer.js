import * as VueRouter from 'vue-router';
import routes from "../routes/developer";
import { createStore } from "vuex";
import * as Sentry from "@sentry/vue";
import {BrowserTracing} from "@sentry/tracing";
import { ApmVuePlugin } from '@elastic/apm-rum-vue';
import apmConfig from '../config/elk-apm';

const vue = require('vue');

/** VUE ROUTER CONFIG */
const router = VueRouter.createRouter({
    history: VueRouter.createWebHistory(),
    routes
});

/** VUE CONFIG */
const app = vue.createApp({});

/** SENTRY CONFIG
 * https://docs.sentry.io/platforms/javascript/guides/vue/
 */
Sentry.init({
    app,
    dsn: process.env.SENTRY_DSN_FRONTEND,
    integrations: [
        new BrowserTracing({
            routingInstrumentation: Sentry.vueRouterInstrumentation(router),
            tracingOrigins: ["*.xgrow.com.br", "*.xgrow.com", /^\//],
        }),
    ],
    tracesSampleRate: 0.2,
    environment: process.env.SENTRY_ENVIRONMENT,
});

app.use(router);


const store = createStore({
    state() {
      return {
        loading: false,
        isOpenTokenModal: false,
        isOpenAuthModal: false,
        isOpenSuccessModal: false,
        isOpenFailedModal: false,
        isDeleteConfirmModal: false
      }
    },
    mutations: {
      setLoading(state, loading) {
        state.loading = loading
      },
      toggleTokenModal(state, status) {
        state.isOpenTokenModal = status
      },
      toggleAuthModal(state, status) {
        state.isOpenAuthModal = status
      },
      toggleSuccessModal(state, status) {
        state.isOpenSuccessModal = status
      },
      toggleFailedModal(state, status) {
        state.isOpenFailedModal = status
      },
      toggleDeleteConfirmModal(state, status) {
        state.isDeleteConfirmModal = status
      },
    },
  })

app.use(store);

app.use(ApmVuePlugin, {
  router,
  config: apmConfig.config
});

app.mount('#developerApp');