<template>
    <div>
        <PreviewPlatform @device="(dev) => {device = dev}" :device="device"/>

        <div class="ob-border">
            <div :class="[device === 'desktop' ? 'ob-inner' : 'ob-inner-mobile']">
                <div class="ob-inner__left">
                    <div class="ob-inner__left-logo">LOGO</div>
                    <div class="ob-inner__left-header">
                        <h1>Seja bem vindo!</h1>
                        <p>Siga os passos abaixo para entender como funciona a plataforma da...</p>
                    </div>
                    <div class="ob-inner__left-steps">
                        <ul class="p-0">
                            <li v-for="(item, i) in values" :key="item._id"
                                @click="changeType(item)">
                                <span :class="{'active': i + 1 === step.order}">{{ i + 1 }}</span>
                                <p v-if="device === 'desktop'">{{ resume(item.title) }}</p>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="ob-inner__right">
                    <PreviewText :values="step" v-if="type === 'text'" :device="device"/>
                    <PreviewVideo :values="step" v-if="type === 'video'" :device="device"/>
                    <PreviewTextAndVideo :values="step" v-if="type === 'textAndVideo'" :device="device"/>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import DefaultButton from "../../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import PreviewText from "./PreviewText";
import PreviewVideo from "./PreviewVideo";
import PreviewTextAndVideo from "./PreviewTextAndVideo";
import PipeVertical from "../../../../../js/components/XgrowDesignSystem/Utils/PipeVertical";
import PreviewPlatform from "../../../../../js/components/XgrowDesignSystem/Utils/PreviewPlatform";


export default {
    name: "Preview",
    components: {PipeVertical, PreviewTextAndVideo, PreviewVideo, PreviewText, DefaultButton, PreviewPlatform},
    props: {
        values: {type: Object, required: true},
        start: {type: Object, default: {}},
    },
    data() {
        return {
            type: 'textAndVideo',
            step: {},
            device: 'desktop'
        }
    },
    methods: {
        resume: function (text, limit = 20) {
            return text.slice(0, limit) + (text.length > limit ? "..." : "");
        },
        changeType: function (item) {
            if (item !== null) {
                this.type = item.contentType
                this.step = item
            }
        },
        changeDeviceType: function (device) {
            this.device = device;
        }
    },
    async mounted() {
        await new Promise(resolve => setTimeout(resolve, 1000));
        this.step = this.start
    }
}
</script>

<style lang="scss" scoped>
.ob-border {
    background: #2A2E39;
    border: 5px dashed #626775;

    .ob-inner {
        display: flex;
        flex-direction: row;
        min-height: 500px;

        &__left {
            background: #222429;
            padding: 10px;
            max-width: 212px;
            width: 100%;
            min-width: 182px;

            &-logo {
                background: #3D4353;
                color: #252932;
                width: 66px;
                height: 34px;
                font-size: 1rem;
                display: flex;
                justify-content: center;
                align-items: center;
                font-weight: 700;
                margin-bottom: 20px;
            }

            &-header {
                color: #FFFFFF;
                margin-bottom: 20px;

                h1 {
                    font-weight: 600;
                    font-size: 0.75rem;
                    line-height: 1rem;
                }

                p {
                    font-weight: 400;
                    font-size: 0.687rem;
                    line-height: 1rem;
                }
            }

            &-steps {
                font-size: 0.625rem;

                ul {
                    li {
                        display: flex;
                        align-items: center;
                        margin-bottom: 20px;
                        cursor: pointer;

                        span {
                            background: #595B63;
                            color: #C1C5CF;
                            width: 20px;
                            height: 20px;
                            border-radius: 100%;
                            align-items: center;
                            justify-content: center;
                            display: flex;
                            margin-right: 10px;

                            &.active {
                                background: #93BC1E;
                                color: #FFFFFF;
                                box-shadow: 0 0 10px rgba(173, 223, 69, 0.2);

                                & ~ p {
                                    color: #FFFFFF;
                                    font-weight: 600;
                                }
                            }
                        }

                        p {
                            color: #C1C5CF;
                        }

                        &::before {
                            content: "";
                            position: relative;
                            width: 3px;
                            height: 20px;
                            background-color: #595B63;
                            left: 12px;
                            top: 20px;

                            &:last-child {
                                background-color: red;
                            }
                        }

                        &:last-child::before {
                            background-color: transparent;
                        }
                    }
                }
            }
        }

        &__right {
            padding: 10px;
            width: 100%;
        }
    }

    .ob-inner-mobile {
        flex-direction: column;
        min-height: 300px;

        .ob-inner__left {
            max-width: 100%;
            min-width: 182px;

            &-steps {
                ul {
                    display: flex;
                    align-items: center;
                    justify-content: center;

                    li {
                        margin-bottom: 0;

                        span {
                            width: 24px;
                            height: 24px;
                            font-size: 1rem;
                        }

                        &::before {
                            content: "";
                            position: relative;
                            width: 30px;
                            height: 3px;
                            background-color: #595B63;
                            right: 0;
                            top: 0;
                            margin: -5px;
                            left: 49px;

                            &:last-child {
                                background-color: red;
                            }
                        }

                        &:last-child::before {
                            background-color: transparent;
                        }
                    }
                }
            }
        }
    }
}
</style>
