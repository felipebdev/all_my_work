<template>
    <div class="input-color">
        <div class="input-color__wrapper">
            <input
                :style="fieldStyles"
                :modelValue="modelValue"
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
                @change="$emit('update:modelValue', $event.target.value)"
                class="input-color__field"
                type="color"
            />
        </div>
        <div class="input-color__texts">
            <h5 class="input-color__title" :style="titleStyles">{{ title }}</h5>
            <p class="input-color__subtitle">{{ subtitle }}</p>
        </div>
    </div>
</template>

<script>
export default {
    name: "inputColor",
    props: {
        modelValue: { type: String, required: true },
        subtitle: { type: String, default: "" },
        title: { type: String, required: true },
        type: { type: String, default: "default" },
    },
    computed: {
        fieldStyles() {
            const styles = {
                primary: { width: "60px", height: "60px" },
                secondary: { width: "45px", height: "45px" },
                default: { width: "32px", height: "32px" },
            };

            return styles[this.type];
        },
        titleStyles() {
            const styles = {
                primary: {
                    fontWeight: "700",
                    fontSize: "1em",
                    lineHeight: "1.5em",
                },
                secondary: { fontSize: "1em", lineHeight: "1.5em" },
                default: { fontSize: ".9em", lineHeight: "1.4em" },
            };

            return styles[this.type];
        },
    },
};
</script>

<style lang="scss" scoped>
.input-color {
    align-items: center;
    color: #ffffff;
    display: flex;
    font-size: 16px;
    gap: 8px;
    height: 60px;

    &__wrapper {
        align-items: center;
        display: flex;
        justify-content: center;
        width: 60px;
    }

    &__field {
        -moz-box-shadow: none;
        -webkit-appearance: none;
        -webkit-box-shadow: none;
        align-items: center;
        border-radius: 50%;
        box-shadow: none;
        box-sizing: border-box;
        display: flex;
        justify-content: center;

        -moz-box-shadow: 0px 4px 4px 0px rgba(0, 0, 0, 0.25);
        -webkit-box-shadow: 0px 4px 4px 0px rgb(0 0 0 / 25%);
        box-shadow: 0px 4px 4px 0px rgb(0 0 0 / 25%);

        &::-webkit-color-swatch-wrapper {
            padding: 2px 0;
            margin: 0 1px;
        }

        &::-webkit-color-swatch {
            border-radius: 50%;
            border: none;
        }

        &::-moz-color-swatch-wrapper {
            -webkit-appearance: none;
            padding: 2px 0;
        }

        &::-moz-color-swatch {
            -webkit-appearance: none;
            border-radius: 50%;
            border: none;
        }
    }

    &__title {
        font-weight: 400;
        margin: 0;
    }

    &__subtitle {
        font-size: 0.7em;
        line-height: 0.8em;
    }
}
</style>
