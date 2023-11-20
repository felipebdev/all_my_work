<template>
    <div class="d-flex align-items-center gap-2">
        <div class='icon-bg' :style="{'background': color}">
            <i :class="icon" :style="{'color': iconColor}"></i>
        </div>
        <div>
            <Title class="title">{{title}}</Title>
            <Subtitle class="subtitle">{{ subtitle }}</Subtitle>
        </div>
    </div>
</template>

<script>
import Title from "../Typography/Title";
import Subtitle from "../Typography/Subtitle";

export default {
    name: "Stats",
    components: {Subtitle, Title},
    props: {
        icon: {type: String, required: true},
        title: {type: String, default: true},
        subtitle: {type: String, default: true},
        color: {type: String, default: '#FFFFFF'},
        iconColor: {type: String, default: '#FFFFFF'},
    },
}
</script>

<style lang="scss" scoped>
.icon-bg {
    background-color: #3D4353;
    height: 40px;
    width: 40px;
    min-width: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;

    i {
        font-size: 1.25rem;
        color: rgba(255, 255, 255, 0.8);
    }
}

.title, .subtitle {
    font-family: 'Open Sans', serif;
    font-style: normal;
    padding: 0;
    margin: 0;
}

.title {
    font-weight: 700;
    font-size: 1rem;
    line-height: 1.625rem;
    color: #FFFFFF;
}

.subtitle {
    font-weight: 600;
    font-size: 0.75rem;
    line-height: 1.125rem;
    color: #C1C5CF;
}
</style>
