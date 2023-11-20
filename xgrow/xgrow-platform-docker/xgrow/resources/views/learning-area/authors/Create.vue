<template>
    <LoadingStore />
    <Breadcrumb :items="breadcrumbs" class="mb-3" />
    <Container>
        <template v-slot:header-left>
            <Title>Novo autor</Title>
        </template>
        <template v-slot:content>
            <Row class="mt-3">
                <Col>
                <Title :is-form-title="true">Informações básicas</Title>
                </Col>
                <Col sm="12" md="12" lg="6" xl="6">
                <Input id="authorName" label="Nome" placeholder="Nome" v-model="author.name_author" />
                </Col>
                <Col sm="12" md="12" lg="6" xl="6">
                <Input id="authorEmail" label="Email" placeholder="E-mail" v-model="author.author_email" />
                </Col>
                <Col sm="12" md="6" lg="8" xl="8">
                <TextInput id="autorResume" label="Currículo" placeholder="Currículo" :limit="500"
                    v-model="author.author_desc" />
                </Col>
                <Col sm="12" md="6" lg="4" xl="4" class="mt-3">
                <ImageUpload title="Foto de perfil"
                    subtitle="Imagem exibida no avatar do autor.(este campo não é obrigatório).<br>Tamanho: 180 x 180"
                    refer="authorImage" ref="authorImage"
                    @send-image="async (obj) => { author.author_photo_url = await uploadImageS3Store.uploadToS3(obj, '/learning-area/upload-image') }" />
                </Col>
            </Row>
            <Row class="mt-3">
                <Col>
                <Title is-form-title>Redes sociais</Title>
                </Col>
                <Col sm="12" md="12" lg="6" xl="6">
                <Input id="authorLinkedin" label="Linkedin" placeholder="https://www.linkedin.com/in/...."
                    v-model="author.author_linkedin" type="url" />
                </Col>
                <Col sm="12" md="12" lg="6" xl="6">
                <Input id="authorYoutube" label="Youtube" placeholder="https://www.youtube.com/..."
                    v-model="author.author_youtube" type="url" />
                </Col>
                <Col>
                <Input id="authorInstagram" label="Instagram" placeholder="https://www.instagram.com/..."
                    v-model="author.author_insta" type="url" />
                </Col>
            </Row>
            <Row class="mt-3">
                <SwitchButton id="authorStatus" v-model="author.status">Autor ativo?</SwitchButton>
            </Row>
        </template>
        <template v-slot:footer>
            <div class="panel__footer">
                <router-link :to="{ name: 'author-index' }">
                    <DefaultButton text="Cancelar" outline />
                </router-link>
                <DefaultButton text="Salvar" status="success" @click="saveAuthor" />
            </div>
        </template>
    </Container>
</template>

<script>
import LoadingStore from "../../../js/components/XgrowDesignSystem/Utils/LoadingStore";
import Breadcrumb from "../../../js/components/XgrowDesignSystem/Breadcrumb/Breadcrumb";
import Container from "../../../js/components/XgrowDesignSystem/Cards/Container";
import Row from "../../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../../js/components/XgrowDesignSystem/Utils/Col";
import Title from "../../../js/components/XgrowDesignSystem/Typography/Title";
import Subtitle from "../../../js/components/XgrowDesignSystem/Typography/Subtitle";
import Input from "../../../js/components/XgrowDesignSystem/Form/Input";
import TextInput from "../../../js/components/XgrowDesignSystem/Form/TextInput";
import ImageUpload from "../../../js/components/XgrowDesignSystem/Form/ImageUpload";
import DefaultButton from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import SwitchButton from "../../../js/components/XgrowDesignSystem/SwitchButton";
import { mapActions, mapStores } from "pinia";
import { useLoadingStore } from "../../../js/store/components/loading";
import { SAVE_AUTHOR_MUTATION_AXIOS } from "../../../js/graphql/mutations/authors";
import { useUploadImageS3Store } from "../../../js/store/components/uploadImageS3";
import { axiosGraphqlClient } from "../../../js/config/axiosGraphql";
import * as Yup from 'yup';
import { pt } from 'yup-locale-pt';

Yup.setLocale(pt);

const authorSchema = Yup.object().shape({
    name_author: Yup.string().label("O nome").required().min(3),
    author_email: Yup.string().label("O email").required().email(),
    author_desc: Yup.string().label("A descriçao").nullable(),
    author_photo_url: Yup.string().label("A foto").nullable(),
    author_insta: Yup.string().label("O Instagram").url().nullable(),
    author_linkedin: Yup.string().label("O Linkedin").url().nullable(),
    author_youtube: Yup.string().label("O Youtube").url().nullable(),
    status: Yup.boolean().default(false)
})

export default {
    name: "Create",
    components: {
        SwitchButton,
        DefaultButton, ImageUpload, TextInput, Input, Subtitle, Title, Col, Row, Container, Breadcrumb, LoadingStore
    },
    data() {
        return {
            /** Breadcrumbs */
            breadcrumbs: [
                { title: "Resumo", link: "/" },
                { title: "Área de aprendizagem", link: '/learning-area', isVueRouter: true },
                { title: "Autores", link: '/learning-area/authors', isVueRouter: true },
                { title: "Novo", link: false },
            ],

            /** Author data */
            author: {
                name_author: '',
                author_desc: '',
                author_email: '',
                author_photo_url: 'https://las.xgrow.com/background-default.png',
                author_linkedin: null,
                author_youtube: null,
                author_insta: null,
                status: false
            },
        }
    },
    computed: {
        ...mapStores(useLoadingStore),
        ...mapStores(useUploadImageS3Store),
    },
    methods: {
        ...mapActions(useLoadingStore, ['setLoading']),
        ...mapActions(useUploadImageS3Store, ['uploadToS3']),
        /** Save Author */
        saveAuthor: async function () {
            try {
                this.loadingStore.setLoading(true);
                await authorSchema.validate(this.author)
                const query = {
                    query: SAVE_AUTHOR_MUTATION_AXIOS,
                    variables: {
                        name_author: this.author.name_author,
                        author_desc: this.author.author_desc,
                        author_email: this.author.author_email,
                        author_insta: this.author.author_insta,
                        author_linkedin: this.author.author_linkedin,
                        author_youtube: this.author.author_youtube,
                        author_photo_url: this.author.author_photo_url,
                        status: this.author.status,
                    },
                };

                const res = await axiosGraphqlClient.post(contentAPI, query);
                if (res.data.hasOwnProperty('errors') && res.data.errors.length > 0) throw new Error(res.data.errors[0].message)
                this.loadingStore.setLoading();
                successToast("Autor cadastrado!", `O autor ${this.author.name_author} foi cadastrado com sucesso!`);
                this.$router.push({ name: 'author-index' })
            } catch (e) {
                this.loadingStore.setLoading();
                errorToast("Ocorreu um erro", e.message ?? "Ocorreu um erro ao salvar as informações, tente novamente em instantes.");
            }
        },
        clearFields: function () {
            this.author = { name_author: '', author_desc: '', author_email: '', author_photo_url: 'https://las.xgrow.com/background-default.png', author_linkedin: null, author_youtube: null, author_insta: null, status: false }
        },
    },
    created() {
        this.clearFields()
    }
}
</script>

<style lang="scss" scoped>
:deep(.upload-image__thumb) {
    border-radius: 100%;
}

:deep(.upload-image__img) {
    height: 100%;
}

:deep(.upload-image) {
    flex-direction: row !important;
    flex-wrap: wrap !important;
    align-self: flex-start !important;
}

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
