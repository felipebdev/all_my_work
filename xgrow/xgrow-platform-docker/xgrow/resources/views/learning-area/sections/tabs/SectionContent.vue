<template>
    <div>
        <LoadingStore />
        <Row class="mb-4 d-none" v-if="false">
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
                    <Title class="content-title">
                        {{ section.title }}
                        <PipeVertical /> <span class="fw-normal d-block">Conteúdo</span>
                    </Title>
                    <Subtitle>
                        <span v-html="section.description ? resume(section.description, 120) : ''"></span>
                    </Subtitle>
                </template>
                <template v-slot:header-right>
                    <default-button id="addButton" text="Adicionar" status="success" icon="fas fa-plus"
                        @click="openModal('typeContentModal')" />
                </template>
                <template v-slot:content>
                    <DraggableTable id="courseDatatable" min-height>
                        <template v-slot:thead>
                            <th style="width: 60px"></th>
                            <th style="width: 60px">Nº</th>
                            <th>Nome</th>
                            <th>Minutos</th>
                            <th style="width: 200px"></th>
                            <th style="width: 80px"></th>
                        </template>
                        <template v-slot:tbody>
                            <template v-if="section.section_items && section.section_items.length">
                                <draggable v-model="section.section_items" item-key="element.position" tag="tbody"
                                    @end="changeOrder" ghost-class="ghost" handle=".btn-widget-drag">
                                    <template #item="{ element }">
                                        <tr>
                                            <td>
                                                <img class="drag btn-widget-drag"
                                                    src="/xgrow-vendor/assets/img/widgets/svg/apps.svg" />
                                            </td>
                                            <td> {{ element.position }} </td>
                                            <td>
                                                <ContentProfile :profile="{
                                                    img: element.item_data.horizontal_image ?? 'https://las.xgrow.com/background-default.png',
                                                    title: element.item_data.name || element.item_data.title,
                                                    subtitle: element.item_data.contentType
                                                }" />
                                            </td>
                                            <td> {{ element.item_data.duration }}</td>
                                            <td>
                                                <SelectStatus :id="`active-${element.id}`" :options="status"
                                                    v-model="element.item_data.active" placeholder="Selecione uma situação"
                                                    v-if="element.type == 'course'" @change="updateCourseStatus(element)" />

                                                <SelectStatus :id="`active-${element.id}`" :options="status" v-else
                                                    v-model="element.item_data.is_published"
                                                    placeholder="Selecione uma situação"
                                                    @change="updateContentStatus(element)" />
                                            </td>
                                            <td width="60">
                                                <ButtonDetail>
                                                    <router-link :to="{
                                                        name: `${element.type == 'course' ? 'course-edit' : 'content-edit'}`,
                                                        params: { id: element.item_id, content_id: element.item_id }
                                                    }">
                                                        <li class="option">
                                                            <button class="option-btn">
                                                                <i class="fa fa-pencil"></i> Editar {{ `${element.type ==
                                                                    'course' ? 'curso' : 'conteúdo'}` }}
                                                            </button>
                                                        </li>
                                                    </router-link>
                                                    <!-- <li class="option d-none">
                                                        <button class="option-btn"
                                                            @click="callAlert('Função duplicar curso: #' + element.id)">
                                                            <i class="fa fa-copy"></i> Duplicar {{`${element.type == 'course' ? 'curso' : 'conteúdo' }`}}
                                                        </button>
                                                    </li> -->
                                                    <li class="option">
                                                        <button class="option-btn" @click="removeSectionItems(element)">
                                                            <i class="fa fa-trash text-danger"></i> Excluir
                                                            {{ `${element.type == 'course' ? 'curso' : 'conteúdo'}` }}
                                                        </button>
                                                    </li>
                                                </ButtonDetail>
                                            </td>
                                        </tr>
                                    </template>
                                </draggable>
                            </template>
                            <DraggableNoResult v-else :colspan="7" title="Nenhum conteúdo encontrado!"
                                subtitle="Não há dados a serem exibidos. Clique em adicionar novo para incluir." />
                        </template>
                    </DraggableTable>
                </template>
            </Container>
            </Col>
        </Row>

        <modal-type-content :isOpen="typeContentModal.isOpen" @close="closeModal('typeContentModal')"
            @confirm="selectTypeContent" />

        <modal-section-items :isOpen="sectionItemsModal.isOpen" @close="closeModal('sectionItemsModal')"
            @confirm="addNewSectionItems" />
    </div>
</template>

<script>
import resume from "../../../../js/components/XgrowDesignSystem/Mixins/resume";

