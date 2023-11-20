import StatusModalComponent from "../components/StatusModalComponent";

import Maska from "maska";
import axios from "axios";
import { VueReCaptcha } from 'vue-recaptcha-v3'
import { ApmVuePlugin } from '@elastic/apm-rum-vue';
import apmConfig from '../config/elk-apm';

const vue = require("vue");

const app = vue.createApp({
    delimiters: ["[[", "]]"],
    data() {
        return {
            statusLoading: false,
            status: "loading",
            activeScreen: "personalData",
            step: 1,
            personalData: {
                name: "",
                email: "",
                confirmEmail: "",
                phone: ""
            },
            acceptTerms: false
        };
    },
    methods: {
        /** Next Step */
        nextStep: async function (screenName) {
            if (await this.verifyFields()) return true;
            this.activeScreen = screenName;
            this.step++;
        },
        /** Previous step */
        previousStep: function (screenName) {
            this.activeScreen = screenName;
            this.step--;
        },
        /** Save informations */
        save: async function () {
            this.statusLoading = true;
            await this.$recaptchaLoaded();
            try {
                const token = await this.$recaptcha('login');
                const formData = new FormData();
                formData.append("name", this.personalData.name);
                formData.append("email", this.personalData.email);
                formData.append("confirmEmail", this.personalData.confirmEmail);
                formData.append("phone", this.personalData.phone);
                formData.append("accepted_terms", this.acceptTerms);
                formData.append("grecaptcha", token);

                const res = await axios.post(registerUrl, formData);
                this.statusLoading = false;
                successToast("Ação realizada com sucesso", res.data.message.toString());
                await this.nextStep("registerEnd");
                return true;
            } catch (e) {
                this.statusLoading = false;
                if (e.response.status >= 500) {
                    errorToast("Falha ao realizar ação", e.response.statusText);
                } else {
                    const error = e.response.data.response.errors;
                    this.isKeyExists(error, "name");
                    this.isKeyExists(error, "email");
                    this.isKeyExists(error, "email_confirmation");
                    this.isKeyExists(error, "phone");
                    this.isKeyExists(error, "accepted_terms");
                }
            }
        },
        /** Verify error on backend */
        isKeyExists: function (obj, key) {
            if (obj.hasOwnProperty(key)) {
                errorToast("Falha ao realizar ação", obj[key][0].toString());
            }
            return false;
        },
        /** Verify if email exists */
        checkEmailExists: async function (email) {
            const checkEmail = checkEmailUrl.replace(/:id/g, email);
            const res = await axios.get(checkEmail);

            return res.data;
        },
        /** Verify if has error */
        verifyFields: async function () {
            if (this.step === 1) {
                if (this.personalData.name.trim() === "") {
                    errorToast("Algum erro aconteceu!", "O nome não pode ficar em branco.");
                    return true;
                }
                if (!this.emailRegex(this.personalData.email)) {
                    errorToast("Algum erro aconteceu!", "O e-mail informado é inválido.");
                    return true;
                }
                if (this.personalData.confirmEmail.trim() === "") {
                    errorToast("Algum erro aconteceu!", "O e-mail de confirmação não pode ficar em branco.");
                    return true;
                }
                if (this.personalData.email.trim() !== this.personalData.confirmEmail.trim()) {
                    errorToast("Algum erro aconteceu!", "O e-mail e confirmação de e-mail não coincidem");
                    return true;
                }
                if (this.personalData.phone.trim() === "") {
                    errorToast("Algum erro aconteceu!", "O telefone não pode ficar em branco.");
                    return true;
                }
                try {
                    document.getElementById('confirm-data-button').disabled = true;
                    await this.checkEmailExists(this.personalData.email);
                    document.getElementById('confirm-data-button').disabled = false;
                    return false;
                } catch (e) {
                    if (e.response.status >= 500) errorToast("Algum erro aconteceu!", e.response.statusText);
                    if (e.response.status === 422) errorToast("Algum erro aconteceu!", "O e-mail informado já encontra-se registrado.");
                    if (e.response.status === 429) errorToast("Algum erro aconteceu!", "Foram realizadas mais de 5 tentativas. Aguarde 2 minutos e retorne");
                    document.getElementById('confirm-data-button').disabled = false;
                    return true;
                }
            }
            if (this.step === 2) {
                if (!this.acceptTerms) {
                    errorToast("Algum erro aconteceu!", "Para continuar você deve ler e aceitar os termos e condições da Xgrow.");
                    return true;
                }
            }
        },
        /** Redirect to login */
        finishRegister: function () {
            window.location.href = "/login";
        },
        /** Regex for valid email */
        emailRegex: function (val) {
            // /\S+@\S+\.\S+/;
            // const expression = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$/;
            const expression = /\S+@\S+\.\S+/;
            const regex = new RegExp(expression);
            return val.match(regex);
        },
    }
});

app.use(Maska);
app.use(VueReCaptcha, { siteKey: `${rustUrl}` });
app.component("status-modal-component", StatusModalComponent);
app.use(ApmVuePlugin, apmConfig);
app.mount("#register");
