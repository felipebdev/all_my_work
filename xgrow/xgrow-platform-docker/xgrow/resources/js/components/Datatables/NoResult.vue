<template>
    <tr>
        <td :colspan='colspan' class='no-content'>
            <img src='/xgrow-vendor/assets/img/no-results.svg' alt='Nenhum resultado encontradox.'/>
            <Title class="no-content__title">{{title}}</Title>
            <Subtitle class='no-content__subtitle'>{{ subtitle }}</Subtitle>
        </td>
    </tr>
</template>

<script>
import Title from '../XgrowDesignSystem/Typography/Title'
import Subtitle from '../XgrowDesignSystem/Typography/Subtitle'

export default {
    name: 'NoResult',
    components: {Subtitle, Title},
    props: {
        colspan: {
            type: Number,
            required: true,
        },
        title: {
            type: String,
            required: true,
        },
        subtitle: {
            type: String,
            required: true,
        },
    },
}
</script>

<style lang='scss' scoped>
.no-content {
    padding: 40px 0;
    text-align: center;
    background-color: transparent;

    &__title {
        display: flex;
        justify-content: center;
    }

    &__subtitle {
        margin: 0 auto !important;
        justify-content: center;
        max-width: 500px;
    }
}
</style>
