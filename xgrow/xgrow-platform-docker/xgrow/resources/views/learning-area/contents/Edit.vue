<template>
    <div>
        <LoadingStore />
        <Breadcrumb :items="breadcrumbs" class="mb-3" />
        <Row>
            <Col>
            <Container>
                <template v-slot:header-left>
                    <Title>{{ !this.course.id ? 'Novo conteúdo' : 'Nova aula' }}</Title>
                </template>
                <template v-slot:header-right>
                </template>
                <template v-slot:content>
                    <Row>
                        <Col xl="8" lg="8">
                        <Row>
                            <Col>
                            <Title is-form-title>
                                Dados {{ !this.course.id ? 'do conteúdo' : 'da aula' }}
                            </Title>
                            </Col>
                            <Col>
                            <Input id="contentTitle" label="Título" placeholder="Insira o título do conteúdo..."
                                v-model="content.title" />
                            </Col>
                            <Col>
                            <Select id="contentAuthor" :options="authorOptions" v-model="content.author_id" label="Autor"
                                placeholder="Selecione um autor" />
                            </Col>
                            <Col v-if="course.exists">
                            <Select id="contentModule" :options="moduleOptions" v-model="content.module_id" label="Módulo"
                                placeholder="Selecione o módulo" @change="getContentByModule" />
                            </Col>
                            <Col class="mt-3">
                            <Title is-form-title>Escolha o tipo de conteúdo</Title>
                            </Col>
                            <Col class="d-flex gap-3 flex-wrap">
                            <SwitchButton id="isAudio" v-model="content.isAudio">
                                Incluir áudio
                            </SwitchButton>
                            <SwitchButton id="isVideo" v-model="content.isVideo">
                                Incluir vídeo
                            </SwitchButton>
                            <SwitchButton id="isRedirectLink" v-model="content.isRedirectLink">
                                Incluir link de redirecionamento
                            </SwitchButton>
                            </Col>
                            <Col v-if="content.isAudio" class="mt-3">
                            <Title is-form-title>Insira a URL do áudio</Title>
                            <Input id="contentAudioURL" label="URL do áudio" placeholder="https://..."
                                v-model="content.contentURL" type="url" />
                            </Col>
                            <Col v-if="content.isVideo" class="mt-3">
                            <Title is-form-title>Insira a URL do vídeo</Title>
                            <Input id="contentVideoURL" label="URL do vídeo" placeholder="https://..."
                                v-model="content.contentURL" type="url" />
                            </Col>
                            <Col v-if="content.isRedirectLink" class="mt-3">
                            <Row>
                                <Col>
                                <Title is-form-title>Insira a URL do conteúdo externo</Title>
                                <Input id="contentRedirectURL" label="URL do conteúdo externo" placeholder="https://..."
                                    v-model="content.contentURL" type="url" />
                                </Col>
                                <Col>
                                <Title is-form-title>Outras opções</Title>
                                <SwitchButton id="useExternalOAuthToken" v-model="content.useExternalOAuthToken">
                                    Utilizar token OAuth para o link externo
                                </SwitchButton>
                                </Col>
                            </Row>
                            </Col>
                            <Col class="mt-3">
                            <Title is-form-title>Duração do conteúdo</Title>
                            <SwitchButton id="useEstimateDuration" v-model="content.useEstimateDuration">
                                Habilitar estimativa de duração do conteúdo
                            </SwitchButton>
                            <Input id="contentDuration" label="Estimativa de duração (em minutos)"
                                placeholder="Insira a duração em minutos..." v-model="content.duration" type="number"
                                v-if="content.useEstimateDuration" />
                            </Col>
                            <Col class="my-3" v-if="!content.isRedirectLink">
                            <Title is-form-title>Texto do conteúdo</Title>
                            <Subtitle is-small>Insira abaixo o texto do seu conteúdo</Subtitle>
                            <ContentBuilder :content="content" />
                            </Col>
                        </Row>
                        </Col>
                        <Col xl="4" lg="4">
                        <Row>
                            <Col>
                            <ImageUpload title="Imagem do conteúdo - vertical"
                                subtitle="A imagem aparece nos menus de navegação e as vezes, no título<br>da seção (este campo não é obrigatório).<br>Tamanho: 848 x 1280"
                                refer="verticalImage" ref="verticalImage" @send-image="receiveVerticalImage" isVertical
                                :src="content.vertical_image" />
                            </Col>
                            <Col class="mt-3">
                            <ImageUpload title="Imagem do conteúdo - horizontal"
                                subtitle="A imagem aparece nos menus de navegação e as vezes, no título<br>da seção (este campo não é obrigatório).<br>Tamanho: 1280 x 848"
                                refer="horizontalImage" ref="horizontalImage" @send-image="receiveHorizontalImage"
                                isHorizontal :src="content.horizontal_image" />
                            </Col>
                        </Row>
                        </Col>
                        <Col>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <DefaultButton text="Cancelar" outline class="w-170" @click="redirectToPath" />
                            <DefaultButton text="Atualizar" status="success" class="w-170" @click="updateContent" />
                        </div>
                        </Col>
                    </Row>
                </template>
            </Container>
            </Col>
        </Row>
    </div>
