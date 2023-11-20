/**
 *
 * @type {*|jQuery|HTMLElement}
 */
const $loader = $('<div id="report-loader"><div class="loader-container"><div class="loader"></div></div></div>'),
$body = $('body');
/**
 * @class {Loader}
 */
export default class Loader
{
	static create()
	{
		$body.removeClass('no-interaction').append($loader.fadeOut(0));
	}
	static show(duration = 500)
	{
		$body.addClass('no-interaction');
		$loader.fadeIn(duration);
	}
	static hide(duration = 500)
	{
		$body.removeClass('no-interaction');
		$loader.fadeOut(duration);
	}
}
