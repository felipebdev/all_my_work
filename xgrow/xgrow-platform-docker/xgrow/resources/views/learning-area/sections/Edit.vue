<template>
    <div>
        <Loading :is-open="isLoading" />
        <Breadcrumb :items="breadcrumbs" class="mb-3" />

        <XgrowTab id="tabSection">
            <template v-slot:header>
                <XgrowTabNav :items="tabs.items" id="tabNav" start-tab="tabContent"
                    @change-page="(val) => { tabs.active = val }" />
            </template>
            <template v-slot:body>
                <XgrowTabContent id="tabBodyContent" :selected="tabs.active === 'tabContent'">
                    <SectionContent />
                </XgrowTabContent>
                <XgrowTabContent id="tabBodyDelivery" :selected="tabs.active === 'tabDelivery'">
                    <SectionDeliveries />
                </XgrowTabContent>
                <XgrowTabContent id="tabBodyConfig" :selected="tabs.active === 'tabConfig'">
                    <SectionConfiguration />
                </XgrowTabContent>
            </template>
        </XgrowTab>
    </div>
</template>

<script>
import Loading from "../../../js/components/XgrowDesignSystem/Utils/Loading";
import Breadcrumb from "../../../js/components/XgrowDesignSystem/Breadcrumb/Breadcrumb";
import Container from "../../../js/components/XgrowDesignSystem/Cards/Container";
import DefaultButton from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import Title from "../../../js/components/XgrowDesignSystem/Typography/Title";
import Row from "../../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../../js/components/XgrowDesignSystem/Utils/Col";
import Input from "../../../js/components/XgrowDesignSystem/Input";
import TextInput from "../../../js/components/XgrowDesignSystem/Form/TextInput";
import ImageUpload from "../../../js/components/XgrowDesignSystem/Form/ImageUpload";
import XgrowTab from "../../../js/components/XgrowDesignSystem/Tab/XgrowTab";
import XgrowTabContent from "../../../js/components/XgrowDesignSystem/Tab/XgrowTabContent";
import XgrowTabNav from "../../../js/components/XgrowDesignSystem/Tab/XgrowTabNav";
import SectionContent from "./tabs/SectionContent.vue";
import SectionDeliveries from "./tabs/SectionDeliveries";
import SectionConfiguration from "./tabs/SectionConfiguration.vue";

import { mapActions, mapStores, mapState } from "pinia";
import { useSectionsStore } from '../../../js/store/sections';
import { axiosGraphqlClient } from '../../../js/config/axiosGraphql';

export default {
    name: "Edit",
    components: {
        SectionConfiguration,
        SectionDeliveries,
        SectionContent,
        XgrowTabNav,
        XgrowTabContent,
        XgrowTab, ImageUpload, TextInput, Input, Col, Row, Title, DefaultButton, Container, Breadcrumb, Loading
    },
    data() {
        return {
            isLoading: false,

            /** Breadcrumbs */
            breadcrumbs: [
                { title: "Resumo", link: "/", isVueRouter: false },
                { title: "Área de aprendizagem", link: '/learning-area', isVueRouter: true },
                { title: "Seções", link: '/learning-area/sections', isVueRouter: true },
            ],

            /** Tab */
            tabs: {
                // active: 'tabContent',
                active: 'tabContent',
                items: [
                    { title: "Conteúdo", screen: "tabContent" },
                    // { title: "Liberação", screen: "tabDelivery" },
                    { title: "Configurações", screen: "tabConfig" },
                ],
            },

            /** Pagination */
            pagination: {
                totalPages: 1,
                totalResults: 0,
                currentPage: 1,
                limit: 25,
            },
        }
    },
    watch: {
        section() {
            this.breadcrumbs.push({ title: this.section.title, link: false, isVueRouter: false })
        }
    },
    methods: {
        openModal(type, id = null) {
            this[type].isOpen = true;

            if (id) this[type].id = id;
        },
        closeModal(type) {
            this[type].isOpen = false;

            if (this[type].id) this[type].id = "";
            if (this[type].items) this[type].items = [];
        }
    },
    computed: {
        ...mapStores(useSectionsStore),
        ...mapState(useSectionsStore, ['section', 'loadingStore'])
    },
}
</script>

<style lang="scss" scoped>
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
</style>
