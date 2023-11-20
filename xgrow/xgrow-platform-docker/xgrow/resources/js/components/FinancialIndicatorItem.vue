<template>
    <div :class="`indicatorItem ${fontSize} ${border ? 'hasBorder': ''}`">
        <p class="indicatorItem__content">
            <i v-if="hasInfo" class="icon fa fa-exclamation-circle indicatorItem__tooltip" :id="id">
                <span v-html="tooltip"></span>
            </i>
            
            <i v-if="icon && !hasInfo && !localIcon" :class="`icon ${icon}`" />
            <img :src="localIcon" v-if="localIcon && !hasInfo" class="icon">

            {{content}}
        </p>
        <p class="indicatorItem__value">{{value}}</p>
    </div>
</template>

<script>
export default {
    name: "FinancialIndicatorItem",
    props: {
        content: {required: false, default: "", type: [String, Number]},
        value: {required: false, default: "", type: [String, Number]},
        border: {required: false, default: true, type: [Boolean]},
        fontSize: {required: false, default: "", type: [String]},
        icon: {required: false, default: "", type: [String]},
        localIcon: {required: false, default: "", type: [String]},
        hasInfo: {default: false, type: Boolean},
        tooltip: {required: false, default: "", type: String}
    },
    data() {
        return {
            id: ""
        };
    },
    created() {
        this.id = Date.now().toString(36) + Math.random().toString(36).substring(2);
    },
};
</script>

<style lang="scss" scoped>
    .indicatorItem {
        display: flex;
        justify-content: space-between;
        font-size: 12.8px;
        line-height: 1.35;
        color: #E7E7E7;
        font-family: 'Open Sans';
        font-weight: 600;
        padding-bottom: 8px;
        margin-top: 10px;

        &.hasBorder {
            border-bottom: 1px solid #2A2E39;
        }

        &.bigger {
            margin-top: 20px;
            font-size: 14px;
            font-weight: bold;
            padding-bottom: 4px;
        }

        &__content {
            .icon {
                margin-right: 8px;
                color: #4E525F;
                max-width: 12.8px;
                display: inline-block;
            }
        }

        &__value {
            color: #ADDF45;
        }

        &__tooltip {
            position: relative;
            cursor: pointer;

            & > span {
                width: 140px;
                position: absolute;
                left: 22px;
                color: #E7E7E7;
                font-size: 12.8px;
                line-height: 1.70;
                display: none;
                top: 50%;
                background: #121419;
                font-family: 'Open Sans';
                border-radius: 8px;
                padding: 8px 12px;
                transform: translateY(-50%);
                box-shadow: 0 1px 3px rgb(0 0 0 / 12%), 0 1px 2px rgb(0 0 0 / 24%);

                &::after {
                    content: '';
                    display: block;
                    position: absolute;
                    border-style: solid;
                    border-width: 5px;
                    border-color: transparent #121419 transparent transparent;
                    top: 50%;
                    transform: translateY(-50%);
                    left: -10px;
                }
            }

            &:hover {
                & > span{
                    display: block;
                }
            }
        }
    }
</style>
