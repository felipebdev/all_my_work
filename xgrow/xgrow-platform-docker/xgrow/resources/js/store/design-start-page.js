import { defineStore } from 'pinia'
import { useLoadingStore } from "./components/loading";
import { useUploadImageS3Store } from "./components/uploadImageS3";
import { useAxiosStore } from "./components/axios";
import axios from "axios";
import { ALL_COURSES_QUERY_AXIOS } from "../graphql/queries/courses";
import { GET_ALL_CONTENTS_QUERY_AXIOS } from "../graphql/queries/contents";
import { GET_ALL_MODULES_DELIVERY_QUERY_AXIOS } from "../graphql/queries/modules";
import { ALL_SECTIONS_QUERY_AXIOS } from "../graphql/queries/sections";
import { axiosGraphqlClient } from '../config/axiosGraphql';

export const useDesignStartPage = defineStore('designStartPage', {
    state: () => ({
        loadingStore: useLoadingStore(),
        uploadImageS3Store: useUploadImageS3Store(),
        bannerObj: {
            is_video: false,
            position: 0,
            type: "image", // image, video
            urlBanner: "https://site-xgrow.vercel.app/assets/img/banner_1_.jpg",
            active: true,
            hasMessage: false,
            title: null,
            description: null,
            urlContent: null,
            isExternalLink: false,
            contentType: "course", // course, live, content, module, ?class or ?section
            contentId: 0
        },

        /** Select with options */
        courseOptions: [],
        liveOptions: [],
        contentOptions: [],
        moduleOptions: [],
        sectionOptions: [],

        /** Banner Data */
        banners: [],
        removedBanners: [],

        /** Widgets Data */
        widgets: [],
    }),
    actions: {
        /** Get banner */
        getBanner: async function () {
            this.loadingStore.setLoading(true);
            try {
                const { fxUrl, fxHeader } = $cookies.get('fxToken');
                const res = await axios.get(`${fxUrl}/producer/mainpage/banners`, fxHeader);
                const data = res.data.data.map(({ is_video, ...item }) => ({ ...item, is_video: item.type === 'video' }));
                (data.length > 0) ? this.banners = data : this.banners.push(this.bannerObj);
            } catch (e) {
                errorToast("Algum erro aconteceu!", e.response?.data.error.message ?? e.message ?? "Não foi possível obter os dados da página inicial, entre em contato com o suporte.");
            }
            this.loadingStore.setLoading();
        },
        getWidgets: async function () {
            this.loadingStore.setLoading(true);
            try {
                const { fxUrl, fxHeader } = $cookies.get('fxToken');
                const res = await axios.get(`${fxUrl}/producer/mainpage/pagewidgets`, fxHeader);
                this.widgets = res.data.data.sort((a, b) => a.position - b.position);
            } catch (e) {
                errorToast("Algum erro aconteceu!", e.response?.data.error.message ?? e.message ?? "Não foi possível obter os dados da página inicial, entre em contato com o suporte.");
            }
            this.loadingStore.setLoading();
        },
        deleteWidget: async function (val) {
            this.loadingStore.setLoading(true);
            try {
                const { fxUrl, fxHeader } = $cookies.get('fxToken');
                if (val.isNew) {
                    this.widgets = this.widgets.filter(widget => widget._id !== val._id)
                } else {
                    await axios.delete(`${fxUrl}/producer/mainpage/pagewidgets/${val._id}`, fxHeader);
                    await this.getWidgets();
                }
                successToast("Ação realizada com sucesso!", `O Widget foi excluído com sucesso!`);
            } catch (e) {
                errorToast("Algum erro aconteceu!", e.response?.data.error.message ?? "Não foi possível remover os widget, tente mais tarde e se o problema persistir entre em contato com o suporte.");
            }
            this.loadingStore.setLoading();
        },
        /*******************
         * GENERAL FUNCTIONS
         *******************/
        /** Upload banner image */
        uploadImage: async function (obj) {
            const img = await this.uploadImageS3Store.uploadToS3(obj, "/learning-area/upload-image")
            return img;
        },
        /** Charge all contents, courses, modules and lives */
        getAllProducerContent: async function () {
            const { fxUrl, fxHeader } = $cookies.get('fxToken');

            /** Get all Courses */
            const courseQuery = {
                "query": ALL_COURSES_QUERY_AXIOS,
                "variables": { page: 1, limit: 100 }
            };
            const course = await axiosGraphqlClient.post(contentAPI, courseQuery);
            this.courseOptions = course.data.data.courses.data.map(content => {
                return {
                    value: content.id,
                    name: content.name,
                    verticalImage: content.vertical_image,
                    horizontalImage: content.horizontal_image,
                    updatedAt: content.updated_at,
                    author: content.author.name_author,
                    isPublished: content.active,
                    createdAt: content.created_at
                }
            });

            /** Get all Lives */
            const lives = await axios.get(`${fxUrl}/producer/lives`, fxHeader);
            this.liveOptions = lives.data.data.map(content => {
                return { value: content._id, name: content.title, date: content.date }
            }).sort((a, b) => new Date(b.date) - new Date(a.date));

            /** Get all Contents */
            const contentQuery = {
                "query": GET_ALL_CONTENTS_QUERY_AXIOS,
                "variables": { page: 1, limit: 100 }
            };

            const contents = await axiosGraphqlClient.post(contentAPI, contentQuery);
            this.contentOptions = contents.data.data.contents.data.map(content => {
                return {
                    value: content.id,
                    name: content.title,
                    verticalImage: content.vertical_image,
                    horizontalImage: content.horizontal_image,
                    updatedAt: content.updated_at,
                    author: content.author.name_author,
                    isPublished: content.is_published,
                    createdAt: content.created_at
                }
            }).sort((a, b) => new Date(b.updatedAt) - new Date(a.updatedAt));

            /** Get all Sections */
            const sectionQuery = {
                "query": ALL_SECTIONS_QUERY_AXIOS,
                "variables": {
                    page: 1,
                    limit: 100,
                    published: true,
                    platform_id: platform_id
                }
            };
            const sections = await axiosGraphqlClient.post(contentAPI, sectionQuery);
            this.sectionOptions = sections.data.data.sections.data.map(section => {
                return {
                    value: section.id,
                    name: section.title,
                    verticalImage: section.thumb_vertical,
                    horizontalImage: section.thumb_horizontal
                }
            });

            /** Get all Modules  */
            const moduleQuery = {
                "query": GET_ALL_MODULES_DELIVERY_QUERY_AXIOS
            };
            const modules = await axiosGraphqlClient.post(contentAPI, moduleQuery);
            this.moduleOptions = modules.data.data.modules.data.map(content => {
                return { value: content.id, name: content.name, updated_at: content.updated_at }
            }).sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at));
        },
        setAxios: async function () {
            const axiosStore = useAxiosStore();
            await axiosStore.setAxiosHeader();
            const token = {
                fxHeader: axiosStore.axiosHeader,
                fxUrl: axiosStore.axiosUrl,
            }
            await $cookies.set('fxToken', token)
        },
        /** Init store by step */
        initStore: async function () {
            this.loadingStore.setLoading(true);
            await this.setAxios();
            await this.getAllProducerContent();
            await this.getBanner();
            await this.getWidgets();
            this.loadingStore.setLoading();
        }
    },
})
