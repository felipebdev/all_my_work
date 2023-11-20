<template>
    <template v-for="(widget, i) in banners" :key="i">
        <template v-if="currentBanner === i">
            <Col class="banner-destaque d-flex flex-column justify-content-between"
                :style="`background-image: linear-gradient(to bottom, rgba(0, 0, 0, .9), rgba(0, 0, 0, .1)), url('${widget.urlBanner}')`"
                @mouseover="hover = true" @mouseleave="hover = false">
            <div class="banner-destaque__header d-flex align-items-center gap-3 mt-2">
                <div class="logo fw-bolder py-1 px-3">LOGO</div>
                <div class="search w-100"></div>
                <div class="profile"></div>
                <div class="profile"></div>
            </div>
            <div class="banner-destaque__info">
                <Title is-form-title>{{ widget.title }}</Title>
                <Subtitle is-small>{{ widget.description }}</Subtitle>
                <div v-if="widget.description" class="banner-destaque__info-button mb-3">
                    <i class="fas fa-play"></i><span>Acessar</span>
                </div>
            </div>


            <div class="banner-destaque__button w-100 h-100 d-flex justify-content-center align-items-center"
                v-if="hover">
                <IconButton img-src="/xgrow-vendor/assets/img/icons/edit.svg" title="Editar"
                    @click="modal.active = true" />
            </div>
            </Col>
        </template>
    </template>
    <BannerHighlightModal :modal="modal.active" @close-modal="(val) => { modal.active = val }" />
</template>

<script>
import Col from "../../../../../../js/components/XgrowDesignSystem/Utils/Col";
import Title from "../../../../../../js/components/XgrowDesignSystem/Typography/Title";
import Subtitle from "../../../../../../js/components/XgrowDesignSystem/Typography/Subtitle";
import IconButton from "../../../../../../js/components/XgrowDesignSystem/Buttons/IconButton";
import BannerHighlightModal from "../modals/BannerHighlightModal";
import { mapState, mapStores } from "pinia";
import { useDesignStartPage } from "../../../../../../js/store/design-start-page";

export default {
    name: "BannerHighlight",
    components: {
        BannerHighlightModal,
        IconButton, Subtitle, Title, Col
    },
    watch: {
        banners: {
            deep: true,
            async handler(newWidget) {
                if (newWidget) {
                    if (newWidget.length > 1) {
                        this.rotateBanner(newWidget.length)
                    } else {
                        clearInterval(this.bannerInterval)
                        this.currentBanner = 0;
                    }
                }
            }
        }
    },
    data() {
        return {
            hover: false,
            modal: {
                active: false
            },
            currentBanner: 0,
            intervalInMilliseconds: 5000,
            bannerInterval: false
        }
    },
    computed: {
        ...mapStores(useDesignStartPage),
        ...mapState(useDesignStartPage, ['banners'])
    },
    methods: {
        rotateBanner: function (qty) {
            clearInterval(this.bannerInterval)
            this.bannerInterval = setInterval(() => {
                if (this.currentBanner < qty - 1) {
                    ++this.currentBanner;
                } else {
                    this.currentBanner = 0;
                }
            }, this.intervalInMilliseconds)
        }
    }
}
</script>

<style lang="scss" scoped>
.banner-destaque {
    height: 300px;
    background-repeat: no-repeat;
    background-size: cover;
    position: relative;

    &__header {

        .logo {
            background-color: #B2B2B2;
            color: #747474;
        }

        .search {
            height: 20px;
            border-radius: 50px;
            background-color: #B2B2B2;
        }

        .profile {
            width: 20px;
            min-width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #B2B2B2;
        }
    }

    &__info {
        position: relative;
        z-index: 2;
        color: #FFFFFF !important;

        &-button {
            width: 96px;
            height: 30px;
            border-radius: 5px;
            background-color: #f2aa0e;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.25rem;
            font-size: .825em;
        }
    }

    &__button {
        position: absolute;
        background-color: rgba(0, 0, 0, .8);
        top: 0;
        left: 0;
        z-index: 5;

        button {
            background-color: transparent;
            filter: brightness(0) saturate(100%) invert(61%) sepia(88%) saturate(383%) hue-rotate(34deg) brightness(92%) contrast(93%);
            border: 2px solid;

            img {
                height: 24px;
            }
        }
    }
}
</style>
