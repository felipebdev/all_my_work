<template>
    <LoadingStore />

    <Row class="mb-4 d-none">
        <Col sm="12" md="12" lg="7" xl="7">
        <Container class="h-100">
            <template v-slot:header-left>
                <Title is-form-title icon="fa fa-bar-chart">Progresso dos alunos</Title>
            </template>
            <template v-slot:content>
                <div class="d-flex gap-3 align-items-center flex-wrap">
                    <Stats icon="fa fa-users" title="1.200" subtitle="Alunos" color="rgba(244, 235, 147, .1)"
                        icon-color="rgba(244, 235, 147, 1)" />
                    <PipeVertical />
                    <Stats icon="fa fa-check-circle" title="1.125" subtitle="Conclusões" color="rgba(165, 217, 100, .1)"
                        icon-color="rgba(165, 217, 100, 1)" />
                    <PipeVertical />
                    <Stats icon="fa fa-book" title="70" subtitle="Em andamento" color="rgba(62, 150, 177, .1)"
                        icon-color="rgba(62, 150, 177, 1)" />
                    <PipeVertical />
                    <Stats icon="fa fa-clock" title="5" subtitle="Não iniciado" color="rgba(231, 231, 231, .1)"
                        icon-color="rgba(231, 231, 231, 1)" />
                </div>
            </template>
        </Container>
        </Col>
        <Col sm="12" md="12" lg="5" xl="5">
        <Container class="h-100">
            <template v-slot:header-left>
                <Title is-form-title icon="fa fa-commenting-o">Comentários</Title>
            </template>
            <template v-slot:content>
                <div class="d-flex gap-3 align-items-center justify-content-between flex-wrap">
                    <div>
                        <Title class="m-0">1.432</Title>
                        <Subtitle class="m-0">Nº total</Subtitle>
                    </div>
                    <PipeVertical />
                    <DefaultButton text="Acessar comentários" status="dark" class="fw-bold" />
                </div>
            </template>
        </Container>
        </Col>
    </Row>

    <Row>
        <Col>
        <Container>
            <template v-slot:header-left>
                <Title>{{ course.name }}
                    <PipeVertical /> <span class="fw-normal">Conteúdo</span>
                </Title>
                <Subtitle>
                    <span v-html="course.description ? resume(course.description, 120) : ''"></span>
                </Subtitle>
            </template>
            <template v-slot:header-right>
                <DefaultButton id="addButton" text="Novo módulo" status="success" @click="openModuleModal"
                    icon="fas fa-plus" />
            </template>
            <template v-slot:content>
                <Alert title="Atenção" status="warning" v-if="hasModuleChanged">
                    <p>Houve alteração na ordenação dos módulos.
                        <b @click="changeOrder" style="cursor:pointer"> Clique aqui para salvar</b>
                    </p>
                </Alert>
                <Alert title="Atenção" status="warning" v-if="hasContentChange">
                    <p>Houve alteração na ordenação dos conteúdos.
                        <b @click="updateList" style="cursor:pointer"> Clique aqui para salvar</b>
                    </p>
                </Alert>
                <hr>
                <div class="list-header d-flex" v-if="modules.length > 0">
                    <p style="margin-right: 60px;"></p>
                    <p class="text-center" style="margin-right: 10px;">Nº</p>
                    <p class="w-100">Nome</p>
                    <p style="margin-right: 28px" class="d-none d-lg-flex">Aulas</p>
                    <!-- <p style="margin-right: 45px" class="d-none d-lg-flex">Horas</p>
                        solução paleativa enquanto não é pensado em outro formato -->
                    <p style="margin-right: 45px" class="d-none d-lg-flex">Minutos</p>
                    <p style="margin-right: 260px" class="d-none d-lg-flex">Situação</p>
                </div>
                <Accordion id="moduleAccordion">
                    <template v-if="modules.length > 0">
                        <draggable :list="modules" item-key="id" @start="() => { drag = true; hasModuleChanged = true }"
                            ghost-class="ghost" @end="drag = false" :disabled="false" group='module' handle=".drag-icon">
                            <template #item="{ element, index }" :key="element.id">
                                <AccordionItem :id="`heading_${element.id}`" :target-id="`collapse_${element.id}`"
                                    accordion-id="moduleAccordion" has-html-header :is-open="index === 0">
                                    <template v-slot:header>
                                        <CourseContentRow :item="element" :index="index" @module-action="moduleAction" />
                                    </template>
                                    <template v-slot:default>
                                        <CourseModuleContentRow :rows="element" @module-action="moduleAction"
                                            @updateList="hasContentChange = true" />
                                    </template>
                                </AccordionItem>
                            </template>
                        </draggable>

                    </template>
                    <table class="w-100" v-else>
                        <tbody>
                            <NoResult :colspan="1" title="Nenhum módulo encontrado!"
                                subtitle="Não há dados a serem exibidos. Clique em adicionar para adicionar um módulo." />
                        </tbody>
                    </table>
                </Accordion>
            </template>
            <template v-slot:footer>
                <Pagination class="mt-5" :total-pages="pagination.totalPages" :total="pagination.totalResults"
                    :current-page="pagination.currentPage" @page-changed="onPageChange" @limit-changed="onLimitChange">
                </Pagination>
            </template>
        </Container>
        </Col>
    </Row>

    <!-- Delete Module Modal -->
    <ConfirmModal :is-open="moduleModal.delete">
        <i class="fas fa-times-circle fa-7x text-danger"></i>
        <div class="modal-body__content">
            <h1>Excluir módulo?</h1>
            <p>Ao remover este módulo, o mesmo não poderá ser recuperado bem como todo seu conteúdo!</p>
        </div>
        <div class="modal-body__footer">
            <DefaultButton text="Cancelar" outline @click="() => { moduleModal.delete = false; module.id = null }" />
            <DefaultButton text="Excluir mesmo assim" status="success" @click="deleteModule" />
        </div>
    </ConfirmModal>

    <!-- New Module Modal -->
    <ConfirmModal :is-open="moduleModal.new">
        <Title>Novo módulo</Title>
        <Row>
            <Col>
            <Subtitle>Nomeie o módulo novo, você poderá adicionar adicionar os detalhes depois na edição deste
                módulo.
            </Subtitle>
            </Col>
            <Col class="p-2">
            <Input id="module_name" label="Nome do módulo" v-model="module.name" placeholder="Insira o nome do módulo..." />
            </Col>
        </Row>
        <div class="modal-body__footer">
            <DefaultButton text="Cancelar" outline @click="() => { moduleModal.new = false; module.name = '' }" />
            <DefaultButton text="Salvar" status="success" @click="addModule" />
        </div>
    </ConfirmModal>

    <!-- Update Module Modal -->
    <ConfirmModal :is-open="moduleModal.update">
        <Title>Editar módulo</Title>
        <Row class="w-100">
            <Col class="p-2">
            <Input id="module_name" label="Nome do módulo" v-model="module.name" placeholder="Insira o nome do módulo..." />
            </Col>
        </Row>
        <div class="modal-body__footer">
            <DefaultButton text="Cancelar" outline @click="() => { moduleModal.update = false; module.name = '' }" />
            <DefaultButton text="Atualizar" status="success" @click="updateModule" />
        </div>
    </ConfirmModal>

    <!-- Transfer Module Modal -->
    <ConfirmModal :is-open="moduleModal.transfer">
        <Title>Transferir conteúdo</Title>
        <Subtitle>
            Selecione abaixo para qual curso este módulo e todo o conteúdo dentro dele será transferido:
        </Subtitle>
        <Row class="w-100">
            <Col class="p-2">
            <Select label="Selecione o curso" placeholder="Selecione ou digite o nome do curso..." :options="courseOptions"
                id="listCourse" v-model="selectedCourse" />
            </Col>
        </Row>
        <div class="modal-body__footer">
            <DefaultButton text="Cancelar" outline @click="() => { moduleModal.transfer = false }" />
            <DefaultButton text="Salvar e transferir" status="success" @click="transferModule"
                :disabled="!this.selectedCourse" />
        </div>
    </ConfirmModal>

    <!-- Delete Content Modal -->
    <ConfirmModal :is-open="moduleModal.content">
        <i class="fas fa-times-circle fa-7x text-danger"></i>
        <div class="modal-body__content">
            <h1>Excluir conteúdo?</h1>
            <p>Ao remover este contúdo, o mesmo não poderá ser recuperado bem como todo os progressos!</p>
        </div>
        <div class="modal-body__footer">
            <DefaultButton text="Cancelar" outline @click="() => { moduleModal.content = false; content.id = null }" />
            <DefaultButton text="Excluir mesmo assim" status="success" @click="deleteContent" />
        </div>
    </ConfirmModal>
