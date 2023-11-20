<template>
    <div class="form-check form-switch ms-2">
        <input
            class="form-check-input"
            type="checkbox"
            :checked="modelValue"
            @change="$emit('update:modelValue', !modelValue)"
            :id="id"
        />
        <label :for="id">
            <slot></slot>
        </label>
    </div>
</template>

<script>
export default {
    name: "XgrowSwitchButton",
    props: {
        id: {
            type: String,
            required: false
        },
        modelValue: {
            type: Boolean,
            default: false
        }
    }
};
</script>

<style scoped lang="scss">
input, label{
    cursor: pointer;
}
</style>
