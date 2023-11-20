<template>
    <div class="d-flex gap-2">
        <input
            type="color"
            :id="id"
            class="input-color"
            :value="modelValue"
            :disabled="disabled"
            @input="$emit('update:modelValue', $event.target.value)">
        <div>
            <label :for="id" class="label-color">{{ label }}</label>
            <p class="m-0 p-0">{{ colorLabel }}</p>
        </div>
    </div>
</template>

<script>
export default {
    name: "ColorInput",
    props: {
        id: {
            type: String,
            required: true
        },
        label: {
            type: String,
            default: ""
        },
        colorLabel: {
            type: String,
            default: "#000000"
        },
        disabled: {
            type: Boolean,
            default: false
        },
        modelValue: {
            type: String,
            default: ""
        },
    },
}
</script>

<style lang="scss" scoped>
.label-color {
    font-family: 'Open Sans', serif;
    font-style: normal;
    font-weight: 700;
    font-size: 0.875rem;
    line-height: 1.125rem;
    color: #93BC1E;
}

.input-color {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background-color: transparent;
    height: 47px;
    width: 47px;
    border: none;
    cursor: pointer;

    &::-webkit-color-swatch {
        border-radius: 50%;
        border: 2px solid #ffffff;
    }

    &::-moz-color-swatch {
        border-radius: 50%;
        border: 2px solid #ffffff;
    }
}
</style>
