export default {
    methods: {
        validator: function (val1, val2, operator, msg) {
            /** Verifica o tipo do comparador e já estoura o erro se não for compatível
             * OBS: Pode adicionar mais mas tem que criar a lógica
             */
            const op = ['===', '!==', '>', '<', '>=', '<='];
            if (!op.includes(operator)) throw new Error('Operador informado é inválido');

            if(operator === '===' && val1 === val2) throw new Error(msg);
            if(operator === '!==' && val1 !== val2) throw new Error(msg);
        }
    }
}
