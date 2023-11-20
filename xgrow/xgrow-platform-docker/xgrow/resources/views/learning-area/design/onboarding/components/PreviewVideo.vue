<template>
    <div class="ob-video">
        <p class="ob-video__title">{{ values.order }}. {{ values.title ?? '1. Comece por aqui' }}</p>
        <p class="ob-video__subtitle">{{ values.subtitle ?? '' }}</p>
        <p class="ob-video__content">
            <img src="/xgrow-vendor/assets/img/live/player.svg" alt="Player icon" class="w-100">
        </p>
        <div class="ob-video__cta gap-3 flex-wrap" v-if="values.callToActionButton ?? false">
            <DefaultButton
                text="Talvez depois"
                status="info"
                :class="{'w-100': device === 'mobile'}"
            />
            <DefaultButton
                :text="values.callToActionLabel ?? ''"
                status="success"
                :class="{'w-100': device === 'mobile'}"
            />
        </div>
        <hr>
        <div class="ob-video__step gap-3">
            <DefaultButton text="" status="info" icon="fas fa-arrow-left" :class="{'w-100': device === 'mobile'}"/>
            <DefaultButton text="" status="success" icon="fas fa-arrow-right" :class="{'w-100': device === 'mobile'}"/>
        </div>
    </div>
</template>

<script>
import DefaultButton from "../../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";

export default {
    name: "PreviewVideo",
    components: {DefaultButton},
    props: {
        values: {type: Object, required: true},
        device: {type: String, default: 'desktop'}
    },
}
</script>

<style lang="scss" scoped>
.ob-video {
    &__title {
        font-weight: 600;
        font-size: 1rem;
        line-height: 2rem;
    }

    &__subtitle {
        font-weight: 600;
        font-size: 0.85rem;
        line-height: 1.5rem;
    }

    &__content {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem 0;
    }

    &__cta, &__step {
        display: flex;
        margin: 20px 0 30px 0;
        justify-content: space-between;
    }
}
</style>
