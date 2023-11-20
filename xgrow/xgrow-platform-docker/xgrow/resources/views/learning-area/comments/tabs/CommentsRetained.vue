<template>
    <Container>
        <template v-slot:header-left>
            <Title>Comentários retidos para análise: {{ pagination.totalResults }}</Title>
            <Subtitle>Veja todos os comentários retidos para aprovação.</Subtitle>
        </template>
        <template v-slot:header-right v-if="false">
            <Row class="gap-2">
                <p class="action">APROVAÇÃO AUTOMÁTICA</p>
                <div class="d-flex gap-2 justify-content-start justify-content-md-end flex-wrap">
                    <SwitchButton id="swtComments">Comentários</SwitchButton>
                    <SwitchButton id="swtReply">Respostas</SwitchButton>
                </div>
            </Row>
        </template>

        <template v-slot:content>
            <Table id="CommentRetainedDatatable" min-height>
                <template v-slot:filter>
                    <Row>
                        <Col sm="12" md="12" lg="6" xl="6" style="row-gap: 0 !important"
                            class="d-flex gap-3 align-items-center flex-wrap mb-3">
                        <template v-if="false">
                            <Input id="search-field" placeholder="Pesquise por nome ou email" style="flex: 3"
                                icon="<i class='fas fa-search'></i>" iconColor="#93BC1E" v-model="filter.searchValue" />
                            <FilterButton target="filterRetainedComments" style="flex: 1" />
                        </template>
                        </Col>
                        <Col sm="12" md="12" lg="6" xl="6"
                            class="d-flex gap-3 align-items-center justify-content-start justify-content-lg-end flex-wrap mb-3">
                        <PipeVertical class="d-none d-lg-block" />
                        <p class="action d-none d-lg-block">Ações:</p>
                        <DefaultButton icon="fa fa-check" text="Aprovar" status="success" :disabled="selected.length === 0"
                            @click="() => { commentModal.approve = true }" />
                        <DefaultButton icon="fa fa-trash" text="Excluir" status="danger" :disabled="selected.length === 0"
                            @click="() => { commentModal.delete = true }" />
                        </Col>
                    </Row>
                </template>
                <template v-slot:collapse>
                    <div class="mb-3 collapse" id="filterRetainedComments">
                        <div class="filter-container">
                            <div class="p-2 px-3">
                                <Row>
                                    <Col class="my-2">
                                    <p class="title-filter">
                                        <i class="fas fa-filter"></i> Filtros avançados
                                    </p>
                                    </Col>
                                    <Col md="6" lg="6" xl="6" class="my-3">
                                    <Select id="slcRetainedAuthor" label="Autor" placeholder="Selecione um autor"
                                        :options="authors" v-model="filter.author" />
                                    </Col>
                                    <Col md="6" lg="6" xl="6" class="my-3">
                                    <Select id="slcRetainedCourse" label="Curso" placeholder="Selecione um curso"
                                        :options="courses" v-model="filter.course" />
                                    </Col>
                                </Row>
                            </div>
                        </div>
                    </div>
                </template>
                <template v-slot:header v-if="results.length > 0">
                    <th>
                        <Checkbox id="checkAllRetained" @checked="() => checkedAll = !checkedAll" @change="selectAll" />
                    </th>
                    <th>Nome</th>
                    <th class="col-limit">Comentário</th>
                    <th>Data</th>
                    <th class="col-limit">Conteúdo</th>
                </template>
                <template v-slot:body>
                    <template v-if="results.length > 0" v-for="item in results" :key="item.id">
                        <tr>
                            <td>
                                <Checkbox :id="`check-retained-${item.id}`" :checked="item.checked" :data-value="item.id"
                                    class="check-retained" @change="selectCheckbox" />
                            </td>
                            <td>
                                <ProfileRow rounded :profile="{
                                    img: 'https://las.xgrow.com/background-default.png',
                                    title: item.name, subtitle: item.email
                                }" />
                            </td>
                            <td class="col-limit">
                                <p v-html="resume(item.text, 80)"></p>
                            </td>
                            <td>
                                <span v-html="formatDateTimeDualLine(item.created_at)"></span>
                            </td>
                            <td class="col-limit">
                                <ContentProfile :profile="{
                                    img: item.Content.horizontal_image ?? 'https://las.xgrow.com/background-default.png',
                                    title: item.Content.title, subtitle: item.Content.contentType
                                }" />
                            </td>
                        </tr>
                    </template>
                    <NoResult v-else :colspan="6" title="Nenhum comentário encontrado!"
                        subtitle="Não há dados a serem exibidos." />
                </template>
                <template v-slot:footer>
                    <Pagination class="mt-4" :total-pages="pagination.totalPages" :total="pagination.totalResults"
                        :current-page="pagination.currentPage" @page-changed="onPageChange" @limit-changed="onLimitChange">
                    </Pagination>
                </template>
            </Table>
        </template>
    </Container>

    <ConfirmModal :is-open="commentModal.delete">
        <i class="fas fa-times-circle fa-7x text-danger"></i>
        <div class="modal-body__content">
            <h1>Excluir comentários?</h1>
            <p>Ao remover estes comentários, não será mais possível recuperá-los!</p>
        </div>
        <div class="modal-body__footer">
            <DefaultButton text="Cancelar" outline @click="() => { commentModal.delete = false }" />
            <DefaultButton text="Excluir mesmo assim" status="success" @click="deleteComments" />
        </div>
    </ConfirmModal>

    <ConfirmModal :is-open="commentModal.approve">
        <i class="fas fa-question-circle fa-7x text-warning"></i>
        <div class="modal-body__content">
            <h1>Aprovar comentários?</h1>
            <p>Ao aprovar os comentários, eles irão para a aba de Comentários publicados!</p>
        </div>
        <div class="modal-body__footer">
            <DefaultButton text="Cancelar" outline @click="() => { commentModal.approve = false }" />
            <DefaultButton text="Aprovar" status="success" @click="approveComments" />
        </div>
    </ConfirmModal>
