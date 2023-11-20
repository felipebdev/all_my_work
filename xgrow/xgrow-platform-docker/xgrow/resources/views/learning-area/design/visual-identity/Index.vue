<template>
    <div key="teste">
        <LoadingStore />
        <Breadcrumb :items="breadcrumbs" class="mb-3"></Breadcrumb>

        <!-- tabs -->
        <tab id="visual-identity">
            <template v-slot:header>
                <tab-nav id="tab-nav" :items="tabs.items" :startTab="tabs.active" @changePage="changeTab" />
            </template>
            <template v-slot:body>
                <tab-content id="colors" :selected="tabs.active === 'colors'">
                    <colors v-if="tabs.active == 'colors'" />
                </tab-content>
                <tab-content id="images" :selected="tabs.active === 'images'">
                    <Images v-if="tabs.active == 'images'" />
                </tab-content>
                <tab-content id="titles" :selected="tabs.active === 'titles'">
                    <titles v-if="tabs.active == 'titles'" />
                </tab-content>
            </template>
        </tab>

        <div class="buttons my-3">
            <router-link :to="{ name: 'design-index' }">
                <DefaultButton text="Voltar" outline />
            </router-link>

            <div class="save-reset">
                <DefaultButton text="Redefinir config." status="danger" @click="openModal('reset')" />
                <DefaultButton text="Salvar e aplicar tema" status="success" @click="openModal('save')" />
            </div>
        </div>



        <ConfirmModal :is-open="modal.open">
            <Title>{{ modal.title }}</Title>
            <Row class="w-100">
                <Col class="modal-body__text">
                <i class="modalIcon fas fa-exclamation-triangle" style="color: #e28a22; font-size: 3rem"></i>
                </Col>
            </Row>

            <Row class="w-100">
                <Col>
                <p class="text-center">{{ modal.text }}</p>
                </Col>
            </Row>

            <div class="modal-body__footer">
                <DefaultButton text="Cancelar" outline @click="clearModal()" />
                <DefaultButton text="Confirmar" status="success" @click="modal.callback()" />
            </div>
        </ConfirmModal>
    </div>
</template>

<script>
import Breadcrumb from "../../../../js/components/XgrowDesignSystem/Breadcrumb/Breadcrumb";
import ConfirmModal from "../../../../js/components/XgrowDesignSystem/Modals/ConfirmModal";
import Tab from "../../../../js/components/XgrowDesignSystem/Tab/XgrowTab.vue";
import TabContent from "../../../../js/components/XgrowDesignSystem/Tab/XgrowTabContent.vue";
import TabNav from "../../../../js/components/XgrowDesignSystem/Tab/XgrowTabNav.vue";
import LoadingStore from "../../../../js/components/XgrowDesignSystem/Utils/LoadingStore.vue";
import DefaultButton from '../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton.vue';
import Colors from "./tabs/colors.vue";
import Titles from "./tabs/titles.vue";
import Images from "./tabs/images.vue";

import { useDesignVisualIdentity } from "../../../../js/store/design-visual-identity";
import { mapActions, mapWritableState, mapStores } from "pinia";
import Title from '../../../../js/components/XgrowDesignSystem/Typography/Title.vue';
import Col from '../../../../js/components/XgrowDesignSystem/Utils/Col.vue';
import Row from '../../../../js/components/XgrowDesignSystem/Utils/Row.vue';

export default {
    components: {
        Breadcrumb,
        ConfirmModal,
        Tab,
        TabContent,
        TabNav,
        LoadingStore,
        Colors,
        Titles,
        Images,
        DefaultButton,
        Title,
        Col,
        Row
    },
    data() {
        return {
            breadcrumbs: [
                { title: "Resumo", link: "/" },
                { title: "Área de aprendizagem", link: false },
                {
                    title: "Design",
                    link: "/learning-area/design",
                    isVueRouter: true,
                },
                { title: "Identidade Visual", link: false },
            ],
            tabs: {
                items: [
                    { title: "Cores", screen: "colors" },
                    { title: "Imagens", screen: "images" },
                    { title: "Títulos e Rodapé", screen: "titles" },
                ],
                active: "colors",
            },
            modal: {
                title: '',
                text: '',
                callback: () => { },
                open: false,
            }
        };
    },
    methods: {
        ...mapActions(useDesignVisualIdentity, ["saveTheme", "getTheme", "updateKeywords", "validate"]),
        changeTab(tab) {
            this.tabs.active = tab;
        },
        openModal(modalType) {
            const modals = {
                reset: {
                    title: 'Redefinindo...',
                    text: 'Deseja realmente redefinir para a última configuração salva?',
                    callback: async () => {
                        this.modal.open = false;
                        await this.getTheme();
                        this.changeTab('colors');
                    },
                    open: true
                },
                save: {
                    title: 'Salvando...',
                    text: 'Deseja realmente salvar a configuração  atual?',
                    callback: async () => {
                        this.modal.open = false;
                        await this.updateKeywords();
                        try {
                            this.validate();

                            await this.saveTheme();
                            this.changeTab('colors');
                            await this.getTheme();
                        } catch (e) {
                            errorToast(
                                "Atenção",
                                e.message ?? "Ocorreu um erro inesperado, por favor entre em contato com o suporte"
                            );
                        }
                    },
                    open: true
                }
            }

            this.modal = { ...modals[modalType] };
        },
        clearModal() {
            const clear = {
                title: '',
                text: '',
                callback: () => { },
                open: false
            }

            this.modal = { ...clear };
        }

    },
    computed: {
        ...mapStores(useDesignVisualIdentity),
        ...mapWritableState(useDesignVisualIdentity, ["loadingStore", "theme"]),
    },
    async mounted() {
        await this.getTheme();
    },
};
</script>

<style lang="scss" scoped>
.buttons {
    display: flex;
    justify-content: space-between;
    border-top: 1px solid rgba(#ffffff, .15);
    padding-top: 10px;

    .save-reset {
        display: flex;
        gap: 20px;
    }
}
</style>
