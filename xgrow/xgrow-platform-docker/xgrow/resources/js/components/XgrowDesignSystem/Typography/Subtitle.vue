<template>
    <h3 :class="{'is-small': isSmall}">
        <template v-if='icon'>
            <div class='icon-bg' :style="`color: ${iconColor}`">
                <i :class="icon"></i>
            </div>
        </template>
        <slot></slot>
    </h3>
</template>

<script>
export default {
    name: "Subtitle",
    props: {
        icon: {type: String, default: null},
        iconColor: {type: String, default: '#ffffff'},
        isSmall: {type: Boolean, default: false},
    },
}
</script>

<style lang='scss' scoped>
h3 {
    color: #E7E7E7;
    font-family: 'Open Sans',serif;
    font-style: normal;
    font-weight: 400;
    font-size: 1rem;
    line-height: 2rem;
    display: flex;
    align-items: center;
    column-gap: 10px;
}

.is-small{
    font-family: 'Open Sans',serif;
    font-style: normal;
    font-weight: 400;
    font-size: 0.875rem;
    line-height: 1.125rem;
    color: #E7E7E7;
}
</style>
