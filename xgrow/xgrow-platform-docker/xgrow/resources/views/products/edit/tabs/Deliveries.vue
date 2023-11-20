<template>
    <div>
        <Loading :isOpen="isLoading" />

        <div id="delivery-app">
            <p class="xgrow-card-title mb-5">Entregas</p>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="d-flex align-items-center mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="chk-only-sale" id="chk-only-sale" class="form-check-input"
                                v-model="onlySell" @change="syncOnlySell" />
                            <label for="chk-only-sale" class="form-check-label">Vou apenas vender</label>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="d-flex align-items-center mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="chk-external-area" id="chk-external-area" class="form-check-input"
                                v-model="externalArea" @change="syncExternalArea" />
                            <label for="chk-external-area" class="form-check-label">Utilizar área de membros
                                externa</label>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 mb-3 d-flex gap-3" v-if="externalArea">
                    <div class="card-integrate">
                        <img class="img-integration-icon" :src="kajabiImg" alt="" />
                        <h2>Kajabi</h2>
                    </div>

                    <div class="card-integrate">
                        <img class="img-integration-icon" :src="cademiImg" alt="" />
                        <h2>Cademí</h2>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="d-flex align-items-center mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="chk-internal-area" id="chk-internal-area" class="form-check-input"
                                v-model="internalArea" @change="syncInternalArea" />
                            <label for="chk-internal-area" class="form-check-label">Utilizar área de aprendizado
                                unificada Xgrow</label>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12" v-if="internalArea">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 my-3">
                            <p class="xgrow-medium-italic">
                                Selecione quais os conteúdos abaixo serão entregues na área de
                                aprendizado (são exibidos apenas conteúdos e seções publicados).
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Lista de Cursos -->
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <h6>Cursos</h6>
                                    <hr />
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12" v-if="graphql.data.length > 0">
                                    <ul class="p-0">
                                        <li v-for="course in graphql.data" :key="course.id">
                                            <div class="xgrow-check mb-2 d-flex align-items-center">
                                                <input type="checkbox" name="course_ids[]" :id="'course_' + course.id"
                                                    class="selected-course" :value="course.id"
                                                    :checked="hasChecked(course.id, 'c')" @change="syncDelivery()"
                                                    :data-id="course.id" :data-content="'c'" />
                                                <label :for="'course_' + course.id" class="mx-1">{{
                                                    course.name
                                                }}</label>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div v-else>
                                    <p>Não há cursos cadastrados.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Lista de Seções -->
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <h6>Seções</h6>
                                    <hr />
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12" v-if="graphql.sections.length > 0">
                                    <ul class="p-0">
                                        <li v-for="section in graphql.sections" :key="section.id">
                                            <div class="xgrow-check mb-2 d-flex align-items-center">
                                                <input type="checkbox" name="section_ids[]" :id="'section_' + section.id"
                                                    class="selected-section" :value="section.id"
                                                    :checked="hasChecked(section.id, 's')" @change="syncDelivery()"
                                                    :data-id="section.id" :data-content="'s'" />
                                                <label :for="'section_' + section.id" class="mx-1">{{
                                                    section.title
                                                }}</label>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div v-else>
                                    <p>Não há seções cadastradas.</p>
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-end">
                            <button class="xgrow-button" @click.prevent="saveDeliveries()">Salvar Entregas</button>
                        </div>
                    </div>

                    <hr class="my-3" />

                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="xgrow-form-control">
                                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                    <input type="text" name="subject_email" id="subject_email"
                                        class="mui--is-empty mui--is-untouched mui--is-pristine" min="0" max="100"
                                        autocomplete="off" spellcheck="false" v-model="email.subjectEmail">
                                    <label for="subject_email" class="form-check-label">Assunto</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                <textarea v-model="email.messageEmail" id="message_email" name="message_email"
                                    disabled="disabled" class="w-100 mui--is-empty mui--is-pristine mui--is-touched"
                                    rows="7" cols="54" maxlength="500"
                                    style="resize:none;height: auto; min-height:200px; margin-top: 20px">
                                    </textarea>
                            </div>
                            <ul class="px-0 xgrow-medium-italic">
                                <li class="my-2">
                                    <span style="color: var(--contrast-green3)">Essa mensagem será enviada ao aluno com
                                        os respectivos dados de
                                        acesso.</span>
                                </li>
                            </ul>
                        </div>

                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="xgrow-card-footer p-3 border-top mt-4 justify-content-end">
                                <button class="xgrow-button" @click.prevent="saveForm()">Salvar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

