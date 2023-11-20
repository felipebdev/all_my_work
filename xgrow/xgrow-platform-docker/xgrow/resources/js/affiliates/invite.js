import axios from "axios";
import Loading from "../components/XgrowDesignSystem/Utils/Loading";
import Modal from "../components/XgrowDesignSystem/Modals/Modal";
import { ApmVuePlugin } from '@elastic/apm-rum-vue';
import apmConfig from '../config/elk-apm';

const vue = require("vue");

const app = vue.createApp({
    delimiters: ["[[", "]]"],
    data() {
        return {
            openModalConfirm: false,
            openModalNotApproved : false,
            isLoading: false,
            status: "loading",
            message: '',
            error: false,
        };
    },
    methods: {
        close(){
            window.location.href = '/';
        },
        confirmAffiliation(invite_link){
            this.isLoading = true;

            const defaultErrorMessage = "Ocorreu um erro ao gerar o recebedor. Entre em contato com o suporte!"

            const json = JSON.stringify({ affiliation_settings_id: invite_link });
            axios.post(affiliationConfirmUrl, json, {
                headers: {
                    'Content-Type': 'application/json'
                }
            }).then(response => {
                this.error = response.data.error;

                if (this.error) {
                    this.message = response?.data?.message || defaultErrorMessage;
                } else {
                    this.message = response.data.message;
                }

                this.openModalConfirm = true;
                this.isLoading = false;
            }).catch(error => {
                this.error = true;
                this.message = error?.response?.data?.message || defaultErrorMessage;
                this.openModalConfirm = true;
                this.isLoading = false;
            });
        },
        loading(){
            this.isLoading = true;
        }
    },

    /** Created lifecycle */
    created() {
        this.isLoading = true;
        this.openModalNotApproved = (openModalNotApproved == 1)? true : false ;
    },

    mounted(){
        this.isLoading = false;
    }
});

app.use(ApmVuePlugin, apmConfig);

app.component("loading", Loading);
app.component('modal', Modal)
app.mount("#invite");
