<template>
    <div class="ob-text">
        <p class="ob-text__title">{{ values.order }}. {{ values.title ?? '1. Comece por aqui' }}</p>
        <p class="ob-text__subtitle">{{ values.subtitle ?? '' }}</p>
        <p class="ob-text__content">{{ values.content ?? '' }}</p>
        <div class="ob-text__cta gap-3 flex-wrap" v-if="values.callToActionButton ?? false">
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
        <div class="ob-text__step gap-3">
            <DefaultButton text="" status="info" icon="fas fa-arrow-left" :class="{'w-100': device === 'mobile'}"/>
            <DefaultButton text="" status="success" icon="fas fa-arrow-right" :class="{'w-100': device === 'mobile'}"/>
        </div>
    </div>
</template>

<script>
import DefaultButton from "../../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";


export default {
    name: "PreviewText",
    components: {DefaultButton},
    props: {
        values: {type: Object, required: true},
        device: {type: String, default: 'desktop'}
    },
}
</script>

<style lang="scss" scoped>
.ob-text {
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
        font-weight: 400;
        font-size: 0.875rem;
        line-height: 1.5rem;
        margin: 10px 0;
        color: #C1C5CF;
    }

    &__cta, &__step {
        display: flex;
        margin: 20px 0 30px 0;
        justify-content: space-between;
    }
}
</style>
