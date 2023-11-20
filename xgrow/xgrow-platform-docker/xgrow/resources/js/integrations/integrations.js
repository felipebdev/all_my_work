import axios from "axios";
import StatusModalComponent from "../components/StatusModalComponent";
import IntegrationsIndexComponent
    from "../../../modules/Integration/Views/integrations/components/IntegrationsIndex";

import { ApmVuePlugin } from '@elastic/apm-rum-vue';
import apmConfig from '../config/elk-apm';

const vue = require("vue");

const app = vue.createApp({
    delimiters: ["[[", "]]"],
    data() {
        return {
            method: "create",
            loading: {
                active: false,
                status: "loading"
            },
            activeScreen: "integrations.index"
        };
    },
    methods: {
        /** Example comments and function async */
        asyncNameFunction: async function (data) {
        },
        /** Example comments and function no async */
        noAsyncNameFunction: function (data) {
        }
    },
    /** Created lifecycle */
    async created() {

    }
});

app.component("status-modal-component", StatusModalComponent);
app.component("integrations-index-page", IntegrationsIndexComponent);

app.use(ApmVuePlugin, apmConfig);

app.mount("#integrations");