import draggable from 'vuedraggable'
import DraggableTable from "../../../../js/components/XgrowDesignSystem/DraggableTable/Table.vue";
import DraggableNoResult from "../../../../js/components/XgrowDesignSystem/DraggableTable/DraggableNoResult.vue";

import Col from "../../../../js/components/XgrowDesignSystem/Utils/Col.vue";
import Row from "../../../../js/components/XgrowDesignSystem/Utils/Row.vue";
import Container from "../../../../js/components/XgrowDesignSystem/Cards/Container.vue";
import Title from "../../../../js/components/XgrowDesignSystem/Typography/Title.vue";
import Subtitle from "../../../../js/components/XgrowDesignSystem/Typography/Subtitle.vue";
import Stats from "../../../../js/components/XgrowDesignSystem/Cards/Stats.vue";
import PipeVertical from "../../../../js/components/XgrowDesignSystem/Utils/PipeVertical.vue";
import DefaultButton from "../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton.vue";
import XgrowTable from "../../../../js/components/Datatables/Table.vue";
import NoResult from "../../../../js/components/Datatables/NoResult.vue";
import Input from "../../../../js/components/XgrowDesignSystem/Form/Input.vue";
import Select from "../../../../js/components/XgrowDesignSystem/Form/Select.vue";
import ConfirmModal from "../../../../js/components/XgrowDesignSystem/Modals/ConfirmModal.vue";
import DropdownButton from "../../../../js/components/XgrowDesignSystem/Buttons/DropdownButton.vue";
import Accordion from "../../../../js/components/XgrowDesignSystem/Accordion/Accordion.vue";
import AccordionItem from "../../../../js/components/XgrowDesignSystem/Accordion/AccordionItem.vue";
import ProfileRow from "../../../../js/components/Datatables/ProfileRow.vue";
import ButtonDetail from "../../../../js/components/Datatables/ButtonDetail.vue";
import LoadingStore from "../../../../js/components/XgrowDesignSystem/Utils/LoadingStore.vue";
import { mapActions, mapStores, mapState, mapWritableState } from "pinia";
import { useSectionsStore } from '../../../../js/store/sections';
import ModalTypeContent from '../modals/TypeContent.vue';
import ModalSectionItems from '../modals/SectionItems.vue';
import ContentProfile from '../components/ContentProfile.vue';
import SelectStatus from '../../components/SelectStatus.vue';
import { axiosGraphqlClient } from '../../../../js/config/axiosGraphql';
import { GET_SECTION_BY_ID_QUERY_AXIOS } from '../../../../js/graphql/queries/sections';
import { UPDATE_CONTENT_STATUS_MUTATION_AXIOS } from '../../../../js/graphql/mutations/contents';
import { UPDATE_COURSE_STATUS_MUTATION_AXIOS } from '../../../../js/graphql/mutations/courses';
import { GET_COURSE_BY_PARAMS_QUERY_AXIOS } from '../../../../js/graphql/queries/courses';
import moment from 'moment';