</template>

<script>
import Container from "../../../../js/components/XgrowDesignSystem/Cards/Container.vue";
import Title from "../../../../js/components/XgrowDesignSystem/Typography/Title.vue";
import Subtitle from "../../../../js/components/XgrowDesignSystem/Typography/Subtitle.vue";
import Table from "../../../../js/components/Datatables/Table.vue";
import Checkbox from "../../../../js/components/XgrowDesignSystem/Form/Checkbox.vue";
import ProfileRow from "../../../../js/components/Datatables/ProfileRow.vue";
import ContentProfile from "../../contents/components/ContentProfile.vue";
import IconButton from "../../../../js/components/XgrowDesignSystem/Buttons/IconButton.vue";
import PipeVertical from "../../../../js/components/XgrowDesignSystem/Utils/PipeVertical.vue";
import Input from "../../../../js/components/XgrowDesignSystem/Form/Input.vue";
import DefaultButton from "../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton.vue";
import NoResult from "../../../../js/components/Datatables/NoResult.vue";
import Pagination from "../../../../js/components/Datatables/Pagination.vue";
import FilterButton from "../../../../js/components/XgrowDesignSystem/Buttons/FilterButton.vue"
import Row from "../../../../js/components/XgrowDesignSystem/Utils/Row.vue"
import Col from "../../../../js/components/XgrowDesignSystem/Utils/Col.vue"
import Select from "../../../../js/components/XgrowDesignSystem/Form/Select.vue";
import SwitchButton from "../../../../js/components/XgrowDesignSystem/Form/SwitchButton.vue";
import ConfirmModal from "../../../../js/components/XgrowDesignSystem/Modals/ConfirmModal.vue";

import formatDateTimeDualLine from "../../../../js/components/XgrowDesignSystem/Mixins/formatDateTimeDualLine";
import resume from "../../../../js/components/XgrowDesignSystem/Mixins/resume";
import { mapActions, mapStores } from "pinia";
import { useLoadingStore } from "../../../../js/store/components/loading";
import { GET_ALL_COMMENTS_QUERY } from "../../../../js/graphql/queries/comments";
import { UPDATE_COMMENT_APPROVE_MUTATION, DELETE_COMMENT_MUTATION } from "../../../../js/graphql/mutations/comments";
import { axiosGraphqlClient } from "../../../../js/config/axiosGraphql";
import axios from "axios";

