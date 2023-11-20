<template>
    <div class="file-widget">
        <div class="file-widget__icon">
            <img class="btn-widget-drag" src="/xgrow-vendor/assets/img/widgets/svg/apps.svg"/>
            <i class="fas fa-file"></i>
        </div>

        <p>{{File.name}}</p>

        <div class="file-widget__actions">
            <span @click="$emit('edit', data)">
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
    name: "file-widget",
    props: {
        File: { type: Object, required: true },
    },
};
</script>

<style lang="scss" scoped>
.file-widget {
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
        .file-widget__actions { display: flex; }

        .file-widget__icon {
            & > img { display: block; }

            & > svg { display: none; }
        }
    }

    &__icon {
        color: #646D85;
        font-size: 18px;
        height: 24px;
        line-height: 24px;
        width: 24px;

        & > img { display: none; }

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
