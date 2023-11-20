import moment from "moment";


function formatDateTimeDualLine(value) {
    if (value !== null && value !== '-') {
        return moment(value).format("DD/MM/YYYY [<br>][ às ] HH:mm");
    }
    return ' - ';
}

function formatDateSingeLine(value) {
    if (value !== null && value !== '-') {
        return moment(value).format("DD/MM/YYYY");
    }
    return ' - ';
}

function formatStatusTransaction(status = '') {
    let statusLow = status.toLowerCase();
    if (statusLow === 'paid') return "Pago";
    if (statusLow === 'pending') return "Pendente";
    if (statusLow === 'canceled') return "Cancelado";
    if (statusLow === 'refunded') return "Estornado";
    if (statusLow === 'failed') return "Falha no pagamento";
    if (statusLow === 'chargeback') return "Chargeback";
    if (statusLow === 'expired') return "Expirado";
    if (statusLow === 'pending_refund') return "Estorno pendente";

    return " - ";
}

function formatPaymentMethod(status = '') {
    let statusLow = status.toLowerCase();
    if (statusLow === 'credit_card') return "Cartão de Crédito";
    if (statusLow === 'boleto') return "Boleto";
    if (statusLow === 'pix') return "Pix";
    if (statusLow === 'paypal') return "Paypal";

    return " - ";
}

function formatWhatsappLink(number) {
    const baseURL = 'https://api.whatsapp.com/send?phone=';
    return `${baseURL}${number}`.replace(" ", "").replace("(", "").replace(")", "");
}

function formatBRLCurrency(value) {
    return new Intl.NumberFormat("pt-BR", {style: "currency", currency: "BRL"}).format(value);
}

export const urlRegex = (url) => {
    let httpRegex = /^https?:\/\/(?:www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b(?:[-a-zA-Z0-9()@:%_\+.~#?&\/=]*)$/;
    return httpRegex.test(url);
}

export const emailRegex = (email) => {
    const validateEmailRegex = /^\S+@\S+\.\S+$/;
    return validateEmailRegex.test(email);
}

export {
    formatDateTimeDualLine,
    formatDateSingeLine,
    formatStatusTransaction,
    formatPaymentMethod,
    formatWhatsappLink,
    formatBRLCurrency
}
