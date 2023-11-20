<template>
    <section 
        class="indicatorCard d-flex flex-column gap-2"
        :style="`background-color: ${backgroundColor};`"
    >
        <header class="indicatorCard__header" v-if="!!title || !!icon">
            <img :src="localIcon" class="indicatorCard__icon" v-if="localIcon">
            <i :class="`indicatorCard__icon ${icon}`" v-else></i>
            <p class="indicatorCard__title">{{title}}</p>
        </header>
        
        <slot name="container"></slot>

        <slot name="list"></slot>
    </section>
</template>

<script>
export default {
    name: "FinancialIndicatorCard",
    props: {
        title: {required: false, default: "", type: [String, Number]},
        icon: {required: false, default: "", type: String},
        borderColor: {required: false, default: "#FFFFFF", type: String},
        backgroundColor: {required: false, default: "#222429", type: String},
        localIcon: {required: false, default: "", type: String}
    },
    computed: {},
    methods: {
        hasList: function () {
            return !!this.$slots.list;
        }
    },
};
</script>

<style lang="scss" scoped>
    .indicatorCard {
        width: 100%;
        height: 100%;
        border-radius: 10px;
        font-size: 16px;
        line-height: 1.6;
        font-family: 'Open Sans';
        color: #FFFFFF;
        padding: 18px;

        &__header {
            display: flex;
            align-items: center;
            column-gap: 10px;
            width: 100%;
        }

        &__title {
            font-weight: bold;
        }

        &__icon {
            display: inline-block;
            font-size: 1.4rem;
            line-height: 1;
            color: #4E525F;
            max-width: 28px;
        }

        &__list {
            width: 100%;
            padding: 0;
            margin: 0;
        }
    }
</style>
