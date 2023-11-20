<template>
    <div
        class="main-page"
        :class="{ 'main-page-mobile': device == 'mobile' }"
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
                >
                    <div class="search" v-if="device != 'mobile'" :style="inputStyle"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </div>

                <div class="d-flex flex-column gap-3">
                    <div class="text" :style="textStyle"></div>
                    <div class="text" :style="textStyle"></div>
                    <div class="button" :style="buttonStyle"></div>
                </div>
            </div>
        </div>
        <div class="background">
            <div class="header">
                <div class="header-text"></div>
            </div>

            <div class="grid">
                <div class="grid-content" :style="gridItemStyle"></div>
                <div class="grid-content" :style="gridItemStyle"></div>
                <div class="grid-content" :style="gridItemStyle"></div>
                <div class="grid-content" :style="gridItemStyle"></div>
                <div class="grid-content" :style="gridItemStyle"></div>
                <div class="grid-content" :style="gridItemStyle"></div>
            </div>

            <div class="mini-grid">
                <div class="mini-grid-content" :style="gridItemStyle"></div>
                <div class="mini-grid-content" :style="gridItemStyle"></div>
                <div class="mini-grid-content" :style="gridItemStyle"></div>
                <div class="mini-grid-content" :style="gridItemStyle"></div>
            </div>
        </div>
    </div>
</template>

<script>
import { useDesignVisualIdentity } from "../../../../../js/store/design-visual-identity";
import { mapStores, mapState } from "pinia";

export default {
    name: "main-page",
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
            return {
                background: this.theme.textColor,
            };
        },
        gridItemStyle() {
            return {
                "border-radius": `${this.theme.borderRadius * 0.25}px`,
            };
        },
    },
};
</script>

<style lang="scss" scoped>
.main-page {
    width: 100%;
    border-radius: 8px;
    display: flex;
    flex-direction: column;

    &-mobile {
        width: 200px;
        margin: 0 auto;

        .banner {
            &-container {
                margin: 30px;
            }
        }

        .text {
            width: 120px;

            &:nth-child(2) {
                width: 80px;
            }
        }

        .button {
            width: 50px;
            height: 25px;
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
            width: 25px;
            height: 25px;

            &-button {
                width: 15px;
                height: 15px;
            }
        }

        .header {
            padding: 10px 20px;
        }

        .grid-content {
            min-width: 120px;
        }

        .mini-grid-content {
            width: 75px;
            height: 75px;
        }
    }
}

.banner {
    display: flex;
    flex-direction: column;
    background-color: #888;
    width: 100%;
    height: 265px;
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
    top: 60px;
    width: 45px;

    &-button {
        width: 25px;
        height: 25px;
        border: 2px solid transparent;
        border-radius: 50%;
    }
}

.search {
    width: 200px;
    height: 20px;
    border: 2px solid transparent;
    margin: 0 auto;
}

.header {
    width: 100%;
    max-width: 360px;
    padding: 20px;
    background: #d0d0d0;

    &-text {
        width: 100%;
        max-width: 200px;
        height: 25px;
        background: #bdbdbd;
        margin: 0 auto;
    }
}

.text {
    width: 200px;
    height: 25px;

    &:nth-child(2) {
        width: 120px;
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
    gap: 40px;
    flex-wrap: nowrap;
}

.grid {
    width: 100%;
    display: flex;
    gap: 20px;

    &-content {
        background-color: #888;
        -moz-box-shadow: inset 0 0 5px #00000033;
        -webkit-box-shadow: inset 0 0 5px #00000033;
        box-shadow: inset 0 0 5px #00000033;
        min-width: 160px;
        height: 100px;
    }
}

.mini-grid {
    width: 100%;
    display: flex;
    justify-content: space-between;
    gap: 20px;

    &-content {
        background-color: #888;
        -moz-box-shadow: inset 0 0 5px #00000033;
        -webkit-box-shadow: inset 0 0 5px #00000033;
        box-shadow: inset 0 0 5px #00000033;
        min-width: 80px;
        height: 80px;
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
</style>
