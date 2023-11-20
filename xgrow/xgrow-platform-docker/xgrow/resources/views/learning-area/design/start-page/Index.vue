<template>
    <LoadingStore />
    <Breadcrumb :items="breadcrumbs" class="mb-3" />
    <Container>
        <template v-slot:header-left>
            <Title>Design da Área de aprendizagem</Title>
            <Subtitle>Arraste os itens abaixo para montar seu template.</Subtitle>
        </template>
        <template v-slot:content>
            <Row>
                <ElementsContainer class="mt-5" />
                <Col sm="12" md="12" lg="2" xl="2" class="arrow d-none d-lg-flex">
                <i class="fas fa-arrow-right"></i>
                </Col>
                <Col sm="12" md="12" lg="6" xl="6">
                <Col class="d-flex align-items-center justify-content-end">
                    <PreviewPlatform @device="(dev) => { device = dev }" :device="device" no-mobile />
                </Col>
                <WidgetPreview :block-drag="lock" />
                </Col>
            </Row>
        </template>
        <template v-slot:footer>
            <div class="panel__footer mt-4">
                <router-link :to="{ name: 'design-index' }">
                    <DefaultButton text="Cancelar" outline />
                </router-link>
                <DefaultButton text="Salvar e aplicar tema" status="success" @click="saveTheme" />
            </div>
        </template>
    </Container>
</template>

<script>
import LoadingStore from "../../../../js/components/XgrowDesignSystem/Utils/LoadingStore.vue";
import Breadcrumb from "../../../../js/components/XgrowDesignSystem/Breadcrumb/Breadcrumb.vue";
import Container from "../../../../js/components/XgrowDesignSystem/Cards/Container.vue";
import Title from "../../../../js/components/XgrowDesignSystem/Typography/Title.vue";
import Subtitle from "../../../../js/components/XgrowDesignSystem/Typography/Subtitle.vue";
import Row from "../../../../js/components/XgrowDesignSystem/Utils/Row.vue";
import ElementsContainer from "./components/ElementsContainer.vue";
import Col from "../../../../js/components/XgrowDesignSystem/Utils/Col.vue";
import DefaultButton from "../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton.vue";
import PreviewPlatform from "../../../../js/components/XgrowDesignSystem/Utils/PreviewPlatform.vue";
import WidgetPreview from "./components/WidgetPreview.vue";
import { mapState, mapStores } from "pinia";
import { useDesignStartPage } from "../../../../js/store/design-start-page";
import axios from "axios";

export default {
    name: "Index",
    components: {
        WidgetPreview,
        PreviewPlatform,
        DefaultButton, Col, ElementsContainer, Row, Subtitle, Title, Container, Breadcrumb, LoadingStore
    },
    data() {
        return {
            /** Breadcrumbs */
            breadcrumbs: [
                { title: "Resumo", link: "/" },
                { title: "Área de aprendizagem", link: false },
                { title: "Design", link: '/learning-area/design', isVueRouter: true },
                { title: "Área de aprendizagem", link: false },
            ],

            /** Misc Start Page */
            device: 'desktop',
            lock: false,
        }
    },
    computed: {
        ...mapStores(useDesignStartPage),
        ...mapState(useDesignStartPage, ['widgets', 'loadingStore'])
    },
    methods: {
        /** Save Theme */
        saveTheme: async function () {
            this.loadingStore.setLoading(true);
            try {
                this.changeOrder();
                const { fxUrl, fxHeader } = $cookies.get('fxToken');
                await axios.put(`${fxUrl}/producer/mainpage/pagewidgets`, { data: this.widgets }, fxHeader);
                successToast("Dados salvos com sucesso!", `O tema foi cadastrado/atualizado com sucesso!`);
            } catch (e) {
                errorToast("Algum erro aconteceu!", e.response?.data.error.message ?? "Não foi possível salvar os dados da Área de aprendizagem, entre em contato com o suporte.");
            }
            this.loadingStore.setLoading();
        },
        /** Change Order and delete isNew and _id keys */
        changeOrder: function () {
            let position = 0;
            for (const item of this.widgets) {
                if (item.isNew) {
                    delete item._id
                    delete item.isNew
                }
                item.position = position;
                position++
            }
        },
    }
}
</script>

<style lang="scss" scoped>
.arrow {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    color: #616981;
}

.panel__footer {
    border-top: 1px solid #393D49;
    margin-top: 1rem;
    padding-top: 1rem;
    display: flex;
    gap: 1rem;
    justify-content: space-between;
    align-items: center;

    button {
        width: 200px;
    }
}

.lock-button {
    font-size: 1.5rem;
    color: #818181;
    cursor: pointer;
}
</style>
