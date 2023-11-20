<template>
    <modal-component :isOpen="isOpen" @close="closeFunction">
        <template v-slot:title>Cancelar recorrência</template>

        <template v-slot:content>
            <p class="text-center w-100">
                Você tem certeza que deseja cancelar esta recorrência?
                <br>
                O produto será cancelado, junto com os pagamentos futuros.
            </p>
        </template>

        <template v-slot:footer>
            <button
                type="button"
                class="btn btn-success"
                @click.prevent="confirmAction"
            >
                Sim, cancelar
            </button>
            <button
                type="button"
                class="btn btn-outline-success"
                @click.prevent="closeFunction"
            >
                Não cancelar
            </button>
        </template>
    </modal-component>
</template>

<script>
import ModalComponent from "../../../../../../js/components/ModalComponent.vue";
import axios from 'axios';

export default {
    name: "CancelRecurrenceModal",
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
            successToast("Aguarde, por favor...", 'Estamos cancelando a recorrência!');
            this.closeFunction();
            const cancelRecurrenceUrl = cancelRecurrenceURL.replace(/:orderNumber/g, this.modalData.payment_order_number);

            axios.delete(cancelRecurrenceUrl).then(res => {
                successToast("Recorrência cancelada!", 'Recorrência cancelada com sucesso.');
            }).catch(error => {
                errorToast("Algum erro aconteceu!", 'Erro ao cancelar recorrência, ' +
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