</template>

<script>
import resume from "../../../../js/components/XgrowDesignSystem/Mixins/resume";
import draggable from 'vuedraggable'

import Col from "../../../../js/components/XgrowDesignSystem/Utils/Col.vue";
import Row from "../../../../js/components/XgrowDesignSystem/Utils/Row.vue";
import Container from "../../../../js/components/XgrowDesignSystem/Cards/Container.vue";
import Title from "../../../../js/components/XgrowDesignSystem/Typography/Title.vue";
import Subtitle from "../../../../js/components/XgrowDesignSystem/Typography/Subtitle.vue";
import Stats from "../../../../js/components/XgrowDesignSystem/Cards/Stats.vue";
import PipeVertical from "../../../../js/components/XgrowDesignSystem/Utils/PipeVertical.vue";
import DefaultButton from "../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton.vue";
import NoResult from "../../../../js/components/Datatables/NoResult.vue";
import Input from "../../../../js/components/XgrowDesignSystem/Form/Input.vue";
import Select from "../../../../js/components/XgrowDesignSystem/Form/Select.vue";
import ConfirmModal from "../../../../js/components/XgrowDesignSystem/Modals/ConfirmModal.vue";
import Accordion from "../../../../js/components/XgrowDesignSystem/Accordion/Accordion.vue";
import AccordionItem from "../../../../js/components/XgrowDesignSystem/Accordion/AccordionItem.vue";
import LoadingStore from "../../../../js/components/XgrowDesignSystem/Utils/LoadingStore.vue";
import Pagination from "../../../../js/components/Datatables/Pagination.vue";
import Alert from "../../../../js/components/XgrowDesignSystem/Alert/Alert.vue";

