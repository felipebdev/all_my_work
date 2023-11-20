<template>
    <LoadingStore />
    <Breadcrumb :items="breadcrumbs" class="mb-3" />

    <XgrowTab id="tabCourse">
        <template v-slot:header>
            <XgrowTabNav :items="tabs.items" id="tabNav" start-tab="tabContent"
                @change-page="(val) => { tabs.active = val }" />
        </template>
        <template v-slot:body>
            <XgrowTabContent id="tabBodyContent" :selected="tabs.active === 'tabContent'">
                <CourseContent :course="course" @page-name="(page) => { tabs.active = page }" />
            </XgrowTabContent>
            <XgrowTabContent id="tabBodyPath" :selected="tabs.active === 'tabPath'">
                <ReleaseContent :course="course" />
            </XgrowTabContent>
            <XgrowTabContent id="tabBodyConfig" :selected="tabs.active === 'tabConfig'">
                <ConfigContent :course="course" @reload-course="getCourse" />
            </XgrowTabContent>
        </template>
    </XgrowTab>
</template>

<script>
import Breadcrumb from "../../../js/components/XgrowDesignSystem/Breadcrumb/Breadcrumb.vue";
import Container from "../../../js/components/XgrowDesignSystem/Cards/Container.vue";
import DefaultButton from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton.vue";
import Title from "../../../js/components/XgrowDesignSystem/Typography/Title.vue";
import Row from "../../../js/components/XgrowDesignSystem/Utils/Row.vue";
import Col from "../../../js/components/XgrowDesignSystem/Utils/Col.vue";
import Input from "../../../js/components/XgrowDesignSystem/Input.vue";
import TextInput from "../../../js/components/XgrowDesignSystem/Form/TextInput.vue";
import ImageUpload from "../../../js/components/XgrowDesignSystem/Form/ImageUpload.vue";
import XgrowTab from "../../../js/components/XgrowDesignSystem/Tab/XgrowTab.vue";
import XgrowTabContent from "../../../js/components/XgrowDesignSystem/Tab/XgrowTabContent.vue";
import XgrowTabNav from "../../../js/components/XgrowDesignSystem/Tab/XgrowTabNav.vue";
import CourseContent from "./tabs/CourseContent.vue";
import ReleaseContent from "./tabs/ReleaseContent.vue";
import ConfigContent from "./tabs/ConfigContent.vue";
import { GET_COURSE_BY_PARAMS_QUERY_AXIOS } from "../../../js/graphql/queries/courses";
import { useLoadingStore } from "../../../js/store/components/loading";
import { mapActions, mapStores } from "pinia";
import LoadingStore from "../../../js/components/XgrowDesignSystem/Utils/LoadingStore.vue";
import { axiosGraphqlClient } from "../../../js/config/axiosGraphql";

export default {
    name: "Edit",
    components: {
        ConfigContent,
        ReleaseContent,
        CourseContent,
        XgrowTabNav,
        XgrowTabContent,
        XgrowTab, ImageUpload, TextInput, Input, Col, Row, Title, DefaultButton, Container, Breadcrumb, LoadingStore
    },
    data() {
        return {
            /** Breadcrumbs */
            breadcrumbs: [
                { title: "Resumo", link: "/", isVueRouter: false },
                { title: "Área de aprendizagem", link: '/learning-area', isVueRouter: true },
                { title: "Cursos", link: '/learning-area/courses', isVueRouter: true },
            ],

            /** Tab */
            tabs: {
                // active: 'tabContent',
                active: 'tabContent',
                items: [
                    { title: "Conteúdo", screen: "tabContent" },
                    { title: "Liberação", screen: "tabPath" },
                    { title: "Configurações", screen: "tabConfig" },
                ],
            },

            course: {
                name: ''
            },
        }
    },
    computed: {
        ...mapStores(useLoadingStore),
    },
    methods: {
        ...mapActions(useLoadingStore, ['setLoading']),
        /** Get Course */
        getCourse: async function () {
            try {
                this.loadingStore.setLoading(true);
                const query = {
                    "query": GET_COURSE_BY_PARAMS_QUERY_AXIOS,
                    "variables": { id: this.$route.params.id, page: 1, limit: 1000 }
                };
                const res = await axiosGraphqlClient.post(contentAPI, query);
                const { data } = res.data.data.courses;
                Object.assign(this.course, data[0])
                if (this.breadcrumbs[this.breadcrumbs.length - 1].title !== this.course.name)
                    this.breadcrumbs.push({ title: this.course.name, link: '', isVueRouter: false })
                this.loadingStore.setLoading();
            } catch (e) {
                this.loadingStore.setLoading();
                console.log(e);
            }
        },
    },
    async created() {
        await this.getCourse();
    }
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