</template>

<script>
import Row from "../../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../../js/components/XgrowDesignSystem/Utils/Col";
import Container from "../../../js/components/XgrowDesignSystem/Cards/Container";
import PipeVertical from "../../../js/components/XgrowDesignSystem/Utils/PipeVertical";
import Title from "../../../js/components/XgrowDesignSystem/Typography/Title";
import DefaultButton from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import ImageUpload from "../../../js/components/XgrowDesignSystem/Form/ImageUpload";
import axios from "axios";
import Input from "../../../js/components/XgrowDesignSystem/Form/Input";
import Select from "../../../js/components/XgrowDesignSystem/Form/Select";
import TextInput from "../../../js/components/XgrowDesignSystem/Form/TextInput";
import Subtitle from "../../../js/components/XgrowDesignSystem/Typography/Subtitle.vue";
import Breadcrumb from "../../../js/components/XgrowDesignSystem/Breadcrumb/Breadcrumb";
import SwitchButton from "../../../js/components/XgrowDesignSystem/Form/SwitchButton.vue"

import { axiosGraphqlClient } from '../../../js/config/axiosGraphql';
import LoadingStore from "../../../js/components/XgrowDesignSystem/Utils/LoadingStore.vue";
import { mapActions, mapStores } from "pinia";
import { useLoadingStore } from "../../../js/store/components/loading";
import { GET_ALL_AUTHORS_QUERY_AXIOS } from "../../../js/graphql/queries/authors";
import { GET_MODULES_BY_PARAMS_QUERY_AXIOS, GET_MODULE_BY_ID_QUERY_AXIOS } from "../../../js/graphql/queries/modules";
import { UPDATE_CONTENT_MUTATION_AXIOS } from "../../../js/graphql/mutations/contents";
import { GET_CONTENT_BY_ID_QUERY_AXIOS } from "../../../js/graphql/queries/contents";
import ContentBuilder from '../../../js/components/XgrowDesignSystem/Form/ContentBuilder/Index.vue';
import { useUploadImageS3Store } from "../../../js/store/components/uploadImageS3";
import { urlRegex } from "../../../js/components/XgrowDesignSystem/Extras/functions";

