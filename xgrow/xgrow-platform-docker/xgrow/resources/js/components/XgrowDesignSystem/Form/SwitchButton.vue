<template>
    <div class='form-check form-switch'>
        <input class='form-check-input' :id='id' type='checkbox' :value='isReverse ? !modelValue : modelValue'
            :checked='isReverse ? !modelValue : modelValue'
            @input="$emit('update:modelValue', isReverse ? !$event.target.checked : $event.target.checked)"
            :disabled='disabled'>
        <label class='form-check-label' :for='id'>
            <slot></slot>
        </label>
    </div>
</template>

<script>

export default {
    name: 'SwitchButton',
    props: {
        id: { type: String, required: true },
        modelValue: { type: Boolean, default: false },
        disabled: { type: Boolean, default: false },
        isReverse: { type: Boolean, default: false }
    },
}
</script>

<style lang='scss' scoped>
.form-check {
    .form-check-input {
        &:checked {
            background-color: #ADFF2F;
            border-color: #93BC1E;
        }

        outline: none;
        cursor: pointer;
    }

    .form-check-label {
        color: #FFFFFF;
        cursor: pointer;
    }
}

input:disabled {
    background-color: #595B63 !important;
}

input:disabled~label {
    color: #595B63 !important;
}
</style>
