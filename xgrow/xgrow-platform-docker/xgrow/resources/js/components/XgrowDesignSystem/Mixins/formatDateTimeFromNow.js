import moment from "moment";

export default {
    methods: {
        formatDateTimeFromNow: function (value) {
            if (value === null || value === '-') return ' - ';
            return moment(value).fromNow();
        }
    }
}
