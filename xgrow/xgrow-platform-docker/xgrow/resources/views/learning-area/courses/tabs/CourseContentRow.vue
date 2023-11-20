<template>
    <div class="d-flex w-100 align-items-baseline gap-3 flex-wrap justify-content-between">
        <div class="d-flex gap-3 align-self-center">
            <div class="drag-icon">
                <i class="fa fa-th"></i>
            </div>
            <div class="text-center" style="width: 20px;">{{ item.position }}</div>
            <div class="text-uppercase w-100">{{ item.name }}</div>
        </div>
        <div class="d-flex gap-3 align-self-center flex-wrap">
            <div class="w-50p align-self-center d-none d-lg-flex">{{ item.Content.length }}</div>
            <div class="w-50p align-self-center d-none d-lg-flex">{{ durationModule }}</div>
            <div>
                <SelectStatus :id="`active-${item.id}`" :options="statusOptions" v-model="item.status"
                    placeholder="Selecione uma situação" @change="changeModuleStatus(item.status, item)" />
            </div>
            <div class="w-50p" style="margin-right: 1rem">
                <ButtonDetail>
                    <li class="option">
                        <button class="option-btn" @click="$emit('moduleAction', { action: 'moduleEdit', id: item.id })">
                            <i class="fa fa-pencil"></i> Editar módulo
                        </button>
                    </li>
                    <li class="option">
                        <button class="option-btn" @click="addContent(item.id)">
                            <i class="fa fa-plus-circle"></i> Adicionar conteúdo
                        </button>
                    </li>
                    <li class="option d-none">
                        <button class="option-btn" @click="$emit('moduleAction', { action: 'moduleClone', id: item.id })">
                            <i class="fa fa-copy"></i> Duplicar módulo
                        </button>
                    </li>
                    <li class="option">
                        <button class="option-btn"
                            @click="$emit('moduleAction', { action: 'moduleTransfer', id: item.id })">
                            <i class="fa fa-exchange"></i> Transferir módulo
                        </button>
                    </li>
                    <li class="option">
                        <button class="option-btn" @click="$emit('moduleAction', { action: 'moduleDelete', id: item.id })">
                            <i class="fa fa-trash text-danger"></i> Excluir módulo
                        </button>
                    </li>
                </ButtonDetail>
            </div>
        </div>
    </div>
</template>

<script>
import ButtonDetail from "../../../../js/components/Datatables/ButtonDetail";
import SelectStatus from "../../components/SelectStatus.vue";
import PipeVertical from "../../../../js/components/XgrowDesignSystem/Utils/PipeVertical";

import { mapActions, mapStores } from "pinia";
import { useLoadingStore } from "../../../../js/store/components/loading";
import { axiosGraphqlClient } from "../../../../js/config/axiosGraphql";
import { UPDATE_MODULE_STATUS_MUTATION_AXIOS } from "../../../../js/graphql/mutations/modules";

export default {
    name: "CourseContentRow",
    components: { PipeVertical, ButtonDetail, SelectStatus },
    props: {
        item: { type: Object },
        index: { type: Number }
    },
    emits: ['moduleAction'],
    data() {
        return {
            /** Select draft status */
            statusOptions: [
                { value: false, name: 'Rascunho', img: '/xgrow-vendor/assets/img/icons/edit.svg' },
                { value: true, name: 'Publicado', img: '/xgrow-vendor/assets/img/icons/web.svg' },
            ],
        }
    },
    computed: {
        ...mapStores(useLoadingStore),
        durationModule() {
            const total = this.item.Content.reduce((acc, item) => acc += item.duration, 0);

            return total > 0 ? total : ' - '
        }
    },
    methods: {
        ...mapActions(useLoadingStore, ['setLoading']),
        /** Add new Content */
        addContent: async function (moduleId) {
            this.$router.push({
                name: 'content-new',
                query: { course: this.$route.params.id, module: moduleId }
            })
        },
        /** Change Module Status */
        changeModuleStatus: async function (status, item) {
            try {
                this.loadingStore.setLoading(true);
                const query = {
                    "query": UPDATE_MODULE_STATUS_MUTATION_AXIOS,
                    "variables": { id: item.id, name: item.name, status: status === 'true' }
                };
                await axiosGraphqlClient.post(contentAPI, query);
                this.loadingStore.setLoading();
                successToast("Módulo atualizado!", `O status do módulo foi atualizado com sucesso!`);
            } catch (e) {
                this.loadingStore.setLoading();
                errorToast("Ocorreu um erro", `Ocorreu um problema ao tentar atualizar o módulo. Tente novamente mais tarde.`);
            }
        }
    }
}
</script>

<style lang="scss" scoped>
tr {
    vertical-align: middle;
    border: 2px solid #2a2f39;
}

.w-50p {
    width: 50px;
}

.drag-icon {
    text-align: center;
    width: 20px;
    cursor: move;

    svg,
    i {
        color: #646D85;
        margin-left: 12px;
    }
}
</style>
