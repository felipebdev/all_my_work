<template>
    <div class="accordion" :id="id">
        <slot></slot>
    </div>
</template>

<script>
export default {
    name: "Accordion",
    props: {
        id: {type: String, required: true}
    }
}
</script>

<style lang="scss" scoped>
</style>
