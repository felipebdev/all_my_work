<template>
    <Row>
        <Col class="d-flex align-items-center justify-content-between">
        <Title>Configurações
            <PipeVertical /> <span class="fw-normal">Certificado</span>
        </Title>
        <SwitchButton id="certificateActive" :value="certificate.active">Ativar certificado</SwitchButton>
        </Col>

        <Col sm="12" md="12" lg="6" xl="6" class="mt-4">
        <Col>
        <Title is-form-title>Estilo do certificado</Title>
        <div class="d-flex gap-3">
            <RadioButton id="chkCertificate_d" option="default" name="certificate_type" label="Utilizar certificado padrão"
                v-model="certificate.style" :checked="certificate.style === 'default'"
                @click="() => { certificate.certificateImg = null }" />
            <RadioButton id="chkCertificate_c" option="custom" name="certificate_type"
                label="Fazer upload do meu certificado" v-model="certificate.style"
                :checked="certificate.style === 'custom'" />
        </div>
        </Col>

        <Col v-if="certificate.style === 'custom'" class="mt-3">
        <FileInput label="Upload do certificado" id="certificateUpload" refer="certificateUpload"
            @send-file="(obj) => this.certificate.certificateImg = obj.file.files[0]" />
        </Col>

        <Col v-if="certificate.style === 'custom'" class="mt-5">
        <Title is-form-title>Posição do nome do aluno</Title>
        <div class="d-flex gap-3 flex-wrap">
            <RadioButton id="position_c" option="center" name="position_name" label="Centro"
                v-model="certificate.positionName" :checked="certificate.positionName === 'center'" />
            <RadioButton id="position_tr" option="top_right" name="position_name" label="Canto superior direito"
                v-model="certificate.positionName" :checked="certificate.positionName === 'top_right'" />
            <RadioButton id="position_tl" option="top_left" name="position_name" label="Canto superior esquerdo"
                v-model="certificate.positionName" :checked="certificate.positionName === 'top_left'" />
            <RadioButton id="position_br" option="bottom_right" name="position_name" label="Canto inferior direito"
                v-model="certificate.positionName" :checked="certificate.positionName === 'bottom_right'" />
            <RadioButton id="position_bl" option="bottom_left" name="position_name" label="Canto inferior esquerdo"
                v-model="certificate.positionName" :checked="certificate.positionName === 'bottom_left'" />
        </div>
        </Col>

        <Col v-if="certificate.style === 'custom'" class="mt-5">
        <Title is-form-title>Tipografia do nome do aluno</Title>
        <Select id="certificateFont" label="Tipografia" placeholder="Selecione o tipo de letra" :options="typefacesOptions"
            v-model="certificate.certificateFont" />
        </Col>

        <Col v-if="certificate.style === 'custom'" class="mt-5">
        <Title is-form-title>Cor do nome do aluno</Title>
        <ColorInput id="fontColor" label="Cor" :color-label="certificate.certificateFontColor"
            v-model="certificate.certificateFontColor" />
        </Col>

        <Col v-if="certificate.style === 'default'" class="mt-5">
        <Title is-form-title>Assinatura</Title>
        <div class="d-flex gap-3">
            <RadioButton id="chkSignature_d" option="default" name="certificate_signature" label="Fazer upload"
                v-model="certificate.signature" :checked="certificate.signature === 'default'" />
            <RadioButton id="chkSignature_c" option="custom" name="certificate_signature"
                label="Escrever a minha assinatura" v-model="certificate.signature"
                :checked="certificate.signature === 'custom'" />
        </div>
        </Col>

        <Col v-if="certificate.style === 'default' && certificate.signature === 'default'" class="mt-3">
        <FileInput label="Upload de assinatura" id="signatureUpload" refer="signatureUpload"
            @send-file="(obj) => this.certificate.signatureImg = obj.file.files[0]" />
        </Col>

        <Col v-if="certificate.style === 'default' && certificate.signature === 'custom'" class="mt-3">
        <div class="signature-box">
            <VPerfectSignature :stroke-options="strokeOptions" pen-color="#000000" ref="signaturePad" />
        </div>
        <div class="d-flex gap-3">
            <DefaultButton text="Limpar assinatura" status="info" @click="clearSignature" />
            <DefaultButton text="Salvar assinatura" status="success" @click="downloadSignature" />
        </div>
        </Col>

        <Col v-if="certificate.style === 'default'" class="mt-5">
        <SwitchButton id="certificateLogo" v-model="certificate.customLogo">Personalizar logo</SwitchButton>
        <Subtitle>Esta opção permite que você utilize seu próprio logo no certificado.</Subtitle>
        </Col>

        <Col v-if="certificate.style === 'default' && certificate.customLogo" class="mt-3">
        <ImageUpload title="Logo" subtitle="Tamanho máximo: 240x240" refer="certificateLogo" ref="certificateLogo"
            @send-image="() => { }" />
        </Col>

        <Col v-if="certificate.style === 'default'" class="mt-5">
        <Title is-form-title>Descrição do certificado</Title>
        <TextInput id="certificateDescription" label="Descrição" :limit="250"
            placeholder="Digite aqui a descrição do certificado" v-model="certificate.description" />
        </Col>
        </Col>

        <Col sm="12" md="12" lg="6" xl="6" class="mt-4">
        <CertificatePreview :certificate-style="certificate.style" :certificate-img="certificate.certificateImg"
            :place-name="certificate.positionName" :color-name="certificate.certificateFontColor"
            :font-family="certificate.certificateFont" />
        </Col>

    </Row>
