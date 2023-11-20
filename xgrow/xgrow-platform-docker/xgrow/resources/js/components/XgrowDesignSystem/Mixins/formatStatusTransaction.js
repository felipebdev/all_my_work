export default {
    methods: {
        formatStatusTransaction: function (value = '') {
            const status = value.toLowerCase();
            if (status === 'paid') return "Pago";
            if (status === 'pending') return "Pendente";
            if (status === 'canceled') return "Cancelado";
            if (status === 'refunded') return "Estornado";
            if (status === 'failed') return "Falha no pagamento";
            if (status === 'chargeback') return "Chargeback";
            if (status === 'expired') return "Expirado";
            if (status === 'pending_refund') return "Estorno pendente";
            return " - ";
        }
    }
}
