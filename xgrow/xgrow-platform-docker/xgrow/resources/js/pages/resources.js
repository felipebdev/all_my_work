import * as VueRouter from "vue-router";
import routes from "../routes/resources";
import { createPinia } from "pinia";
import { ApmVuePlugin } from '@elastic/apm-rum-vue';
import apmConfig from '../config/elk-apm';

const vue = require('vue');
const pinia = createPinia();

/** VUE ROUTER CONFIG */
export const router = VueRouter.createRouter({
    history: VueRouter.createWebHistory(),
    routes,
});

/** VUE CONFIG */
const app = vue.createApp({
    async beforeCreate() {
        localStorage.setItem('sidenav-state', 'untoggled');
    }
});

app.use(router);
app.use(pinia);
app.use(ApmVuePlugin, {
    router,
    config: apmConfig.config
});

app.mount('#resourcesApp');
