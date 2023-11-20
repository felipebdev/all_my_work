<template>
    <div>
        <LoadingStore />
        <Breadcrumb :items="breadcrumbs" class="mb-3" />

        <XgrowTab id="tabCoupons">
            <template v-slot:header>
                <XgrowTabNav
                    :items="tabs.items"
                    id="tabNav"
                    :start-tab="tabs.active"
                    @change-page="changeTab"
                />
            </template>
            <template v-slot:body>
                <XgrowTabContent
                    id="coupons"
                    :selected="tabs.active === 'coupons'"
                >
                    <Coupons />
                </XgrowTabContent>
                <XgrowTabContent
                    id="mailing"
                    :selected="tabs.active === 'mailing'"
                >
                    <Mailing />
                </XgrowTabContent>
                <XgrowTabContent
                    id="importStatus"
                    :selected="tabs.active === 'importStatus'"
                >
                    <StatusImport />
                </XgrowTabContent>
            </template>
        </XgrowTab>

        <!-- <ConfirmModal :is-open="authorModal.active">
        </ConfirmModal> -->
    </div>
</template>

<script>
import Breadcrumb from "../../js/components/XgrowDesignSystem/Breadcrumb/Breadcrumb";
import ConfirmModal from "../../js/components/XgrowDesignSystem/Modals/ConfirmModal";
import XgrowTab from "../../js/components/XgrowDesignSystem/Tab/XgrowTab";
import XgrowTabContent from "../../js/components/XgrowDesignSystem/Tab/XgrowTabContent";
import XgrowTabNav from "../../js/components/XgrowDesignSystem/Tab/XgrowTabNav";
import LoadingStore from "../../js/components/XgrowDesignSystem/Utils/LoadingStore";

import Coupons from "./tabs/Coupons";
import Mailing from "./tabs/Mailing";
import StatusImport from "./tabs/StatusImport.vue"

export default {
    components: {
        Breadcrumb,
        ConfirmModal,
        XgrowTab,
        XgrowTabContent,
        XgrowTabNav,
        LoadingStore,
        Coupons,
        Mailing,
        StatusImport
    },
    data() {
        return {
            breadcrumbs: [
                { title: "Resumo", link: "/" },
                { title: "Produtos", link: false },
                { title: "Cupons", link: false },
                { title: "Editar", link: false },
            ],
            tabs: {
                active: "coupons",
                items: [
                    { title: "Cupons", screen: "coupons" },
                    { title: "Mailing", screen: "mailing" },
                    { title: "Status da importação", screen: "importStatus" },
                ],
            },
        };
    },
    methods: {
        changeTab(tab) {
            this.tabs.active = tab;
        },
    },
};
</script>
