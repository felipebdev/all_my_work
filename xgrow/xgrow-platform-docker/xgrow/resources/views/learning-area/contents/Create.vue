<template>
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
                            refer="verticalImage" ref="verticalImage" @send-image="receiveVerticalImage" isVertical />
                        </Col>
                        <Col class="mt-3">
                        <ImageUpload title="Imagem do conteúdo - horizontal"
                            subtitle="A imagem aparece nos menus de navegação e as vezes, no título<br>da seção (este campo não é obrigatório).<br>Tamanho: 1280 x 848"
                            refer="horizontalImage" ref="horizontalImage" @send-image="receiveHorizontalImage"
                            isHorizontal />
                        </Col>
                    </Row>
                    </Col>
                    <Col>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <DefaultButton text="Cancelar" outline class="w-170" @click="redirectToPath" />
                        <DefaultButton text="Salvar" status="success" class="w-170" @click="saveContent" />
                    </div>
                    </Col>
                </Row>
            </template>
        </Container>
        </Col>
    </Row>
</template>

<script>
import Row from "../../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../../js/components/XgrowDesignSystem/Utils/Col";
import Container from "../../../js/components/XgrowDesignSystem/Cards/Container";
import PipeVertical from "../../../js/components/XgrowDesignSystem/Utils/PipeVertical";
import Title from "../../../js/components/XgrowDesignSystem/Typography/Title";
import DefaultButton from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import ImageUpload from "../../../js/components/XgrowDesignSystem/Form/ImageUpload";
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
import { GET_MODULES_BY_PARAMS_QUERY_AXIOS } from "../../../js/graphql/queries/modules";
import { GET_SECTION_BY_ID_QUERY_AXIOS } from "../../../js/graphql/queries/sections";
import { CREATE_CONTENT_MUTATION_AXIOS } from "../../../js/graphql/mutations/contents";
import { UPDATE_SECTIONS_AXIOS } from '../../../js/graphql/mutations/sections';
import ContentBuilder from '../../../js/components/XgrowDesignSystem/Form/ContentBuilder/Index.vue';
import { urlRegex } from "../../../js/components/XgrowDesignSystem/Extras/functions";
import { useUploadImageS3Store } from "../../../js/store/components/uploadImageS3";

