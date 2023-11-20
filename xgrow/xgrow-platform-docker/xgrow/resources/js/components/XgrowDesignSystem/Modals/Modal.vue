<template>
    <div class="bg" :style="controller ? 'display:block' : 'display:none'">
        <div
            class="modal-sections modal"
            tabindex="-1"
            data-bs-backdrop="static"
            data-bs-keyboard="false"
            :style="controller ? 'display:block' : 'display:none'"
        >
            <div :class="`modal-dialog modal-dialog-centered modal-${modalSize}`">
                <div class="modal__content">
                    <button
                        type="button"
                        class="modal__close"
                        @click="closeModal"
                    >
                        <i class="fa fa-times"></i>
                    </button>
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Modal",
    props: {
        isOpen: {required: true, type: Boolean},
        modalSize: {required: false, type: String, default: "md"}
    },
    watch: {
        isOpen: function (val) {
            this.controller = val;

            if (val === true) {
                document.querySelector('html').style.overflowY = 'hidden';
            } else {
                document.querySelector('html').style.overflowY = 'auto';
            }
        }
    },
    data() {
        return {
            controller: false
        };
    },
    emits: ["close"],
    methods: {
        closeModal: function () {
            this.controller = false;
            this.$emit("close", false);
        }
    },
    created() {
        this.controller = this.isOpen;
    }
};
</script>

<style lang="scss" scoped>
.modal__content {
    width: 100%;
    min-height: 320px;
    background-color: var(--card-color);
    position: relative;
    border-radius: 7px;
}

.modal__close {
    color: var(--font-color);
    background-color: transparent;
    border: 0;
    outline: 0;
    width: fit-content;
    position: absolute;
    right: 18px;
    top: 18px;

    i {
        font-size: 1.5rem;
    }
}

.bg {
    background: rgba(0, 0, 0, 0.5);
    width: 100%;
    height: 100%;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 9999;
    display: block;
}

.modal {
    overflow-y: auto;
}

.modal-content > hr {
    background-color: #565656;
    height: 2px;
    margin: 10px 0 !important;
}
.modal-dialog {
    pointer-events: inherit;
}
@media (min-width: 992px) {
    .modal-md {
        max-width: 600px;
    }
}
</style>
