<template>
    <ConfirmModal :is-open="modal" modal-size="lg">
        <Row class="text-start w-100">
            <Col>
            <Title is-form-title icon="fas fa-trophy" icon-color="#FFFFFF" icon-bg="#3f4450" class="m-0">
                Top mais assistidos
            </Title>
            <hr>
            </Col>
            <Col>
            <SwitchButton id="forceTopWatched" v-model="item.forceTopWatched" is-reverse
                @change="changeItems(item.forceTopWatched)">
                Seleção automática
            </SwitchButton>
            <Subtitle is-small>
                Quando ativada, sua seleção será feita de acordo com os conteúdos mais acessados
            </Subtitle>
            <hr>
            </Col>
            <template v-if="item.forceTopWatched">
                <Col class="text-end my-2">
                <DefaultButton text="Adicionar conteúdo" status="success" icon="fas fa-check" @click="openContentModal" />
                </Col>
                <Col class="d-flex gap-3 flex-wrap justify-content-center justify-content-md-start mt-3">
                <template v-if="item.topWatchedForceItems.length > 0">
                    <div v-for="(content, i) in item.topWatchedForceItems" :key="i" class="img-box"
                        @mouseleave="content.hover = false" @mouseover="content.hover = true"
                        :class="groupThumbStyle === 'horizontal' ? 'h-image' : 'v-image'">
                        <template v-if="content.verticalImage || content.horizontalImage">
                            <img :src="groupThumbStyle === 'horizontal' ? content.horizontalImage : content.verticalImage"
                                class="img-fluid w-100 h-100" :alt="content.name" />
                        </template>
                        <template v-else>
                            <img :src="defaultImage" class="img-fluid w-100 h-100" alt="Imagem padrão da Xgrow" />
                        </template>
                        <div class="img-box__button w-100 h-100 d-flex justify-content-center align-items-center"
                            v-if="content.hover">
                            <DefaultButton text="" icon="fas fa-times" outline title="Remover" class="btn-closex"
                                @click="removeContent(content.itemId)" />
                            <span class="badge badge-content" :class="content?.isCourse ? 'is-course' : 'is-content'">
                                {{ content?.isCourse ? 'Curso' : 'Conteúdo' }}
                            </span>
                        </div>
                    </div>
                </template>
                <table class="w-100" v-else>
                    <tbody>
                        <NoResult :colspan="1" title="Nenhum conteúdo encontrado!"
                            subtitle="Não há dados a serem exibidos. Clique no botão acima para adicionar um novo conteúdo." />
                    </tbody>
                </table>
                </Col>
            </template>
        </Row>
        <div class="modal-body__footer">
            <DefaultButton text="Cancelar" outline @click="$emit('closeModal', false)" />
            <DefaultButton text="Salvar" status="success" icon="fas fa-check"
                @click="async () => { $emit('closeModal', false) }" />
        </div>
    </ConfirmModal>

    <ConfirmModal :is-open="contentModal" modal-size="lg">
        <Row class="text-start w-100">
            <Col>
            <Title is-form-title icon="fas fa-trophy" icon-color="#FFFFFF" icon-bg="#3f4450" class="m-0">
                Selecionar o tipo de conteúdo que quer incluir
            </Title>
            <Subtitle>Você pode adicionar entre 5 a 10 conteúdos no Top mais.</Subtitle>
            <hr>
            </Col>
            <Col>
            <Select id="contentType" :options="contentTypeOptions" v-model="contentSelection.type"
                label="Selecione o tipo de conteúdo" placeholder="Selecione o tipo de conteúdo"
                @change="changeContentType" />
            </Col>

            <Col>
            <XgrowTable id="contentDatatable">
                <template v-slot:filter v-if="false">
                    <Input id="search-field" placeholder="Pesquise pelo título..." class="w-100"
                        icon="<i class='fas fa-search'></i>" iconColor="#93BC1E" v-model="filter.searchValue" />
                </template>
                <template v-slot:header>
                    <th>Título</th>
                    <th>Criado em</th>
                    <th>Status</th>
                    <th>Autor</th>
                    <th></th>
                </template>
                <template v-slot:body>
                    <tr v-if="paginatedItems.length > 0" v-for="item in paginatedItems" :key="item.value">
                        <td>
                            <ProfileRow
                                :profile="{ img: item.horizontalImage ?? 'https://las.xgrow.com/background-default.png', title: item.name, subtitle: '' }" />
                        </td>
                        <td v-html="formatDateTimeDualLine(item.createdAt)"></td>
                        <td>
                            {{ item.isPublished ? 'Publicado' : 'Rascunho' }}
                        </td>
                        <td>
                            {{ item.author }}
                        </td>
                        <td>
                            <DefaultButton v-if="!isSelected(item.value)" text="Adicionar" outline
                                @click="toggleContent(item.value)" />
                            <DefaultButton v-else text="Remover" status="success" @click="toggleContent(item.value)" />
                        </td>
                    </tr>
                    <NoResult v-else :colspan="5" title="Nenhum conteúdo encontrado!"
                        subtitle="Não há dados a serem exibidos. Clique no botão acima para adicionar um novo conteúdo." />
                </template>
                <template v-slot:footer>
                    <Pagination class="mt-4" :total-pages="pagination.totalPages" :total="pagination.totalResults"
                        :current-page="pagination.currentPage" @page-changed="onPageChange" @limit-changed="onLimitChange">
                    </Pagination>
                </template>
            </XgrowTable>
            </Col>
        </Row>
        <div class="modal-body__footer">
            <DefaultButton text="Cancelar" outline @click="contentModal = false" />
            <DefaultButton text="Adicionar" status="success" icon="fas fa-check" @click="contentModal = false" />
        </div>
    </ConfirmModal>
