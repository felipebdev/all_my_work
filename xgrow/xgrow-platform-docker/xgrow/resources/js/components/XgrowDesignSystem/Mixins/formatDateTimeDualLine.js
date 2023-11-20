import moment from "moment-timezone";

export default {
    methods: {
        formatDateTimeDualLine: function (value, withoutTimezone = false) {
            if (value === null || value === '-') return ' - ';
            const time = moment(value);
            if (withoutTimezone) {
                return time.format("DD/MM/YYYY [<br>][ às ]HH:mm");
            }
            /** America/Denver is a hack for correct exibition on reports */
            return time.tz('America/Denver', false).format("DD/MM/YYYY [<br>][ às ]HH:mm");
        }
    }
}
