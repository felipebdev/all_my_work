<template>
    <div class="title-widget">
        <div class="title-widget__icon">
            <img class="btn-widget-drag" src="/xgrow-vendor/assets/img/widgets/svg/apps.svg"/>
        </div>

        <component
            :is="text_type" class="title-widget__title"
            :class="{
                'title-widget__title--h2': text_type == 'h2',
                'title-widget__title--h3': text_type == 'h3'
            }"
        >
            {{text}}
        </component>

        <div class="title-widget__actions">
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
    name: "title-widget",
    props: {
        text: { type: String, required: true },
        text_type: { type: String, required: true },
    },
};
</script>

<style lang="scss" scoped>
.title-widget {
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
        .title-widget__actions, .title-widget__icon { display: flex; }
    }

    &__title {
        color: #FFFFFF;
        font-size: 20px;
        font-weight: 700;
        line-height: 1.6;
        margin: 0;

        &--h2 {
            font-size: 16px;
            font-weight: 600;
        }

        &--h3 {
            font-size: 14px;
            font-weight: 600;
        }
    }

    &__icon {
        display: none;
        height: 24px;
        width: 24px;

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