export default {
    name: "Section-Content",
    components: {
        LoadingStore,
        ButtonDetail,
        ProfileRow,
        AccordionItem,
        Accordion,
        DropdownButton,
        DraggableNoResult,
        DraggableTable,
        ConfirmModal, Input, Select,
        NoResult, XgrowTable, DefaultButton, PipeVertical,
        Stats, Subtitle, Title, Container, Row, Col, draggable,
        ModalTypeContent,
        ModalSectionItems,
        ContentProfile,
        SelectStatus,
    },
    mixins: [resume],
    emits: ['pageName'],
    data() {
        return {
            /** Select draft status */
            status: [
                { value: false, name: 'Rascunho', img: '/xgrow-vendor/assets/img/icons/edit.svg' },
                { value: true, name: 'Publicado', img: '/xgrow-vendor/assets/img/icons/web.svg' },
            ],
            courses: [],
            contents: [],
            typeContentModal: {
                isOpen: false
            },
            sectionItemsModal: {
                isOpen: false,
                items: []
            },
            /** Pagination */
            pagination: {
                totalPages: 1,
                totalResults: 0,
                currentPage: 1,
                limit: 25,
            },
        }
    },
    computed: {
        ...mapStores(useSectionsStore),
        ...mapState(useSectionsStore, ['loadingStore']),
        ...mapWritableState(useSectionsStore, ['section'])
    },
    methods: {
        ...mapActions(useSectionsStore, ['updateSection']),
        /** Get all courses for this platform */
        async getCourses() {
            try {
                this.loadingStore.setLoading(true);

                const query = {
                    "query": GET_COURSE_BY_PARAMS_QUERY_AXIOS,
                    "variables": {
                        name: this.searchContent,
                        page: this.pagination.currentPage,
                        limit: this.pagination.limit,
                    }
                };

                if (this.searchContent == "") delete query.variables.name;
                const res = await axiosGraphqlClient.post(contentAPI, query);
                const { data, total } = res.data.data.courses;

                this.courses = data.map(el => ({ ...el, checked: false }));

                this.pagination.totalResults = total;
                this.pagination.totalPages = Math.ceil(total / this.pagination.limit);

            } catch (e) {
                console.log(e)
                errorToast("Ocorreu um erro", `Ocorreu um problema ao listar as seções. Tente novamente mais tarde.`);
            }

            this.loadingStore.setLoading();
        },
        /** Get Section To Edit  */
        async getSectionToEdit(id) {
            try {
                this.loadingStore.setLoading(true);

                const query = {
                    "query": GET_SECTION_BY_ID_QUERY_AXIOS,
                    "variables": { id }
                };

                const res = await axiosGraphqlClient.post(contentAPI, query);

                const section = res.data.data.section;

                section.section_items.filter(el => el.item_data).forEach(item => {
                    if (item.item_data) {
                        item.item_data.deliveryType = item.item_data.delivery_option ?
                            "scheduled" : "sequential";

                        item.item_data.dateToDisplay = item.item_data.delivered_at ?
                            moment.utc(item.item_data.delivered_at).format('DD/MM/YYYY') : null
                    }
                })

                this.section = section;

            } catch (e) {
                console.log(e)
                errorToast("Ocorreu um erro", `Ocorreu um problema ao obter a seção. Tente novamente mais tarde.`);
            }

            this.loadingStore.setLoading();
        },
        /** add new Items on section_items */
        async addNewSectionItems(items) {
            items.forEach(item => {
                this.section.section_items.push({ ...item, position: this.section.section_items.length + 1, section_id: this.section.id });
            });

            this.closeModal('sectionItemsModal')

            await this.updateSection();
        },
        /** add new Items on section_items */
        async removeSectionItems(elem) {

            this.section.section_items = this.section.section_items.filter(item => item.position != elem.position);
            await this.updateSection();
        },
        async selectTypeContent(type) {
            if (type == 'new')
                this.$router.push({ name: 'content-new', query: { section: this.$route.params.id } });
            if (type == 'old') {
                this.closeModal('typeContentModal');
                await this.getCourses();
                this.openModal('sectionItemsModal');
            }
        },
        //** update item status */
        async updateContentStatus(item) {
            try {
                this.loadingStore.setLoading(true);

                const query = {
                    "query": UPDATE_CONTENT_STATUS_MUTATION_AXIOS,
                    "variables": {
                        id: item.item_id,
                        is_published: item.item_data.is_published == "true" ? true : false
                    }
                };

                await axiosGraphqlClient.post(contentAPI, query);

                successToast("Ação realizada", `O status do conteúdo foi atualizado com sucesso!`);
            } catch (e) {
                console.log(e)
                errorToast("Ocorreu um erro", `Ocorreu um problema ao atualizar os itens da Seção. Tente novamente mais tarde.`);
            }

            this.loadingStore.setLoading();
        },
        //** update item status */
        async updateCourseStatus(item) {
            try {
                this.loadingStore.setLoading(true);

                const query = {
                    "query": UPDATE_COURSE_STATUS_MUTATION_AXIOS,
                    "variables": {
                        id: item.item_id,
                        active: item.item_data.active == "true" ? true : false
                    }
                };

                await axiosGraphqlClient.post(contentAPI, query);

                successToast("Ação realizada", `O status do curso foi atualizado com sucesso!`);
            } catch (e) {
                console.log(e)
                errorToast("Ocorreu um erro", `Ocorreu um problema ao atualizar os itens da Seção. Tente novamente mais tarde.`);
            }

            this.loadingStore.setLoading();
        },
        async changeOrder() {
            this.section.section_items.forEach((el, order) => {
                el.position = order + 1;
            });

            await this.updateSection();
        },
        openModal(type, id = null) {
            this[type].isOpen = true;

            if (id) this[type].id = id;
        },
        closeModal(type) {
            this[type].isOpen = false;

            if (this[type].id) this[type].id = "";
            if (this[type].items) this[type].items = [];
        }
    },
    async mounted() {
        this.getSectionToEdit(this.$route.params.id)
    }
}
</script>

<style lang="scss" scoped>
.drag-icon {
    text-align: center;
    width: 20px;
    cursor: pointer;

    svg,
    i {
        color: #646D85;
        margin-left: 12px;
    }
}

.w-50p {
    width: 50px;
}

.content-title {
    display: flex;
    flex-wrap: wrap;
}

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
</style>
