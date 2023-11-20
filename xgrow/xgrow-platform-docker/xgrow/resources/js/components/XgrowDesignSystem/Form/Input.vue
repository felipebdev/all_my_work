<template>
    <div class="form-group" :class="{ 'no-label': label === '' || label === null }">
        <input :id="id" :placeholder="placeholder || label" :value="modelValue" :type="type"
            :class="{ 'is-search': isSearch }" @input="$emit('update:modelValue', $event.target.value)" :disabled="disabled"
            :readonly="readonly" v-maska="mask || pattern" :maxlength='maxlength || limit' :autocomplete="autocomplete"
            :required="required">
        <label :for="id" v-if="label">{{ label }}</label>
        <span v-if="icon" v-html="icon" :style="`color: ${iconColor}`"></span>
        <div v-if="limit" class="hint">{{ count }}/{{ limit }} caracteres</div>
        <div v-if="info" class="hint">{{ info }}</div>
        <DefaultButton v-if="hasClipboard" class="clipboard-button" text="" icon="fas fa-copy" status="success"
            title="Copiar para a área de transferência" @click="copyToClipboard" />
    </div>
</template>

<script>
import { maska } from "maska";
import IconButton from "../Buttons/IconButton";
import DefaultButton from "../Buttons/DefaultButton";

export default {
    name: "Input",
    components: { DefaultButton, IconButton },
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
            type: [String, Number, Date],
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
            type: [String, Array],
            default: null
        },
        pattern: {
            type: [Object, null],
            default: null
        },
        limit: {
            type: Number,
            required: false,
        },
        maxlength: {
            type: Number,
            default: null
        },
        readonly: {
            type: Boolean,
            default: false,
        },
        info: {
            type: String,
            default: null
        },
        hasClipboard: {
            type: Boolean,
            default: false,
        },
        autocomplete: {
            type: String,
            default: 'chrome-off'
        },
        required: {
            type: Boolean,
            default: false
        },
        isSearch: {
            type: Boolean,
            default: false
        }
    },
    directives: { maska },
    data() {
        return {
            count: this.limit,
        }
    },
    watch: {
        modelValue: function (old, _) {
            if (this.limit) {
                this.count = this.limit - this.modelValue.toString().length;
            }
        }
    },
    methods: {
        copyToClipboard: function () {
            const copyText = document.getElementById(this.id);
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value);
            successToast("Cópia realizada!", "O texto foi copiado com sucesso para a área de transferência.");
        }
    }
};
</script>

<style lang="scss" scoped>
.form-group {
    display: flex;
    flex-direction: column;
    margin: 12px 0;
    position: relative;
}

.form-group.no-label>input {
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

input:focus~label,
input:focus~span {
    color: #93BC1E;
}

input:not(:placeholder-shown) {
    border-bottom-color: #F96C6C;
}

input:valid:not(:placeholder-shown) {
    border-bottom-color: #93BC1E;
    color: #E7E7E7;
}

input:valid:not(:placeholder-shown)~label,
input:valid:not(:placeholder-shown)~span {
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
    border-color: #595B63 !important;
}

input:disabled~label,
input:disabled~span,
input:disabled~.hint {
    color: #595B63;
}

.hint {
    font-family: 'Open Sans', serif;
    font-style: normal;
    font-weight: 600;
    font-size: 12px;
    line-height: 19px;
    color: #E7E7E7;
    margin-top: 5px;
}

input:read-only {
    border-color: #93BC1E;
}

input:read-only~label {
    color: #93BC1E;
}

input:disabled~label,
input:disabled~span {
    color: #595B63;
}

input[type="date"],
input[type="time"] {
    -webkit-align-items: center !important;
    -webkit-justify-content: flex-start !important;
    -webkit-align-content: flex-start !important;
    display: -webkit-inline-flex !important;
    -webkit-appearance: none !important;
    box-sizing: border-box !important;
    -moz-box-sizing: border-box !important;
    -webkit-box-sizing: border-box !important;
    text-align: -webkit-left !important;
}

input[type='date']~label,
input[type='time']~label {
    margin-top: 0 !important;
}

input[type='date']:not(:focus),
input[type='time']:not(:focus),
input[type='date']:focus,
input[type='time']:focus {
    padding-top: 23px !important;
    padding-right: 4px;
}

.clipboard-button {
    width: 40px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    left: calc(100% - 55px);
    bottom: 57px;
    margin-bottom: -50px;
}

// Used if is-search active
.is-search {
    height: 40px;
}

.is-search~span {
    top: 9px;
}
</style>
