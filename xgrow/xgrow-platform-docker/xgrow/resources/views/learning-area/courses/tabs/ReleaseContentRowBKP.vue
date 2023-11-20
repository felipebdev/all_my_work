<template>
    <tr class="module-header">
        <td style="width: 20px; color:#646D85; cursor: pointer" class="text-center">
            <i class="fa fa-th" style="margin-left: 12px;"></i>
        </td>
        <td class="text-center">{{ item.order }}</td>
        <td class="text-uppercase" colspan="2">{{ item.name }}</td>
        <td style="width: 300px">
            <Multiselect :options="programmingOptions" v-model="programming" placeholder="Modo da programação"
                :canClear="false" @select="changeModuleStatus(programming, item)">
            </Multiselect>
        </td>
        <td class="w-170">
            <Multiselect v-model="release" placeholder="Modo de liberação" label="name" :options="releaseOptions"
                :canClear="false" @select="changeModuleStatus(release, item)">
                <template v-slot:singlelabel="{ value }">
                    <div class="multiselect-single-label">
                        <img class="multiselect-icon" :src="value.icon" :alt="value.name">
                        {{ value.name }}
                    </div>
                </template>
                <template v-slot:option="{ option }">
                    <img class="multiselect-icon" :src="option.icon" :alt="option.name">
                    {{ option.name }}
                </template>
            </Multiselect>
        </td>
        <td></td>
    </tr>
    <tr v-if="(item.contents?.length ?? 0) > 0" v-for="content in item.contents" :key="content.id"
        style="background: #454c5f !important;">
        <td></td>
        <td style="width: 10px; color:#646D85; cursor: pointer" class="text-center">
            <i class="fa fa-th"></i>
        </td>
        <td colspan="2">
            <div class="img-content">
                <img src="https://las.xgrow.com/background-default.png" :alt="content.title" height="64">
                <div>
                    <p class="img-content__title">
                        {{ content.title }} | {{ content.subtitle }}
                    </p>
                    <p class="img-content__subtitle">
                        <span v-html="getTypeContent(content.category)"></span>
                    </p>
                </div>
            </div>
        </td>
        <td style="width: 300px">
            <Multiselect :options="programmingOptions" v-model="programming" placeholder="Modo da programação"
                :canClear="false" @select="changeModuleStatus(programming, item)">
            </Multiselect>
        </td>
        <td class="w-170">
            <Multiselect v-model="release" placeholder="Selecione o modo de liberação" label="name"
                :options="releaseOptions" :canClear="false" @select="changeModuleStatus(release, item)">
                <template v-slot:singlelabel="{ value }">
                    <div class="multiselect-single-label">
                        <img class="multiselect-icon" :src="value.icon" :alt="value.name">
                        {{ value.name }}
                    </div>
                </template>
                <template v-slot:option="{ option }">
                    <img class="multiselect-icon" :src="option.icon" :alt="option.name">
                    {{ option.name }}
                </template>
            </Multiselect>
        </td>
        <td></td>
    </tr>
</template>

<script>
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import ButtonDetail from "../../../../js/components/Datatables/ButtonDetail";

export default {
    name: "ReleaseContentRow",
    components: { ButtonDetail, Multiselect },
    props: { item: { type: Object } },
    emits: ['moduleAction', 'reloadCourse'],
    data() {
        return {
            /** Select release mode */
            releaseOptions: [
                {
                    name: 'Livre',
                    icon: '/xgrow-vendor/assets/img/icons/mdi-list-bulleted.svg',
                    value: 0,
                },
                {
                    name: 'Programada',
                    icon: '/xgrow-vendor/assets/img/icons/mdi-send-clock.svg',
                    value: 1,
                },
            ],
            release: 0,
            /** Type of programming */
            programmingOptions: [
                { value: 1, label: 'Dias após a compra' },
                { value: 2, label: 'Data programada' },
                { value: 3, label: 'Em breve' },
                { value: 4, label: 'Oculto' },
            ],
            programming: 0,

            isLoading: false,
        }
    },
    methods: {
        /** Change the module status */
        changeModuleStatus: async function (value, course) {
            console.log(value, course)
        },
        /** Get content type */
        getTypeContent: function (type) {
            if (type === 'archive') return '<i class="fas fa-paperclip me-2"></i> Arquivo'
            if (type === 'content') return '<i class="fa fa-file-alt me-2"></i> Conteúdo'
            if (type === 'link') return '<i class="fas fa-link me-2"></i> Link'
            if (type === 'text') return '<i class="fas fa-align-left me-2"></i> Texto'
            if (type === 'video') return '<i class="fa fa-photo-video me-2"></i> Vídeo'
            if (type === 'audio') return '<i class="fas headphones me-2"></i> Áudio'
            return '<i class="fa fa-file-alt me-2"></i> Conteúdo'
        },
        /** Module Edit */
        moduleEdit: function (moduleId) {
            try {
                this.isLoading = true;

                this.isLoading = false;
                successToast("Edição realizada!", `As edições no módulo "${moduleId}" foram realizadas com sucesso!`);
                this.$emit('reloadCourse');
                // console.log(data.data) // TODO REMOVER
            } catch (e) {
                console.log(e)
                this.isLoading = false;
                errorToast("Erro ao salvar!", "Ocorreu um problema ao tentar salvar as edições no módulo. Tente novamente mais tarde.");
            }
        }
    }
}
</script>

<style lang="scss" scoped>
.img-content {
    display: flex;
    align-items: center;
    gap: .75rem;

    img {
        height: 64px;
        border-radius: 4px;
    }

    .img-content__title {
        color: #FFFFFF;
        font-family: 'Open Sans', serif;
        font-style: normal;
        font-weight: 400;
        font-size: 0.875rem;
        line-height: 1.5rem;
    }

    .img-content__subtitle {
        color: #C1C5CF;
        font-family: 'Open Sans', serif;
        font-style: normal;
        font-weight: 400;
        font-size: 0.75rem;
        line-height: 1.5rem;
    }
}

.multiselect-option>img,
.character-label-icon {
    margin: 0 6px 0 0 !important;
    height: 22px !important;
    border: 1px #646c85 solid;
    border-radius: 50% !important;
}

.multiselect-option>.multiselect-icon,
.multiselect-icon {
    margin: 0 6px 0 0 !important;
    height: 22px !important;
    border: none !important;
    border-radius: 0 !important;
}

.multiselect {
    background: #252932 !important;
    border: 1px solid #646D85 !important;
    border-radius: 8px !important;
    height: 40px !important;
    min-height: 40px !important;
}

.w-170 {
    width: 170px !important;
}

tr {
    vertical-align: middle;
    border: 2px solid #2a2f39;
}

.module-header {
    background: #333844;
}
</style>
