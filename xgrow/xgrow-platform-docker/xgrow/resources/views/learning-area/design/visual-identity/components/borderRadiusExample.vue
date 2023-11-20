<template>
    <section
        v-if="theme"
        class="border-radius-example"
        :style="backgroundStyle"
    >
        <button :style="buttonStyle" class="border-radius-example__button">
            Action
        </button>
        <input
            :style="inputStyle"
            class="border-radius-example__input"
            type="text"
            value="campos"
        />
    </section>
</template>

<script>
import { useDesignVisualIdentity } from "../../../../../js/store/design-visual-identity";
import { mapStores, mapState } from "pinia";

export default {
    name: "borderRadiusExample",
    computed: {
        ...mapStores(useDesignVisualIdentity),
        ...mapState(useDesignVisualIdentity, [
            "theme",
            "backgroundStyle",
            "buttonStyle",
            "inputStyle",
        ]),
    },
};
</script>

<style lang="scss" scoped>
.border-radius-example {
    align-items: center;
    border-radius: 10px;
    box-shadow: 0px 0px 5px 0px rgb(0 0 0 / 50%);
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    padding: 30px;

    &__button,
    &__input {
        font-size: 16px;
        text-align: center;
    }

    &__button {
        padding: 10px 30px;
        border: none;
    }

    &__input {
        width: 180px;
        padding: 10px;
        border: 2px solid transparent;
    }
}
</style>
