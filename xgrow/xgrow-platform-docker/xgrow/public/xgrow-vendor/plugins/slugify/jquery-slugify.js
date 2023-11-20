/**
 * Generate Slug for url
 * Permitted only char and number
 * Obs: Depends on the jQuery, speakingurl and slugify (in this order).
 * @author Felipe Panegalli
 * @param idTarget - jQuery #ID Selector
 * @param slugUrl - domain
 * @param slugSeparator - separator for slug
 * @param limit - max slug size
 * @returns {string} - return Slug to target value
 * @type (idTarget: string, slugUrl: string, slugSeparator: string, limit: number) => string
 **/
(function ($) {
  $.fn.urlSlugify = function (idTarget = '', slugUrl = '', slugSeparator = '-', limit = 30) {
    $(this).keyup(function (evt) {
      let slug = $.slugify(evt.target.value, {separator: slugSeparator});
      let url = slugUrl + slug.toString();
      $(idTarget).val(url);
    }).keypress(function (e) {
      let regex = new RegExp('^[a-zA-Z0-9-]+$');
      let text = String.fromCharCode(!e.charCode ? e.which : e.charCode);
      if (e.target.value.length < limit && regex.test(text)) return true;
      e.preventDefault();
      return false;
    });
  };
})(jQuery);
