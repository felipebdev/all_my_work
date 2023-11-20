<template>
    <Loading :is-open="isLoading"/>
    <Breadcrumb :items="breadcrumbs" class="mb-3"/>

    <XgrowTab id="tabLives">
        <template v-slot:header>
            <XgrowTabNav
                :items="tabs.items" id="tabNav" start-tab="tabInProgress"
                @change-page="(val) => { tabs.active = val }"/>
        </template>
        <template v-slot:body>
            <XgrowTabContent id="tabInProgress" :selected="tabs.active === 'tabInProgress'">
                <InProgess :results="getOnlyOnLive" @delete="openLiveModal" @new-live="tabs.active = 'tabNewLive'"/>
            </XgrowTabContent>
            <XgrowTabContent id="tabNextLives" :selected="tabs.active === 'tabNextLives'">
                <NextLives :results="getFutureLives" @delete="openLiveModal" @new-live="tabs.active = 'tabNewLive'"/>
            </XgrowTabContent>
            <XgrowTabContent id="tabOldLives" :selected="tabs.active === 'tabOldLives'">
                <PastLives :results="getPastLives" @delete="openLiveModal" @new-live="tabs.active = 'tabNewLive'"/>
            </XgrowTabContent>
        </template>
    </XgrowTab>

    <ConfirmModal :is-open="liveModal.active">
        <i class="fa fa-question-circle fa-6x"></i>
        <div class="modal-body__content">
            <h1>Tem certeza que deseja excluir esta live?</h1>
            <p>Esta ação não poderá ser desfeita!</p>
        </div>
        <div class="modal-body__footer">
            <DefaultButton text="Cancelar" outline @click="liveModal.canceled.callback()"/>
            <DefaultButton text="Excluir mesmo assim" status="success" @click="liveModal.confirmed.callback()"/>
        </div>
    </ConfirmModal>
</template>

<script>
import Breadcrumb from "../../../js/components/XgrowDesignSystem/Breadcrumb/Breadcrumb";
import Loading from "../../../js/components/XgrowDesignSystem/Utils/Loading";
import Container from "../../../js/components/XgrowDesignSystem/Cards/Container";
import DefaultButton from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import Subtitle from "../../../js/components/XgrowDesignSystem/Typography/Subtitle";
import Title from "../../../js/components/XgrowDesignSystem/Typography/Title";
import XgrowTable from "../../../js/components/Datatables/Table";
import Pagination from "../../../js/components/Datatables/Pagination";
import NoResult from "../../../js/components/Datatables/NoResult";
import ButtonDetail from "../../../js/components/Datatables/ButtonDetail";
import SwitchButton from "../../../js/components/XgrowDesignSystem/SwitchButton";
import FilterButton from "../../../js/components/XgrowDesignSystem/Buttons/FilterButton";
import Input from "../../../js/components/XgrowDesignSystem/Form/Input";
import Modal from "../../../js/components/XgrowDesignSystem/Modals/Modal";
import Row from "../../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../../js/components/XgrowDesignSystem/Utils/Col";
import LiveCard from "./components/LiveCard";
import XgrowTabContent from "../../../js/components/XgrowDesignSystem/Tab/XgrowTabContent";
import XgrowTab from "../../../js/components/XgrowDesignSystem/Tab/XgrowTab";
import XgrowTabNav from "../../../js/components/XgrowDesignSystem/Tab/XgrowTabNav";
import InProgess from "./tabs/InProgess";
import NextLives from "./tabs/NextLives";
import PastLives from "./tabs/PastLives";
import ConfirmModal from "../../../js/components/XgrowDesignSystem/Modals/ConfirmModal";
import axios from "axios";

export default {
    name: "LivesIndex",
    components: {
        ConfirmModal,
        PastLives,
        NextLives,
        InProgess,
        XgrowTabNav,
        XgrowTab,
        XgrowTabContent,
        LiveCard,
        Col,
        Row,
        Modal,
        Input,
        FilterButton,
        SwitchButton,
        ButtonDetail,
        NoResult, Pagination, XgrowTable, Title, Subtitle, DefaultButton, Container, Loading, Breadcrumb
    },
    data() {
        return {
            isLoading: false,
            /** Breadcrumbs */
            breadcrumbs: [
                {title: "Resumo", link: "/"},
                {title: "Área de aprendizagem", link: "/learning-area", isVueRouter: true},
                {title: "Lives", link: false},
            ],

            /** Author */
            liveModal: {
                active: false,
                confirmed: { callback: null },
                canceled: { callback: this.closeLiveModal }
            },

            /** Tab */
            tabs: {
                active: 'tabInProgress',
                items: [
                    {title: "Lives em andamento", screen: "tabInProgress"},
                    {title: "Próximas lives", screen: "tabNextLives"},
                    {title: "Lives realizadas", screen: "tabOldLives"},
                ],
            },

            /** Datatables and Pagination */
            results: [],
            /** Axios config */
            axiosHeader: null,
            axiosUrl: null
        }
    },
    computed: {
        getOnlyOnLive: function () {
            return this.results.filter((item) => {
                const now = Date.now();
                if (now > new Date(item.date) && now < new Date(item.finishDate)) return item;
            });
        },
        getFutureLives: function () {
            return this.results.filter((item) => {
                const now = Date.now();
                const onLive = now > new Date(item.date) && now < new Date(item.finishDate);
                if (!onLive && now < new Date(item.finishDate)) return item;
            });
        },
        getPastLives: function () {
            return this.results.filter((item) => {
                const now = Date.now();
                if (now > new Date(item.finishDate)) return item;
            });
        },
    },
    methods: {
        setAxiosHeader: async function () {
            this.isLoading = true;

            let res = await axios.get('/learning-area/producer-connect');
            this.axiosHeader = {headers: {Authorization: 'Bearer ' + res.data.response.atx}};
            this.axiosUrl = res.data.response.url;

            this.isLoading = false;
        },
        getLives: async function () {
            try {
                this.isLoading = true;
                const res = await axios.get(`${this.axiosUrl}/producer/lives`, this.axiosHeader);
                this.results = res.data.data
                this.isLoading = false;
            } catch (e) {
                this.isLoading = false;
                errorToast("Algum erro aconteceu!", e.message ?? "Não foi possível salvar os dados, entre em contato com o suporte.");
            }
        },
        deleteLive: async function (id) {
            this.liveModal.active = false;
            this.isLoading = true;

            try {
                await axios.delete(`${this.axiosUrl}/producer/lives/${id}`, this.axiosHeader);
                successToast("Ação realizada!", "O usuário foi deletado com sucesso.");
                this.closeLiveModal();
                this.isLoading = false;
                await this.getLives();
            } catch (e) {
                this.isLoading = false;
                errorToast("Algum erro aconteceu!", e.message ?? "Não foi possível realizar essa ação, entre em contato com o suporte.");
            }
        },
        openLiveModal: function(id) {
            this.liveModal.active = true;

            this.liveModal.confirmed.callback = () => this.deleteLive(id);
        },
        closeLiveModal: function() {
            this.liveModal.active = false;
        }
    },
    async created() {
        await this.setAxiosHeader();
        await this.getLives();
    }
}
</script>
