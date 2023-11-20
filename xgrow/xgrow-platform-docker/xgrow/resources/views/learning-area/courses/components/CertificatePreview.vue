<template>
    <div class="ct-preview">
        <div v-if="certificateStyle === 'default'">
            <img src="/xgrow-vendor/assets/img/certificate.svg" alt="certificate" class="w-100"/>
        </div>
        <div class="ct-content" v-if="certificateStyle === 'custom' && !certificateImg">
            <img src="/xgrow-vendor/assets/img/icons/magic.svg" alt="icon"/>
            <Title>A pré-visualização está vazia!</Title>
            <Subtitle>Preencha os campos ao lado com os detalhes do<br>certificado para começar a visualização.
            </Subtitle>
        </div>
        <div v-show="certificateStyle === 'custom' && certificateImg" class="ct-preview__custom"
             :style="getNameReference">
            <p :style="`color: ${colorName}; font-family: '${fontFamily}'`">Nome do aluno</p>
            <img src="" alt="certificate" id="customImg" class="w-100" ref="customImg"/>
        </div>
    </div>
</template>

<script>
import Subtitle from "../../../../js/components/XgrowDesignSystem/Typography/Subtitle";
import Title from "../../../../js/components/XgrowDesignSystem/Typography/Title";

export default {
    name: "CertificatePreview",
    components: {Title, Subtitle},
    props: {
        certificateStyle: {type: String},
        placeName: {type: String, default: 'center'},
        colorName: {type: String, default: '#000000'},
        fontFamily: {type: String, default: 'Open Sans'},
        certificateImg: {default: null}
    },
    watch: {
        certificateImg: function (old, _) {
            if (this.certificateImg !== null) this.updateCertificate();
        }
    },
    computed: {
        getNameReference: function () {
            if (this.placeName === 'center') return 'justify-content: center; align-items: center;'
            if (this.placeName === 'top_right') return 'justify-content: flex-end; align-items: flex-start;'
            if (this.placeName === 'top_left') return 'justify-content: flex-start; align-items: flex-start;'
            if (this.placeName === 'bottom_right') return 'justify-content: flex-end; align-items: flex-end;'
            if (this.placeName === 'bottom_left') return 'justify-content: flex-start; align-items: flex-end;'
        }
    },
    methods: {
        updateCertificate: function () {
            let x = document.getElementById('customImg')
            let fr = new FileReader()
            fr.onload = function () {
                x.src = fr.result
            }
            fr.readAsDataURL(this.certificateImg)
        }
    }
}
</script>

<style lang="scss" scoped>
.ct-preview {
    background: #2A2E39;
    border: 5px dashed #626775;
    min-height: 344px;
    display: flex;
    align-items: center;
    justify-content: center;

    .ct-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        padding: 12px;

        img {
            opacity: .1;
        }

        h1 {
            font-family: 'Open Sans', serif;
            font-style: normal;
            font-weight: 700;
            font-size: 1.5rem;
            line-height: 2rem;
            text-align: center;
            color: #A1A5AF;
        }

        h3 {
            font-family: 'Open Sans', serif;
            font-style: normal;
            font-weight: 600;
            font-size: 1.125rem;
            line-height: 1.5rem;
            text-align: center;
            color: #717686;
        }
    }

    &__custom {
        position: relative;
        display: flex;

        p {
            position: absolute;
            padding: 1rem;
        }
    }
}
</style>
