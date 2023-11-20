import { defineStore } from 'pinia'
import { useLoadingStore } from "./components/loading";
import { UPDATE_SECTIONS_AXIOS } from "../graphql/mutations/sections";
import { axiosGraphqlClient } from "../../js/config/axiosGraphql"

export const useSectionsStore = defineStore('sections', {
    state: () => ({
        loadingStore: useLoadingStore(),
        section: {
            title: "",
            description: "",
            section_items: []
        },
    }),
    getters: {},
    actions: {
        //** updateSection */
        async updateSection() {
            try {
                this.loadingStore.setLoading(true);

                const query = {
                    "query": UPDATE_SECTIONS_AXIOS,
                    "variables": {
                        id: this.section.id,
                        title: this.section.title,
                        thumb_vertical: this.section.thumb_vertical,
                        thumb_horizontal: this.section.thumb_horizontal,
                        published: this.section.published,
                        description: this.section.description,
                        section_items: this.section.section_items.map(({ type, position, item_id }) => ({ type, position, item_id })),
                    }
                };

                await axiosGraphqlClient.post(contentAPI, query);

                successToast("Ação realizada", `Os itens da seção foram atualizados com sucesso!`);
            } catch (e) {
                console.log(e)
                this.loadingStore.setLoading();
                errorToast("Ocorreu um erro", `Ocorreu um problema ao atualizar os itens da Seção. Tente novamente mais tarde.`);
            }

            this.loadingStore.setLoading();
        },
    },
})