import CourseContentRow from "./CourseContentRow.vue";
import CourseModuleContentRow from "../components/CourseModuleContentRow.vue";

import { GET_MODULE_BY_ID_QUERY_AXIOS, GET_MODULES_BY_PARAMS_QUERY_AXIOS } from "../../../../js/graphql/queries/modules";
import { ALL_COURSES_QUERY_AXIOS } from "../../../../js/graphql/queries/courses";
import { DELETE_CONTENT_MUTATION_AXIOS, UPDATE_CONTENT_ORDER_MUTATION_AXIOS } from "../../../../js/graphql/mutations/contents";
import {
    DELETE_MODULE_MUTATION_AXIOS,
    SAVE_MODULE_MUTATION_AXIOS,
    UPDATE_MODULE_MUTATION_AXIOS,
    UPDATE_MODULE_ORDER_MUTATION_AXIOS,
    UPDATE_MODULE_NAME_MUTATION_AXIOS
} from "../../../../js/graphql/mutations/modules";
import { mapActions, mapStores } from "pinia";
import { useLoadingStore } from "../../../../js/store/components/loading";
import { axiosGraphqlClient } from "../../../../js/config/axiosGraphql";

export default {
    name: "CourseContent",
    components: {
        CourseModuleContentRow, LoadingStore,
        Alert, AccordionItem, Accordion, Pagination,
        ConfirmModal, Input, Select, CourseContentRow,
        NoResult, DefaultButton, PipeVertical, Stats,
        Subtitle, Title, Container, Row, Col, draggable
    },
    props: { course: { type: Object } },
    mixins: [resume],
    emits: ['pageName'],
    data() {
        return {
            drag: false,
            hasModuleChanged: false,
            hasContentChange: false,

            /** Datatables and Pagination */
            pagination: {
                totalPages: 1,
                totalResults: 0,
                currentPage: 1,
                limit: 25,
            },

            /** Module modal controller */
            moduleModal: {
                update: false,
                delete: false,
                new: false,
                transfer: false,
                content: false,
            },

            /** Course Transfer Data */
            courseOptions: [],
            selectedCourse: null,

            /** Module Data */
            modules: [],
            module: {
                id: null,
                name: ''
            },

            /** Content Data */
            content: {
                id: null,
                title: ''
            },
        }
    },
    computed: {
        ...mapStores(useLoadingStore),
    },
    methods: {
        ...mapActions(useLoadingStore, ['setLoading']),
        /** Get Courses for transfer */
        getCourses: async function () {
            try {
                this.loadingStore.setLoading(true);
                const query = {
                    "query": ALL_COURSES_QUERY_AXIOS,
                    "variables": { page: 1, limit: 1000 }
                };
                const res = await axiosGraphqlClient.post(contentAPI, query);
                const { data } = res.data.data.courses;

                this.courseOptions = data.map(item => {
                    return { value: item.id, name: item.name };
                });
                this.loadingStore.setLoading();
            } catch (e) {
                errorToast("Ocorreu um erro", `Ocorreu um problema ao listar os cursos. Tente novamente mais tarde.`);
            }
        },
        /** On change page */
        onPageChange: async function (page) {
            this.pagination.currentPage = page;
            await this.getModules();
        },
        /** Limit by size items */
        onLimitChange: async function (value) {
            this.pagination.limit = value;
            this.pagination.currentPage = 1;
            await this.getModules();
        },
        /** Open New module Modal */
        openModuleModal: function () {
            this.moduleModal.new = true;
            this.module = {}
        },
        /** Get Modules */
        getModules: async function () {
            try {
                this.loadingStore.setLoading(true);
                const query = {
                    "query": GET_MODULES_BY_PARAMS_QUERY_AXIOS,
                    "variables": { course_id: this.$route.params.id, page: this.pagination.currentPage, limit: this.pagination.limit }
                };
                const res = await axiosGraphqlClient.post(contentAPI, query);
                const { total, data } = res.data.data.modules;
                this.pagination.totalResults = total;
                this.pagination.totalPages = Math.ceil(total / this.pagination.limit);
                if (this.pagination.totalPages === 0) this.pagination.totalPages = 1;
                this.modules = data;
                this.loadingStore.setLoading();
            } catch (e) {
                this.loadingStore.setLoading();
                errorToast("Ocorreu um erro", `Ocorreu um problema ao listar os módulos. Tente novamente mais tarde.`);
            }
        },
        /** Get Module By ID */
        getModuleById: async function (id) {
            try {
                this.loadingStore.setLoading(true);
                const query = {
                    "query": GET_MODULE_BY_ID_QUERY_AXIOS,
                    "variables": { id: id }
                };
                const res = await axiosGraphqlClient.post(contentAPI, query);
                Object.assign(this.module, res.data.data.module)
                this.loadingStore.setLoading();
            } catch (e) {
                this.loadingStore.setLoading();
                errorToast("Ocorreu um erro", `Ocorreu um problema ao buscar o módulo selecionado. Tente novamente mais tarde.`);
            }
        },
        /** Update module */
        updateModule: async function () {
            try {
                this.loadingStore.setLoading(true);
                this.moduleModal.update = false
                const query = {
                    "query": UPDATE_MODULE_NAME_MUTATION_AXIOS,
                    "variables": { id: this.module.id, name: this.module.name }
                };
                await axiosGraphqlClient.post(contentAPI, query);
                this.loadingStore.setLoading();
                await this.getModules();
                successToast("Módulo atualizado!", `O módulo ${this.module.name} foi atualizado com sucesso!`);
            } catch (e) {
                this.loadingStore.setLoading();
                errorToast("Ocorreu um erro", `Ocorreu um problema ao buscar o módulo selecionado. Tente novamente mais tarde.`);
            }
        },
        /** Delete module */
        deleteModule: async function () {
            try {
                this.moduleModal.delete = false;
                this.loadingStore.setLoading(true);
                const query = {
                    "query": DELETE_MODULE_MUTATION_AXIOS,
                    "variables": { id: this.module.id }
                };
                await axiosGraphqlClient.post(contentAPI, query);
                this.loadingStore.setLoading();
                await this.getModules();
                await this.changeOrder();
                successToast("Módulo removido!", `Módulo removido com sucesso!`);
            } catch (e) {
                this.loadingStore.setLoading();
                errorToast("Ocorreu um erro", `Ocorreu um problema ao remover o módulo. Tente novamente mais tarde.`);
            }
        },
        /** Get module callbacks */
        moduleAction: async function (obj) {
            if (obj.action === "moduleEdit") {
                await this.getModuleById(obj.id)
                this.moduleModal.update = true;
            }
            if (obj.action === "moduleDelete") {
                await this.getModuleById(obj.id)
                this.moduleModal.delete = true;
            }
            if (obj.action === "moduleTransfer") {
                await this.getModuleById(obj.id)
                this.selectedCourse = null;
                this.moduleModal.transfer = true;
            }
            if (obj.action === "deleteContent") {
                this.moduleModal.content = true;
                this.content.id = obj.id;
                this.content.title = obj.title;
            }
        },
        /** New Module */
        addModule: async function () {
            try {
                if (Object.keys(this.module).length === 0)
                    throw new Error(`O nome do módulo não pode ficar em branco.`);
                this.moduleModal.new = false
                this.loadingStore.setLoading(true);
                const query = {
                    "query": SAVE_MODULE_MUTATION_AXIOS,
                    "variables": {
                        name: this.module.name,
                        position: this.modules.length + 1,
                        status: true,
                        course_id: this.$route.params.id
                    }
                };
                await axiosGraphqlClient.post(contentAPI, query);
                await this.changeOrder();
                await this.getModules();
                this.loadingStore.setLoading();
                successToast("Módulo cadastrado!", `O módulo "${this.module.name}" foi cadastrado com sucesso!`);
            } catch (e) {
                this.loadingStore.setLoading();
                errorToast("Erro ao salvar!", e.message ?? "Ocorreu um problema ao tentar salvar o módulo. Tente novamente mais tarde.");
            }
        },
        /** Transfer Module */
        transferModule: async function () {
            try {
                this.moduleModal.transfer = false;
                this.loadingStore.setLoading(true);
                const query = {
                    "query": UPDATE_MODULE_MUTATION_AXIOS,
                    "variables": {
                        id: this.module.id,
                        name: this.module.name,
                        course_id: this.selectedCourse,
                        position: 0
                    }
                };
                await axiosGraphqlClient.post(contentAPI, query);
                this.loadingStore.setLoading();
                await this.getModules();
                successToast("Módulo transferido!", `O módulo foi transferido para o curso selecionado com sucesso!`);
            } catch (e) {
                this.loadingStore.setLoading();
                errorToast("Erro ao salvar!", e.message ?? "Ocorreu um erro ao transferir o módulo. Tente novamente mais tarde.");
            }
        },
        /** Delete Content */
        deleteContent: async function () {
            try {
                this.moduleModal.content = false;
                this.loadingStore.setLoading(true);
                const query = {
                    "query": DELETE_CONTENT_MUTATION_AXIOS,
                    "variables": { id: this.content.id }
                };
                await axiosGraphqlClient.post(contentAPI, query);
                await this.changeOrder();
                await this.getModules();
                this.loadingStore.setLoading();
                successToast("Conteúdo removido!", `O conteúdo foi removido com sucesso!`)
            } catch (e) {
                this.loadingStore.setLoading();
                errorToast("Ocorreu um erro!", e.message ?? "Ocorreu um erro ao remover o conteúdo. Tente novamente mais tarde.");
            }
        },
        /** Change module order */
        changeOrder: async function () {
            try {
                this.hasModuleChanged = false;
                this.loadingStore.setLoading(true);
                let index = 1;
                for (const item of this.modules) {
                    const query = {
                        "query": UPDATE_MODULE_ORDER_MUTATION_AXIOS,
                        "variables": { id: item.id, name: item.name, position: index }
                    };
                    await axiosGraphqlClient.post(contentAPI, query);
                    index++
                }
                await this.getModules();
            } catch (e) {
                this.loadingStore.setLoading();
                errorToast("Ocorreu um erro!", e.message ?? "Ocorreu um erro ao remover o conteúdo. Tente novamente mais tarde.");
                console.log(e);
            }
        },
        /** Change content order */
        updateList: async function () {
            try {
                this.hasContentChange = false;
                this.loadingStore.setLoading(true);
                let order_content = 1;
                for (const module of this.modules) {
                    for (const content of module.Content) {
                        const query = {
                            "query": UPDATE_CONTENT_ORDER_MUTATION_AXIOS,
                            "variables": { id: content.id, order_content: order_content, module_id: module.id }
                        };
                        await axiosGraphqlClient.post(contentAPI, query);
                        order_content++;
                    }
                    order_content = 1;
                }
                await this.getModules();
            } catch (e) {
                this.loadingStore.setLoading();
                errorToast("Ocorreu um erro!", e.message ?? "Ocorreu um erro ao remover o conteúdo. Tente novamente mais tarde.");
                console.log(e);
            }
        }
    },
    async created() {
        await this.getModules();
        await this.getCourses();
    }
}
</script>

<style lang="scss" scoped>
.list-header {
    background-color: transparent;
    color: #7A7F8D;
    font-size: 0.875rem;
    font-weight: 600;
    line-height: 1.375rem;
    height: 36px;
}

.no-module-modal {
    :deep(.modal-body) {
        gap: 0;
    }
}

.ghost {
    cursor: grab !important;
    background: #222329 !important;
    border: 2px dashed #93BC1E !important;
    border-radius: 6px !important;
    padding: 10px 10px 25px 10px;
}
</style>