export default {
    name: "CommentsRetained",
    components: {
        Container, Title, Subtitle, Table, Checkbox, NoResult, FilterButton, Select, SwitchButton, ConfirmModal,
        ProfileRow, ContentProfile, IconButton, PipeVertical, Input, DefaultButton, Pagination, Row, Col
    },
    props: { refreshComments: { type: String } },
    mixins: [formatDateTimeDualLine, resume],
    data() {
        return {
            results: [],
            authors: [{ value: 1, name: "Christian Barbosa" }],
            courses: [{ value: 1, name: "IA com Python" }],

            filter: {
                searchValue: "",
                author: null,
                course: null,
            },
            pagination: {
                totalPages: 1,
                totalResults: 0,
                currentPage: 1,
                limit: 25,
            },

            checkedAll: false,
            selected: [],

            commentModal: {
                delete: false,
                approve: false
            }
        }
    },
    watch: {
        "refreshComments": async function (newVal, _) {
            if (newVal === 'tabRetained')
                await this.getAllComments();
        }
    },
    computed: {
        ...mapStores(useLoadingStore),
    },
    methods: {
        ...mapActions(useLoadingStore, ['setLoading']),
        /** On change page */
        onPageChange: async function (page) {
            this.pagination.currentPage = page;
            await this.getAllComments();
        },
        /** Limit by size itens */
        onLimitChange: async function (value) {
            this.pagination.limit = value;
            this.pagination.currentPage = 1;
            await this.getAllComments();
        },
        getAllComments: async function () {
            try {
                this.loadingStore.setLoading(true);
                this.selected = [];
                const query = {
                    "query": GET_ALL_COMMENTS_QUERY,
                    "variables": {
                        approved: false,
                        page: this.pagination.currentPage
                    }
                };
                const res = await axiosGraphqlClient.post(contentAPI, query);
                const { current_page, data, total, total_pages } = res.data.data.comments;
                this.pagination.totalPages = total_pages === 0 ? 1 : total_pages;
                this.pagination.currentPage = current_page === 0 ? 1 : current_page;
                this.pagination.totalResults = total;

                /** Create merge between Content and Platform */
                let ids = data.map(item => item.user_id);
                let subscribers = await axios.post(getSubscriberInfoURL, { userList: ids });
                subscribers = subscribers.data.response;

                let dataMerge = data.map(sub => {
                    let index = subscribers.findIndex(item => item.id == sub.user_id)
                    return { ...sub, name: subscribers[index].name, email: subscribers[index].email }
                })

                this.results = dataMerge;
                this.loadingStore.setLoading();
            } catch (e) {
                this.loadingStore.setLoading();
                console.log(e);
            }
        },
        /** Select all items */
        selectAll: function () {
            const checked = document.querySelectorAll('.check-retained')
            checked.forEach(item => {
                item.childNodes[0].checked = this.checkedAll;
            })
            this.selectCheckbox()
        },
        /** Select one */
        selectCheckbox: function () {
            this.selected = []
            const checked = document.querySelectorAll('.check-retained')
            checked.forEach(item => {
                if (item.childNodes[0].checked) {
                    this.selected.push(item.dataset.value)
                }
            })
        },
        /** Approve selected comments */
        approveComments: async function () {
            try {
                this.selectCheckbox();
                this.commentModal.approve = false;
                this.loadingStore.setLoading(true);
                for (const item of this.selected) {
                    const query = {
                        "query": UPDATE_COMMENT_APPROVE_MUTATION,
                        "variables": { comment_id: item, approved: true }
                    };
                    await axiosGraphqlClient.post(contentAPI, query);
                }
                await this.getAllComments();
                successToast("Comentários aprovados!", `Os comentários selecionados foram aprovados com sucesso!`);
                this.loadingStore.setLoading();
            } catch (e) {
                this.loadingStore.setLoading();
                errorToast("Erro ao realizar essa ação!", `Os comentários selecionados não foram aprovados. Tente em alguns instantes`);
                console.log(e);
            }
        },
        /** Delete selected comments */
        deleteComments: async function () {
            try {
                this.selectCheckbox();
                this.commentModal.delete = false;
                this.loadingStore.setLoading(true);
                for (const item of this.selected) {
                    const query = {
                        "query": DELETE_COMMENT_MUTATION,
                        "variables": { id: item }
                    };
                    await axiosGraphqlClient.post(contentAPI, query);
                }
                await this.getAllComments();
                successToast("Comentários removidos!", `Os comentários selecionados foram excluídos com sucesso!`);
                this.loadingStore.setLoading();
            } catch (e) {
                this.loadingStore.setLoading();
                errorToast("Erro ao realizar essa ação!", `Os comentários selecionados não foram excluídos. Tente em alguns instantes`);
                console.log(e);
            }
        }
    },
    async mounted() {
        await this.getAllComments();
    }
}
</script>

