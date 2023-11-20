<template>
    <ConfirmModal :is-open="isOpen" modalSize="xl">
        <div class="modal-body__content w-100 mt-0 p-2">
            <h1 class="text-start">Selecionar conteúdo ou curso</h1>
            <hr style="border-bottom: 1px solid white;" />
            <p class="text-start">
                Selecione qual conteúdo ou curso você quer incluir na seção.
            </p>
        </div>

        <div class="filters row w-100 gap-3 justify-content-between p-2">
            <div class="d-flex gap-3 col-12 col-lg-3 p-0 align-items-center">
                <span style="font-size: .8rem">Filtrar:</span>
                <select-without-icon id="typeContent" :options="contentOptions" v-model="selectTypeContent" />
            </div>

            <PipeVertical class="p-0 pipe" />

            <Input id="search-field" class="col-12 col-lg-7 m-0 p-0 ms-2" placeholder="Pesquise..."
                icon="<i class='fas fa-search'></i>" iconColor="#93BC1E" v-model="searchContent" />
        </div>

        <div class="d-flex flex-column gap-2 w-100 p-2">
            <Table id="users-table" v-if="selectTypeContent == 'course'">
                <template v-slot:header>
                    <th></th>
                    <th v-for="header in ['Curso', 'Autor', 'Aulas', 'Módulos', 'Horas', 'Status']" :key="header">
                        {{ header }}
                    </th>
                </template>
                <template v-slot:body v-if="courses.length">
                    <tr :key="`link-${i}`" v-for="(item, i) in courses">
                        <td>
                            <checkbox :id="i + 'checkbox'" @change="toggleCourseItem(item)" :checked="item.checked" />
                        </td>
                        <td>
                            <ContentProfile :profile="{
                                img: item.horizontal_image ?? 'https://las.xgrow.com/background-default.png',
                                title: item.name || item.title,
                                subtitle: item.contentType
                            }" />
                        </td>
                        <td>
                            <AuthorProfile :name="item.Authors.name_author"
                                :photo="item.Authors.author_photo_url ?? 'https://las.xgrow.com/background-default.png'" />
                        </td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>
                            <badge-status :status="item.active" />
                        </td>
                    </tr>
                </template>
                <template v-slot:body v-else>
                    <NoResult :colspan="11" title="Nenhum curso encontrado!" subtitle="Não há dados a serem exibidos." />
                </template>
                <template v-slot:footer>
                    <Pagination class="mt-4" :total-pages="pagination.totalPages" :total="pagination.totalResults"
                        :current-page="pagination.currentPage" @page-changed="onPageChange" @limit-changed="onLimitChange">
                    </Pagination>
                </template>
            </Table>

            <Table id="users-table" v-if="selectTypeContent == 'content'">
                <template v-slot:header>
                    <th></th>
                    <th v-for="header in ['Conteúdo', 'Autor', 'Comentários', 'Status']" :key="header">
                        {{ header }}
                    </th>
                </template>
                <template v-slot:body v-if="contents.length">
                    <tr :key="`link-${i}`" v-for="(item, i) in contents">
                        <td>
                            <checkbox :id="i + 'checkbox'" @change="toggleContentItem(item)" :checked="item.checked" />
                        </td>
                        <td>
                            <ContentProfile :profile="{
                                img: item.horizontal_image ?? 'https://las.xgrow.com/background-default.png',
                                title: item.name || item.title,
                                subtitle: item.contentType
                            }" />
                        </td>
                        <td>
                            <AuthorProfile :name="item.author.name_author"
                                :photo="item.author.author_photo_url ?? 'https://las.xgrow.com/background-default.png'" />
                        </td>
                        <td>-</td>
                        <td>
                            <badge-status :status="item.active || item.is_published" />
                        </td>
                    </tr>
                </template>
                <template v-slot:body v-else>
                    <NoResult :colspan="11" title="Nenhum conteúdo encontrado!" subtitle="Não há dados a serem exibidos." />
                </template>
                <template v-slot:footer>
                    <Pagination class="mt-4" :total-pages="pagination.totalPages" :total="pagination.totalResults"
                        :current-page="pagination.currentPage" @page-changed="onPageChange" @limit-changed="onLimitChange">
                    </Pagination>
                </template>
            </Table>
        </div>

        <div class="modal-body__footer p-2">
            <hr>
            <DefaultButton text="Cancelar" outline @click="$emit('close')" />
            <DefaultButton text="Salvar e prosseguir" status="success" @click="$emit('confirm', newItems)" />
        </div>
    </ConfirmModal>
</template>

