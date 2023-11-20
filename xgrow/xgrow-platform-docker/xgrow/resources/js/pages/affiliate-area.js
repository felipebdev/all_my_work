import * as VueRouter from 'vue-router';
import routes from "../routes/affiliate-area";

import { ApmVuePlugin } from '@elastic/apm-rum-vue';
import apmConfig from '../config/elk-apm';

const vue = require('vue');
const Maska = require('maska');


const router = VueRouter.createRouter({
    history: VueRouter.createWebHistory(),
    routes
});

const app = vue.createApp({
    mounted() {

    }
});

app.use(router);
app.use(Maska);

app.use(ApmVuePlugin, {
    router,
    config: apmConfig.config
});


app.mount('#affiliateAreaApp');
