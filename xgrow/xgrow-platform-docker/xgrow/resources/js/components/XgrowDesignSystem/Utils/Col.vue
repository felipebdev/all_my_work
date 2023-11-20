<template>
    <div :class="`col-sm-${sm} col-md-${md} col-lg-${lg} col-xl-${xl}`">
        <slot></slot>
    </div>
</template>

<script>
export default {
    name: 'Col',
    props: {
        sm: { required: false, type: String, default: '12' },
        md: { required: false, type: String, default: '12' },
        lg: { required: false, type: String, default: '12' },
        xl: { required: false, type: String, default: '12' },
    },
}
</script>
