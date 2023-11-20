<template>
    <div class="container-badge " :class="getColorStatus">
        <span class="text-badge " :class="getColorStatus">{{ getStatus }}</span>

    </div>
</template>

<script>
export default {
    name: "StatusBadge",
    props: {
        status: {
            type: String,
            required: true
        }
    },
    computed: {
        getStatus: function () {
            if (['paid', 'pago'].includes(this.status.toLowerCase())) {
                return 'Pago'.toUpperCase()
            }
            if (['pending', 'pendente'].includes(this.status.toLowerCase())) {
                return 'Pendente'.toUpperCase()
            }
            if (['blocked', 'bloqueado'].includes(this.status.toLowerCase())) {
                return 'Bloqueado'.toUpperCase()
            }
            if (['refused', 'recusado'].includes(this.status.toLowerCase())) {
                return 'Recusado'.toUpperCase()
            }
            if (['canceled', 'cancelado'].includes(this.status.toLowerCase())) {
                return 'Cancelado'.toUpperCase()
            }
            if (['refunded', 'estornado'].includes(this.status.toLowerCase())) {
                return 'Estornado'.toUpperCase()
            }
            if (['failed', 'falha no pagamento'].includes(this.status.toLowerCase())) {
                return 'Falha no pagamento'.toUpperCase()
            }
            if (['chargeback'].includes(this.status.toLowerCase())) {
                return 'Chargeback'.toUpperCase()
            }
            if (['expired', 'expirado'].includes(this.status.toLowerCase())) {
                return 'Expirado'.toUpperCase()
            }
            if (['pending_refund'].includes(this.status.toLowerCase())) {
                return 'Estorno pendente'.toUpperCase()
            }
            if (['active', 'ativo', 'true'].includes(this.status.toLowerCase())) {
                return 'Ativo'.toUpperCase()
            }
            if (['false'].includes(this.status.toLowerCase())) {
                return 'Inativo'.toUpperCase()
            }
            if (['transferred'].includes(this.status.toLowerCase())) {
                return 'Transferido'.toUpperCase()
            }
            if (['processing'].includes(this.status.toLowerCase())) {
                return 'Processando'.toUpperCase()
            }
            if (['created '].includes(this.status.toLowerCase())) {
                return 'Criado'.toUpperCase()
            }
            if (['recipient_failed'].includes(this.status.toLowerCase())) {
                return 'Falha'.toUpperCase()
            }
            if (['allowed'].includes(this.status.toLowerCase())) {
                return 'Liberado'.toUpperCase()
            }
            return ' - '
        },
        getColorStatus: function () {
            if (['paid', 'pago', 'active', 'ativo', 'true', 'transferred', 'allowed'].includes(this.status.toLowerCase())) {
                return 'success'
            }
            if (['refunded', 'estornado', 'blocked', 'bloqueado', 'false', 'refused', 'recusado', 'recipient_failed'].includes(this.status.toLowerCase())) {
                return 'danger'
            }
            if (['pending', 'pending_refund'].includes(this.status.toLowerCase())) {
                return 'warning-medium'
            }
            if (['canceled', 'cancelado'].includes(this.status.toLowerCase())) {
                return 'info-dark'
            }
            if (['failed', 'falha no pagamento'].includes(this.status.toLowerCase())) {
                return 'warning'
            }
            if (['chargeback', 'processing'].includes(this.status.toLowerCase())) {
                return 'info'
            }
            if (['expired', 'expirado', 'created'].includes(this.status.toLowerCase())) {
                return 'info-medium'
            }
            return ' - '
        },
    },
}
</script>

<style lang="scss" scoped>
.container-badge {
    width: fit-content;
    height: fit-content;
    padding: 0.25rem 0.625rem;
    border-radius: 4px;
    max-width: 110px;
    text-align: center;

    &.success {
        background: #3D4640;
    }

    &.danger {
        background: #473E48;
    }

    &.warning {
        background: #464546;
    }

    &.warning-medium {
        background: #464A46;
    }

    &.info {
        background: #474C56;
    }

    &.info-medium {
        background: #3D4353;
    }

    &.info-dark {
        background: #2C313D;
    }
}

.text-badge {
    &.success {
        color: #93BC1E !important;
    }

    &.danger {
        color: #F96C6C !important;
    }

    &.warning {
        color: #f9c56c !important;
    }

    &.warning-medium {
        color: #F4E558 !important;
    }

    &.info {
        color: #FFFFFF !important;
    }

    &.info-medium {
        color: #C1C5CF !important;
    }

    &.info-dark {
        color: #7A7F8D !important;
    }
}
</style>