<style lang="scss" scoped>
.custom-select {
    :deep(select) {
        background: #252932 !important;
        border: 1px solid #646D85 !important;
        border-radius: 8px !important;
        height: 40px !important;
        min-height: 40px !important;
    }
}

.img-icon {
    filter: brightness(0) saturate(100%) invert(73%) sepia(86%) saturate(2134%) hue-rotate(30deg) brightness(99%) contrast(76%);
    height: 1rem;
}

.modal-body__content {
    border-radius: 8px;
    padding: 1rem;
    margin-top: 0 !important;
}

:deep(.form-group) {
    #search-field {
        height: 40px;
    }

    span {
        top: 7px !important;
    }
}

.show-comments {
    color: #646D85;
    background-color: transparent;
    font-size: 12px;

    &:hover {
        background-color: rgba(0, 0, 0, .1);
    }
}

.btn-comment {
    &-danger {
        color: #ffffff;
        font-size: 14px;
        background-color: #F96C6C;
        margin-top: 14px;
        min-width: 40px;
        min-height: 40px;
    }

    &-success {
        color: #ffffff;
        font-size: 14px;
        background-color: #93BC1E;
        margin-top: 14px;
        min-width: 40px;
        min-height: 40px;
    }

    &-none {
        color: #ffffff;
        font-size: 14px;
        background-color: transparent;
        border: 1px solid #ffffff;
        margin-top: 14px;
        min-width: 40px;
        min-height: 40px;
    }
}

.checkbox-size {
    width: 40px;
    display: flex;
    justify-content: center;
}

.btn-comment {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    cursor: pointer;

    &:after {
        content: " • ";
        margin-right: 10px;
        font-size: 1.5rem;
        color: #646D85;
    }

    &:last-child:after {
        content: "";
        margin-right: 0;
    }

    &:hover {
        color: #ADDF45;
    }

    span {
        font-size: 18px;
    }
}

.chip {
    background: #2b2f38;
    padding: 4px 10px;
    border-radius: 40px;
    width: fit-content;
    height: fit-content;
}

.comment-ballon {
    background: #50576B;
    border-radius: 12px;
    padding: 12px 8px;
    margin-top: 14px;
    position: relative;

    &:after {
        content: '';
        position: absolute;
        top: 0;
        left: 15px;
        width: 0;
        height: 0;
        border: 10px solid transparent;
        border-left: 7px solid transparent;
        border-right: 7px solid transparent;
        border-bottom-color: #50576B;
        border-top: 0;
        margin-top: -10px;
    }

    &.is-user {
        background: #252932;
    }

    &.is-user:after {
        content: '';
        border-bottom-color: #252932;
    }
}

:deep(.reply-input) {
    height: 60px;
    width: 100%;

    input {
        border-bottom: none;
        border-radius: 12px;
    }
}

:deep(.subtable) {
    padding-top: 1rem;

    tr {
        background: #333844 !important;
        border-bottom: none !important;
        font-size: 0.875rem;
        vertical-align: middle !important;

        &:last-child {
            border-bottom: none !important;
        }

        td {
            border: none !important;
            background: transparent;
        }
    }
}

:deep(.user-profile) {
    img {
        height: 44px;
        width: 44px;
    }
}

.action {
    color: #646D85;
    text-transform: uppercase;
    font-size: 0.75rem;
    font-weight: 700;
}
</style>