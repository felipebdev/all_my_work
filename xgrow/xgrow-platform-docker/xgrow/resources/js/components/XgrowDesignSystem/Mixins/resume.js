export default {
    methods: {
        resume: function (text, index = 50) {
            if (text) {
                text = text.replace(/(\\r)*\\n/g, '<br>');
                return text.substring(0, index) + (text.length > index ? '...' : '');
            }
            return ' - '
        }
    }
}
