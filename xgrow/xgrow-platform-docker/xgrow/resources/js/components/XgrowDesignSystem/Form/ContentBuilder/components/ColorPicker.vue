<template>
    <div class="color-picker">
        <div class="color-picker__wrapper">
            <input
                :modelValue="modelValue"
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
                @change="$emit('update:modelValue', $event.target.value)"
                class="color-picker__field"
                type="color"
            />
        </div>
        <p class="color-picker__text">{{ modelValue }}</p>
    </div>
</template>

<script>
export default {
    name: "color-picker",
    props: {
        modelValue: { type: String, default: "" },
    },
};
</script>

<style lang="scss" scoped>
.color-picker {
    align-items: center;
    border-radius: 8px;
    border: 1px solid #646D85;
    color: #ffffff;
    display: flex;
    font-size: 16px;
    gap: 8px;
    height: 38px;
    padding: 8px;
    width: 160px;


    &__wrapper {
        align-items: center;
        display: flex;
        justify-content: center;
        width: 34px;
        height: 22px;
    }

    &__field {
        -moz-box-shadow: none;
        -webkit-appearance: none;
        -webkit-box-shadow: none;
        align-items: center;
        border-radius: 4px;
        box-shadow: none;
        box-sizing: border-box;
        display: flex;
        justify-content: center;
        padding: 0;
        margin: 0;
        border: none;

        -moz-box-shadow: 0px 4px 4px 0px rgba(0, 0, 0, 0.25);
        -webkit-box-shadow: 0px 4px 4px 0px rgb(0 0 0 / 25%);
        box-shadow: 0px 4px 4px 0px rgb(0 0 0 / 25%);

        &::-webkit-color-swatch-wrapper {
            margin: 0;
        }

        &::-webkit-color-swatch {
            border-radius: 4px;
        }

        &::-moz-color-swatch-wrapper {
            -webkit-appearance: none;
        }

        &::-moz-color-swatch {
            -webkit-appearance: none;
            border-radius: 4px;
        }
    }

    &__text {
        font-size: 14px;
        font-weight: 400;
        line-height: 1.6;
        margin: 0;
    }
}
</style>
