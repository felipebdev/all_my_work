<template>
    <LoadingStore />
    <Breadcrumb :items="breadcrumbs" class="mb-3" />
    <Container>
        <template v-slot:header-left>
            <Title>Novo curso</Title>
        </template>
        <template v-slot:content>
            <Row class="mt-3">
                <Col>
                <Title :is-form-title="true">Informações básicas</Title>
                </Col>
                <Col sm="12" md="6" lg="6" xl="6">
                <Input id="course_name" label="Nome" placeholder="Insira o nome do curso..." v-model="course.title" />
                </Col>
                <Col sm="12" md="6" lg="6" xl="6">
                <Select id="courseAuthor" :options="authors" v-model="course.author" label="Autor"
                    placeholder="Selecione um autor" @change="openAuthorModal(course.author, course)" />
                </Col>
                <Col>
                <TextInput id="course_description" label="Descrição (opcional)" v-model="course.description"
                    placeholder="Insira a descrição do curso..." />
                </Col>
            </Row>
            <Row class="mt-3">
                <Col xl="6" lg="6">
                <ImageUpload title="Imagem do curso - vertical"
                    subtitle="A imagem aparece nos menus de navegação e as vezes, no título<br>da seção (este campo não é obrigatório).<br>Tamanho: 848 x 1280"
                    refer="verticalImage" ref="verticalImage" @send-image="receiveVerticalImage" isVertical />
                </Col>
                <Col xl="6" lg="6">
                <ImageUpload title="Imagem do curso - horizontal"
                    subtitle="A imagem aparece nos menus de navegação e as vezes, no título<br>da seção (este campo não é obrigatório).<br>Tamanho: 1280 x 848"
                    refer="horizontalImage" ref="horizontalImage" @send-image="receiveHorizontalImage" isHorizontal />
                </Col>
            </Row>
        </template>
        <template v-slot:footer>
            <div class="panel__footer">
                <router-link :to="{ name: 'course-index' }">
                    <DefaultButton text="Cancelar" outline @click="null" />
                </router-link>
                <DefaultButton text="Salvar" status="success" @click="saveCourse" />
            </div>
        </template>
    </Container>

    <ConfirmModal :is-open="authorModal.active">
        <Title>Novo autor</Title>
        <Subtitle>Insira os dados básicos do novo autor da sua plataforma</Subtitle>
        <div class="modal-body__content">
            <Row class="w-100">
                <Col>
                <Title :is-form-title="true">Dados do autor</Title>
                <Input id="author_name" label="Nome" v-model="authorModal.name" placeholder="Insira o nome do autor..." />
                </Col>
                <Col>
                <Input id="author_email" label="Email" v-model="authorModal.email" placeholder="Insira o email do autor..."
                    type="email" />
                </Col>
                <Col class="text-start">
                <ImageUpload title="Foto do autor"
                    subtitle="A imagem aparece nos menus de navegação e as vezes, no título da seção.<br>Tamanho mínimo: 180 x 180"
                    refer="authorImage" ref="authorImage" @send-image="receiveAuthorImage" />
                </Col>
            </Row>
        </div>
        <div class="modal-body__footer">
            <hr>
            <DefaultButton text="Cancelar" outline @click="authorModal.active = false" />
            <DefaultButton text="Salvar" status="success" @click="saveAuthor" />
        </div>
    </ConfirmModal>
</template>

<script>
import Breadcrumb from "../../../js/components/XgrowDesignSystem/Breadcrumb/Breadcrumb.vue";
import Container from "../../../js/components/XgrowDesignSystem/Cards/Container.vue";
import DefaultButton from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton.vue";
import Title from "../../../js/components/XgrowDesignSystem/Typography/Title.vue";
import Subtitle from "../../../js/components/XgrowDesignSystem/Typography/Subtitle.vue";
import Row from "../../../js/components/XgrowDesignSystem/Utils/Row.vue";
import Col from "../../../js/components/XgrowDesignSystem/Utils/Col.vue";
import Input from "../../../js/components/XgrowDesignSystem/Input.vue";
import TextInput from "../../../js/components/XgrowDesignSystem/Form/TextInput.vue";
import ImageUpload from "../../../js/components/XgrowDesignSystem/Form/ImageUpload.vue";
import { SAVE_COURSE_MUTATION_AXIOS } from "../../../js/graphql/mutations/courses";
import Select from "../../../js/components/XgrowDesignSystem/Form/Select.vue";
import { GET_ALL_AUTHORS_QUERY_AXIOS } from "../../../js/graphql/queries/authors";
import { useLoadingStore } from "../../../js/store/components/loading";
import { mapActions, mapStores } from "pinia";
import LoadingStore from "../../../js/components/XgrowDesignSystem/Utils/LoadingStore.vue";
import { useUploadImageS3Store } from "../../../js/store/components/uploadImageS3";
import ConfirmModal from "../../../js/components/XgrowDesignSystem/Modals/ConfirmModal.vue";
import { axiosGraphqlClient } from "../../../js/config/axiosGraphql";
import { SAVE_FAST_AUTHOR_MUTATION_AXIOS } from "../../../js/graphql/mutations/authors";
import { emailRegex } from "../../../js/components/XgrowDesignSystem/Extras/functions";

