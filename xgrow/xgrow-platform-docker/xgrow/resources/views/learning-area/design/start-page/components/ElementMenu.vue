<template>
    <button class="widget-button my-1">
        <Title is-form-title :icon="icon" icon-color="#FFFFFF" icon-bg="#3f4450" class="m-0">{{ title }}</Title>
    </button>
</template>

<script>
import Title from "../../../../../js/components/XgrowDesignSystem/Typography/Title.vue";

export default {
    name: "ElementMenu",
    components: { Title },
    props: {
        icon: { type: String, default: null },
        title: { type: String, required: true },
    }
}
</script>

<style lang="scss" scoped>
.widget-button {
    width: 100%;
    border: none;
    background: #343945;
    border-radius: 5px;
    padding: 12px;
}
</style>