import Modal from "../../../../js/components/XgrowDesignSystem/Modals/Modal";
import StatusModalComponent from "../../../../js/components/StatusModalComponent";
import Table from "../../../../js/components/Datatables/Table";
import { ALL_COURSES_FOR_DELIVERY_QUERY_AXIOS } from '../../../../js/graphql/queries/courses';
import { ALL_SECTIONS_QUERY_AXIOS } from '../../../../js/graphql/queries/sections';
import Loading from '../../../../js/components/XgrowDesignSystem/Utils/Loading.vue';
import { axiosGraphqlClient } from "../../../../js/config/axiosGraphql";

export default {
    components: {
        Modal,
        StatusModalComponent,
        Table,
        Loading
    },
    mixins: [],
    data() {
        return {
            kajabiImg,
            cademiImg,
            deliveries: [],
            hasDelivery: false,
            externalArea: false,
            internalArea: false,
            onlySell: false,
            email: {
                subjectEmail: "",
                messageEmail: "",
            },
            graphql: {
                active: false,
                data: [],
                sections: []
            },
            selectedContent: {
                courses: [],
                sections: [],
            },
            isLoading: false
        };
    },
    methods: {
        async getSelectedContents() {
            this.isLoading = true;
            const res = await axios.post(listContents, { idProduct: product });
            this.isLoading = false;
            const { courses, sections } = res.data.response;
            this.selectedContent.courses = courses;
            this.selectedContent.sections = sections;
        },
        async getAllDeliveries() {
            this.isLoading = true;
            const res = await axios.post(getAllDeliveries, { product });
            this.isLoading = false;

            const {
                external_area,
                internal_area,
                only_sell,
                email,
                message,
            } = res.data.product;
            this.externalArea = !!external_area;
            this.internalArea = !!internal_area;
            this.onlySell = !!only_sell;

            this.hasDelivery = res.data.has.course || res.data.has.section;

            this.email.subjectEmail = email;
            this.email.messageEmail = message;

            if (message === "" || message === null) {
                const msg =
                    "Olá ##NOME_ASSINANTE##,\n" +
                    " \n" +
                    "Seus dados de acesso são os abaixo:\n" +
                    " \n" +
                    "Login: ##EMAIL_ASSINANTE##\n" +
                    "Senha: ##AUTO##\n" +
                    " \n" +
                    "Link de acesso: " +
                    accessLink;

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
                try {
                    this.isLoading = true;
                    const res = await axios.post(`${attachContent}`, {
                        typeContent,
                        idContent,
                        idProduct,
                    });
                    this.isLoading = false;
                    successToast("Item adicionado.", res.data.message.toString());
                } catch (e) {
                    this.isLoading = false;
                    errorToast(
                        "Erro ao adicionar item",
                        "Ocorreu um erro ao adicionar esse item, por favor tente mais tarde."
                    );
                }
            } else {
                this.isLoading = true;
                const res = await axios.post(`${detachContent}`, {
                    typeContent,
                    idContent,
                    idProduct,
                });
                try {
                    this.isLoading = false;
                    successToast("Item removido.", res.data.message.toString());
                } catch (e) {
                    this.isLoading = false;
                    errorToast(
                        "Erro ao remover item",
                        "Ocorreu um erro ao remover esse item, por favor tente mais tarde."
                    );
                }
            }
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
        saveForm() {
            let type_delivery;
            if (this.onlySell) type_delivery = "onlySell";
            if (this.externalArea) type_delivery = "external";
            if (this.internalArea) type_delivery = "internal";

            if (this.internalArea) {
                const idProduct = product;
                const subject_email = this.email.subjectEmail;
                const message_email = this.email.messageEmail;
                axios
                    .post(`${setDeliveryURL}`, {
                        idProduct,
                        subject_email,
                        message_email,
                        type_delivery,
                    })
                    .then((res) => {
                        successToast("Dados salvos.", "Dados salvos com sucesso.");
                    })
                    .catch(function (error) {
                        errorToast(
                            "Erro ao salvar os dados",
                            "Ocorreu um erro ao salvar os dados, por favor tente mais tarde."
                        );
                    });
            }
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

            axios
                .post(`${setDeliveryURL}`, { idProduct, type_delivery })
                .catch(function (error) {
                    errorToast("Erro ao adicionar item", error.response.data.message.toString());
                });
        },
        ifNotSelected() {
            if (!this.internalArea && !this.onlySell && !this.externalArea) {
                errorToast("Verifique", "Você deve marcar 1 tipo de entrega obrigatóriamente.");
            }
        },
    },
    async mounted() {
        await this.getCourses();
        await this.getSections();
        await this.getSelectedContents();
        await this.getAllDeliveries();
    },
};
</script>

<style></style>
