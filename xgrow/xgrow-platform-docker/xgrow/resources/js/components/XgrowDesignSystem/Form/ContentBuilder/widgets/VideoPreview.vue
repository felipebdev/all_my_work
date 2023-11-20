<template>
    <div class="link-widget">
        <div class="link-widget__icon">
            <img class="btn-widget-drag" src="/xgrow-vendor/assets/img/widgets/svg/apps.svg"/>
            <img class="icon" src="/xgrow-vendor/assets/img/icons/mdi-video-outline.svg" />
        </div>

        <p>{{video_url}}</p>

        <div class="link-widget__actions">
            <span @click="$emit('edit')">
                <i class="fas fa-pen edit" />
            </span>
            <div class="pipe"></div>
            <span @click="$emit('delete')">
                <i class="fas fa-trash delete" />
            </span>
        </div>
    </div>
</template>

<script>
export default {
    name: "link-widget",
    props: {
        video_url: { type: String, required: true },
    },
};
</script>

<style lang="scss" scoped>
.link-widget {
    align-items: center;
    background: #2A2E39;
    border-radius: 4px;
    color: #E7E7E7;
    display: flex;
    font-size: 14px;
    gap: 8px;
    line-height: 1.6;
    padding: 4px 8px;
    position: relative;
    width: 100%;

    &:hover {
        .link-widget__actions { display: flex; }

        .link-widget__icon {
            & > .btn-widget-drag { display: block; }

            & > .icon { display: none; }
        }
    }

    &__icon {
        color: #646D85;
        font-size: 18px;
        height: 24px;
        line-height: 24px;
        width: 24px;
        display: flex;
        justify-content: center;
        align-items: center;

        & > .icon {
            display: block;
            filter: brightness(0) saturate(100%) invert(44%) sepia(9%) saturate(973%) hue-rotate(186deg) brightness(93%) contrast(89%);
        }

        & > .btn-widget-drag { display:  none; }

        &:hover {
            cursor: grab;

            &:active { cursor: grabbing; }
        }

    }

    &__actions {
        align-items: center;
        background: linear-gradient(269.89deg, rgba(#333844, .85) 22.33%, rgba(#333844, 0) 90.13%);
        border-radius: inherit;
        display: none;
        gap: 8px;
        height: 100%;
        justify-content: flex-end;
        padding: 8px;
        position: absolute;
        right: 0;
        top: 0;
        width: calc(100% - 32px);

        .pipe {
            background:#222429;
            height: 14px;
            width: 1px;
        }

        .edit, .delete { cursor: pointer; }

        .edit { color: #ADDF45; }

        .delete { color: #F96C6C; }
    }
}
</style>
