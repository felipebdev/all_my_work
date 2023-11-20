<template>
    <ConfirmModal :is-open="modal">
        <Row class="text-start w-100">
            <Col>
            <Title is-form-title icon="fas fa-image" icon-color="#FFFFFF" icon-bg="#3f4450" class="m-0">
                Banner de destaque
            </Title>
            <hr>
            </Col>

            <Accordion id="accordionBanner">
                <template v-for="(banner, i) in banners" :key="banner?._id">
                    <AccordionItem :id="`heading_${banner?._id}`" :title="`Banner ${i + 1}`" :subtitle="banner.title"
                        :is-open="i === 0" :target-id="`collapse_${i}`" accordion-id="accordionBanner">
                        <Row>
                            <Col v-if="banner.type === 'image'">
                            <ImageUpload title="Imagem principal"
                                subtitle="Esta será a imagem do seu banner de destaque" refer="horizontalImage"
                                ref="horizontalImage" :src="banner.urlBanner"
                                @send-image="(obj) => { uploadBanner(obj, banner) }" isHorizontal />
                            </Col>
                            <Col v-else>
                            <Input id="videoUrl" label="Link do vídeo" type="url" placeholder="Link do vídeo"
                                v-model="banner.urlBanner" info="Cada vídeo deverá ter 20 segundos no máximo." />
                            </Col>
                            <Col>
                            <SwitchButton :id="`isVideo_${i}`" v-model="banner.is_video"
                                @change="changeContentType(banner)">
                                Adicionar vídeo no banner de destaque
                            </SwitchButton>
                            </Col>
                            <Col>
                            <hr>
                            <Title is-form-title>Mensagem de destaque</Title>
                            <Subtitle is-small>Esse texto ficará localizado sobre o seu banner principal</Subtitle>
                            </Col>
                            <Col>
                            <SwitchButton :id="`hasMessage_${i}`" v-model="banner.hasMessage">
                                Ativar mensagem de destaque
                            </SwitchButton>
                            </Col>
                            <template v-if="banner.hasMessage">
                                <Col>
                                <Input id="title" label="Título na plataforma" placeholder="Título na plataforma"
                                    v-model="banner.title" :limit="30" />
                                </Col>
                                <Col>
                                <TextInput id="description" :limit="120" placeholder="Descrição" label="Descrição"
                                    v-model="banner.description" />
                                </Col>
                                <Col>
                                <Select id="contentType" :options="contentTypeOptions" v-model="banner.contentType"
                                    label="Tipo" placeholder="Selecione um tipo"
                                    @change="searchContentsByType(banner.contentType, banner)" />
                                </Col>
                                <template v-if="banner.contentType !== 'lives'">
                                    <Col v-if="banner.contentType === 'course'">
                                    <Select id="contentId" :options="courseOptions" v-model="banner.contentId"
                                        label="Selecione o curso" placeholder="Selecione o curso" />
                                    </Col>
                                    <Col v-if="banner.contentType === 'live'">
                                    <Select id="contentId" :options="liveOptions" v-model="banner.contentId"
                                        label="Selecione a live" placeholder="Selecione a live" />
                                    </Col>
                                    <Col v-if="banner.contentType === 'content'">
                                    <Select id="contentId" :options="contentOptions" v-model="banner.contentId"
                                        label="Selecione o conteúdo" placeholder="Selecione o conteúdo" />
                                    </Col>
                                    <Col v-if="banner.contentType === 'module'">
                                    <Select id="contentId" :options="moduleOptions" v-model="banner.contentId"
                                        label="Selecione o módulo" placeholder="Selecione o módulo" />
                                    </Col>
                                    <Col v-if="banner.contentType === 'link'">
                                    <Input id="contentId" label="Informe o link" type="url" placeholder="Informe o link"
                                        v-model="banner.urlContent" />
                                    </Col>
                                </template>
                            </template>
                            <Col v-if="i !== 0" class="mt-2">
                            <DefaultButton text="" icon="fas fa-trash" status="danger" alt="Excluir Banner"
                                title="Excluir Banner" @click="deleteBanner(banner._id, banner.position)" />
                            </Col>
                        </Row>
                    </AccordionItem>
                </template>
            </Accordion>

            <Col class="mt-2">
            <DefaultButton text="" icon="fas fa-plus-circle" status="success" alt="Adicionar Banner"
                title="Adicionar Banner" @click="addBanner" />
            </Col>
        </Row>
        <div class="modal-body__footer">
            <DefaultButton text="Cancelar" outline @click="$emit('closeModal', false)" />
            <DefaultButton text="Salvar" status="success" icon="fas fa-check" @click="saveBanner" />
        </div>
    </ConfirmModal>
