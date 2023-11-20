<template>
    <div class='bg' :style="open ? 'display:block' : 'display:none'">
        <div class='modal-sections modal' tabindex='-1' data-bs-backdrop='static' data-bs-keyboard='false'
            :style="open ? 'display:block' : 'display:none'">
            <div :class="`modal-dialog modal-dialog-centered modal-${modalSize}`">
                <div class='modal-content bg-black-80'>
                    <div class='modal-header' v-if="$slots.header">
                        <slot name="header"></slot>
                    </div>
                    <div class='modal-body' :class="{ 'gap-0': noGap }">
                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    name: 'ConfirmModal',
    props: {
        isOpen: { type: Boolean, default: false },
        modalSize: { required: false, type: String, default: "md" },
        noGap: { type: Boolean, default: false },
    },
    data() {
        return {
            open: false,
        }
    },
    watch: {
        isOpen: function (_new, _old) {
            this.open = _new
        },
    },
    created() {
        this.open = this.isOpen
    },
}
</script>

<style lang='scss' scoped>
.bg {
    background: rgba(0, 0, 0, 0.5);
    width: 100%;
    height: 100%;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 99999;
    display: block;
}

.modal-header {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    border: none;
    padding: 12px 15px 12px 0 !important;

    button {
        cursor: pointer;
        background: transparent;
        border: none;

        img {
            height: 22px;
        }
    }
}

.modal-body {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    color: #FFFFFF;
    gap: 1.25rem;
    padding: 28px;

    img {
        height: 48px;
    }
}

:slotted(.modal-body__content) {
    margin-top: 2rem;

    h1 {
        line-height: 2rem;
        font-size: 1.25rem;
    }

    svg {
        color: #9B9B9B;
    }
}

:slotted(.modal-body__footer) {
    width: 100%;
    display: flex;
    justify-content: center;
    gap: 1.25rem;
    margin-top: 2rem;

    button {
        min-width: 170px;
    }
}

.modal-content {
    max-width: 100% !important;
    padding: 0 0 20px 0;
    border-radius: 7px;
}

.modal-sections {
    overflow: auto;
}

.gap-0 {
    gap: 0 !important;
}

@media (min-width: 992px) {
    .modal-md {
        max-width: 600px;
    }
}
</style>
