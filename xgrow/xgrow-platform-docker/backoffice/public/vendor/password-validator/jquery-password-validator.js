/**
 * Password Strong Verification
 * Obs: Depends on the jQuery, password-validation.css (in this order).
 * @author Felipe Panegalli
 * @param el - jQuery #ID element Selector
 * @param elCompare - jQuery #ID element Selector for comparison
 * @param requireNumber - require number
 * @param requireLetters - require letters
 * @param requireSpecialChars - require special chars
 * @param length - password length
 * @returns {el} - return div element
 * @type (el: string, requireNumber: bool, requireLetters: bool, requireSpecialChars: bool, length: number) => string
 **/
(function ($) {
    $.fn.passwordValidator = function (elCompare = '', requireNumber = false, requireLetters = true, requireSpecialChars = false, length = 8) {
        $('#validator-lenght').text(length.toString());
        const elOriginal = $(this);

        $(this).add(elCompare).keypress(function (e) {
            if (e.which === 32) return false;
        }).focus(function () {
            $('.password-policies').addClass('active');
        }).blur(function () {
            $('.password-policies').removeClass('active');
        });

        $(this).add(elCompare).keyup(function (e) {
            if (elCompare !== '') {
                elOriginal.val() !== $(elCompare).val() ? $('.policy-compare').addClass('active') : $('.policy-compare').removeClass('active');
            }
        });

        $(this).keyup(function (e) {
            let value = e.target.value;
            if (requireNumber) {
                /([0-9])+/g.test(value) ? $('.policy-number').addClass('active') : $('.policy-number').removeClass('active');
            }
            if (requireLetters) {
                /([a-zA-z])+/g.test(value) ? $('.policy-letter').addClass('active') : $('.policy-letter').removeClass('active');
            }
            if (requireSpecialChars) {
                /([!-/:-@[-`{-˜])+/g.test(value) ? $('.policy-special').addClass('active') : $('.policy-special').removeClass('active');
            }
            value.length > length ? $('.policy-length').addClass('active') : $('.policy-length').removeClass('active');
        });
    };
})(jQuery);
