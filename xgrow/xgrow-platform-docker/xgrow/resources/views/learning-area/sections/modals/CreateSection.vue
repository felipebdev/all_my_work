<template>
    <ConfirmModal :is-open="isOpen">
        <Row class="w-100">
            <Title class="justify-content-center">Nova seção</Title>
            <Subtitle class="justify-content-center">Insira os dados da nova seção:</Subtitle>
        </Row>
        <div class="modal-body__content">
            <Row>
                <Col class="mb-2">
                <Input id="author_name" label="Título" v-model="title" placeholder="Insira o título da seção..." />
                </Col>
                <Col xl="6" lg="6">
                <ImageUpload title="Imagem de capa - vertical"
                    subtitle="As imagens devem ser na proporção 9:16 com as dimensões de 848 x 1280 e até 2MB"
                    refer="verticalImage" ref="verticalImage" @send-image="receiveVerticalImage" isVertical />
                </Col>
                <Col xl="6" lg="6">
                <ImageUpload title="Imagem de capa - horizontal"
                    subtitle="As imagens devem ser na proporção 16:9 com as dimensões de 848 x 1280 e até 2MB"
                    refer="horizontalImage" ref="horizontalImage" @send-image="receiveHorizontalImage" isHorizontal />
                </Col>
            </Row>
        </div>
        <HeaderLine />
        <div class="modal-body__footer mt-0">
            <hr>
            <DefaultButton text="Cancelar" outline @click="$emit('close')" />
            <DefaultButton text="Salvar" :disabled="disableButton" status="success" @click="saveSection" />
        </div>
    </ConfirmModal>
</template>

<script>
import ConfirmModal from "../../../../js/components/XgrowDesignSystem/Modals/ConfirmModal";
import DefaultButton from "../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import Col from '../../../../js/components/XgrowDesignSystem/Utils/Col.vue';
import Row from '../../../../js/components/XgrowDesignSystem/Utils/Row.vue';
import Title from '../../../../js/components/XgrowDesignSystem/Typography/Title.vue';
import Subtitle from '../../../../js/components/XgrowDesignSystem/Typography/Subtitle.vue';
import Input from '../../../../js/components/XgrowDesignSystem/Input.vue';
import ImageUpload from '../../../../js/components/XgrowDesignSystem/Form/ImageUpload.vue';
import axios from 'axios';
import HeaderLine from "../../../../js/components/XgrowDesignSystem/Utils/HeaderLine.vue";

export default {
    name: "modal-create-section",
    components: {
        ConfirmModal,
        DefaultButton,
        Col,
        Row,
        Title,
        Subtitle,
        Input,
        ImageUpload,
        HeaderLine
    },
    data() {
        return {
            title: "",
            thumb_vertical: 'https://las.xgrow.com/background-default.png',
            thumb_horizontal: 'https://las.xgrow.com/background-default.png',
            disableButton: false
        }
    },
    props: {
        isOpen: { type: Boolean, required: true },
    },
    methods: {
        saveSection() {
            try {
                this.validation();

                this.$emit('confirm', {
                    title: this.title,
                    thumb_vertical: this.thumb_vertical,
                    thumb_horizontal: this.thumb_horizontal,
                });
            } catch (message) {
                errorToast("Não foi possível realizar essa operação", message)
            }
        },
        /** Upload Vertical Image */
        receiveVerticalImage: async function (obj) {
            this.disableButton = true;

            try {
                const formData = new FormData();
                formData.append('image', obj.file.files[0])
                this.isLoading = true;
                const res = await axios.post(uploadImageURL, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                this.isLoading = false;
                this.thumb_vertical = res.data.response.file
            } catch (e) {
                errorToast("Algum erro aconteceu!", e.message);
            }

            this.disableButton = false;
        },
        /** Upload Horizontal Image */
        receiveHorizontalImage: async function (obj) {
            this.disableButton = true;

            try {
                const formData = new FormData();
                formData.append('image', obj.file.files[0])
                this.isLoading = true;
                const res = await axios.post(uploadImageURL, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                this.isLoading = false;
                this.thumb_horizontal = res.data.response.file
            } catch (e) {
                errorToast("Algum erro aconteceu!", e.message);
            }

            this.disableButton = false;
        },
        validation() {
            if (this.title == "")
                throw new Error("O campo de título é obrigatório");
        }
    }
}
</script>

<style lang="scss" scoped>
:deep(.modal-body) {
    gap: 0 !important;

    .modal-body__content {
        background: #333844;
        border-radius: 10px;
        padding: 20px;
    }
}

.modal-body__footer {
    border-top: 1px solid #393D49;
    padding-top: 24px;
}
</style>
