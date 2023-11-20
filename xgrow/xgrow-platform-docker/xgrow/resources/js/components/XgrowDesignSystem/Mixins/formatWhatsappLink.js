export default {
    methods: {
        formatWhatsappLink: function (value = 0) {
            const baseURL = 'https://api.whatsapp.com/send?phone=';
            return `${baseURL}55${value}`.replace(" ", "").replace("(", "").replace(")", "");
        }
    }
}
