<template>
    <div class="img-content d-flex align-items-center gap-2">
        <img :src="profile.img" :alt="profile.title" :class="rounded ? addBorder : ''"
            onerror="this.src='https://las.xgrow.com/background-default.png'">
        <div v-if="profile.title || profile.subtitle">
            <p class="img-content__title">{{ resume(profile.title) }}</p>
            <p class="img-content__subtitle">{{ profile.subtitle }}</p>
        </div>
    </div>
</template>

<script>
import Resume from "../../../js/components/XgrowDesignSystem/Mixins/resume"

export default {
    name: "ProfileRow",
    mixins: [Resume],
    props: {
        profile: { type: Object, required: true },
        rounded: { type: Boolean, default: false },
        borderType: { type: String, default: 'light' }
    },
    computed: {
        addBorder: function () {
            return `border border-${this.$props.borderType} rounded-circle`;
        }
    }
}
</script>

<style lang="scss" scoped>
.border {
    &-light {
        border: 2px solid #646D85 !important;
    }

    &-success {
        border: 2px solid #93BC1E !important;
    }
}

.img-content {
    img {
        height: 64px;
        width: 64px;
        object-fit: cover;
    }

    .img-content__title {
        color: #FFFFFF;
        font-family: 'Open Sans', serif;
        font-style: normal;
        font-weight: 400;
        font-size: 0.875rem;
        line-height: 1.5rem;
    }

    .img-content__subtitle {
        color: #C1C5CF;
        font-family: 'Open Sans', serif;
        font-style: normal;
        font-weight: 400;
        font-size: 0.75rem;
        line-height: 1.5rem;
    }
}
</style>
