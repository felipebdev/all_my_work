<template>
    <div class="alert-widget">
        <div class="alert-widget__icon">
            <img class="drag btn-widget-drag" src="/xgrow-vendor/assets/img/widgets/svg/apps.svg"/>
            <img class="icon" src="/xgrow-vendor/assets/img/widgets/svg/alert.svg"/>
        </div>

        <div class="alert-widget__texts">
            <h3>{{alert_title}}</h3>
            <p>{{alert_description}}</p>
        </div>

        <div class="alert-widget__actions">
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
    name: "alert-widget",
    props: {
        alert_title: { type: String, required: true },
        alert_description: { type: String, required: true },
    },
};
</script>

<style lang="scss" scoped>
.alert-widget {
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
        .alert-widget__actions { display: flex; }

        .alert-widget__icon {
            .drag { display: block; }
            .icon { display: none; }
        }

    }


    &__icon {
        height: 24px;
        width: 24px;

        .drag { display: none; }

        .icon { margin-top: -32px; }

        &:hover {
            cursor: grab;

            &:active { cursor: grabbing; }
        }

    }

    &__texts {
        display: flex;
        flex-direction: column;

        & > p, & > h3 {
            font-size: 14px;
            line-height: 1.6;
        }

        & > h3 { font-weight: 700; }
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
