<template>
    <Loading :is-open="isLoading" />
    <Breadcrumb :items="breadcrumbs" class="mb-3" />

    <Container no-header>
        <template v-slot:content>
            <XgrowTab id="tabInnerLive">
                <template v-slot:header>
                    <XgrowTabNav :items="tabs.items" id="tabInnerNav" start-tab="tabLiveData"
                        @change-page="(val) => { tabs.active = val }" />
                </template>
                <template v-slot:body>
                    <XgrowTabContent id="tabLiveData" :selected="tabs.active === 'tabLiveData'">
                        <LiveData :values="live" :timezone="dateTimeZoneOptions" />
                    </XgrowTabContent>
                    <XgrowTabContent id="tabLiveConfig" :selected="tabs.active === 'tabLiveConfig'">
                        <LiveConfig :values="live" />
                    </XgrowTabContent>
                </template>
            </XgrowTab>
        </template>
        <template v-slot:footer>
            <div class="container-footer__footer d-flex justify-content-between pt-3 mt-4 flex-wrap">
                <DefaultButton text="Cancelar" outline @click="backPage" />
                <DefaultButton text="Salvar" status="success" @click="save" />
            </div>
        </template>
    </Container>
</template>

<script>

import Breadcrumb from "../../../js/components/XgrowDesignSystem/Breadcrumb/Breadcrumb";
import LiveData from "./tabs/LiveData.vue";
import Container from "../../../js/components/XgrowDesignSystem/Cards/Container";
import DefaultButton from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import XgrowTabNav from "../../../js/components/XgrowDesignSystem/Tab/XgrowTabNav";
import XgrowTabContent from "../../../js/components/XgrowDesignSystem/Tab/XgrowTabContent.vue";
import LiveConfig from "./tabs/LiveConfig.vue";
import XgrowTab from "../../../js/components/XgrowDesignSystem/Tab/XgrowTab";
import momentTZ from "moment-timezone";
import axios from "axios";
import Loading from "../../../js/components/XgrowDesignSystem/Utils/Loading";
import { RouterLink } from 'vue-router'

export default {
    name: "CreateLive",
    components: { Loading, XgrowTab, LiveConfig, XgrowTabContent, XgrowTabNav, DefaultButton, Container, LiveData, RouterLink, Breadcrumb },
    data() {
        return {
            isLoading: false,

            /** Tab */
            tabs: {
                active: 'tabLiveData',
                items: [
                    { title: "Dados da live", screen: "tabLiveData" },
                    { title: "Configurações", screen: "tabLiveConfig" },
                ],
            },
            breadcrumbs: [
                { title: "Resumo", link: "/" },
                { title: "Área de aprendizagem", link: "/learning-area", isVueRouter: true },
                { title: "Lives", link: "/learning-area/lives", isVueRouter: true },
                { title: "Nova live", link: false },
            ],
            /** Live data */
            dateTimeZoneOptions: [],
            live: {
                title: '',
                description: '',
                date: null,
                finishDate: null,
                dateTimeZone: "America/Sao_Paulo",
                author: '',
                isEnabled: false,
                enableAutoScroll: false,
                hasComments: false,
                hasQuestions: false,
                isVimeoChat: false,
                embed: '',
                link: '',
                thumbnail: 'https://las.xgrow.com/background-default.png'
            },
            /** Axios config */
            axiosHeader: null,
            axiosUrl: null
        }
    },
    methods: {
        setAxiosHeader: async function () {
            let res = await axios.get('/learning-area/producer-connect');
            this.axiosHeader = { headers: { Authorization: 'Bearer ' + res.data.response.atx } };
            this.axiosUrl = res.data.response.url;
        },
        save: async function () {
            const params = {
                ...this.live,
                date: this.formatDateToSave(this.live.date, this.live.start),
                finishDate: this.formatDateToSave(this.live.finishDate === null ? this.live.date : this.live.finishDate, this.live.end)
            }

            try {
                this.validation();
                this.isLoading = true;
                await axios.post(`${this.axiosUrl}/producer/lives`, params, this.axiosHeader);
                this.isLoading = false;
                this.clearForm();
                this.$router.push({ name: 'lives-index' });
                successToast("Live salva com sucesso!", "Aguarde 5min para ter efeito na Área de Aprendizagem.");
            } catch (e) {
                this.isLoading = false;
                errorToast("Algum erro aconteceu!", e.response?.data.error.message ?? e.message ?? "Não foi possível salvar os dados, entre em contato com o suporte.");
            }
        },
        validation: function () {
            if (this.live.title === '')
                throw new Error(`O título não pode ficar em branco.`);
            if (this.live.author === '')
                throw new Error(`O subtítulo não pode ficar em branco.`);
            if (this.live.link === '')
                throw new Error(`O link da live não pode ficar em branco.`);
            if (this.live.date === null)
                throw new Error(`A data é obrigatória.`);
            if (this.live.start === null || this.live.end === null)
                throw new Error(`O horário não pode ficar em branco.`);
            if (this.live.thumbnail === '')
                throw new Error(`A imagem da live é obrigatória.`);
        },
        clearForm: function () {
            this.live = {
                title: '',
                description: '',
                date: null,
                finishDate: null,
                dateTimeZone: "America/Sao_Paulo",
                author: '',
                isEnabled: false,
                enableAutoScroll: false,
                hasComments: false,
                hasQuestions: false,
                isVimeoChat: false,
                embed: '',
                link: '',
                thumbnail: ''
            };
            this.tabs.active = 'tabLiveData';
        },
        backPage: function () {
            this.clearForm();
            this.$router.push({ name: 'lives-index' });
        },
        formatDateToSave: function (date, hourAndMinutes) {
            return new Date(Date.parse(`${date} ${hourAndMinutes} GMT-0300`));
        }
    },
    async created() {
        this.dateTimeZoneOptions = momentTZ.tz.names();
        await this.setAxiosHeader();
    }
}
</script>

<style lang="scss" scoped></style>
