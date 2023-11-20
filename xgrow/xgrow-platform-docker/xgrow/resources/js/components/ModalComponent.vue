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
                <div class="modal-content">
                    <div class="modal-header">
                        <p>
                            <slot name="title"></slot>
                        </p>
                        <button
                            type="button"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                            @click="closeModal"
                        >
                            <i class="fa fa-times" style="font-size: 2rem"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <slot name="content"></slot>
                    </div>
                    <hr/>
                    <div class="modal-footer">
                        <slot name="footer" :closeModal="closeModal"></slot>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "ModalComponent",
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

<style scoped>
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

.modal-header {
    justify-content: space-between !important;
    border-bottom: 1px solid #565656 !important;
    margin: 0 0 5px !important;
    padding: 10px 0 !important;
}

.modal-content {
    padding: 10px 20px !important;
    max-width: 100% !important;
    /*padding: 0 0 65px 0 !important;*/
}

.modal-content > hr {
    background-color: #565656;
    height: 2px;
    margin: 10px 0 !important;
}

.modal-body {
    justify-content: center !important;
    align-items: flex-start !important;
    text-align: left !important;
}

@media (min-width: 992px) {
    .modal-md {
        max-width: 600px;
    }
}
</style>