export default {
    name: "Create",
    components: { Subtitle, ConfirmModal, Select, ImageUpload, TextInput, Input, Col, Row, Title, DefaultButton, Container, Breadcrumb, LoadingStore },
    data() {
        return {
            isLoading: false,

            /** Breadcrumbs */
            breadcrumbs: [
                { title: "Resumo", link: "/" },
                { title: "Área de aprendizagem", link: '/learning-area', isVueRouter: true },
                { title: "Cursos", link: '/learning-area/courses', isVueRouter: true },
                { title: "Novo", link: false },
            ],

            course: {
                title: '',
                description: '',
                author: null,
                vertical_image: 'https://las.xgrow.com/background-default.png',
                horizontal_image: 'https://las.xgrow.com/background-default.png',
                active: false
            },

            authors: [],
            authorModal: { active: false, name: '', email: '', image: 'https://las.xgrow.com/background-default.png' },
        }
    },
    computed: {
        ...mapStores(useLoadingStore),
        ...mapStores(useUploadImageS3Store),
    },
    methods: {
        ...mapActions(useLoadingStore, ['setLoading']),
        ...mapActions(useUploadImageS3Store, ['uploadToS3']),
        /** Open Author modal if you need add one */
        openAuthorModal: async function (value, course) {
            if (value === 'new') {
                this.authorModal.active = true;
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
                this.authors = [];
                this.authors = data.map(item => ({ value: item.id, name: item.name_author, img: item.author_photo_url }));
                this.authors.push({ value: 'new', name: 'Adicionar novo', img: '/xgrow-vendor/assets/img/icons/plus-cicle.svg' });
                this.loadingStore.setLoading();
            } catch (e) {
                this.loadingStore.setLoading();
                errorToast("Ocorreu um erro", `Ocorreu um problema ao tentar salvar as alterações no curso. Tente novamente mais tarde.`);
                console.log(e);
            }
        },
        /** Author Validation */
        authorValidation: function () {
            if (this.authorModal.name === '')
                throw new Error("O nome do autor é obrigatório!")
            if (this.authorModal.email === '')
                throw new Error("O email do autor é obrigatório!")
            if (this.authorModal.email && !emailRegex(this.authorModal.email))
                throw new Error("O email digitado é inválido!")
            if (this.authorModal.image === 'https://las.xgrow.com/background-default.png')
                throw new Error("A imagem do autor é obrigatória!")
        },
        /** Save Author */
        saveAuthor: async function () {
            try {
                this.authorValidation();
                this.authorModal.active = false
                this.loadingStore.setLoading(true);
                const query = {
                    "query": SAVE_FAST_AUTHOR_MUTATION_AXIOS,
                    "variables": {
                        name_author: this.authorModal.name,
                        author_email: this.authorModal.email,
                        author_photo_url: this.authorModal.image ?? null,
                        status: true
                    }
                };
                await axiosGraphqlClient.post(contentAPI, query);
                this.loadingStore.setLoading();
                await this.getAuthors();
                successToast("Autor cadastrado!", `O autor ${this.authorModal.name} foi cadastrado com sucesso!`);
                this.authorModal = { active: false, name: '', email: '', image: 'https://las.xgrow.com/background-default.png' }
            } catch (e) {
                this.loadingStore.setLoading();
                errorToast("Ocorreu um erro", e.message ?? `Ocorreu um problema ao tentar salvar as alterações no curso. Tente novamente mais tarde.`);
                console.log(e);
            }
        },
        /** Receive Author image */
        receiveAuthorImage: async function (obj) {
            this.authorModal.image = await this.uploadImageS3Store.uploadToS3(obj)
        },
        /** Save Course */
        saveCourse: async function () {
            try {
                this.loadingStore.setLoading(true);
                const query = {
                    "query": SAVE_COURSE_MUTATION_AXIOS,
                    "variables": {
                        name: this.course.title,
                        description: this.course.description,
                        active: true,
                        author_id: this.course.author,
                        horizontal_image: this.course.horizontal_image,
                        vertical_image: this.course.vertical_image
                    }
                };
                await axiosGraphqlClient.post(contentAPI, query);
                this.loadingStore.setLoading();
                successToast("Curso cadastrado!", `O curso ${this.course.title} foi cadastrado com sucesso!`);
                this.$router.push({ name: 'course-index' })
            } catch (e) {
                this.loadingStore.setLoading();
                console.log(e);
            }
        },
        /** Upload Vertical Image */
        receiveVerticalImage: async function (obj) {
            this.course.vertical_image = await this.uploadImageS3Store.uploadToS3(obj, uploadImageURL)
        },
        /** Upload Horizontal Image */
        receiveHorizontalImage: async function (obj) {
            this.course.horizontal_image = await this.uploadImageS3Store.uploadToS3(obj, uploadImageURL)
        }
    },
    async created() {
        await this.getAuthors();
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
