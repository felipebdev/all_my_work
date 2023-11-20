<template>
    <modal-component :isOpen="isOpen" @close="closeFunction">
        <template v-slot:title>Confirmar reenvio de confirmação de compra</template>

        <template v-slot:content>
            <p class="text-center w-100">
                Você tem certeza que deseja reenviar o comprovante de
                <br>
                confirmação de compra por e-mail?
            </p>
        </template>

        <template v-slot:footer>
            <button
                type="button"
                class="btn btn-success"
                @click.prevent="confirmAction"
            >
                Sim, reenviar
            </button>
            <button
                type="button"
                class="btn btn-outline-success"
                @click.prevent="closeFunction"
            >
                Não reenviar
            </button>
        </template>
    </modal-component>
</template>

<script>
import ModalComponent from "../../../../../../js/components/ModalComponent.vue";
import axios from 'axios';

export default {
    name: "payment-proof-modal",
    components: {
        "modal-component": ModalComponent,
    },
    props: {
        isOpen: {
            type: Boolean,
            required: true,
        },
        closeFunction: {
            type: Function,
            required: true,
        },
        modalData: {
            type: Object,
            required: true,
        }
    },
    data() {
        return {}
    },
    methods: {
        confirmAction: function () {
            successToast('Aguarde, por favor...',  'Estamos reenviando o comprovante de confirmação de compra!');
            this.closeFunction();
            const sendBuyedProofUrl = sendBuyedProofURL.replace(/:paymentId/g, this.modalData.paymentId);

            axios.get(sendBuyedProofUrl).then(res => {
                successToast('Comprovante reenviado!',  'O comprovante de confirmação de compra foi reenviado com sucesso.');
            }).catch(error => {
                errorToast('Algum erro aconteceu!',  'Não foi possível reenviar o comprovante de confirmação de compra, ' +
                    'recarrega a página e tente novamente.');
                console.log(error)
            });
        },
    },
};
</script>

<style scoped lang="scss">
@import "../../../../../../sass/util";

:deep(.modal-body) {
    padding: 0;
    width: 100%;
    flex-direction: column;
    justify-content: flex-start;
}

:deep(.modal-footer) {
    padding: 0;
}

.value {
    span {
        font-weight: 700;
    }
}

.message {
    font-size: pxToRem(12px);
    font-style: italic;
}

.type-toggle {
    margin: 12px 0;
    padding: 0 12px;
    width: 100%;

    label {
        display: inline-block;
        color: #ffffff;
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .buttons-toggle {
        button {
            box-sizing: border-box;
            width: 50%;
            border: 1px solid #ffffff;
            color: #ffffff;
            padding: 6px 12px;
            background-color: transparent;

            &.active {
                border: 1px solid #93bc1e !important;
                color: #93bc1e;
                font-weight: bold;
            }

            &:first-child {
                border-top-left-radius: 0.25rem;
                border-bottom-left-radius: 0.25rem;
                border-right: none;
            }

            &:last-child {
                border-top-right-radius: 0.25rem;
                border-bottom-right-radius: 0.25rem;
                border-left: none;
            }
        }
    }
}
</style>
