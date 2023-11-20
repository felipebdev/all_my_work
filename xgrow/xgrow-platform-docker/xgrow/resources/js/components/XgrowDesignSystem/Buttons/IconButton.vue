<template>
    <button class="xgrow-button export-button me-1" :class="buttonClass" :title="title">
        <img v-if="imgSrc" :src="imgSrc" :alt="title">
        <i v-if="icon" :class="`${icon} ${iconClass}`"></i>
    </button>
</template>

<script>
export default {
    name: "IconButton",
    props: {
        imgSrc: { type: String },
        title: { type: String },
        buttonClass: { type: String },
        icon: { type: String },
        iconClass: { type: String }
    }
}
</script>

<style lang="scss" scoped>
.export-button {
    &:focus {
        border: none;
        filter: brightness(.9);
    }
}
</style>
