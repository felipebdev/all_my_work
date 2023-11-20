<template>
    <span>
        <Col class="widget-box d-flex flex-column justify-content-between p-0" @mouseover="hover = true"
            @mouseleave="hover = false">
        <div class="widget-box__header d-flex align-items-center gap-3 mt-2">
            <Title icon="fas fa-clock" is-form-title icon-bg="transparent" icon-color="#FFFFFF">
                Últimos Acessos
            </Title>
        </div>
        <div class="widget-box__content">
            <img src="https://las.xgrow.com/background-default.png" class="widget-img" alt="Imagem padrão de exibição" />
            <div class="widget-box__content-info">
                <Title is-form-title class="mb-0">Título do último conteúdo acessado</Title>
                <div class="stars mt-2 mb-3">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-stroke"></i>
                    <i class="far fa-star"></i>
                </div>
                <div class="widget-box__content-info-button"></div>
            </div>
        </div>
        <div class="widget-box__button w-100 h-100 d-flex justify-content-center align-items-center" v-if="hover">
            <IconButton img-src="/xgrow-vendor/assets/img/icons/trash-alt.svg" title="Excluir" class="btn-widget-delete"
                @click="$emit('deleteWidget', item)" />
            <IconButton img-src="/xgrow-vendor/assets/img/icons/edit.svg" title="Editar" class="btn-widget-edit"
                @click="modal.active = true" />
            <IconButton img-src="/xgrow-vendor/assets/img/icons/arrows-alt.svg" title="Mover" class="btn-widget-drag"
                v-if="blockDrag" />
        </div>
        </Col>

        <LastAccessModal :modal="modal.active" :item="item" @close-modal="(val) => { modal.active = val }" />
    </span>
</template>

<script>
import Col from "../../../../../../js/components/XgrowDesignSystem/Utils/Col";
import Title from "../../../../../../js/components/XgrowDesignSystem/Typography/Title";
import Subtitle from "../../../../../../js/components/XgrowDesignSystem/Typography/Subtitle";
import IconButton from "../../../../../../js/components/XgrowDesignSystem/Buttons/IconButton";
import LastAccessModal from "../modals/LastAccessModal";

export default {
    name: "LastAccess",
    components: { LastAccessModal, IconButton, Subtitle, Title, Col },
    props: {
        item: { type: Object },
        blockDrag: { type: Boolean }
    },
    data() {
        return {
            hover: false,
            modal: {
                active: false
            }
        }
    },
}
</script>

<style lang="scss" scoped>
.widget-box {
    min-height: 50px;
    background-color: #333844; //2a2f39
    position: relative;

    &__header {
        background-color: #2c2d37 !important;
        margin-top: 0 !important;
        padding: 10px 10px 0 !important;
    }

    &__content {
        display: flex;
        overflow: hidden;
        gap: 1rem;
        padding: 1rem;

        &-info {
            h1 {
                font-weight: bold !important;
                font-size: .9rem !important;
                white-space: nowrap;
            }

            .stars {
                color: #ADFF2F;
            }

            &-button {
                width: 80px;
                height: 30px;
                border-radius: 5px;
                background-color: #f2aa0e;
            }
        }

        .widget-img {
            min-width: 180px;
            width: 180px;
            max-height: 120px;
            object-fit: cover;
        }
    }

    &__button {
        position: absolute;
        background-color: rgba(0, 0, 0, .8);
        top: 0;
        left: 0;
        gap: .8rem;

        button {
            background-color: transparent;
            border: 2px solid;
        }

        .btn-widget-edit {
            filter: brightness(0) saturate(100%) invert(61%) sepia(88%) saturate(383%) hue-rotate(34deg) brightness(92%) contrast(93%);
            cursor: cursor;

            :deep(img) {
                height: 15px !important;
            }

            &:hover {
                background-color: rgba(255, 255, 255, .1);
            }
        }

        .btn-widget-drag {
            filter: brightness(0) saturate(100%) invert(100%) sepia(100%) saturate(0%) hue-rotate(288deg) brightness(102%) contrast(102%);
            cursor: move;

            :deep(img) {
                height: 22px !important;
            }

            &:hover {
                background-color: rgba(255, 255, 255, .2);
            }
        }

        .btn-widget-delete {
            filter: brightness(0) saturate(100%) invert(18%) sepia(64%) saturate(4297%) hue-rotate(355deg) brightness(109%) contrast(81%);
            cursor: cursor;

            :deep(img) {
                height: 18px !important;
            }

            &:hover {
                background-color: rgba(255, 255, 255, .2);
            }
        }
    }
}
</style>
