<template>
    <div class="login-preview" :style="backgroundStyle">
        <img
            class="background-image"
            v-if="device != 'mobile' && theme.backgroundImageUrl"
            :src="theme.backgroundImageUrl"
        />
        <div class="background-image background-image--color" v-if="device != 'mobile' && !theme.backgroundImageUrl">
            <span v-if="theme.useBanner">sua Imagem aqui</span>
        </div>

        <div
            class="background"
            :class="{ mobile: device == 'mobile' }"
        >
            <div
                class="logo"
                v-if="device != 'mobile'"
                :style="logoStyle"
            ></div>
            <div class="form" :style="formStyle">
                <div class="text" :style="textStyle"></div>
                <div class="input" :style="inputStyle"></div>
                <div class="input" :style="inputStyle"></div>
                <div class="button" :style="buttonStyle"></div>
                <div class="footer-text" v-if="device != 'mobile'"></div>
            </div>
        </div>
    </div>
</template>

<script>
import { useDesignVisualIdentity } from "../../../../../js/store/design-visual-identity";
import { mapStores, mapState } from "pinia";

export default {
    name: "loginPreview",
    props: {
        device: { type: String, default: "desktop" },
    },
    computed: {
        ...mapStores(useDesignVisualIdentity),
        ...mapState(useDesignVisualIdentity, [
            "theme",
            "backgroundStyle",
            "buttonStyle",
            "inputStyle",
        ]),
        formStyle() {
            return {
                background: this.theme.tertiaryColor,
                "border-radius": `${this.theme.borderRadius * 0.25}px`,
            };
        },
        textStyle() {
            return {
                background: this.theme.textColor,
            };
        },
        logoStyle() {
            const logo = this.theme.logoUrl;
            return {
                background: logo ? `url(${logo})` : "#cbcbcb",
            };
        },
    },
};
</script>

<style lang="scss" scoped>
.login-preview {
    width: 100%;
    height: 280px;
    border-radius: 8px;
    display: flex;
    justify-content: center;
}

.background-image {
    width: 60%;
    border-bottom-left-radius: inherit;
    border-top-left-radius: inherit;

    &--color {
        display: flex;
        justify-content: center;
        align-items: center;
        background: #888;
    }
}

.background {
    width: 40%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 20px;
    align-items: center;
    border-bottom-right-radius: inherit;
    border-top-right-radius: inherit;

    &.mobile {
        width: 100%;
        max-width: 200px;
        border-radius: inherit;
    }
}

.logo {
    width: 120px;
    height: 25px;
    box-shadow: 0 0 5px 0 rgb(0 0 0 / 50%);
}

.text {
    width: 60%;
    align-self: flex-start;
    height: 15px;
}

.form {
    width: 130px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}

.input {
    width: 100%;
    height: 15px;
}

.button {
    width: 100%;
    height: 20px;
}

.footer-text {
    height: 10px;
    width: 45px;
    background-color: #888;
    box-shadow: 0 0 5px 0 rgb(0 0 0 / 50%);
}
</style>
