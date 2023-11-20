<template>
    <LoadingStore />
    <Row>
        <Col>
        <Title>Configurações
            <PipeVertical /> <span class="fw-normal">Sobre o curso</span>
        </Title>
        </Col>
        <Col lg="8" xl="8">
        <Row>
            <Col>
            <Title is-form-title>Informações básicas</Title>
            <Input id="courseName" label="Nome" placeholder="Insira o nome do curso..." v-model="course.name" />
            </Col>
            <Col>
            <Select id="courseAuthor" :options="authorOptions" v-model="course.author_id" label="Autor"
                placeholder="Selecione um autor" @change="openAuthorModal(course.author_id, course)" />
            </Col>
            <Col>
            <TextInput id="courseDescription" label="Descrição (opcional)" v-model="course.description" :limit="500"
                placeholder="Insira a descrição do curso..." />
            </Col>
        </Row>

        <Row>
            <Col>
            <Title is-form-title>Modo de entrega</Title>
            <RadioButton option="default" name="course_delivery" label="Entrega tradicional (módulos e aulas)"
                v-model="course.delivery_mode" :checked="course.delivery_mode === 'default'" />
            <RadioButton option="experience" name="course_delivery" label="Xgrow experience (diagrama)" v-if="false"
                v-model="course.delivery_mode" :checked="course.delivery_mode === 'experience'" />
            </Col>

            <Col class="mt-4">
            <Title is-form-title>Oferta</Title>
            <SwitchButton id="hasOfferLink" v-model="course.has_offer_link" @input="toggleOffer">
                Ativar oferta
            </SwitchButton>
            <Subtitle is-small class="mb-3">
                Caso um usuário não tenha acesso à esse produto, ele será redirecionado para essa URL.
            </Subtitle>
            <Input id="offerLink" label="URL" placeholder="https://nomedolink.com.br" type="url" v-model="course.offer_link"
                :disabled="!course.has_offer_link" />
            </Col>
        </Row>
        </Col>
        <Col lg="4" xl="4">
        <Row class="mt-3">
            <Col>
            <ImageUpload title="Imagem do curso - vertical"
                subtitle="A imagem aparece nos menus de navegação e as vezes, no título<br>da seção (este campo não é obrigatório).<br>Tamanho: 848 x 1280"
                refer="verticalImage" ref="verticalImage" :src="course.vertical_image" @send-image="receiveVerticalImg"
                isVertical />
            </Col>
            <Col class="mt-4">
            <ImageUpload title="Imagem do curso - horizontal"
                subtitle="A imagem aparece nos menus de navegação e as vezes, no título<br>da seção (este campo não é obrigatório).<br>Tamanho: 1280 x 848"
                refer="horizontalImage" ref="horizontalImage" :src="course.horizontal_image"
                @send-image="receiveHorizontalImg" isHorizontal />
            </Col>
        </Row>
        </Col>
        <Col>
        </Col>
        <Col>
        <hr>
        <div class="d-flex justify-content-between">
            <DefaultButton text="Cancelar" outline class="w-170" />
            <DefaultButton text="Salvar" status="success" class="w-170" @click="updateCourse" />
        </div>
        </Col>
    </Row>

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
                    refer="authorImage" ref="authorImage" @send-image="receiveAuthorImage" :src="authorModal.image" />
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
import Row from "../../../../js/components/XgrowDesignSystem/Utils/Row.vue";
import Col from "../../../../js/components/XgrowDesignSystem/Utils/Col.vue";
import Container from "../../../../js/components/XgrowDesignSystem/Cards/Container.vue";
import Title from "../../../../js/components/XgrowDesignSystem/Typography/Title.vue";
import Subtitle from "../../../../js/components/XgrowDesignSystem/Typography/Subtitle.vue";
import PipeVertical from "../../../../js/components/XgrowDesignSystem/Utils/PipeVertical.vue";
import ImageUpload from "../../../../js/components/XgrowDesignSystem/Form/ImageUpload.vue";
import Select from "../../../../js/components/XgrowDesignSystem/Form/Select.vue";
import Input from "../../../../js/components/XgrowDesignSystem/Form/Input.vue";
import TextInput from "../../../../js/components/XgrowDesignSystem/Form/TextInput.vue";
import RadioButton from "../../../../js/components/XgrowDesignSystem/Form/RadioButton.vue";
import SwitchButton from "../../../../js/components/XgrowDesignSystem/Form/SwitchButton.vue";
import DefaultButton from "../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton.vue";
import { GET_ALL_AUTHORS_QUERY_AXIOS } from "../../../../js/graphql/queries/authors";
import { GET_COURSE_BY_ID_QUERY_AXIOS } from "../../../../js/graphql/queries/courses";
import { UPDATE_COURSE_AUTHOR_MUTATION_AXIOS, UPDATE_COURSE_MUTATION_AXIOS } from "../../../../js/graphql/mutations/courses";
import Loading from "../../../../js/components/XgrowDesignSystem/Utils/Loading.vue";
import { emailRegex, urlRegex } from "../../../../js/components/XgrowDesignSystem/Extras/functions";
import LoadingStore from "../../../../js/components/XgrowDesignSystem/Utils/LoadingStore.vue";
import { useLoadingStore } from "../../../../js/store/components/loading";
import { useUploadImageS3Store } from "../../../../js/store/components/uploadImageS3";
import { mapActions, mapStores } from "pinia";
import { axiosGraphqlClient } from "../../../../js/config/axiosGraphql";
import ConfirmModal from "../../../../js/components/XgrowDesignSystem/Modals/ConfirmModal.vue";
import { SAVE_FAST_AUTHOR_MUTATION_AXIOS } from "../../../../js/graphql/mutations/authors";

