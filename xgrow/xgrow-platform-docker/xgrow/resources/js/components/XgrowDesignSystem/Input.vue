<template>
    <div class="form-group" :class="{ 'no-label': label === '' || label === null }">
        <input
            :id="id"
            :placeholder="placeholder || label"
            :value="modelValue"
            :type="type"
            @input="$emit('update:modelValue', $event.target.value)"
            :disabled="disabled"
            v-maska="mask"
        >
        <label :for="id">{{ label }}</label>
        <span v-if="icon" v-html="icon" :style="`color: ${iconColor}`"></span>
    </div>
</template>

<script>
import {maska} from "maska";

export default {
    name: "InputX",
    props: {
        id: {
            type: String,
            required: true
        },
        label: {
            type: String,
            default: ""
        },
        placeholder: {
            type: String,
            default: ""
        },
        modelValue: {
            type: [String, Number],
            default: ""
        },
        icon: {
            type: String,
            default: null
        },
        iconColor: {
            type: String,
            default: '#ffffff'
        },
        type: {
            type: String,
            default: "text"
        },
        disabled: {
            type: Boolean,
            default: false
        },
        mask: {
            type: String,
            default: null
        }
    },
    directives: {maska},
    watch: {
        modelValue: function (oldValue, _) {
            if (oldValue.length > 0) {

            }
        }
    }
};
</script>

<style scoped>
.form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin: 12px 0;
    position: relative;
}

.form-group.no-label > input {
    padding-top: 0 !important;
}

input {
    height: 65px;
    background-color: #252932;
    border: none;
    border-bottom: 1px solid #FFFFFF;
    padding-left: 12px;
    padding-top: 24px;
    padding-right: 36px;
    color: #E7E7E7;
    transition: border .5s ease;
}

input::placeholder {
    color: #C1C5CF;
    font-weight: 400;
}

input:focus {
    border-bottom: 1px solid #93BC1E;
}

label {
    display: inline-block;
    position: absolute;
    color: #FFFFFF;
    font-size: 14px;
    padding-left: 12px;
    font-weight: 700;
    top: 6px;
}

input:focus ~ label, input:focus ~ span {
    color: #93BC1E;
}

input:not(:placeholder-shown) {
    border-bottom-color: #F96C6C;
}

input:valid:not(:placeholder-shown) {
    border-bottom-color: #93BC1E;
    color: #E7E7E7;
}

input:valid:not(:placeholder-shown) ~ label, input:valid:not(:placeholder-shown) ~ span {
    color: #93BC1E;
}

span {
    color: #FFFFFF;
    position: absolute;
    right: 12px;
    top: 18px;
}

input:disabled {
    cursor: not-allowed;
    background-color: #393D49;
    border-color: #595B63;
}

input:disabled ~ label, input:disabled ~ span {
    color: #595B63;
}

</style>
