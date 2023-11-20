<template>
    <div
        class="course-page"
        :class="{ 'course-page-mobile': device == 'mobile' }"
        :style="backgroundStyle"
    >
        <div class="banner">
            <div class="banner-container">
                <div class="menu">
                    <div
                        class="menu-button"
                        :style="{ 'border-color': theme.secondaryColor }"
                    ></div>
                </div>

                <div
                    class="d-flex gap-3 jutify-content-center position-relative"
                    v-if="device == 'mobile'"
                >
                    <div class="dot"></div>
                    <div class="dot"></div>
                </div>

                <div class="d-flex gap-3" v-if="device != 'mobile'">
                    <div class="course-presentation">
                        <div class="course-presentation-tabs">
                            <div class="tab" :style="{background : this.theme.textColor}"></div>
                            <div class="tab active" :style="{background : this.theme.secondaryColor}"></div>
                        </div>

                        <div class="course-presentation-title" :style="textStyle"></div>

                        <div class="d-flex gap-2">
                            <div class="course-presentation-image" :style="gridItemStyle"></div>

                            <div class="d-flex flex-column" style="gap: 10px;">
                                <div class="course-presentation-text" :style="textStyle"></div>
                                <div class="course-presentation-text" :style="textStyle"></div>
                                <div class="course-presentation-text" :style="textStyle"></div>
                                <div class="course-presentation-button" :style="buttonStyle"></div>
                            </div>
                        </div>
                    </div>

                    <div class="review">
                        <div class="review-bar" :style="{ background: theme.secondaryColor }"></div>
                        <div class="d-flex gap-2 align-items-center">
                            <div class="review-image" :style="textStyle"></div>
                            <div class="review-text" :style="textStyle"></div>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <div class="review-image" :style="textStyle"></div>
                            <div class="review-text" :style="textStyle"></div>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <div class="review-image" :style="textStyle"></div>
                            <div class="review-text" :style="textStyle"></div>
                        </div>

                        <div class="review-stars">
                            <i class="icon fas fa-star" :style="{ color: theme.primaryColor }"></i>
                            <i class="icon fas fa-star" :style="{ color: theme.primaryColor }"></i>
                            <i class="icon fas fa-star" :style="{ color: theme.primaryColor }"></i>
                            <i class="icon fas fa-star" :style="{ color: theme.primaryColor }"></i>
                            <i class="icon fas fa-star" :style="{ color: theme.primaryColor }"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="background">

            <div class="mobile-review" v-if="device == 'mobile'">
                <div class="d-flex justify-content-between">
                    <div class="mobile-review-texts">
                        <div class="text" :style="textStyle"></div>
                        <div class="text" :style="textStyle"></div>

                        <div class="stars">
                            <i class="icon fas fa-star" :style="{ color: theme.primaryColor }"></i>
                            <i class="icon fas fa-star" :style="{ color: theme.primaryColor }"></i>
                            <i class="icon fas fa-star" :style="{ color: theme.primaryColor }"></i>
                            <i class="icon fas fa-star" :style="{ color: theme.primaryColor }"></i>
                            <i class="icon fas fa-star" :style="{ color: theme.primaryColor }"></i>
                        </div>
                    </div>
                    <div class="button" :style="buttonStyle"></div>
                </div>

                <div class="d-flex flex-column gap-1 mt-2">
                    <div class="text thin" :style="textStyle"></div>
                    <div class="text thin" :style="textStyle"></div>
                    <div class="text thin" :style="textStyle"></div>
                </div>
            </div>


            <div class="course-content" :style="courseContentStyle">
                <div class="course-text" :style="textStyle"></div>
                <div class="indicator-progress">
                    <div class="course-indicator" :style="indicatorStyle"></div>
                    <div class="course-progress"></div>
                </div>
                <i class="icon fas fa-arrow-right" :style="arrowStyle"></i>
            </div>
            <div class="course-content" :style="courseContentStyle">
                <div class="course-text" :style="textStyle"></div>
                <div class="indicator-progress">
                    <div class="course-indicator" :style="indicatorStyle"></div>
                    <div class="course-progress"></div>
                </div>
                <i class="icon fas fa-arrow-right" :style="arrowStyle"></i>
            </div>
            <div class="course-content" :style="courseContentStyle">
                <div class="course-text" :style="textStyle"></div>
                <div class="indicator-progress">
                    <div class="course-indicator" :style="indicatorStyle"></div>
                    <div class="course-progress"></div>
                </div>
                <i class="icon fas fa-arrow-right" :style="arrowStyle"></i>
            </div>
        </div>
    </div>
</template>

<script>
import { useDesignVisualIdentity } from "../../../../../js/store/design-visual-identity";
import { mapStores, mapState } from "pinia";

