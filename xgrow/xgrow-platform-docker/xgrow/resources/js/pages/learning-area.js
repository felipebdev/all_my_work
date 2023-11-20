import Menu from "../../views/learning-area/components/Menu";
import MobileMenu from "../../views/learning-area/components/MobileMenu";
import { createPinia } from "pinia";
import * as VueRouter from "vue-router";
import routes from "../routes/learning-area";
import initSentry from "../config/sentry";
import { useAxiosStore } from "../store/components/axios";
import VueCookies from 'vue-cookies'
import { ApmVuePlugin } from '@elastic/apm-rum-vue';
import apmConfig from '../config/elk-apm';

const vue = require('vue');
const pinia = createPinia()

/** VUE ROUTER CONFIG */
export const router = VueRouter.createRouter({
    history: VueRouter.createWebHistory(),
    routes,
});

router.beforeEach(async (to, from) => {
    const axiosStore = useAxiosStore();
    await axiosStore.setAxiosHeader();
    const token = {
        fxHeader: axiosStore.axiosHeader,
        fxUrl: axiosStore.axiosUrl,
    }
    $cookies.set('fxToken', token)
})

/** VUE CONFIG */
const app = vue.createApp({
    async beforeCreate() {
        localStorage.setItem('sidenav-state', 'toggled');
    }
});

/** INIT SENTRY */
initSentry(app, router);

app.component('menu-component', Menu);
app.component('mobile-menu-component', MobileMenu);
app.use(VueCookies, { expires: '1d', secure: true })
app.use(router);
app.use(pinia);
app.use(ApmVuePlugin, {
    router,
    config: apmConfig.config
});

app.mount('#learningAreaApp');