</template>

<script>
import Col from "../../../../../../js/components/XgrowDesignSystem/Utils/Col.vue";
import Row from "../../../../../../js/components/XgrowDesignSystem/Utils/Row.vue";
import Title from "../../../../../../js/components/XgrowDesignSystem/Typography/Title.vue";
import Subtitle from "../../../../../../js/components/XgrowDesignSystem/Typography/Subtitle.vue";
import ConfirmModal from "../../../../../../js/components/XgrowDesignSystem/Modals/ConfirmModal.vue";
import DefaultButton from "../../../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton.vue";
import Input from "../../../../../../js/components/XgrowDesignSystem/Form/Input.vue";
import Select from "../../../../../../js/components/XgrowDesignSystem/Form/Select.vue";
import { mapActions, mapState, mapStores } from "pinia";
import { useDesignStartPage } from "../../../../../../js/store/design-start-page";
import IconButton from "../../../../../../js/components/XgrowDesignSystem/Buttons/IconButton.vue";
import NoResult from "../../../../../../js/components/Datatables/NoResult.vue";
import SwitchButton from "../../../../../../js/components/XgrowDesignSystem/Form/SwitchButton.vue";
import XgrowTable from "../../../../../../js/components/Datatables/Table.vue";
import formatDateTimeDualLine from "../../../../../../js/components/XgrowDesignSystem/Mixins/formatDateTimeDualLine";
import ProfileRow from "../../../../../../js/components/Datatables/ProfileRow.vue";
import Pagination from "../../../../../../js/components/Datatables/Pagination.vue";

