<template>
    <div class="xgrow-tooltip">
        <span class="icon" v-html="icon" :style="{ color: iconColor }"></span>
        <span
            class="text"
            :class="position"
            id="tooltip-message"
            ref="tooltipMessage"
            :style="position === 'left' ? { left: leftOffset } : ''"
        >
            {{ text }}
        </span>
    </div>
</template>

<script>
export default {
    name: "xgrow-tooltip-component",
    props: {
        icon: {
            type: String,
            required: false,
            default: '<i class="fas fa-info-circle"></i>',
        },
        iconColor: {
            type: String,
            required: false,
            default: "#adff2f",
        },
        text: {
            type: String,
            required: true,
        },
        position: {
            type: String,
            required: false,
            default: "top",
        },
    },
    data() {
        return {
            leftOffset: 0,
        };
    },
    mounted() {
        const spanSize = this.$refs.tooltipMessage.clientWidth;
        this.leftOffset = `-${spanSize + 5}px`;
    },
};
</script>

<style scoped lang="scss">
@import "../../sass/util.scss";

.xgrow-tooltip {
    position: relative;
    color: #fff;

    .icon {
        cursor: pointer;
        z-index: 1;

        &:hover + .text,
        &:focus + .text {
            display: block;
        }
    }

    .text {
        position: absolute;
        background-color: #222429;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
        padding: pxToRem(4px) pxToRem(6px);
        border-radius: pxToRem(4px);
        font-size: 0.8rem;
        min-width: max-content;
        font-weight: 500;
        display: none;
        z-index: 2;

        &.top {
            top: -25px;
            left: -5px;
        }

        &.right {
            top: -2.5px;
            left: 20px;
        }

        &.bottom {
            top: 25px;
            left: -5px;
        }

        &.left {
            top: -2.5px;
        }

        &:hover, &:focus {
            display: block;
        }
    }
}
</style>
