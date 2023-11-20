<template>
    <div>
        <Loading :is-open="isLoading"></Loading>
        <Breadcrumb :items="breadcrumbs" class="mb-3"></Breadcrumb>

        <Container>
            <template v-slot:header-left>
                <Title>Primeiros passos
                    <PipeVertical /> Passo a passo
                </Title>
                <Subtitle>Crie um passo a passo para que o seu aluno deva seguir para
                    acessar a sua plataforma.</Subtitle>
            </template>
            <template v-slot:content>
                <Steps :values="form" />
            </template>
            <template v-slot:footer>
                <div class="panel__footer">
                    <router-link :to="{ name: 'design-index' }">
                        <DefaultButton text="Cancelar" outline></DefaultButton>
                    </router-link>
                    <DefaultButton text="Salvar" status="success" @click="save"></DefaultButton>
                </div>
            </template>
        </Container>
    </div>
</template>

<script>
import Loading from "../../../../js/components/XgrowDesignSystem/Utils/Loading";
import Breadcrumb from "../../../../js/components/XgrowDesignSystem/Breadcrumb/Breadcrumb";
import Container from "../../../../js/components/XgrowDesignSystem/Cards/Container";
import DefaultButton from "../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import XgrowTabNav from "../../../../js/components/XgrowDesignSystem/Tab/XgrowTabNav";
import XgrowTabContent from "../../../../js/components/XgrowDesignSystem/Tab/XgrowTabContent";
import XgrowTab from "../../../../js/components/XgrowDesignSystem/Tab/XgrowTab";
import Steps from "./components/Steps";
import axios from "axios";
import Title from "../../../../js/components/XgrowDesignSystem/Typography/Title";
import PipeVertical from "../../../../js/components/XgrowDesignSystem/Utils/PipeVertical";
import Subtitle from "../../../../js/components/XgrowDesignSystem/Typography/Subtitle";

export default {
    name: "Design-Onboarding",
    components: {
        Breadcrumb,
        Container,
        DefaultButton,
        Loading,
        PipeVertical,
        Steps,
        Subtitle,
        Title,
        XgrowTab,
        XgrowTabContent,
        XgrowTabNav,
    },
    data() {
        return {
            isLoading: false,

            /** Breadcrumbs */
            breadcrumbs: [
                { title: "Resumo", link: "/" },
                { title: "Área de aprendizagem", link: false },
                { title: "Design", link: '/learning-area/design', isVueRouter: true },
                { title: "Primeiros passos", link: false },
            ],

            /** Tab */
            tabs: {
                active: "tabStep",
                items: [{ title: "Passos", screen: "tabStep" }],
            },

            /** Form to send to LA */
            mode: "create",
            form: {
                enabled: false,
                steps: [],
            },
            errorMessages: "",
            hasError: false,
            axiosHeader: null,
            axiosUrl: null,
        };
    },
    methods: {
        setAxiosHeader: async function () {
            this.isLoading = true;

            let res = await axios.get("/learning-area/producer-connect");
            this.axiosHeader = {
                headers: { Authorization: "Bearer " + res.data.response.atx },
            };
            this.axiosUrl = res.data.response.url;

            this.isLoading = false;
        },
        /** Save onboarding */
        getOnboarding: async function () {
            try {
                this.isLoading = true;
                const res = await axios.get(
                    `${this.axiosUrl}/first-step?platformId=${platform_id}`,
                    this.axiosHeader
                );
                const data = res.data.data[0];
                if (data) {
                    this.mode = "update";
                    this.form.enabled = data.enabled;
                    this.form.steps = data.steps;
                }
                this.isLoading = false;
            } catch (e) {
                this.isLoading = false;
            }
        },
        /** Save onboarding */
        save: async function () {
            try {
                this.checkSteps();
                this.isLoading = true;
                this.mode === "create"
                    ? await axios.post(
                        `${this.axiosUrl}/first-step?platformId=${platform_id}`,
                        this.form,
                        this.axiosHeader
                    )
                    : await axios.put(
                        `${this.axiosUrl}/first-step?platformId=${platform_id}`,
                        this.form,
                        this.axiosHeader
                    );
                await this.getOnboarding();
                this.isLoading = false;
                successToast(
                    "Onboarding salvo com sucesso!",
                    "Aguarde 5min para ter efeito na Área de Aprendizagem."
                );
            } catch (e) {
                this.isLoading = false;
                errorToast(
                    "Algum erro aconteceu!",
                    e.response?.data.error.message ??
                    e.message ??
                    "Não foi possível salvar os dados, entre em contato com o suporte."
                );
            }
        },
        checkSteps: function () {
            if (this.form.enabled && this.form.steps.length === 0)
                throw new Error("Você deve ao menos incluir 1 passo.");
            this.form.steps.forEach((item) => {
                if (item.title === "")
                    throw new Error(
                        `O título do Passo ${item.order} não pode ficar em branco.`
                    );
                if (item.subtitle === "")
                    throw new Error(
                        `O subtítulo do Passo ${item.order} não pode ficar em branco.`
                    );
                if (item.contentType === "text" && item.content === "")
                    throw new Error(
                        `O conteúdo do Passo ${item.order} não pode ficar em branco.`
                    );
                if (item.contentType === "video" && item.contentUrl === "")
                    throw new Error(
                        `A URL do vídeo do Passo ${item.order} não pode ficar em branco.`
                    );
                if (
                    item.contentType === "textAndVideo" &&
                    (item.content === "" || item.contentUrl === "")
                )
                    throw new Error(
                        `A URL do vídeo e o conteúdo do Passo ${item.order} não podem ficar em branco.`
                    );
            });
        },
    },
    async created() {
        await this.setAxiosHeader();
        await this.getOnboarding();
    },
};
</script>

<style lang="scss" scoped>
.panel {
    &__footer {
        display: flex;
        justify-content: space-between;
        padding: 24px 0 0;
        margin-top: 24px;
        border-top: 1px solid #414655;
    }
}

p {
    color: #ffffff !important;
}
</style>