export default {
    name: "TopWatchedModal",
    components: {
        SwitchButton, XgrowTable, ProfileRow, Pagination, NoResult, IconButton,
        Select, Input, Row, DefaultButton, ConfirmModal, Subtitle, Title, Col
    },
    props: {
        modal: { type: Boolean, default: false },
        item: { type: Object }
    },
    emits: ['closeModal'],
    mixins: [formatDateTimeDualLine],
    data() {
        return {
            groupThumbStyle: 'horizontal',
            widget: {
                position: 0,
                type: "topWatched",
                forceTopWatched: false,
                groupTitle: null,
                groupThumbStyle: 'horizontal',
                topWatchedForceItems: [],
                hover: false
            },
            contentSelection: {
                type: 'course',
                contentId: 0,
            },
            contentTypeOptions: [
                { value: "course", name: "Curso" },
                { value: "content", name: "Conteúdo" },
            ],
            defaultImage: "https://las.xgrow.com/background-default.png",

            // Content Search
            contentItems: [],
            paginatedItems: [],
            contentModal: false,
            filter: {
                searchValue: ""
            },
            pagination: {
                totalPages: 1,
                totalResults: 0,
                currentPage: 1,
                limit: 25,
            },
        }
    },
    computed: {
        ...mapStores(useDesignStartPage),
        ...mapState(useDesignStartPage, ['courseOptions', 'contentOptions', 'loadingStore'])
    },
    methods: {
        ...mapActions(useDesignStartPage, ['changeContentType', 'searchContentsByType', 'getAllProducerContent']),
        /** On change page */
        onPageChange: async function (page) {
            this.pagination.currentPage = page;
            this.paginator(this.contentItems, this.pagination.currentPage, this.pagination.limit);
        },
        /** Limit by size itens */
        onLimitChange: async function (value) {
            this.pagination.limit = value;
            this.pagination.currentPage = 1;
            this.paginator(this.contentItems, this.pagination.currentPage, this.pagination.limit);
        },
        /** Open add content modal */
        openContentModal: function () {
            this.contentModal = true
            this.pagination.currentPage = 1;
            this.contentSelection = { type: 'course', contentId: 0 };
            this.contentItems = this.courseOptions;
            this.paginator(this.contentItems, this.pagination.currentPage, this.pagination.limit);
        },
        /** Toggle used for add or remove content from group */
        toggleContent: function (contentId) {
            const hasContent = this.item.topWatchedForceItems.some(content => content.itemId === contentId);
            if (this.item.topWatchedForceItems.length < 10 && !hasContent) {
                const { type } = this.contentSelection;
                if (type === 'course') {
                    const content = this.courseOptions.find(content => content.value === contentId)
                    if (content) {
                        this.item.topWatchedForceItems.push({
                            itemId: content.value,
                            isCourse: true,
                            verticalImage: content.verticalImage ?? this.defaultImage,
                            horizontalImage: content.horizontalImage ?? this.defaultImage,
                        })
                    }
                }
                if (type === 'content') {
                    const content = this.contentOptions.find(content => content.value === contentId)
                    if (content) {
                        this.item.topWatchedForceItems.push({
                            itemId: content.value,
                            isContent: true,
                            verticalImage: content.verticalImage ?? this.defaultImage,
                            horizontalImage: content.horizontalImage ?? this.defaultImage,
                        })
                    }
                }
            }
            if (hasContent) {
                this.removeContent(contentId)
            }
        },
        /** Remove content id */
        removeContent: function (id) {
            this.$props.item.topWatchedForceItems = this.$props.item.topWatchedForceItems.filter(item => item.itemId !== id)
        },
        /** Verify if content is selected */
        isSelected: function (contentId) {
            return this.item.topWatchedForceItems.some(content => content.itemId === contentId);
        },
        /** Change the content Type */
        changeContentType: function () {
            this.loadingStore.setLoading(true);
            const { type } = this.contentSelection;
            if (type === 'course') {
                this.contentItems = this.courseOptions;
            }
            if (type === 'content') {
                this.contentItems = this.contentOptions;
            }
            this.pagination.currentPage = 1;
            this.paginator(this.contentItems, this.pagination.currentPage, this.pagination.limit);
            this.loadingStore.setLoading();
        },
        /** Clear topWatchedForceItems === false */
        changeItems: function (topWatchedForceItems) {
            if (topWatchedForceItems === false)
                this.$props.item.topWatchedForceItems = []
        },
        /** Paginator for frontend only */
        paginator: async function (items, page, offset) {
            this.paginatedItems = [];
            this.pagination.totalResults = items.length;
            this.pagination.totalPages = Math.ceil(items.length / offset);
            if (this.pagination.totalPages === 0) this.pagination.totalPages = 1;
            let count = (page * offset) - offset;
            let delimiter = count + offset;

            if (page <= this.pagination.totalPages) {
                for (let i = count; i < delimiter; i++) {
                    if (items[i]) {
                        this.paginatedItems.push(items[i]);
                    }
                    count++
                }
            }
        }
    },
}
</script>

<style lang="scss" scoped>
:deep(.modal-body) {
    justify-content: space-between;
}

.img-box {
    display: flex;
    justify-content: center;
    align-items: center;
    background: #0c0e11;
    position: relative;

    &.h-image {
        width: 172px;
        height: 116px;
    }

    &.v-image {
        width: 134px;
        height: 200px;
    }

    img {
        object-fit: cover;
    }

    &__button {
        position: absolute;
        background-color: rgba(0, 0, 0, .8);
        top: 0;
        left: 0;

        .btn-move {
            position: absolute;
            border-radius: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 36px;
            height: 36px;
            padding: 0;

            &:hover {
                background-color: rgba(255, 255, 255, .5) !important;
            }
        }

        .btn-closex {
            position: absolute;
            top: -12px;
            right: -11px;
            border-radius: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 24px;
            height: 24px;
            padding: 0;

            &:hover {
                background-color: rgba(255, 255, 255, .5) !important;
            }
        }
    }
}

.badge-content {
    bottom: 10px;
    left: 10px;
    position: absolute;

    &.is-course {
        background-color: #e13e1b;
    }

    &.is-content {
        background-color: #9fc000;
    }
}
</style>
