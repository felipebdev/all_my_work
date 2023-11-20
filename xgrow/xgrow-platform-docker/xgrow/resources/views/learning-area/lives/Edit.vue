<template>
    <Loading :is-open="isLoading"/>
    <Breadcrumb :items="breadcrumbs" class="mb-3"/>

    <Container no-header>
        <template v-slot:content>
            <XgrowTab id="tabInnerLive">
                <template v-slot:header>
                    <XgrowTabNav
                        :items="tabs.items" id="tabInnerNav" start-tab="tabLiveData"
                        @change-page="(val) => { tabs.active = val }"/>
                </template>
                <template v-slot:body>
                    <XgrowTabContent id="tabLiveData" :selected="tabs.active === 'tabLiveData'">
                        <LiveData :values="live" :timezone="dateTimeZoneOptions" :is-edit="true" />
                    </XgrowTabContent>
                    <XgrowTabContent id="tabLiveConfig" :selected="tabs.active === 'tabLiveConfig'">
                        <LiveConfig :values="live" />
                    </XgrowTabContent>
                </template>
            </XgrowTab>
        </template>
        <template v-slot:footer>
            <div class="container-footer__footer d-flex justify-content-between pt-3 mt-4 flex-wrap">
                <router-link :to="{name: 'lives-index'}">
                    <DefaultButton text="Cancelar" outline/>
                </router-link>
                <DefaultButton text="Salvar" status="success" @click="save"/>
            </div>
        </template>
    </Container>
</template>

<script>

import Breadcrumb from "../../../js/components/XgrowDesignSystem/Breadcrumb/Breadcrumb";
import LiveData from "./tabs/LiveData";
import Container from "../../../js/components/XgrowDesignSystem/Cards/Container";
import DefaultButton from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import XgrowTabNav from "../../../js/components/XgrowDesignSystem/Tab/XgrowTabNav";
import XgrowTabContent from "../../../js/components/XgrowDesignSystem/Tab/XgrowTabContent";
import LiveConfig from "./tabs/LiveConfig";
import XgrowTab from "../../../js/components/XgrowDesignSystem/Tab/XgrowTab";
import momentTZ from "moment-timezone";
import axios from "axios";
import Loading from "../../../js/components/XgrowDesignSystem/Utils/Loading";
import moment from "moment";

export default {
    name: "EditLive",
    components: {Loading, XgrowTab, LiveConfig, XgrowTabContent, XgrowTabNav, DefaultButton, Container, LiveData, Breadcrumb },
    data() {
        return {
            isLoading: false,

            /** Tab */
            tabs: {
                active: 'tabLiveData',
                items: [
                    {title: "Dados da live", screen: "tabLiveData"},
                    {title: "Configurações", screen: "tabLiveConfig"},
                ],
            },
            breadcrumbs: [
                {title: "Resumo", link: "/"},
                {title: "Área de aprendizagem", link: "/learning-area", isVueRouter: true},
                {title: "Lives", link: "/learning-area/lives", isVueRouter: true},
                {title: "Editar live", link: false},
            ],
            /** Live data */
            dateTimeZoneOptions: [],
            live: {},
            /** Axios config */
            axiosHeader: null,
            axiosUrl: null
        }
    },
    methods: {
        setAxiosHeader: async function () {
            this.isLoading = true;

            let res = await axios.get('/learning-area/producer-connect');
            this.axiosHeader = {headers: {Authorization: 'Bearer ' + res.data.response.atx}};
            this.axiosUrl = res.data.response.url;

            this.isLoading = false;
        },
        getLive: async function() {
            this.isLoading = true;
            const id = this.$route.params.id;

            try {
                const res = await axios.get(`${this.axiosUrl}/producer/lives/${id}`, this.axiosHeader);
                const { data } = res.data;

                this.live = {
                    title: data.title,
                    description: data.description,
                    date: this.decodeDate(data.date),
                    finishDate: this.decodeDate(data.finishDate),
                    dateTimeZone: data.dateTimeZone,
                    start: this.decodeHourAndMinutes(data.date),
                    end: this.decodeHourAndMinutes(data.finishDate),
                    author: data.author,
                    isEnabled: data.isEnabled,
                    enableAutoScroll: data.enableAutoScroll,
                    hasComments: data.hasComments,
                    hasQuestions: data.hasQuestions,
                    isVimeoChat: data.isVimeoChat,
                    embed: '',
                    link: data.link,
                    thumbnail: data.thumbnail
                }

            } catch (e) {
                errorToast("Algum erro aconteceu!", e.response?.data.error.message ?? e.message ?? "Não foi possível obter os dados da live, entre em contato com o suporte.");
            }
            this.isLoading = false;
        },
        save: async function () {
            this.isLoading = true;
            const id = this.$route.params.id;

            try {
                this.validation();

                const params = {
                    ...this.live,
                    date: this.formatDateToSave(this.live.date, this.live.start),
                    finishDate: this.formatDateToSave(this.live.finishDate === null ? this.live.date : this.live.finishDate, this.live.end)
                }

                await axios.put(`${this.axiosUrl}/producer/lives/${id}`, params, this.axiosHeader);
                this.isLoading = false;
                this.$router.push({name: 'lives-index'});
                successToast("Live editada com sucesso!", "Aguarde 5min para ter efeito na Área de Aprendizagem.");
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
        decodeDate: function(date) {
            return moment(date).format('YYYY-MM-DD');
        },
        decodeHourAndMinutes: function(date) {
            return moment(date).format('HH:mm');
        },
        formatDateToSave: function(date, hourAndMinutes) {
            return new Date(Date.parse(`${date} ${hourAndMinutes} GMT-0300`));
        }
    },
   async mounted() {
        await this.setAxiosHeader();
        await this.getLive();
        this.dateTimeZoneOptions = momentTZ.tz.names();
    }
}
</script>

<style lang="scss" scoped>
</style>
