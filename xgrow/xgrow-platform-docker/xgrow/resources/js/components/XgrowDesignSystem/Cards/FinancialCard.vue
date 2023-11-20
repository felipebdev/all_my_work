<template>
    <div class="colored-card my-2 w-100" :style="{ borderColor }">
        <p class="title">
            {{ title }}
            <slot></slot>
        </p>
        <div class="bottom-content">
            <p class="subtitle">{{ subtitle }}</p>
            <slot name="button"></slot>
        </div>
    </div>
</template>

<script>
export default {
    name: "FinancialCard",
    props: {
        title: {
            type: [String, Number],
            required: true
        },
        subtitle: {
            type: String,
            required: true
        },
        borderColor: {
            type: String,
            default: '#FFFFFF'
        },
    },
}
</script>

<style lang="scss" scoped>
.colored-card {
    color: #FFFFFF;
    font-family: "Open Sans", serif;
    padding: 1rem 1rem 1rem 1rem;
    background-color: transparent;
    border-radius: .5rem;
    border-width: 2px;
    border-style: solid;
    min-width: 200px;

    .title {
        word-break: normal;
        font-size: 1rem;
        font-weight: 700;
        display: flex;
        gap: .5rem;
        z-index: 9;
    }

    .subtitle {
        word-break: normal;
        font-size: 0.75rem;
    }

    .bottom-content {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
}
</style>