export default {
    name: "ConfigContentAbout",
    components: {
        ConfirmModal,
        Loading,
        DefaultButton,
        SwitchButton,
        RadioButton,
        Select,
        LoadingStore,
        TextInput, Input, ImageUpload, PipeVertical, Subtitle, Title, Container, Col, Row
    },
    data() {
        return {
            authorOptions: [],
            authorModal: { active: false, name: '', email: '', image: 'https://las.xgrow.com/background-default.png' },
            course: {
                id: null,
                active: false,
                author_id: '',
                delivery_mode: 'default',
                description: '',
                name: '',
                vertical_image: 'https://las.xgrow.com/background-default.png',
                horizontal_image: 'https://las.xgrow.com/background-default.png',
                has_offer_link: false,
                offer_link: ''
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
        /** Get Course Data */
        getCourse: async function () {
            try {
                this.loadingStore.setLoading(true);
                const query = {
                    "query": GET_COURSE_BY_ID_QUERY_AXIOS,
                    "variables": { id: this.$route.params.id }
                };
                const res = await axiosGraphqlClient.post(contentAPI, query);
                Object.assign(this.course, res.data.data.course)
                this.loadingStore.setLoading();
            } catch (e) {
                this.loadingStore.setLoading();
                console.log(e);
            }
        },
        /** Open Author modal if you need add one */
        openAuthorModal: async function (value, course) {
            if (value === 'new') {
                /** Trick to return select */
                this.authorModal.name = '';
                this.authorModal.email = '';
                this.authorModal.image = 'https://las.xgrow.com/background-default.png';
                this.course.author_id = course.authorOld;
                this.authorModal.active = true;
            } else {
                const query = {
                    "query": UPDATE_COURSE_AUTHOR_MUTATION_AXIOS,
                    "variables": { id: course.id, name: course.name, author_id: value }
                };
                try {
                    this.loadingStore.setLoading(true);
                    await axiosGraphqlClient.post(contentAPI, query);
                    this.loadingStore.setLoading();
                    successToast("Curso atualizado!", "O autor foi alterado com sucesso.")
                } catch (e) {
                    this.loadingStore.setLoading();
                    errorToast("Ocorreu um erro", `Ocorreu um problema ao tentar salvar as alterações no curso. Tente novamente mais tarde.`);
                }
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
                this.authorOptions.push({ value: 'new', name: 'Adicionar novo', img: '/xgrow-vendor/assets/img/icons/plus-cicle.svg' });
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
        /** Toggle Offer */
        toggleOffer: function () {
            if (!this.course.has_offer_link) this.course.offer_link = ''
        },
        /** Receiver Vertical and Horizontal Img */
        receiveVerticalImg: async function (obj) {
            this.course.vertical_image = await this.uploadImageS3Store.uploadToS3(obj, uploadImageURL)
        },
        receiveHorizontalImg: async function (obj) {
            this.course.horizontal_image = await this.uploadImageS3Store.uploadToS3(obj, uploadImageURL)
        },
        /** Save Course */
        updateCourse: async function () {
            try {
                this.loadingStore.setLoading(true);
                if (this.course.name === '')
                    throw new Error("O nome do curso é obrigatório!")
                if (this.course.has_offer_link && !urlRegex(this.course.offer_link))
                    throw new Error("Você precisa adicionar uma URL válida na oferta!");
                if (this.course.has_offer_link && this.course.offer_link === '')
                    throw new Error("A URL de oferta não pode ficar em branco!")

                const query = {
                    "query": UPDATE_COURSE_MUTATION_AXIOS,
                    "variables": {
                        id: this.course.id,
                        name: this.course.name,
                        description: this.course.description,
                        active: !!this.course.active,
                        author_id: this.course.author_id,
                        horizontal_image: this.course.horizontal_image,
                        vertical_image: this.course.vertical_image,
                        has_offer_link: !!this.course.has_offer_link,
                        offer_link: this.course.offer_link
                    }
                };
                await axiosGraphqlClient.post(contentAPI, query);
                this.loadingStore.setLoading();
                successToast("Curso atualizado!", `O curso ${this.course.name} foi atualizado com sucesso!`);
                // this.$router.push({name: 'course-index'})
            } catch (e) {
                console.log(e);
            }
        },
    },
    async created() {
        await this.getAuthors();
        await this.getCourse();
    }
}
</script>

<style scoped>
.w-170 {
    width: 170px;
}
</style>
