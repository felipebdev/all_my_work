<template>
    <Container>
        <template v-slot:header-left>
            <Title>Comentários publicados: {{ pagination.totalResults }}</Title>
            <Subtitle>Veja todos os comentários realizados na área de aprendizagem.</Subtitle>
        </template>

        <template v-slot:content>
            <Table id="CommentDatatable" min-height>
                <template v-slot:filter>
                    <Row>
                        <Col sm="12" md="12" lg="6" xl="6" style="row-gap: 0 !important"
                            class="d-flex gap-3 align-items-center flex-wrap mb-3">
                        <template v-if="false">
                            <Input id="search-field" placeholder="Pesquise por nome ou email" style="flex: 3"
                                icon="<i class='fas fa-search'></i>" iconColor="#93BC1E" v-model="filter.searchValue" />
                            <FilterButton target="filterComments" style="flex: 1" />
                        </template>
                        </Col>
                        <Col sm="12" md="12" lg="6" xl="6"
                            class="d-flex gap-3 align-items-center justify-content-start justify-content-lg-end flex-wrap mb-3">
                        <PipeVertical class="d-none d-lg-block" />
                        <p class="action d-none d-lg-block">Ações:</p>
                        <DefaultButton icon="fa fa-comment" text="Mover para análise" status="dark"
                            :disabled="selected.length === 0" @click="() => { commentModal.reprove = true }" />
                        <DefaultButton icon="fa fa-trash" text="Excluir" status="danger" :disabled="selected.length === 0"
                            @click="() => { commentModal.delete = true }" />
                        </Col>
                    </Row>
                </template>
                <template v-slot:collapse>
                    <div class="mb-3 collapse" id="filterComments">
                        <div class="filter-container">
                            <div class="p-2 px-3">
                                <Row>
                                    <Col class="my-2">
                                    <p class="title-filter">
                                        <i class="fas fa-filter"></i> Filtros avançados
                                    </p>
                                    </Col>
                                    <Col md="6" lg="6" xl="6" class="my-3">
                                    <Select id="slcAuthor" label="Autor" placeholder="Selecione um autor" :options="authors"
                                        v-model="filter.author" />
                                    </Col>
                                    <Col md="6" lg="6" xl="6" class="my-3">
                                    <Select id="slcCourse" label="Curso" placeholder="Selecione um curso" :options="courses"
                                        v-model="filter.course" />
                                    </Col>
                                </Row>
                            </div>
                        </div>
                    </div>
                </template>
                <template v-slot:header v-if="results.length > 0">
                    <th>
                        <Checkbox id="checkAll" @checked="() => checkedAll = !checkedAll" @change="selectAll" />
                    </th>
                    <th>Nome</th>
                    <th class="col-limit">Comentário</th>
                    <th>Data</th>
                    <th class="col-limit">Conteúdo</th>
                    <th></th>
                </template>
                <template v-slot:body>
                    <template v-if="results.length > 0" v-for="(item, i) in results" :key="item.id">
                        <tr>
                            <td>
                                <Checkbox :id="`check-approved-${item.id}`" :checked="item.checked" :data-value="item.id"
                                    class="check-approved" @change="selectCheckbox" />
                            </td>
                            <td>
                                <ProfileRow rounded :profile="{
                                    img: 'https://las.xgrow.com/background-default.png',
                                    title: item.name ?? 'Não informado', subtitle: item.email ?? 'Não informado'
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
                            <td>
                                <IconButton icon="fa fa-chevron-down" button-class="show-comments" title="Abrir"
                                    @click="open(`tr-comment-${item.id}`)" />
                            </td>
                        </tr>
                        <tr :class="`d-none tr-comment-${item.id}`">
                            <td></td>
                            <td colspan="5">
                                <div class="d-flex align-items-center">
                                    <p @click="open(`tr-reply-${item.id}`)" class="btn-comment">
                                        <span class="material-symbols-outlined">subdirectory_arrow_right</span>
                                        Responder
                                    </p>
                                    <p @click="getAnswers(item.id)" class="btn-comment">
                                        <span class="material-symbols-outlined">chat</span>
                                        Ver todas as respostas ({{ item.total_answers }})
                                    </p>
                                    <p @click="null" class="btn-comment chip d-none">
                                        <span class="material-symbols-outlined">check_circle</span>
                                        Respondido pelo autor
                                    </p>
                                </div>
                            </td>
                        </tr>

                        <!-- ANSWERS -->
                        <tr :class="`d-none tr-answers-${item.id}`">
                            <td colspan="6">
                                <table class="subtable">
                                    <tr v-for="response in answers[item.id]" :key="response.id">
                                        <td style="width: 38px;"></td>
                                        <td colspan="5" class="d-block">
                                            <div class="d-flex flex-column">
                                                <ProfileRow rounded
                                                    :border-type="response.is_answer_by_producer ? 'success' : 'light'"
                                                    :profile="{
                                                        img: 'https://las.xgrow.com/background-default.png',
                                                        title: `${response.is_answer_by_producer ? '(Você)' : response.name}`, subtitle: formatDateTimeFromNow(response.created_at)
                                                    }" class="user-profile" />
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="comment-ballon is-user">
                                                        {{ response.text }}
                                                    </div>
                                                    <IconButton icon="fa fa-trash" button-class="btn-comment-danger"
                                                        title="Excluir resposta"
                                                        @click="() => { commentModal.deleteOne = true; answerId = response.id; commentId = item.id }" />
                                                    <IconButton icon="fa fa-check-circle" button-class="btn-comment-success"
                                                        v-if="false" title="Aprovar resposta" />
                                                    <IconButton icon="fa fa-times" button-class="btn-comment-none"
                                                        v-if="false" title="Reprovar resposta" />
                                                </div>
                                            </div>
                                            <p class="chip my-2 d-flex gap-1 align-items-center">
                                                <img src="/xgrow-vendor/assets/img/icons/emo-thumbs-up.svg" alt="thumbs-up"
                                                    style="height: 14px">
                                                {{ getReactions(response.reactions) }} {{
                                                    getReactions(response.reactions) < 2 ? 'reação' : 'reações' }} </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- OWNER REPLY -->
                        <tr :class="`d-none tr-reply-${item.id}`">
                            <td></td>
                            <td colspan="4">
                                <div class="d-flex align-items-center gap-2">
                                    <ProfileRow
                                        :profile="{ img: 'https://las.xgrow.com/background-default.png', title: '', subtitle: '' }"
                                        rounded class="user-profile" />
                                    <Input id="reply" label="Resposta" placeholder="Adicione uma resposta"
                                        class="reply-input" v-model="replyText" />
                                    <DefaultButton id="replyButton" text="Responder" icon="fas fa-paper-plane"
                                        style="min-width: fit-content" status="success" @click="reply(item.id)" />
                                </div>
                            </td>
                            <td></td>
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

    <ConfirmModal :is-open="commentModal.reprove">
        <i class="fas fa-question-circle fa-7x text-warning"></i>
        <div class="modal-body__content">
            <h1>Mover comentários para análise?</h1>
            <p>Ao mover os comentários, eles irão para a aba de Comentários retidos para análise!</p>
        </div>
        <div class="modal-body__footer">
            <DefaultButton text="Cancelar" outline @click="() => { commentModal.reprove = false }" />
            <DefaultButton text="Sim, mover para análise" status="success" @click="reproveComments" />
        </div>
    </ConfirmModal>

    <ConfirmModal :is-open="commentModal.deleteOne">
        <i class="fas fa-times-circle fa-7x text-danger"></i>
        <div class="modal-body__content">
            <h1>Excluir comentário?</h1>
            <p>Ao remover este comentário, não será mais possível recuperá-lo!</p>
        </div>
        <div class="modal-body__footer">
            <DefaultButton text="Cancelar" outline @click="() => { commentModal.deleteOne = false }" />
            <DefaultButton text="Excluir mesmo assim" status="success" @click="deleteAnswer" />
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
import ConfirmModal from "../../../../js/components/XgrowDesignSystem/Modals/ConfirmModal.vue";

