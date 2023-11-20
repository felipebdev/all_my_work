export default {
    methods: {
        formatPaymentMethod: function (value = '') {
            const status = value.toLowerCase();
            if (status === 'credit_card') return "Cartão de Crédito";
            if (status === 'boleto') return "Boleto";
            if (status === 'pix') return "Pix";
            if (status === 'paypal') return "Paypal";
            return " - ";
        }
    }
}
