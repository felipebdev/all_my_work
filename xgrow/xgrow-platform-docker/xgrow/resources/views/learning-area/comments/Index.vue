<template>
    <div>
        <LoadingStore />
        <Breadcrumb :items="breadcrumbs" class="mb-3" />

        <XgrowTab id="tabComments">
            <template v-slot:header>
                <XgrowTabNav :items="tabs.items" id="tabNav" start-tab="tabPublished"
                    @change-page="(val) => { tabs.active = val }" />
            </template>
            <template v-slot:body>
                <XgrowTabContent id="tabBodyPublished" :selected="tabs.active === 'tabPublished'">
                    <CommentsPublished :refreshComments="tabs.active" />
                </XgrowTabContent>
                <XgrowTabContent id="tabBodyRetained" :selected="tabs.active === 'tabRetained'">
                    <CommentsRetained :refreshComments="tabs.active" />
                </XgrowTabContent>
            </template>
        </XgrowTab>

    </div>
</template>

<script>
import Breadcrumb from "../../../js/components/XgrowDesignSystem/Breadcrumb/Breadcrumb.vue";
import LoadingStore from "../../../js/components/XgrowDesignSystem/Utils/LoadingStore.vue";
import XgrowTab from "../../../js/components/XgrowDesignSystem/Tab/XgrowTab.vue";
import XgrowTabContent from "../../../js/components/XgrowDesignSystem/Tab/XgrowTabContent.vue";
import XgrowTabNav from "../../../js/components/XgrowDesignSystem/Tab/XgrowTabNav.vue";
import CommentsPublished from "./tabs/CommentsPublished.vue";
import CommentsRetained from "./tabs/CommentsRetained.vue";

export default {
    name: "Index",
    components: { Breadcrumb, LoadingStore, XgrowTab, XgrowTabContent, XgrowTabNav, CommentsPublished, CommentsRetained },
    data() {
        return {
            /** Breadcrumbs */
            breadcrumbs: [
                { title: "Resumo", link: "/" },
                { title: "Área de aprendizagem", link: false },
                { title: "Comentários", link: false },
            ],

            /** Tabs */
            tabs: {
                active: 'tabPublished',
                items: [
                    { title: "Comentários publicados", screen: "tabPublished" },
                    { title: "Comentários retidos para análise", screen: "tabRetained" },
                ],
            },
        }
    },
};
</script>

<style lang="scss" scoped>
:deep(.col-limit) {
    min-width: 260px;
    max-width: 300px;
    word-wrap: break-word;
}
</style>