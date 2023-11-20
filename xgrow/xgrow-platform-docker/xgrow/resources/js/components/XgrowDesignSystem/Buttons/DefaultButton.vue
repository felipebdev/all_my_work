<template>
    <button :class="[`btn ${status}`, { outline: outline }]" @click="hasHandleClick" :disabled="disabled">
        <i v-if="disabled && isLoading" class="fa fa-spinner fa-spin fa-fw"></i>
        <i v-else-if="icon" :class="`${icon} ${text ? 'icon' : ''}`"></i>
        {{ text }}
    </button>
</template>

<script>
export default {
    name: "DefaultButton",
    props: {
        text: { type: String, required: true },
        icon: { type: String, default: "" },
        status: { type: String, default: "" },
        disabled: { type: Boolean, default: false },
        outline: { type: Boolean, default: false },
        onClick: { type: Function, required: false },
        isLoading: {
            type: Boolean,
            default: false,
        },
    },
    computed: {
        hasHandleClick() {
            const isAFunction = typeof this.onClick == 'function';

            return isAFunction ? this.onClick : this.$emit('click');
        }
    }
};
</script>

<style lang="scss" scoped>
button {
    height: 42px;
    border-radius: 8px;
    padding-left: 1.5rem;
    padding-right: 1.5rem;
    border: none;
    color: #ffffff;

    &:hover {
        filter: brightness(1.1);
        color: #ffffff;
    }

    &:disabled {
        background-color: #393d49 !important;
        color: #595b63 !important;
        cursor: not-allowed;
    }

    &.success {
        background: #93bc1e;

        &:hover {
            background: #93bc1e !important;
            border-color: #93bc1e !important;
        }
    }

    &.danger {
        background: #e22222;

        &:hover {
            background: #e22222 !important;
            border-color: #e22222 !important;
        }
    }

    &.warning {
        background: #e28a22;

        &:hover {
            background: #e28a22 !important;
            border-color: #e28a22 !important;
        }
    }

    &.info {
        background: #393d49;

        &:hover {
            background: #393d49 !important;
            border-color: #393d49 !important;
        }
    }

    &.dark {
        background: #222429;

        &:hover {
            background: #222429 !important;
            border-color: #222429 !important;
        }
    }
}

.icon {
    margin-right: 10px;
}

.outline {
    background-color: transparent !important;
    border: 1px solid #ffffff;

    &:hover {
        filter: brightness(1.1);
    }
}

.btn:focus,
.btn:active {
    outline: none !important;
    box-shadow: none;
}
</style>
