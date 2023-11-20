<template>
    <Breadcrumb :items="breadcrumbs" class="mb-3" />
    <Container>
        <template v-slot:header-left>
            <Title>Escolha a área de para configurar o design</Title>
        </template>
        <template v-slot:content>
            <hr class="mt-0 mb-5" />
            <div class="d-flex justify-content-center gap-3 flex-wrap mb-5">
                <template v-for="button in pageButtons" :key="button.title">
                    <PageButton :title="button.title" :subtitle="button.subtitle" :img="button.img" :url="button.url" />
                </template>
            </div>
        </template>
    </Container>
</template>

<script>
import Loading from "../../../js/components/XgrowDesignSystem/Utils/Loading";
import Breadcrumb from "../../../js/components/XgrowDesignSystem/Breadcrumb/Breadcrumb";
import Container from "../../../js/components/XgrowDesignSystem/Cards/Container";
import Title from "../../../js/components/XgrowDesignSystem/Typography/Title";
import PageButton from "./components/PageButton";

export default {
    name: "DesignIndex",
    components: { PageButton, Title, Container, Breadcrumb, Loading },
    data() {
        return {
            /** Breadcrumbs */
            breadcrumbs: [
                { title: "Resumo", link: "/" },
                { title: "Área de aprendizagem", link: false },
                { title: "Design", link: false },
            ],

            pageButtons: [
                {
                    title: "Identidade Visual",
                    subtitle: "Personalize as cores e logo da sua página",
                    img: "/xgrow-vendor/assets/img/lacustom/visual-identity.svg",
                    url: 'design-visual-identity'
                },
                {
                    title: "Área de aprendizagem",
                    subtitle: "Personalize como os seus alunos verão seu conteúdo",
                    img: "/xgrow-vendor/assets/img/lacustom/main-page.svg",
                    url: 'design-start-page'
                },
                {
                    title: "Menus",
                    subtitle: "Insira e edite os itens no menu de sua página",
                    img: "/xgrow-vendor/assets/img/lacustom/menu.svg",
                    url: "design-config-menu"
                },
                {
                    title: "Primeiros passos",
                    subtitle: "Personalize a tela de boas vindas para seus alunos",
                    img: "/xgrow-vendor/assets/img/lacustom/onboarding.svg",
                    url: "design-onboarding"
                }
            ]
        }
    },
}
</script>
