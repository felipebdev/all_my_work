import axios from "axios";
import { ALL_COURSES_FOR_DELIVERY_QUERY_AXIOS } from "../graphql/queries/courses";
import { ALL_SECTIONS_QUERY_AXIOS } from '../graphql/queries/sections';
import { axiosGraphqlClient } from "../config/axiosGraphql";
import { ApmVuePlugin } from '@elastic/apm-rum-vue';
import apmConfig from '../config/elk-apm';

const vue = require("vue");

const app = vue.createApp({
    delimiters: ["[[", "]]"],
    data() {
        return {
            deliveries: [],
            hasDelivery: false,
            externalArea: false,
            internalArea: false,
            onlySell: false,
            email: {
                subjectEmail: "",
                messageEmail: ""
            },
            graphql: {
                active: false,
                data: [],
                sections: []
            },
            selectedContent: {
                courses: [],
                sections: [],
            }
        };
    },
    methods: {
        async getAllDeliveries() {
            const res = await axios.post(getAllDeliveries, { product });

            const { external_area, internal_area, only_sell, email, message } = res.data.product;
            this.externalArea = !!external_area;
            this.internalArea = !!internal_area;
            this.onlySell = !!only_sell;

            this.hasDelivery = res.data.has.course || res.data.has.section;

            this.courses = res.data.courses;
            this.sections = res.data.sections;

            this.email.subjectEmail = email;
            this.email.messageEmail = message;

            if (message === "" || message === null) {
                const msg = "Olá ##NOME_ASSINANTE##,\n" +
                    " \n" +
                    "Seus dados de acesso são os abaixo:\n" +
                    " \n" +
                    "Login: ##EMAIL_ASSINANTE##\n" +
                    "Senha: ##AUTO##\n" +
                    " \n" +
                    "Link de acesso: " + accessLink;

                this.email.messageEmail = msg;
                this.email.subjectEmail = "Bem-vindo";
            }
        },
        hasChecked(idSearch, type) {
            if (type === "c") {
                return this.selectedContent.courses.some((x) => x === idSearch);
            } else {
                return this.selectedContent.sections.some((x) => x === idSearch);
            }
        },
        async syncDelivery() {
            const idContent = event.target.getAttribute("data-id");
            const typeContent = event.target.getAttribute("data-content");
            const idProduct = product;

            if (event.target.checked) {
                showStatusModal(true, "saving");
                try {
                    const res = await axios.post(`${attachContent}`, { typeContent, idContent, idProduct });
                    showStatusModal(false);
                    successToast("Item adicionado.", res.data.message.toString());
                } catch (e) {
                    showStatusModal(false);
                    errorToast("Erro ao adicionar item", "Ocorreu um erro ao adicionar esse item, por favor tente mais tarde.");
                }
            } else {
                showStatusModal(true, "saving");
                const res = await axios.post(`${detachContent}`, { typeContent, idContent, idProduct });
                try {
                    showStatusModal(false);
                    successToast("Item removido.", res.data.message.toString());
                } catch (e) {
                    showStatusModal(false);
                    errorToast("Erro ao remover item", "Ocorreu um erro ao remover esse item, por favor tente mais tarde.");
                }
            }
        },
        saveForm() {
            let type_delivery;
            if (this.onlySell) type_delivery = "onlySell";
            if (this.externalArea) type_delivery = "external";
            if (this.internalArea) type_delivery = "internal";

            if (!this.internalArea && !this.onlySell && !this.externalArea) {
                errorToast("Verifique", "Você deve marcar 1 tipo de entrega obrigatóriamente.");
                return true;
            }

            if (this.internalArea) {
                const idProduct = product;
                const subject_email = this.email.subjectEmail;
                const message_email = this.email.messageEmail;
                axios.post(`${setDeliveryURL}`, { idProduct, subject_email, message_email, type_delivery }
                ).then(res => {
                    successToast("Dados salvos.", "Dados salvos com sucesso.");
                }).catch(function (error) {
                    errorToast("Erro ao salvar os dados", "Ocorreu um erro ao os dados, por favor tente mais tarde.");
                });
            }
            const url = productInfoURL.replace(/:id/g, product);
            successToast("Dados salvos.", "Dados salvos com sucesso.");
            setTimeout(function () {
                window.location.href = url;
            }, 1500);
        },
        async saveDeliveries() {
            showStatusModal(true, "saving");

            await axios.post(clearSubscribersCache, { idProduct: product })
                .then(() => {
                    successToast("Dados salvos.", "Entregas salvas com sucesso. Isso pode levar até 5 minutos para refletir na plataforma");
                })
                .catch((error) => errorToast(
                    "Erro ao salvar os dados",
                    "Ocorreu um erro ao salvar as entregas, por favor tente mais tarde."
                )
                );

            showStatusModal(false);
        },
        syncOnlySell() {
            this.onlySell = true;
            this.externalArea = false;
            this.internalArea = false;
            this.sync();
        },
        syncExternalArea() {
            this.externalArea = true;
            this.onlySell = false;
            this.internalArea = false;
            this.sync();
        },
        syncInternalArea() {
            this.internalArea = true;
            this.onlySell = false;
            this.externalArea = false;
            this.sync();
        },
        sync() {
            const idProduct = product;
            let type_delivery;

            if (this.internalArea) type_delivery = "internal";
            if (this.onlySell) type_delivery = "onlySell";
            if (this.externalArea) type_delivery = "external";

            this.ifNotSelected();

            axios.post(`${setDeliveryURL}`, { idProduct, type_delivery }
            ).catch(function (error) {
                errorToast("Erro ao adicionar item", error.response.data.message.toString());
            });
        },
        ifNotSelected() {
            if (!this.internalArea && !this.onlySell && !this.externalArea) {
                errorToast("Verifique", "Você deve marcar 1 tipo de entrega obrigatóriamente.");
                return true;
            }
            return false;
        },
        getCourses: async function () {
            try {
                const query = {
                    "query": ALL_COURSES_FOR_DELIVERY_QUERY_AXIOS,
                    "variables": { active: true, page: 1, limit: 1000 }
                };
                const res = await axiosGraphqlClient.post(contentAPI, query);
                const { data } = res.data.data.courses;
                this.graphql.data = data;
            } catch (e) {
                console.log(e)
            }
        },
        getSections: async function () {
            try {
                const query = {
                    "query": ALL_SECTIONS_QUERY_AXIOS,
                    "variables": { platform_id, published: true, page: 1, limit: 1000 }
                };
                const res = await axiosGraphqlClient.post(contentAPI, query);
                const { data } = res.data.data.sections;
                this.graphql.sections = data;
            } catch (e) {
                console.log(e)
            }
        },
        async getSelectedContents() {
            const res = await axios.post(listContents, { idProduct: product });
            const { courses, sections } = res.data.response;
            this.selectedContent.courses = courses;
            this.selectedContent.sections = sections;
        },
    },
    async mounted() {
        await this.getCourses();
        await this.getSections();
        await this.getSelectedContents();
        await this.getAllDeliveries();
    }
});

app.use(ApmVuePlugin, apmConfig);
app.mount("#deliveryApp");
