<template>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" :id="id" :disabled="disabled" :checked='checked'
            @change="$emit('update:modelValue', checked)" @input="$emit('checked')" />
        <label class="form-check-label text-white" :for="id"> {{ label }} </label>
    </div>
</template>

<script>
export default {
    name: 'Checkbox',
    props: {
        id: {
            type: String,
            required: true,
        },
        label: {
            type: String,
            default: '',
        },
        modelValue: {
            type: Boolean,
            default: false,
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

<style lang="scss" scoped>
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