import formatDateTimeDualLine from "../../../../js/components/XgrowDesignSystem/Mixins/formatDateTimeDualLine";
import resume from "../../../../js/components/XgrowDesignSystem/Mixins/resume";
import formatDateTimeFromNow from "../../../../js/components/XgrowDesignSystem/Mixins/formatDateTimeFromNow";
import { mapActions, mapStores } from "pinia";
import { useLoadingStore } from "../../../../js/store/components/loading";
import { GET_ALL_COMMENTS_QUERY, GET_ANSWERS_QUERY } from "../../../../js/graphql/queries/comments";
import { axiosGraphqlClient } from "../../../../js/config/axiosGraphql";
import {
    DELETE_COMMENT_MUTATION,
    UPDATE_COMMENT_APPROVE_MUTATION,
    CREATE_ANSWER_MUTATION,
    DELETE_ANSWER_MUTATION
} from "../../../../js/graphql/mutations/comments";
import axios from "axios";

export default {
    name: "CommentsPublished",
    components: {
        Container, Title, Subtitle, Table, Checkbox, NoResult, FilterButton, Select, ConfirmModal,
        ProfileRow, ContentProfile, IconButton, PipeVertical, Input, DefaultButton, Pagination, Row, Col
    },
    props: { refreshComments: { type: String } },
    mixins: [formatDateTimeDualLine, resume, formatDateTimeFromNow],
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
                deleteOne: false,
                reprove: false
            },

            answerId: null,
            commentId: null,
            answers: [],
            replyText: null
        }
    },
    watch: {
        "refreshComments": async function (newVal, _) {
            if (newVal === 'tabPublished')
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
        open: async function (id) {
            this.selected = [];
            const tr = window.document.querySelectorAll(`.${id}`);
            Array.from(tr).forEach(item => {
                item.classList.toggle('d-none');
            })
        },
        /** Get all answers by comment */
        getAnswers: async function (comment_id) {
            try {
                this.loadingStore.setLoading(true);
                const query = {
                    "query": GET_ANSWERS_QUERY,
                    "variables": {
                        comment_id: comment_id
                    }
                };
                const res = await axiosGraphqlClient.post(contentAPI, query);

                /** Create merge between Content and Platform */
                let data = res.data.data.answers.data;
                let ids = data.map(item => item.user_id);
                let subscribers = await axios.post(getSubscriberInfoURL, { userList: ids });
                subscribers = subscribers.data.response;

                let dataMerge = data.map(sub => {
                    let index = subscribers.findIndex(item => item.id == sub.user_id)
                    return { ...sub, name: subscribers[index].name, email: subscribers[index].email }
                })

                this.answers[comment_id] = dataMerge;
                // this.answers[comment_id] = res.data.data.answers.data;
                await this.open(`tr-answers-${comment_id}`);
                this.loadingStore.setLoading();
            } catch (e) {
                this.loadingStore.setLoading();
                console.log(e);
            }
        },
        getAllComments: async function () {
            try {
                this.loadingStore.setLoading(true);
                this.selected = [];
                const query = {
                    "query": GET_ALL_COMMENTS_QUERY,
                    "variables": {
                        approved: true,
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
            const checked = document.querySelectorAll('.check-approved')
            checked.forEach(item => {
                item.childNodes[0].checked = this.checkedAll;
            })
            this.selectCheckbox()
        },
        /** Select one */
        selectCheckbox: function () {
            this.selected = []
            const checked = document.querySelectorAll('.check-approved')
            checked.forEach(item => {
                if (item.childNodes[0].checked) {
                    this.selected.push(item.dataset.value)
                }
            })
        },
        /** Approve selected comments */
        reproveComments: async function () {
            try {
                this.selectCheckbox();
                this.commentModal.reprove = false;
                this.loadingStore.setLoading(true);
                for (const item of this.selected) {
                    const query = {
                        "query": UPDATE_COMMENT_APPROVE_MUTATION,
                        "variables": { comment_id: item, approved: false }
                    };
                    await axiosGraphqlClient.post(contentAPI, query);
                }
                await this.getAllComments();
                successToast("Comentários em análise!", `Os comentários selecionados foram enviados para análise!`);
                this.loadingStore.setLoading();
            } catch (e) {
                this.loadingStore.setLoading();
                errorToast("Erro ao realizar essa ação!", `Os comentários selecionados não foram para análise. Tente em alguns instantes`);
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
        },
        /** Reply User */
        reply: async function (comment_id) {
            try {
                this.loadingStore.setLoading(true);
                const query = {
                    "query": CREATE_ANSWER_MUTATION,
                    "variables": { comment_id: comment_id, text: this.replyText }
                };
                await axiosGraphqlClient.post(contentAPI, query);
                this.replyText = null;
                await this.open(`tr-answers-${comment_id}`);
                await this.getAnswers(comment_id);
                successToast("Ação realizada com sucesso!", `A resposta adicionada com sucesso!`);
                this.loadingStore.setLoading();
            } catch (e) {
                this.loadingStore.setLoading();
                errorToast("Erro ao realizar essa ação!", `O comentário não pode ser respondido. Tente em alguns instantes`);
                console.log(e);
            }
        },
        /** Get reactions */
        getReactions: function (reactions) {
            const total = Object.keys(reactions).reduce(function (previous, key) {
                return previous + reactions[key];
            }, 0);
            return total;
        },
        /** Delete Answer */
        deleteAnswer: async function () {
            try {
                this.loadingStore.setLoading(true);
                this.commentModal.deleteOne = false;
                const query = {
                    "query": DELETE_ANSWER_MUTATION,
                    "variables": { answer_id: this.answerId }
                };
                await axiosGraphqlClient.post(contentAPI, query);
                await this.open(`tr-answers-${this.commentId}`);
                await this.getAnswers(this.commentId);
                successToast("Resposta removida!", `Resposta excluída com sucesso!`);
                this.answerId = null;
                this.commentId = null;
                this.loadingStore.setLoading();
            } catch (e) {
                this.loadingStore.setLoading();
                errorToast("Erro ao realizar essa ação!", `A resposta não foi excluída. Tente em alguns instantes`);
                console.log(e);
            }
        },
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
    border-collapse: separate;
    border-spacing: 0 24px;
    width: 100%;

    tr {
        td {
            border-bottom: #646D85 1px solid !important;
        }

        &:last-child {
            td {
                border-bottom: none !important;
            }
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