</template>

<script>
import Row from "../../../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../../../js/components/XgrowDesignSystem/Utils/Col";
import Container from "../../../../js/components/XgrowDesignSystem/Cards/Container";
import Title from "../../../../js/components/XgrowDesignSystem/Typography/Title";
import Subtitle from "../../../../js/components/XgrowDesignSystem/Typography/Subtitle";
import PipeVertical from "../../../../js/components/XgrowDesignSystem/Utils/PipeVertical";
import SwitchButton from "../../../../js/components/XgrowDesignSystem/Form/SwitchButton";
import RadioButton from "../../../../js/components/XgrowDesignSystem/Form/RadioButton";
import DefaultButton from "../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import ImageUpload from "../../../../js/components/XgrowDesignSystem/Form/ImageUpload";
import TextInput from "../../../../js/components/XgrowDesignSystem/Form/TextInput";
import CertificatePreview from "../components/CertificatePreview";
import VPerfectSignature from 'v-perfect-signature';
import FileInput from "../../../../js/components/XgrowDesignSystem/Form/FileInput";
import Select from "../../../../js/components/XgrowDesignSystem/Form/Select";
import ColorInput from "../../../../js/components/XgrowDesignSystem/Form/ColorInput";

export default {
    name: "ConfigContentCertificate",
    components: {
        ColorInput,
        FileInput,
        VPerfectSignature,
        CertificatePreview,
        TextInput,
        ImageUpload,
        DefaultButton,
        RadioButton,
        SwitchButton, Select,
        PipeVertical, Subtitle, Title, Container, Col, Row
    },
    data() {
        return {
            certificate: {
                active: false,
                style: 'default', //default or custom
                certificateImg: 'https://las.xgrow.com/background-default.png',
                signature: 'default', //default or custom
                signatureImg: null,
                customLogo: false,
                logoImg: 'https://las.xgrow.com/background-default.png',
                positionName: 'center',
                certificateFont: 'Open Sans',
                certificateFontColor: '#FFFFFF',
                description: ''
            },
            strokeOptions: {
                size: 16,
                thinning: 0.75,
                smoothing: 0.5,
                streamline: 0.5,
            },
            typefacesOptions: [
                { value: 'Open Sans', name: 'Open Sans' },
                { value: 'Arial', name: 'Arial' },
                { value: 'Times New Roman', name: 'Times New Roman' },
            ]
        }
    },
    methods: {
        /** Download the signature */
        downloadSignature: function () {
            this.certificate.signatureImg = this.$refs.signaturePad.toDataURL();
        },
        /** Clear the signature */
        clearSignature: function () {
            this.$refs.signaturePad.clear()
        },
        receiveSignatureImg: function (obj) {
            this.certificate.signatureImg = obj.file.files[0]
        },
    },
}
</script>

<style scoped>
.signature-box {
    background: #FFFFFF;
    border: 5px dashed #626775;
    margin-bottom: 10px;
}
</style>
