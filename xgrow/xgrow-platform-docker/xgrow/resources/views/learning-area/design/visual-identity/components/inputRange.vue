<template>
    <div class="input-range">
        <p class="input-range__label">{{ label }}</p>

        <input
            type="range"
            :min="min"
            :max="max"
            :step="step"
            :value="modelValue"
            @input="updateValue($event.target.value)"
            @change="updateValue($event.target.value)"
            class="input-range__field"
        />

        <slot name="values" :min="min" :value="modelValue" :max="max">
            <div class="input-range__values">
                <div class="input-range__values__text">{{ min }}</div>
                <div class="
                    input-range__values__text
                    input-range__values__text--current
                ">
                    {{ modelValue }}
                </div>
                <div class="input-range__values__text">{{ max }}</div>
            </div>
        </slot>
    </div>
</template>

<script>
export default {
    name: "inputRange",
    props: {
        label: { type: String, default: "" },
        max: { type: Number, default: 100},
        min: { type: Number, default: 0 },
        modelValue: { type: Number, required: true },
        step: { type: Number, default: 1 },
    },
    methods: {
        updateValue(value) {
            this.$emit("update:modelValue", value);
        },
    }
};
</script>

<style lang="scss" scoped>
.input-range {
    &__label {
        font: normal normal 400 12.8px/17.43px "Open Sans", sans-serif;
    }

    &__field {
        background: transparent;
        -webkit-appearance: none;
        width: 100%;

        &::-webkit-slider-runnable-track {
            background: #93bc1e;
            border-radius: 25px;
            border: 0;
            cursor: pointer;
            height: 8.4px;
            width: 100%;
        }

        &::-moz-range-track {
            background: #93bc1e;
            border-radius: 25px;
            border: 0;
            cursor: pointer;
            height: 8.4px;
            width: 100%;
        }

        &::-webkit-slider-thumb {
            -webkit-appearance: none;
            background: #fff;
            border-radius: 50px;
            border: 0;
            box-shadow: 0px 0px 5px 1px #000;
            cursor: pointer;
            height: 20px;
            margin-top: -5.8px;
            width: 20px;
        }

        &::-moz-range-thumb  {
            -webkit-appearance: none;
            background: #fff;
            border-radius: 50px;
            border: 0;
            box-shadow: 0px 0px 5px 1px #000;
            cursor: pointer;
            height: 20px;
            margin-top: -5.8px;
            width: 20px;
        }
    }

    &__values {
        display: flex;
        justify-content: space-between;

        &__text {
        font-size: 12.8px;
        line-height:17.43px ;

            &--current {
                font-size: 16px;
                font-weight: 700;
                line-height: 21,79px;
            }
        }

    }
}
</style>
