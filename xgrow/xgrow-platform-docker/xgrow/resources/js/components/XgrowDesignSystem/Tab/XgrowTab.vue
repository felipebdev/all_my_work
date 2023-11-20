<template>
    <div>
        <slot name="header"></slot>
        <div class="tab-content" :id="id">
            <slot name="body"></slot>
        </div>
    </div>
</template>

<script>
export default {
    name: "XgrowTab",
    props: {
        id: {type: String, required: true},
    }
};
</script>

<style scoped>

</style>
