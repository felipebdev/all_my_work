import * as VueRouter from 'vue-router';
import routes from "../routes/products";
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

      }
    },
    mutations: {

    },
  })

app.use(store);
app.use(ApmVuePlugin, {
    router,
    config: apmConfig.config
});

app.mount('#productsPage');