<script>
import ConfirmModal from "../../../../js/components/XgrowDesignSystem/Modals/ConfirmModal.vue";
import DefaultButton from "../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import Subtitle from '../../../../js/components/XgrowDesignSystem/Typography/Subtitle.vue';
import RadioButton from '../../../../js/components/XgrowDesignSystem/Form/RadioButton.vue';
import Table from '../../../../js/components/Datatables/Table.vue';
import { useSectionsStore } from '../../../../js/store/sections';
import { mapState, mapStores } from "pinia";
import Pagination from '../../../../js/components/Datatables/Pagination.vue';
import Checkbox from '../../../../js/components/XgrowDesignSystem/Form/Checkbox.vue';
import ContentProfile from '../components/ContentProfile.vue';
import AuthorProfile from '../components/AuthorProfile.vue';
import BadgeStatus from '../components/BadgeStatus.vue';
import SelectWithoutIcon from '../../components/SelectWithoutIcon.vue';
import Input from '../../../../js/components/XgrowDesignSystem/Form/Input.vue';
import NoResult from '../../../../js/components/Datatables/NoResult.vue';
import PipeVertical from '../../../../js/components/XgrowDesignSystem/Utils/PipeVertical.vue';
import { axiosGraphqlClient } from '../../../../js/config/axiosGraphql';
import { GET_ALL_CONTENTS_QUERY_AXIOS } from '../../../../js/graphql/queries/contents';
import { GET_COURSE_BY_PARAMS_QUERY_AXIOS } from '../../../../js/graphql/queries/courses';

export default {
    name: 'modal-type-content',
    components: {
        ConfirmModal,
        DefaultButton,
        Subtitle,
        RadioButton,
        Table,
        Pagination,
        Checkbox,
        ContentProfile,
        AuthorProfile,
        BadgeStatus,
        SelectWithoutIcon,
        Input,
        NoResult,
        PipeVertical
    },
    props: {
        isOpen: { type: Boolean, required: true },
    },
    data() {
        return {
            typeContent: "new",
            newItems: [],
            contentOptions: [
                { value: 'course', name: 'Cursos' },
                { value: 'content', name: 'Contéudos' },
            ],
            selectTypeContent: 'course',
            courses: [],
            contents: [],
            /** Pagination */
            pagination: {
                totalPages: 1,
                totalResults: 0,
                currentPage: 1,
                limit: 25,
            },
            searchContent: ""
        }
    },
    watch: {
        selectTypeContent(state) {
            if (state == 'course') this.getCourses();

            if (state == 'content') this.getContent();

            this.searchContent = "";
            this.pagination.limit = 25;
            this.pagination.currentPage = 1;
        },
        searchContent() {
            let term = this.searchContent;

            setTimeout(async () => {
                if (term === this.searchContent) {
                    this.pagination.currentPage = 1;
                    if (this.selectTypeContent == 'course') this.getCourses();
                    if (this.selectTypeContent == 'content') this.getContent();
                }
            }, 1000);
        }
    },
    computed: {
        ...mapStores(useSectionsStore),
        ...mapState(useSectionsStore, ['loadingStore'])
    },
    methods: {
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
        /** Get all contents for this platform */
        async getContent() {
            try {
                this.loadingStore.setLoading(true);

                const query = {
                    "query": GET_ALL_CONTENTS_QUERY_AXIOS,
                    "variables": {
                        title: this.searchContent,
                        page: this.pagination.currentPage,
                        limit: this.pagination.limit,
                        module_id: null
                    }
                };

                if (this.searchContent == "") delete query.variables.title;
                const res = await axiosGraphqlClient.post(contentAPI, query);
                const { data, total } = res.data.data.contents;

                this.contents = data.map(el => ({ ...el, checked: false }));

                this.pagination.totalResults = total;
                this.pagination.totalPages = Math.ceil(total / this.pagination.limit);

            } catch (e) {
                errorToast("Ocorreu um erro", `Ocorreu um problema ao listar as seções. Tente novamente mais tarde.`);
            }

            this.loadingStore.setLoading();
        },
        toggleCourseItem(item) {
            item.checked = !item.checked;

            this.newItems = this.courses.filter(item => item.checked)
                .map(item => {

                    let newItem = {
                        item_id: item.id,
                        type: "course",
                        item_data: { ...item }
                    };
                    delete newItem.item_data.id;
                    delete newItem.item_data.checked;

                    return newItem
                })
        },
        toggleContentItem(item) {
            item.checked = !item.checked;

            console.log(item)

            this.newItems = this.contents.filter(item => item.checked)
                .map(item => {

                    let newItem = {
                        item_id: item.id,
                        type: "content",
                        item_data: { ...item }
                    };
                    delete newItem.item_data.id;
                    delete newItem.item_data.checked;

                    return newItem
                })
        },
        async onPageChange(page) {
            this.pagination.currentPage = page;

            if (this.selectTypeContent == 'course') this.getCourses();

            if (this.selectTypeContent == 'content') this.getContent();
        },
        /** Limit by size items */
        async onLimitChange(value) {
            this.pagination.limit = value;
            this.pagination.currentPage = 1;

            if (this.selectTypeContent == 'course') this.getCourses();

            if (this.selectTypeContent == 'content') this.getContent();
        },
    },
    async mounted() {
        await this.getCourses()
    }
}
</script>

<style lang="scss" scoped>
@media (max-width: 991px) {
    .modal-body__footer {
        flex-wrap: wrap;
    }

    .pipe {
        display: none;
    }
}

:deep(.form-group) {
    #search-field {
        height: 40px;
    }

    span {
        top: 7px !important;
    }
}
</style>
