<template>
    <h1 :class="{'form-title': isFormTitle}">
        <template v-if='icon'>
            <div class='icon-bg' :style="`background-color: ${iconBg}`">
                <i :class="icon" :style="`color: ${iconColor}`"></i>
            </div>
        </template>
        <slot></slot>
    </h1>
</template>

<script>
export default {
    name: "Title",
    props: {
        icon: {type: String, default: null},
        iconColor: {type: String, default: '#222329'},
        iconBg: {type: String, default: '#3D4353'},
        isFormTitle: {type: Boolean, default: false}
    },
}
</script>

<style lang='scss' scoped>
h1 {
    font-family: 'Open Sans', serif;
    font-style: normal;
    font-weight: 700;
    font-size: 1.5rem;
    line-height: 2rem;
    color: #FFFFFF;
    display: flex;
    gap: .5rem;
    align-items: center;

    .icon-bg {
        background-color: #3D4353;
        height: 1.75rem;
        width: 1.75rem;
        min-width: 1.75rem;
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
        justify-content: center;

        i {
            font-size: 1rem;
        }
    }

    &.form-title {
        font-size: 1rem !important;
        line-height: 1.5rem !important;
    }
}
</style>
