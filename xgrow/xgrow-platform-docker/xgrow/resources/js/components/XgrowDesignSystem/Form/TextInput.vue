<template>
    <div class="form-group">
        <textarea
            :id="id"
            :placeholder="placeholder"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :disabled="disabled"
            :cols="cols"
            :rows="rows"
            class="form-control"
            :maxlength='limit'
        >
        </textarea>
        <div class='hint' v-if='limit'>{{ count }}/{{ limit }} caracteres</div>
        <label class="text-area-label" :for="id">{{ label }}</label>
    </div>
</template>

<script>

export default {
    name: "TextInput",
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
        disabled: {
            type: Boolean,
            default: false
        },
        cols: {
            type: String,
            default: "10"
        },
        rows: {
            type: String,
            default: "8"
        },
        limit: {
            type: Number,
            required: false,
        },
    },
    data() {
        return {
            count: this.limit,
        }
    },
    watch: {
        modelValue: function (old, _) {
            if (this.limit) {
                this.count = this.limit - (this.modelValue ? this.modelValue.toString().length : 0);
            }
        }
    }
};
</script>

<style scoped>
.form-group {
    display: flex;
    flex-direction: column;
    margin: 12px 0;
    position: relative;
}

.form-group label {
    background: #252831;
    font-size: 14px;
    font-weight: 700;
}

label {
    padding-top: 6px;
    width: 99%;
    display: inline-block;
    position: absolute;
    color: #FFFFFF;
    font-size: 14px;
    padding-left: 12px;
    font-weight: 700;
}

textarea {
    padding-top: 35px;
}

textarea, textarea:focus {
    background: #252932;
    cursor: default;
    border: none;
    border-bottom: 1px solid #FFFFFF;
    color: #ffffff;
    resize: none;
    border-radius: 0;
}

textarea::placeholder {
    color: #C1C5CF;
    font-weight: 400;
    margin-top: 4rem;
}

textarea:focus {
    border-bottom: 1px solid #93BC1E;
}

textarea:valid:not(:placeholder-shown) {
    color: #E7E7E7;
}

textarea:focus ~ label, textarea:focus ~ .form-group label {
    color: #93BC1E!important;
}

textarea:valid:not(:placeholder-shown) {
    border-bottom-color: #93BC1E;
    color: #E7E7E7;
}

textarea:valid:not(:placeholder-shown) ~ label, textarea:valid:not(:placeholder-shown) ~ span {
    color: #93BC1E;
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

textarea:disabled {
    background-color: #393D49 !important;
    border-bottom-color: #595B63 !important;
}

textarea:disabled ~ label {
    color: #595B63 !important;
    background: none !important;
}
</style>
