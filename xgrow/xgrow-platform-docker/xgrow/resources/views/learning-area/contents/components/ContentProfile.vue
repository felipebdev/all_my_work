<template>
    <div class="img-content d-flex align-items-center gap-2">
        <img :src="profile.img" :alt="profile.title" class="img-profile"
            :class="{ 'border border-light rounded-circle': rounded }"
            onerror="this.src='https://las.xgrow.com/background-default.png'">
        <div>
            <p class="img-content__title">{{ resume(profile.title) }}</p>
            <p class="img-content__subtitle">
                <img :src="getTypeIcon" :alt="getTypeContent" class="xg-icon"> {{ getTypeContent }}
            </p>
        </div>
    </div>
</template>

<script>
import Resume from '../../../../js/components/XgrowDesignSystem/Mixins/resume';

export default {
    name: "ContentProfile",
    mixins: [Resume],
    props: {
        profile: { type: Object, required: true },
        rounded: { type: Boolean, default: false }
    },
    computed: {
        getTypeIcon: function () {
            if (this.profile.subtitle === 'audio') return '/xgrow-vendor/assets/img/icons/mdi-headset.svg'
            if (this.profile.subtitle === 'video') return '/xgrow-vendor/assets/img/icons/mdi-youtube.svg'
            if (this.profile.subtitle === 'html') return '/xgrow-vendor/assets/img/icons/mdi-code-tags.svg'
            if (this.profile.subtitle === 'externalLink') return '/xgrow-vendor/assets/img/icons/mdi-link.svg'
            return '/xgrow-vendor/assets/img/icons/fa-file.svg'
        },
        getTypeContent: function () {
            if (this.profile.subtitle === 'audio') return 'Áudio'
            if (this.profile.subtitle === 'video') return 'Vídeo'
            if (this.profile.subtitle === 'html') return 'Html'
            if (this.profile.subtitle === 'externalLink') return 'Link externo'
            return 'Conteúdo'
        }
    }
}
</script>

<style lang="scss" scoped>
.border-light {
    border: 2px solid #646D85 !important;
}

.img-content {
    .img-profile {
        height: 64px;
        width: 92px;
        min-width: 92px;
        object-fit: cover;

        &.rounded-circle {
            width: 64px;
            min-width: 64px;
        }
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
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
}

.xg-icon {
    filter: brightness(0) saturate(100%) invert(42%) sepia(29%) saturate(285%) hue-rotate(185deg) brightness(96%) contrast(96%);
    height: 18px !important;
    width: 18px !important;
}
</style>