export default {
    name: "EditContent",
    components: {
        Breadcrumb,
        Subtitle,
        Select,
        TextInput,
        Input,
        ImageUpload,
        DefaultButton,
        Title,
        PipeVertical,
        Container,
        Col,
        Row,
        LoadingStore,
        SwitchButton,
        ContentBuilder
    },
    data() {
        return {
            isLoading: false,

            /** Breadcrumbs */
            breadcrumbs: [
                { title: "Resumo", link: "/", isVueRouter: false },
                { title: "Área de aprendizagem", link: '/learning-area', isVueRouter: true },
            ],

            /** Content data */
            authorOptions: [],
            moduleOptions: [],
            qtyContent: 0,
            courseId: '',
            content: {
                title: '',
                subtitle: '',
                description: '',
                hashtags: [],
                is_published: false,
                author_id: null,
                module_id: null,
                duration: 0,
                useEstimateDuration: false,
                vertical_image: 'https://las.xgrow.com/background-default.png',
                horizontal_image: 'https://las.xgrow.com/background-default.png',
                isAudio: false,
                isVideo: false,
                isRedirectLink: false,
                useExternalOAuthToken: false,
                contentURL: '',
                contentType: 'widgets',
                order_content: null,
                form_delivery: null,
                frequency: null,
                delivery_model: null,
                delivery_option: null,
                started_at: null,
            },

            module: {
                exists: false,
                id: null
            },
            course: {
                exists: false,
                id: null
            },
        }
    },
    watch: {
        'content.isAudio': function (newData, _) {
            if (newData) {
                this.content.isVideo = false;
                this.content.isRedirectLink = false;
            }
        },
        'content.isVideo': function (newData, _) {
            if (newData) {
                this.content.isAudio = false;
                this.content.isRedirectLink = false;
            }
        },
        'content.isRedirectLink': function (newData, _) {
            if (newData) {
                this.content.isVideo = false;
                this.content.isAudio = false;
            }
        }
    },
    computed: {
        ...mapStores(useLoadingStore),
        ...mapStores(useUploadImageS3Store),
    },
    methods: {
        ...mapActions(useLoadingStore, ['setLoading']),
        ...mapActions(useUploadImageS3Store, ['uploadToS3']),
        /** Get size of contents by module */
        getContentByModule: function () {
            const { count } = this.moduleOptions.find(item => item.value === this.content.module_id);
            this.qtyContent = count;
        },
        /** Redirect to correct path */
        redirectToPath: function () {
            this.course.id
                ? this.$router.push({ name: 'course-edit', params: { id: this.course.id } })
                : this.$router.push({ name: 'content-index' })
        },
        /** Add content Breadcrumb */
        updateBreadcrumb() {
            if (this.course.id) {
                if (this.breadcrumbs[this.breadcrumbs.length - 1].title !== this.moduleOptions[0].course) {
                    this.breadcrumbs.push({ title: "Cursos", link: '/learning-area/courses', isVueRouter: true })
                    this.breadcrumbs.push({
                        title: this.moduleOptions[0].course,
                        link: `/learning-area/courses/${this.course.id}/edit`,
                        isVueRouter: true
                    })
                    this.breadcrumbs.push({ title: 'Editar', link: '', isVueRouter: false })
                }
            } else {
                this.breadcrumbs.push({ title: "Conteúdos", link: '/learning-area/content', isVueRouter: true })
                this.breadcrumbs.push({ title: 'Editar', link: '', isVueRouter: false })
            }
        },
        /** Get all authors */
        getAuthors: async function () {
            try {
                this.loadingStore.setLoading(true);
                const query = {
                    "query": GET_ALL_AUTHORS_QUERY_AXIOS,
                    "variables": { name_author: "", author_email: "", page: 1, limit: 1000 }
                };
                const res = await axiosGraphqlClient.post(contentAPI, query);
                const { data } = res.data.data.authors;
                this.authorOptions = [];
                this.authorOptions = data.map(item => ({ value: item.id, name: item.name_author, img: item.author_photo_url }));
                // this.authors.push({ value: 'new', name: 'Adicionar novo', img: '/xgrow-vendor/assets/img/icons/plus-cicle.svg' });
                this.loadingStore.setLoading();
            } catch (e) {
                this.loadingStore.setLoading();
                errorToast("Ocorreu um erro", "Ocorreu um erro ao tentar recuperar os dados. Tente novamente mais tarde.");
            }
        },
        /** Get Modules */
        getModules: async function () {
            try {
                this.loadingStore.setLoading(true);
                const query = {
                    "query": GET_MODULES_BY_PARAMS_QUERY_AXIOS,
                    "variables": { course_id: this.course.id, page: 1, limit: 50 }
                };
                const res = await axiosGraphqlClient.post(contentAPI, query);
                const { data } = res.data.data.modules;
                this.moduleOptions = data.map(module => {
                    return { value: module.id, name: module.name, count: module.Content.length ?? 0, course: module.courses.name }
                });
                this.loadingStore.setLoading();
            } catch (e) {
                this.loadingStore.setLoading();
                errorToast("Ocorreu um erro", "Ocorreu um erro ao tentar recuperar os dados. Tente novamente mais tarde.");
            }
        },
        /** Upload Vertical Image */
        receiveVerticalImage: async function (obj) {
            this.content.vertical_image = await this.uploadImageS3Store.uploadToS3(obj, uploadImageURL)
        },
        /** Upload Horizontal Image */
        receiveHorizontalImage: async function (obj) {
            this.content.horizontal_image = await this.uploadImageS3Store.uploadToS3(obj, uploadImageURL)
        },
        /** Author Validation */
        contentValidation: function () {
            if (this.content.title === '')
                throw new Error("O título é obrigatório!")
            if (this.content.author_id === null)
                throw new Error("O autor é obrigatório!")
            if (this.course.exists && this.content.module_id === null)
                throw new Error("O módulo é obrigatório!")
            if ((this.content.isAudio || this.content.isVideo || this.content.isRedirectLink) && !urlRegex(this.content.contentURL))
                throw new Error("URL inválida, precisa iniciar com https://...")
        },
        /** Update Content */
        updateContent: async function () {
            try {
                this.contentValidation();
                this.loadingStore.setLoading(true);

                /** Detect the type of content */
                let contentType = 'widgets';
                if (this.content.isAudio) contentType = 'audio';
                if (this.content.isVideo) contentType = 'video';
                if (this.content.isRedirectLink) contentType = 'redirect';

                const query = {
                    query: UPDATE_CONTENT_MUTATION_AXIOS,
                    variables: {
                        id: this.$route.params.content_id,
                        title: this.content.title,
                        subtitle: this.content.subtitle,
                        description: this.content.description,
                        hashtags: this.content.hashtags,
                        is_published: this.content.is_published,
                        author_id: this.content.author_id,
                        module_id: this.content.module_id,
                        useEstimateDuration: this.content.duration > 0,
                        vertical_image: this.content.vertical_image,
                        horizontal_image: this.content.horizontal_image,
                        order_content: this.content.order_content,
                        form_delivery: this.content.form_delivery,
                        frequency: this.content.frequency,
                        delivery_model: this.content.delivery_model,
                        delivery_option: this.content.delivery_option,
                        widgets: this.content.widgets.map(widget => {
                            const widgetWithoutFile = { ...widget };
                            delete widgetWithoutFile.File
                            return widgetWithoutFile;
                        }),
                        started_at: this.content.started_at,
                        contentType: contentType,
                        contentUrl: this.content.contentURL,
                        useExternalOAuthToken: this.content.useExternalOAuthToken
                    }
                };

                query.variables.duration = this.content.useEstimateDuration ?
                    parseInt(this.content.duration) : 0;

                const res = await axiosGraphqlClient.post(contentAPI, query);

                if (res.data.hasOwnProperty('errors') && res.data.errors.length > 0) throw new Error(res.data.errors[0].message)

                this.redirectToPath();
                successToast("Conteúdo cadastrado!", `O conteúdo "${this.content.title}" foi cadastrado com sucesso!`);
            } catch (e) {
                errorToast("Ocorreu um erro", "Ocorreu um erro ao tentar recuperar os dados. Tente novamente mais tarde.");
            }

            this.loadingStore.setLoading();
        },
        /** Get params for select type of content */
        getParams: async function () {
            if (this.content.module_id) {
                const query = {
                    "query": GET_MODULE_BY_ID_QUERY_AXIOS,
                    "variables": { id: this.content.module_id }
                };
                const res = await axiosGraphqlClient.post(contentAPI, query);
                this.course.exists = true
                this.course.id = res.data.data.module.course_id
                await this.getModules()
            }
            this.updateBreadcrumb()
        },
        /** Get Contents */
        getContent: async function () {
            try {
                this.loadingStore.setLoading(true);
                const query = {
                    "query": GET_CONTENT_BY_ID_QUERY_AXIOS,
                    "variables": { id: this.$route.params.content_id }
                };
                const res = await axiosGraphqlClient.post(contentAPI, query);
                const { content } = res.data.data;

                this.content = {
                    title: content.title,
                    subtitle: content.subtitle,
                    description: content.description,
                    hashtags: content.hashtags,
                    is_published: content.is_published,
                    author_id: content.author_id,
                    module_id: content.module_id,
                    duration: content.duration,
                    useEstimateDuration: content.duration > 0,
                    vertical_image: content.vertical_image,
                    horizontal_image: content.horizontal_image,
                    isAudio: content.contentType === 'audio',
                    isVideo: content.contentType === 'video',
                    isRedirectLink: content.contentType === 'redirect',
                    useExternalOAuthToken: content.useExternalOAuthToken,
                    widgets: content.widgets,
                    contentURL: content.contentUrl,
                    contentType: content.contentType,
                    order_content: content.order_content,
                    form_delivery: content.form_delivery,
                    frequency: content.frequency,
                    delivery_model: content.delivery_model,
                    delivery_option: content.delivery_option,
                    started_at: content.started_at
                };
                await this.getParams();
                this.loadingStore.setLoading();
            } catch (e) {
                console.log(e);
                this.loadingStore.setLoading();
                errorToast("Ocorreu um erro", "Ocorreu um erro ao tentar recuperar os dados. Tente novamente mais tarde.");
            }
        },
    },
    async created() {
        await this.getAuthors();
        await this.getContent();
    }
}
</script>