export default {
    name: "course-page",
    props: {
        device: { type: String, default: 'desktop' },
    },
    computed: {
        ...mapStores(useDesignVisualIdentity),
        ...mapState(useDesignVisualIdentity, [
            "theme",
            "backgroundStyle",
            "buttonStyle",
            "inputStyle",
        ]),
        textStyle() {
            return { background: this.theme.textColor };
        },
        gridItemStyle() {
            return { "border-radius": `${this.theme.borderRadius * 0.25}px` };
        },
        indicatorStyle() {
            return { background : this.theme.secondaryColor };
        },
        arrowStyle() {
            return { color: this.theme.textColor };
        },
        courseContentStyle() {
            return { background: this.theme.tertiaryColor };
        }
    },
};
</script>

<style lang="scss" scoped>
.course-page {
    width: 100%;
    border-radius: 8px;
    display: flex;
    flex-direction: column;

    &-mobile {
        width: 200px;
        margin: 0 auto;

        .banner {
            background: #888;
            height: 180px;

            &-container {
                margin: 30px;
            }
        }

        .background {
            padding: 10px;
            gap: 15px;
        }

        .dot {
            width: 10px;
            height: 10px;
            right: 10px;

            &:nth-child(2) {
                right: -10px;
            }
        }

        .menu {
            top: 25px;
            width: 25px;
            height: 25px;

            &-button {
                width: 15px;
                height: 15px;
            }
        }


        .course {
            &-text {
                width: 30px
            }
            &-progress {
                width: 30px;
            }

        }

    }
}

.banner {
    display: flex;
    flex-direction: column;
    background-color: #c4c4c4;
    width: 100%;
    height: 235px;
    border-top-right-radius: inherit;
    border-top-left-radius: inherit;
    background-repeat: no-repeat;
    background-size: 100% 100%;
    position: relative;

    &-container {
        margin: 20px 60px 30px;
        display: flex;
        height: 100%;
        flex-direction: column;
        justify-content: space-between;
    }
}

.menu {
    align-items: center;
    background: #2a2e39;
    border-radius: 0 50px 50px 0;
    display: flex;
    height: 35px;
    justify-content: flex-end;
    left: 0;
    padding: 4px;
    position: absolute;
    top: 70px;
    width: 45px;

    &-button {
        width: 25px;
        height: 25px;
        border: 2px solid transparent;
        border-radius: 50%;
    }
}

.button {
    width: 80px;
    height: 30px;
}

.background {
    padding: 20px;
    height: 100%;
    max-height: 365px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 20px;
}

.course {
    &-content {
        max-width: 380px;
        width: 100%;
        height: 44px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 20px;

        .indicator-progress {
            display: flex;
            align-items: center;
            gap: 10px;
        }
    }

    &-text {
        width: 60px;
        height: 10px;
    }

    &-indicator {
        width: 15px;
        height: 15px;
        border-radius: 50%;
    }


    &-progress {
        width: 160px;
        height: 10px;
        background: #c4c4c4;
    }
}

.course-presentation {
    display: flex;
    flex-direction: column;
    gap: 20px;
    width: 250px;

    &-title {
        width: 100%;
        height: 25px;
    }

    &-image {
        width: 120px;
        height: 85px;
        background-color: #888;
        -moz-box-shadow: inset 0 0 5px #00000033;
        -webkit-box-shadow: inset 0 0 5px #00000033;
        box-shadow: inset 0 0 5px #00000033;
    }

    &-text {
        width: 120px;
        height: 10px;
    }

    &-button {
        width: 80px;
        height: 25px;
    }

    &-tabs {
        display: flex;
        gap: 10px;
        width: 100%;
        height: 40px;
        align-items: flex-end;


        .tab {
            width: 40px;
            height: 5px;
        }
    }
}

.review {
    width: 84px;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    gap: 10px;

    &-bar {
        width: 100%;
        height: 5px;
    }

    &-image {
        min-width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    &-text {
        width: 100%;
        height: 5px;
    }

    &-stars {
        font-size: 12px;
        display: flex;
        justify-content: space-between;
    }
}

.dot {
    background: #c4c4c4;
    border-radius: 50%;
    width: 15px;
    height: 15px;
    position: absolute;
    right: 20px;

    &:nth-child(2) {
        right: -10px;
    }
}

.mobile-review {
    width:  100%;

    .text {
        width: 100%;
        height: 10px;

        &.thin {
            height: 5px;
            &:nth-child(3) {
                width: 60%;
            }
        }
    }

    .stars {
        font-size: 10px;
    }

    &-texts {
        display: flex;
        flex-direction: column;
        gap: 10px;

        .text {
            width: 90px;
            height: 10px;

            &:nth-child(2) {
                width: 80px;
            }
        }
    }

    .button {
        width: 70px;
        height: 25px;
    }
}
</style>
