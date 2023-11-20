<template>
    <draggable :list="rows.Content" item-key="id" @start="drag = true" ghost-class="ghost" @end="changeOrder"
        :disabled="false" group='content' handle=".drag-icon">
        <template #item="{ element, index }">
            <div class="d-flex w-100 align-items-baseline content-child flex-wrap justify-content-between gap-3">
                <div class="d-flex gap-3">
                    <div class="drag-icon d-flex h-100 align-self-center">
                        <i class="fa fa-th"></i>
                    </div>
                    <ProfileRow class="w-300p"
                        :profile="{ img: element.horizontal_image ?? 'https://las.xgrow.com/background-default.png', title: `Aula ${index + 1}: ${element.title}`, subtitle: element.subtitle }" />
                </div>
                <div class="d-flex gap-3">
                    <div class="align-self-center d-none d-lg-flex w-50p">
                        {{ element.duration > 0 ? element.duration : ' - ' }}
                    </div>
                    <div class="align-self-center">
                        <SelectStatus :id="`isPublished-${element.id}`" :options="statusOptions"
                            v-model="element.is_published" placeholder="Selecione uma situação"
                            @change="changeContentStatus(element.is_published, element)" />

                    </div>
                    <div class="d-flex align-self-center" style="margin-right: 3rem">
                        <ButtonDetail>
                            <li class="option">
                                <router-link :to="{ name: 'content-edit', params: { content_id: element.id } }" tag="button"
                                    class="option-btn">
                                    <i class="fa fa-pencil"></i> Editar conteúdo
                                </router-link>
                            </li>
                            <li class="option">
                                <button class="option-btn"
                                    @click="$emit('moduleAction', { action: 'deleteContent', id: element.id, title: element.title })">
                                    <i class="fa fa-trash text-danger"></i> Excluir conteúdo
                                </button>
                            </li>
                        </ButtonDetail>
                    </div>
                </div>
            </div>
        </template>
    </draggable>
    <template v-if="rows.Content.length === 0">
        <table class="w-100">
            <tbody>
                <NoResult :colspan="1" title="Nenhum conteúdo encontrado!"
                    subtitle="Não há dados a serem exibidos. Clique em adicionar para adicionar um novo conteúdo." />
            </tbody>
        </table>
    </template>
</template>

<script>
import draggable from 'vuedraggable'
import ProfileRow from "../../../../js/components/Datatables/ProfileRow.vue";
import ButtonDetail from "../../../../js/components/Datatables/ButtonDetail.vue";
import SelectStatus from "../../components/SelectStatus.vue";

import NoResult from "../../../../js/components/Datatables/NoResult.vue";
import { mapActions, mapStores } from "pinia";
import { useLoadingStore } from "../../../../js/store/components/loading";
import { axiosGraphqlClient } from "../../../../js/config/axiosGraphql";
import { UPDATE_CONTENT_STATUS_MUTATION_AXIOS } from "../../../../js/graphql/mutations/contents";

export default {
    name: "CourseModuleContentRow",
    components: { NoResult, ButtonDetail, ProfileRow, draggable, SelectStatus },
    props: {
        rows: { type: Object },
    },
    data() {
        return {
            drag: false,
            /** Select draft status */
            statusOptions: [
                { value: false, name: 'Rascunho', img: '/xgrow-vendor/assets/img/icons/edit.svg' },
                { value: true, name: 'Publicado', img: '/xgrow-vendor/assets/img/icons/web.svg' },
            ],
        }
    },
    emits: ['moduleAction', 'updateList'],
    computed: {
        ...mapStores(useLoadingStore),
    },
    methods: {
        ...mapActions(useLoadingStore, ['setLoading']),
        /** Change Content Status */
        changeContentStatus: async function (status, content) {
            try {
                const query = {
                    "query": UPDATE_CONTENT_STATUS_MUTATION_AXIOS,
                    "variables": { id: content.id, is_published: status === 'true' }
                };
                this.loadingStore.setLoading(true);
                await axiosGraphqlClient.post(contentAPI, query);
                this.loadingStore.setLoading();
                successToast("Conteúdo atualizado!", `A situação foi alterada com sucesso!`)
            } catch (e) {
                this.loadingStore.setLoading();
                errorToast("Ocorreu um erro", `Ocorreu um problema ao tentar salvar as alterações no conteúdo. Tente novamente mais tarde.`);
            }
        },
        /** Change content Order */
        changeOrder: async function () {
            this.drag = false
            this.$emit('updateList');
        }
    }
}
</script>

<style lang="scss" scoped>
.drag-icon {
    text-align: center;
    cursor: move;

    svg,
    i {
        color: #646D85;
        margin-left: 12px;
    }
}

.w-50p {
    width: 50px;
}

.w-300p {
    width: 300px;
}

.ghost {
    cursor: grab !important;
    background: #222329 !important;
    border: 2px dashed #93BC1E !important;
    border-radius: 6px !important;
    padding: 10px 10px 25px 10px;
}

.content-child {
    background: #2f333f;
    margin-top: 10px;
    padding: 10px 5px;
    box-shadow: 0 4px 4px rgb(0 0 0 / 25%);
    border-radius: 6px;
}
</style>
