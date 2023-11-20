import moment from "moment";

export default {
    methods: {
        formatDateSingleLine: function (value) {
            if (value === null || value === '-') return ' - ';
            return moment(value).format("DD/MM/YYYY");
        }
    }
}
