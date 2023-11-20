<template>
    <span>
        <Col class="widget-box d-flex flex-column justify-content-between p-0" @mouseover="hover = true"
            @mouseleave="hover = false">
        <div class="widget-box__header d-flex align-items-center gap-3 mt-2">
            <Title icon="fas fa-trophy" is-form-title icon-bg="transparent" icon-color="#FFFFFF">
                Top mais assistidos
            </Title>
        </div>
        <div class="widget-box__content overflow-hidden">
            <template v-for="(obj, i) in item.topWatchedForceItems" :key="i">
                <template v-if="obj.verticalImage || obj.horizontalImage">
                    <img :src="obj.horizontalImage" class="widget-img" alt="Imagem do conteúdo" />
                </template>
                <template v-else>
                    <img src="https://las.xgrow.com/background-default.png" class="widget-img"
                        alt="Imagem padrão da Xgrow" />
                </template>
            </template>
            <template v-if="item.topWatchedForceItems.length === 0">
                <Title is-form-title>Seleção automática.</Title>
            </template>
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

        <TopWatchedModal :modal="modal.active" :item="item" @close-modal="(val) => { modal.active = val }" />
    </span>
</template>

<script>
import Col from "../../../../../../js/components/XgrowDesignSystem/Utils/Col.vue";
import Title from "../../../../../../js/components/XgrowDesignSystem/Typography/Title.vue";
import Subtitle from "../../../../../../js/components/XgrowDesignSystem/Typography/Subtitle.vue";
import IconButton from "../../../../../../js/components/XgrowDesignSystem/Buttons/IconButton.vue";
import TopWatchedModal from "../modals/TopWatchedModal.vue";
import { GET_COURSE_BY_PARAMS_QUERY_AXIOS } from "../../../../../../js/graphql/queries/courses";
import { axiosGraphqlClient } from "../../../../../../js/config/axiosGraphql";
import { GET_CONTENT_BY_ID_QUERY_AXIOS } from "../../../../../../js/graphql/queries/contents";

export default {
    name: "TopWatched",
    components: { TopWatchedModal, IconButton, Subtitle, Title, Col },
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
    mounted() {
        this.$props.item.topWatchedForceItems.forEach(async item => {
            const isCourse = item?.isCourse
            if (isCourse) {
                const courseQuery = {
                    "query": GET_COURSE_BY_PARAMS_QUERY_AXIOS,
                    "variables": { id: item.itemId }
                };
                const res = await axiosGraphqlClient.post(contentAPI, courseQuery);
                const course = res.data.data.courses.data[0];
                const index = this.$props.item.topWatchedForceItems.findIndex(item => item.itemId == course.id)
                this.$props.item.topWatchedForceItems[index].horizontalImage = course.horizontal_image
                this.$props.item.topWatchedForceItems[index].verticalImage = course.vertical_image
            } else {
                const contentQuery = {
                    "query": GET_CONTENT_BY_ID_QUERY_AXIOS,
                    "variables": { id: item.itemId }
                };
                const res = await axiosGraphqlClient.post(contentAPI, contentQuery);
                const content = res.data.data.content;
                const index = this.$props.item.topWatchedForceItems.findIndex(item => item.itemId == content.id)
                this.$props.item.topWatchedForceItems[index].horizontalImage = content.horizontal_image
                this.$props.item.topWatchedForceItems[index].verticalImage = content.vertical_image
            }
        });
    }
}
</script>

<style lang="scss" scoped>
.widget-box {
    min-height: 50px;
    background-color: #333844; //2a2f39
    position: relative;
    padding: 0 !important;

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