</template>

<script>
import axios from "axios";
import Col from "../../../../../../js/components/XgrowDesignSystem/Utils/Col.vue";
import Title from "../../../../../../js/components/XgrowDesignSystem/Typography/Title.vue";
import Subtitle from "../../../../../../js/components/XgrowDesignSystem/Typography/Subtitle.vue";
import ConfirmModal from "../../../../../../js/components/XgrowDesignSystem/Modals/ConfirmModal.vue";
import DefaultButton from "../../../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton.vue";
import Row from "../../../../../../js/components/XgrowDesignSystem/Utils/Row.vue";
import ImageUpload from "../../../../../../js/components/XgrowDesignSystem/Form/ImageUpload.vue";
import SwitchButton from "../../../../../../js/components/XgrowDesignSystem/Form/SwitchButton.vue";
import Input from "../../../../../../js/components/XgrowDesignSystem/Form/Input.vue";
import TextInput from "../../../../../../js/components/XgrowDesignSystem/Form/TextInput.vue";
import Select from "../../../../../../js/components/XgrowDesignSystem/Form/Select.vue";
import { mapActions, mapState, mapStores } from "pinia";
import { useDesignStartPage } from "../../../../../../js/store/design-start-page.js";
import Accordion from "../../../../../../js/components/XgrowDesignSystem/Accordion/Accordion.vue";
import AccordionItem from "../../../../../../js/components/XgrowDesignSystem/Accordion/AccordionItem.vue";

