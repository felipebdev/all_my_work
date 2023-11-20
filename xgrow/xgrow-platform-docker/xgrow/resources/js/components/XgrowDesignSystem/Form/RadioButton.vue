<template>
    <div class='form-check'>
        <input
            class='form-check-input'
            type='radio'
            :name='name'
            :id='id ?? option'
            :disabled='disabled'
            :value='option'
            :checked='checked'
            @input="$emit('update:modelValue', option)"
        />
        <label class='form-check-label text-white' :for='id ?? option'> {{ label }} </label>
    </div>
</template>

<script>

export default {
    name: 'RadioButton',
    props: {
        id: {
            type: String,
            required: false,
        },
        name: {
            type: String,
            required: false,
        },
        label: {
            type: String,
            default: '',
        },
        option: {
            type: [String, Number],
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        checked: {
            type: Boolean,
            default: false,
        },
    },
}

</script>

<style lang='scss' scoped>
.form-check {
    margin-bottom: 0;

    label,
    input {
        cursor: pointer;
        outline: none;
        box-shadow: none;
    }
}

.form-check-input:checked {
    background-color: #ADFF2F;
    border-color: #93BC1E;
}
</style>