export default {
    name: "CreateContent",
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
                widgets: []
            },

            module: {
                exists: false,
                id: null
            },
            course: {
                exists: false,
                id: null
            },
            section: {
                exists: false,
                id: null,
                name: "",
                contentId: "",
                section_items: []
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
        redirectToPath: async function () {
            if (this.course.id) {
                this.$router.push({ name: 'course-edit', params: { id: this.course.id } })
            } else if (this.section.id) {
                this.$router.push({ name: 'section-edit', params: { id: this.section.id } })
            } else {
                this.$router.push({ name: 'content-index' })
            }
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
                    this.breadcrumbs.push({ title: 'Novo', link: '', isVueRouter: false })
                }
            } else if (this.section.id) {
                this.breadcrumbs.push({ title: "Seções", link: '/learning-area/sections', isVueRouter: true })
                this.breadcrumbs.push({
                    title: this.section.name,
                    link: `/learning-area/sections/${this.section.id}/edit`,
                    isVueRouter: true
                })
                this.breadcrumbs.push({ title: 'Novo', link: '', isVueRouter: false })
            } else {
                this.breadcrumbs.push({ title: "Conteúdos", link: '/learning-area/content', isVueRouter: true })
                this.breadcrumbs.push({ title: 'Novo', link: '', isVueRouter: false })
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
        /** Get Sections */
        getSections: async function () {
            try {
                this.loadingStore.setLoading(true);
                const query = {
                    "query": GET_SECTION_BY_ID_QUERY_AXIOS,
                    "variables": { id: this.section.id }
                };
                const res = await axiosGraphqlClient.post(contentAPI, query);
                const data = res.data.data.section;
                this.section.name = data.title;
                this.section.section_items = data.section_items;

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
        /** Save Content */
        saveContent: async function () {
            try {
                this.contentValidation();
                this.loadingStore.setLoading(true);

                /** Detect the type of content */
                let contentType = 'widgets';
                if (this.content.isAudio) contentType = 'audio';
                if (this.content.isVideo) contentType = 'video';
                if (this.content.isRedirectLink) contentType = 'redirect';

                const query = {
                    query: CREATE_CONTENT_MUTATION_AXIOS,
                    variables: {
                        title: this.content.title,
                        subtitle: this.content.subtitle,
                        description: this.content.description,
                        vertical_image: this.content.vertical_image,
                        horizontal_image: this.content.horizontal_image,
                        author_id: this.content.author_id,
                        module_id: this.content.module_id,
                        order_content: this.qtyContent + 1,
                        form_delivery: "sequential",
                        frequency: 1,
                        delivery_model: "lastModule",
                        delivery_option: "startDateCourse",
                        started_at: new Date(),
                        contentType: contentType,
                        contentUrl: this.content.contentURL,
                        useExternalOAuthToken: contentType == 'redirect' ? this.content.useExternalOAuthToken : false,
                        is_published: true
                    }
                };
                if (this.course.exists)
                    query.variables.course_id = this.content.course_id;
                if (this.content.widgets.length)
                    query.variables.widgets = this.content.widgets.map(widget => {
                        const widgetWithoutFile = { ...widget };
                        delete widgetWithoutFile.File
                        return widgetWithoutFile;
                    });
                if (this.section.exists) query.variables.section_id = this.section.id;
                query.variables.duration = this.content.useEstimateDuration
                    ? parseInt(this.content.duration)
                    : 0;
                const content = await axiosGraphqlClient.post(contentAPI, query);
                if (content.data.hasOwnProperty('errors') && content.data.errors.length > 0) throw new Error(content.data.errors[0].message)
                this.section.contentId = content.data.data.content.id;
                this.loadingStore.setLoading();
                if (this.section.exists)
                    await this.addNewItemSection();

                this.redirectToPath();
                successToast("Conteúdo cadastrado!", `O conteúdo "${this.content.title}" foi cadastrado com sucesso!`);
            } catch (e) {
                this.loadingStore.setLoading();
                errorToast("Ocorreu um erro", e?.message ?? "Ocorreu um erro ao tentar recuperar os dados. Tente novamente mais tarde.");
            }
        },
        /** Get params for select type of content */
        getParams: async function () {
            if (this.$route.query.hasOwnProperty('course')) {
                this.course.exists = true
                this.course.id = this.$route.query.course
                await this.getModules()
            }
            if (this.$route.query.hasOwnProperty('module')) {
                this.module.exists = true
                this.module.id = this.$route.query.module
                this.content.module_id = this.module.id
            }
            if (this.$route.query.hasOwnProperty('section')) {
                this.section.exists = true
                this.section.id = this.$route.query.section
                await this.getSections()
            }
            if (this.$route.query.hasOwnProperty('single')) { }

            this.updateBreadcrumb()
        },
        async addNewItemSection() {
            this.loadingStore.setLoading(true);
            const lastItem = this.section.section_items[this.section.section_items.length - 1];
            let newItem = { type: "content", position: (lastItem?.position ?? 0) + 1, item_id: this.section.contentId };
            this.section.section_items.push(newItem);
            const query = {
                "query": UPDATE_SECTIONS_AXIOS,
                "variables": {
                    id: this.section.id,
                    section_items: this.section.section_items.map(({ type, position, item_id }) => ({ type, position, item_id })),
                }
            };
            await axiosGraphqlClient.post(contentAPI, query);
            this.loadingStore.setLoading();
        }
    },
    async created() {
        await this.getParams();
        await this.getAuthors();
    }
}
</script>
