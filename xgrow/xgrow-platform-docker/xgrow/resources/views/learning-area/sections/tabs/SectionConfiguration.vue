<template>
    <div>
        <LoadingStore />
        <Container>
            <template v-slot:header-left>
                <Title>Configurações</Title>
            </template>

            <template v-slot:content>
                <Row>
                    <Col lg="8" xl="8">
                        <Row>
                            <Col>
                                <Title is-form-title>Informações básicas</Title>
                                <Input
                                    id="sectionTitle"
                                    label="Nome da seção"
                                    placeholder="Insira o nome da seção..."
                                    v-model="section.title"
                                />
                            </Col>
                            <Col>
                                <TextInput
                                    id="sectionDescription"
                                    label="Descrição (opcional)"
                                    v-model="section.description"
                                    :limit="250"
                                    placeholder="Insira a descrição da seção..."
                                />
                            </Col>
                        </Row>

                        <Row class="mt-3" v-if="false">
                            <Col>
                                <Title is-form-title>Comentários</Title>
                                <SwitchButton
                                    id="hasOfferLink"
                                    v-model="section.has_offer_link"
                                    @input="toggleOffer"
                                >
                                    Ativar comentários
                                </SwitchButton>
                            </Col>
                        </Row>
                    </Col>
                    <Col lg="4" xl="4">
                        <Row class="mt-3">
                            <Col>
                                <ImageUpload
                                    title="Imagem de capa - vertical"
                                    subtitle="As imagens devem ser na proporção 9:16 com as dimensões de 848 x 1280 e até 2MB"
                                    refer="verticalImage"
                                    ref="verticalImage"
                                    :src="section.thumb_vertical"
                                    @send-image="receiveVerticalImg"
                                    isVertical
                                />
                            </Col>
                            <Col class="mt-4">
                                <ImageUpload
                                    title="Imagem de capa - horizontal"
                                    subtitle="As imagens devem ser na proporção 16:9 com as dimensões de 848 x 1280 e até 2MB"
                                    refer="horizontalImage"
                                    ref="horizontalImage"
                                    :src="section.thumb_horizontal"
                                    @send-image="receiveHorizontalImg"
                                    isHorizontal
                                />
                            </Col>
                        </Row>
                    </Col>
                    <Col>
                        <hr />
                        <div class="d-flex justify-content-between">
                            <DefaultButton
                                text="Cancelar"
                                outline
                                class="w-170"
                                @click="returnPath"
                            />
                            <DefaultButton
                                text="Salvar"
                                status="success"
                                class="w-170"
                                :disable="disableButton"
                                @click="updateSection"
                            />
                        </div>
                    </Col>
                </Row>
            </template>
        </Container>
    </div>
</template>

<script>
import Row from "../../../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../../../js/components/XgrowDesignSystem/Utils/Col";
import Container from "../../../../js/components/XgrowDesignSystem/Cards/Container.vue";
import Title from "../../../../js/components/XgrowDesignSystem/Typography/Title";
import Subtitle from "../../../../js/components/XgrowDesignSystem/Typography/Subtitle";
import PipeVertical from "../../../../js/components/XgrowDesignSystem/Utils/PipeVertical";
import ImageUpload from "../../../../js/components/XgrowDesignSystem/Form/ImageUpload";
import Select from "../../../../js/components/XgrowDesignSystem/Form/Select";
import Input from "../../../../js/components/XgrowDesignSystem/Form/Input";
import TextInput from "../../../../js/components/XgrowDesignSystem/Form/TextInput";
import RadioButton from "../../../../js/components/XgrowDesignSystem/Form/RadioButton";
import SwitchButton from "../../../../js/components/XgrowDesignSystem/Form/SwitchButton";
import DefaultButton from "../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";

import LoadingStore from "../../../../js/components/XgrowDesignSystem/Utils/LoadingStore.vue";

import { mapActions, mapStores, mapState } from "pinia";
import { useSectionsStore } from "../../../../js/store/sections";
import axios from 'axios';

export default {
    name: "Section-Config",
    components: {
        DefaultButton,
        SwitchButton,
        RadioButton,
        Select,
        TextInput,
        Input,
        ImageUpload,
        PipeVertical,
        Subtitle,
        Title,
        Container,
        Col,
        Row,
        LoadingStore,
    },
    data() {
        return {
            disableButton: false
        };
    },
    computed: {
        ...mapStores(useSectionsStore),
        ...mapState(useSectionsStore, ["section"]),
    },
    methods: {
        ...mapActions(useSectionsStore, ["setLoading", "updateSection"]),
        /** Receiver Vertical and Horizontal Img */
        receiveVerticalImg: async function (obj) {
            try {
                const formData = new FormData();
                formData.append('image', obj.file.files[0])
                this.disableButton = true;

                const res = await axios.post(uploadImageURL, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                this.disableButton = false;
                this.section.thumb_vertical = res.data.response.file
            } catch (e) {
                errorToast("Algum erro aconteceu!", e.message);
            }
        },
        receiveHorizontalImg: async function (obj) {
            try {
                const formData = new FormData();
                formData.append('image', obj.file.files[0])
                this.disableButton = true;

                const res = await axios.post(uploadImageURL, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                this.disableButton = false;

                this.section.thumb_horizontal = res.data.response.file
            } catch (e) {
                console.log(e)
                errorToast("Algum erro aconteceu!", e.message);
            }
        },
        returnPath() {
            this.$router.push({name: "section-index"})
        }
    },
    async created() {},
};
</script>

<style scoped>
.w-170 {
    width: 170px;
}
</style>