export default {
    name: "BannerHighlightModal",
    components: {
        AccordionItem,
        Accordion,
        TextInput, Select,
        Input, SwitchButton, ImageUpload, Row, DefaultButton, ConfirmModal, Subtitle, Title, Col
    },
    props: {
        modal: { type: Boolean, default: false }
    },
    data() {
        return {
            banner: {
                is_video: false,
                position: 0,
                type: "image", // image, video
                urlBanner: "https://site-xgrow.vercel.app/assets/img/banner_1_.jpg",
                active: true,
                hasMessage: false,
                title: null,
                description: null,
                contentType: "course", // course, live, content, module, ?class or ?section
                isExternalLink: false,
                urlContent: null,
                contentId: 0,
            },
            contentTypeOptions: [
                { value: "course", name: "Curso" },
                { value: "live", name: "Live" },
                { value: "content", name: "Conteúdo" },
                { value: "module", name: "Módulo" },
                { value: "lives", name: "Agenda de Eventos" },
                { value: "link", name: "Link Externo" },
            ],
        }
    },
    computed: {
        ...mapStores(useDesignStartPage),
        ...mapState(useDesignStartPage, ['banners', 'removedBanners', 'courseOptions', 'liveOptions', 'contentOptions', 'moduleOptions', 'loadingStore'])
    },
    methods: {
        ...mapActions(useDesignStartPage, ['initStore', 'uploadImage']),
        /** Validate of minimun on create/update Banner */
        validation() {
            // if (this.author.name_author === '')
            //     throw new Error("O nome do autor é obrigatório!")
            // if (this.author.author_email === '')
            //     throw new Error("O email do autor é obrigatório!")
            // if (this.author.author_email && !emailRegex(this.author.author_email))
            //     throw new Error("O email digitado é inválido!")
            // if (this.author.author_insta && !urlRegex(this.author.author_insta))
            //     throw new Error("Você precisa adicionar uma URL válida no link do instagram!");
            // if (this.author.author_linkedin && !urlRegex(this.author.author_linkedin))
            //     throw new Error("Você precisa adicionar uma URL válida no link do linkedin!");
            // if (this.author.author_youtube && !urlRegex(this.author.author_youtube))
            //     throw new Error("Você precisa adicionar uma URL válida no link do youtube!");
        },
        /** Delete Banner Widget */
        addBanner: function () {
            const obj = Object.assign({}, this.banner);
            obj.tempId = this.createUUID();
            obj.position = this.banners[this.banners.length - 1].position + 1;
            this.banners.push(obj);
        },
        /** Delete Banner Widget */
        deleteBanner: function (id, position) {
            if (id) {
                this.removedBanners.push(id);
                this.designStartPageStore.banners = this.banners.filter(banner => banner._id !== id);
            } else {
                this.designStartPageStore.banners = this.banners.filter(banner => banner.position !== position);
            }
            successToast("Banner removido!", `Para concluir a alteração clique em salvar!`);
        },
        /** Create/Update Banner Widget */
        saveBanner: async function () {
            this.loadingStore.setLoading(true);
            try {
                const { fxUrl, fxHeader } = $cookies.get('fxToken');
                if (this.removedBanners.length > 0) {
                    for (const banner of this.removedBanners) {
                        await axios.delete(`${fxUrl}/producer/mainpage/banners/${banner}`, fxHeader);
                    }
                }
                this.designStartPageStore.removedBanners = [];
                await axios.post(`${fxUrl}/producer/mainpage/banners`, this.banners, fxHeader);
                this.$emit('closeModal', false)
                successToast("Dados salvos com sucesso!", `O Banner de Destaque foi cadastrado com sucesso!`);
            } catch (e) {
                errorToast("Algum erro aconteceu!", e.response?.data.error.message ?? e.message ?? "Não foi possível salvar os dados da página inicial, entre em contato com o suporte.");
            }
            this.loadingStore.setLoading();
        },
        /** Change the banner content type */
        changeContentType: function (banner) {
            banner.is_video ? banner.type = 'video' : banner.type = 'image'
        },
        /** Search contents by type */
        searchContentsByType: async function (type, banner = null) {
            this.loadingStore.setLoading(true);
            let index;
            if (banner._id) {
                index = this.banners.findIndex(item => item._id === banner._id)
            } else {
                index = this.banners.findIndex(item => item.tempId === banner.tempId)
            }

            this.banners[index].isExternalLink = false;
            this.banners[index].urlContent = null;

            if (type === 'lives') {
                this.banners[index].contentId = null;
            }
            if (type === 'link') {
                this.banners[index].isExternalLink = true;
                this.banners[index].contentId = null;
            }

            this.loadingStore.setLoading();
        },
        uploadBanner: async function (obj, banner) {
            banner.urlBanner = await this.uploadImage(obj)
        },
        /** UUID Generator */
        createUUID() {
            let s = [];
            let hexDigits = "0123456789abcdef";
            for (var i = 0; i < 36; i++) {
                s[i] = hexDigits.substr(Math.floor(Math.random() * 0x10), 1);
            }
            s[14] = "4"; // bits 12-15 of the time_hi_and_version field to 0010
            s[19] = hexDigits.substr((s[19] & 0x3) | 0x8, 1); // bits 6-7 of the clock_seq_hi_and_reserved to 01
            s[8] = s[13] = s[18] = s[23] = "-";
            return s.join("");
        },
    },
    async created() {
        await this.initStore();
    }
}
</script>

<style lang="scss" scoped>
:deep(.modal-body) {
    justify-content: space-between;
}

:deep(.is-horizontal) {
    width: 100% !important;
}

:deep(.upload-container-button) {
    align-self: center !important;
}
</style>
