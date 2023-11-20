<template>
    <div class="tab-pane fade" :id="id" :class="{'show active': selected}">
        <slot></slot>
    </div>
</template>

<script>
export default {
    name: "XgrowTabContent",
    props: {
        id: {type: String, required: true},
        selected: {type: Boolean, default: false}
    }
};
</script>

<style scoped>
</style>
