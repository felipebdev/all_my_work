<template id='uploadImage'>
    <div class="text-start">
        <Title :is-form-title="true">{{ title }}</Title>
        <Subtitle class="upload-image__subtitle" v-html="subtitle" />
        <div class='col-sm-12 col-md-12 col-lg-12 mb-3 upload-image gap-3 flex-md-nowrap justify-content-start'>
            <div class='upload-image__thumb' :class="{ 'is-vertical': isVertical, 'is-horizontal': isHorizontal }">
                <img :src='imgSrc' class='my-3 upload-image__img' alt='upload image' :ref="'img-' + refer"
                    :id="'img-' + refer" v-show='imgSrc'
                    :class="{ 'is-vertical': isVertical, 'is-horizontal': isHorizontal }" />

                <img v-if='!imgSrc' :src="getImage" class="upload-image__img" alt="Imagem default"
                    :class="{ 'is-vertical': isVertical, 'is-horizontal': isHorizontal }">
            </div>
            <div class='d-flex align-items-center gap-3 flex-wrap justify-content-start upload-container-button'>
                <button type='button' @click="$refs['file-' + refer].click()" class='btn upload-image__btn'>
                    <i class="fa fa-solid fa-upload"></i> {{ btnTitle }}
                </button>
            </div>
            <input type='file' class='upload-image__input-file' id='file' :ref="'file-' + refer" :accept='accept'
                @change='preview' />
        </div>
    </div>
</template>

<script>
import Subtitle from '../Typography/Subtitle'
import Title from "../Typography/Title";

export default {
    name: 'ImageUpload',
    components: { Title, Subtitle },
    props: {
        title: { required: true, type: String },
        subtitle: { required: true, type: String },
        refer: { required: true, type: String },
        src: { type: String, default: "" },
        btnTitle: { default: 'Upload', type: String },
        accept: { default: 'image/*', type: String },
        isHorizontal: { type: Boolean, default: false },
        isVertical: { type: Boolean, default: false }
    },
    emits: ['sendImage'],
    watch: {
        src: function (newVal, _) {
            if (newVal)
                this.imgSrc = newVal
        }
    },
    data() {
        return {
            imgSrc: '',
        }
    },
    computed: {
        getImage: function () {
            if (!this.imgSrc && this.isVertical)
                return '/xgrow-vendor/assets/img/logo/vertical-image.svg';
            if (!this.imgSrc && this.isHorizontal)
                return '/xgrow-vendor/assets/img/logo/horizontal-image.svg';
            if (!this.imgSrc && !this.isHorizontal && !this.isVertical)
                return 'https://las.xgrow.com/background-default.png';

            return 'https://las.xgrow.com/background-default.png';
        }
    },
    methods: {
        preview: function () {
            let file = this.$refs['file-' + this.refer]
            let img = this.$refs['img-' + this.refer]
            this.imgSrc = img
            let fr = new FileReader()
            fr.onload = function () {
                img.src = fr.result
            }
            fr.readAsDataURL(file.files[0])
            this.sendImage()
        },
        sendImage: function () {
            this.$emit('sendImage', {
                file: this.$refs['file-' + this.refer],
                name: 'file-' + this.refer,
            })
        }
    },
    mounted() {
        this.imgSrc = this.$props.src
    }
}
</script>

<style lang='scss' scoped>
.upload-image__subtitle {
    font-family: 'Open Sans', serif;
    font-style: italic;
    font-weight: 400;
    font-size: 0.75rem;
    line-height: 1rem;
    color: #E7E7E7;
}

.upload-image {
    display: flex;
    flex-direction: column;
    width: 100%;
    column-gap: 1rem;
    flex-wrap: wrap;

    &__btn {
        display: flex;
        gap: 0.75rem;
        justify-content: center;
        align-items: center;
        width: 128px;
        color: #FFFFFF;
        border: 1px solid #FFFFFF;
        border-radius: 8px;
        height: 48px;

        &:active,
        &:focus {
            border: 1px solid #FFFFFF;
            outline: none;
            box-shadow: none;
        }

        &:hover {
            background-color: #93BC1E;
            border-color: #93BC1E;

            &:active {
                background-color: #93BC1E;
            }
        }
    }

    &__thumb {
        width: 128px;
        height: 128px;
        background-color: #252932;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 8px;
        padding: .5rem;
    }

    &__img {
        width: 100%;
        object-fit: cover;
        border-radius: inherit;
    }

    &__placeholder {
        color: #333844;
        font-size: 30px;
    }

    &__input-file {
        display: none;
    }
}

.is-vertical {
    width: 122px !important;
    height: 183px !important;
    padding: 5px;
}

.is-horizontal {
    width: 183px !important;
    height: 122px !important;
    padding: 5px;
}
</style>
