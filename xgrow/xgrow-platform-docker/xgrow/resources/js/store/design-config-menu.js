import { defineStore } from "pinia";
import { useLoadingStore } from "./components/loading";
import { useAxiosStore } from "./components/axios";
import axios from "axios";
import { ALL_COURSES_QUERY_AXIOS } from "../graphql/queries/courses";
import { GET_ALL_CONTENTS_QUERY_AXIOS } from "../graphql/queries/contents";
import { axiosGraphqlClient } from "../config/axiosGraphql";

export const useDesignConfigMenu = defineStore("designConfigMenu", {
    state: () => ({
        loadingStore: useLoadingStore(),
        axiosStore: useAxiosStore(),
        listLibOptions: [],
        listIcons: [],
        enableLives: false,
        enableForum: false,
        contentTypeOptions: [
            { value: "course", name: "Curso" },
            { value: "content", name: "Conteúdo" },
            { value: "link", name: "Link Externo" },
        ],
        courseOptions: [],
        contentOptions: [],
        newMenuItem: {},
    }),
    actions: {
        getIconByName(iconName, iconGroup) {
            if (!this.listIcons.length) return false;

            const group = this.listIcons.filter(({ name }) => name == iconGroup)[0];
            return group.icons.filter(({ name }) => name == iconName)[0];
        },
        async getIconList() {
            try {
                this.loadingStore.setLoading(true);
                const res = await axios.get(
                    `${this.axiosStore.axiosUrl}/producer/mainpage/menu/icons/`,
                    this.axiosStore.axiosHeader
                );

                this.listLibOptions = res.data.data.map(({ name }) => (
                    { value: name, name: this.capitalizeAndJoin(name) }
                ));

                this.listIcons = res.data.data;

                this.loadingStore.setLoading();
            } catch (e) {
                errorToast(
                    "Algum erro aconteceu!",
                    e.response?.data.error.message ??
                    e.message ??
                    "Não foi possível receber os ícones."
                );
                this.loadingStore.setLoading();
            }
        },
        async getAllProducerContent() {
            this.loadingStore.setLoading(true);
            /** Get all Courses */

            const courseQuery = {
                query: ALL_COURSES_QUERY_AXIOS,
                variables: { page: 1, limit: 10000 },
            }

            const course = await axiosGraphqlClient.post(contentAPI, courseQuery);
            this.courseOptions = course.data.data.courses.data.map((content) => {
                return { value: content.id, name: content.name };
            });

            const contentQuery = {
                query: GET_ALL_CONTENTS_QUERY_AXIOS,
                variables: { page: 1, limit: 10000 },
            }

            /** Get all Contents */
            const contents = await axiosGraphqlClient.post(contentAPI, contentQuery);
            this.contentOptions = contents.data.data.contents.data.map((content) => {
                return { value: content.id, name: content.title };
            });

            this.contentOptions.sort((a, b) => {
                if (a.name < b.name) return -1;
                if (a.name > b.name) return 1;
                return 0;
            });

            this.loadingStore.setLoading();
        },
        capitalizeAndJoin(str) {
            return str.split('-').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
        }
    },
});
