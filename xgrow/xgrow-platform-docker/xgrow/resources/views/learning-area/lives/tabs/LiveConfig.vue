<template>
    <Loading :is-open="isLoading" />
    <Row>
        <Col class="my-4">
        <Title>Nova live
            <PipeVertical /> <span class="fw-500">Configurações</span>
        </Title>
        </Col>

        <Col>
        <Title is-form-title>Opções da live</Title>
        </Col>
        <Col sm="12" md="6" lg="4" xl="4">
        <SwitchButton id="hasQuestions" v-model="values.hasQuestions">
            Habilitar perguntas
        </SwitchButton>
        </Col>
        <Col sm="12" md="6" lg="4" xl="4">
        <SwitchButton id="hasComments" v-model="values.hasComments">
            Habilitar comentários
        </SwitchButton>
        </Col>
        <Col sm="12" md="6" lg="4" xl="4">
        <SwitchButton id="enableAutoScroll" v-model="values.enableAutoScroll">
            Rolamento Automático do Chat
        </SwitchButton>
        </Col>


        <Col class="mt-4">
        <Title is-form-title>Detalhes dos comentários</Title>
        </Col>
        <Col sm="12" md="6" lg="6" xl="6">
        <SwitchButton id="isVimeoChat" v-model="values.isVimeoChat">
            Chat do Vimeo
        </SwitchButton>
        </Col>
        <Col v-if="values.isVimeoChat">
        <Input id="embedCode" label="Código embed" v-model="values.embed" placeholder="Código embed" has-clipboard />
        </Col>
        <Col v-if="values.isVimeoChat">
        <Input id="link" label="Link do chat" v-model="values.link" placeholder="Link do chat" />
        </Col>

        <Col class="mt-4">
        <Title is-form-title>Thumbnail</Title>
        </Col>
        <Col sm="12" md="6" lg="6" xl="6">
        <ImageUpload title="Imagem da live - horizontal"
            subtitle="A imagem aparece nos menus de navegação e as vezes, no título da seção.<br>Tamanho: 1280 x 848"
            refer="horizontalImage" ref="horizontalImage" @send-image="receiveHorizontalImage" is-horizontal />
        </Col>
    </Row>
</template>

<script>
import Row from "../../../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../../../js/components/XgrowDesignSystem/Utils/Col";
import SwitchButton from "../../../../js/components/XgrowDesignSystem/Form/SwitchButton";
import Title from "../../../../js/components/XgrowDesignSystem/Typography/Title";
import Input from "../../../../js/components/XgrowDesignSystem/Form/Input";
import ImageUpload from "../../../../js/components/XgrowDesignSystem/Form/ImageUpload.vue";
import PipeVertical from "../../../../js/components/XgrowDesignSystem/Utils/PipeVertical";
import axios from "axios";
import Loading from "../../../../js/components/XgrowDesignSystem/Utils/Loading";

export default {
    name: "LiveConfig",
    components: { Loading, PipeVertical, ImageUpload, Input, Title, SwitchButton, Col, Row },
    props: {
        values: { type: Object, required: true },
    },
    data() {
        return {
            isLoading: false,
        }
    },
    methods: {
        /** Upload Horizontal Image */
        receiveHorizontalImage: async function (obj) {
            try {
                const formData = new FormData();
                formData.append('image', obj.file.files[0])
                this.isLoading = true;
                const res = await axios.post(uploadImageURL, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                this.$props.values.thumbnail = res.data.response.file
                this.isLoading = false;
            } catch (e) {
                errorToast("Algum erro aconteceu!", e.message);
            }
        },
    },
    watch: {
        'values.thumbnail': function () {
            this.$refs.horizontalImage.imgSrc = this.values.thumbnail
        }
    },
}
</script>

<style lang="scss" scoped>
.fw-500 {
    font-weight: 500;
}
</style>
