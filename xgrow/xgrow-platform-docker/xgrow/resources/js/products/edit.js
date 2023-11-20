import formatBRLCurrency from "../components/XgrowDesignSystem/Mixins/formatBRLCurrency";
import Plans from "../../views/products/edit/tabs/Plans"
import Links from "../../views/products/edit/tabs/Links"
import Deliveries from "../../views/products/edit/tabs/Deliveries"
import Affiliates from "../../views/products/edit/tabs/Affiliates"
import UpsellGenerator from "../../views/products/edit/tabs/UpsellGenerator"
import CoProducers from "../../views/products/edit/tabs/CoProducers"
import { ApmVuePlugin } from '@elastic/apm-rum-vue';
import apmConfig from '../config/elk-apm';

const vue = require("vue");

const app = vue.createApp({
    delimiters: ["[[", "]]"],
    mixins: [formatBRLCurrency],
    data() {
        return {
            isOpen: false,
        };
    },
    async mounted() {

    },
    methods: {

    },

});

app.component('Plans', Plans)
app.component('Links', Links)
app.component('Deliveries', Deliveries)
app.component('Affiliates', Affiliates)
app.component('Upsell', UpsellGenerator)
app.component('Coproducers', CoProducers)
app.use(ApmVuePlugin, apmConfig)

app.mount("#productEditApp");
