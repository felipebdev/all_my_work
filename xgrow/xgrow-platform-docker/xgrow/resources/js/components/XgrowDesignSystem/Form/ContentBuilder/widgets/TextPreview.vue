<template>
    <div class="text-widget">
        <div class="text-widget__icon">
            <img class="btn-widget-drag" src="/xgrow-vendor/assets/img/widgets/svg/apps.svg"/>
        </div>

        <pre>{{text}}</pre>

        <div class="text-widget__actions">
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
    name: "text-widget",
    props: {
        text: { type: String, required: true },
    },
};
</script>

<style lang="scss" scoped>
.text-widget {
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
        .text-widget__actions, .text-widget__icon { display: flex; }
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

    pre {
        font-family: var(--bs-font-sans-serif);
        font-size: 14px;
        line-height: 1.6;
    }
}
</style>
