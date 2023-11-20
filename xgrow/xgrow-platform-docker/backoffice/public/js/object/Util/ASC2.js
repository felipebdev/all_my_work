/**
 *
 * @type {number}
 */
export const C = 67;
/**
 *
 * @type {number}
 */
export const V = 86;
/**
 *
 * @type {number}
 */
export const X = 88;
/**
 *
 * @type {number[]}
 */
export const ARROWS = [37, 38, 39, 40];
/**
 *
 * @type {number[]}
 */
export const NUMBERS = [48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105];
/**
 *
 * @type {number[]}
 */
export const POINT = [108, 190];
/**
 *
 * @type {number[]}
 */
export const PLUS = [43, 107];
/**
 *
 * @type {number[]}
 */
export const MINUS = [109, 173];
/**
 *
 * @type {number[]}
 */
export const COMMA = [44, 110];
/**
 *
 * @type {number}
 */
export const TAB = 9;
/**
 *
 * @type {number}
 */
export const BACKSPACE = 8;
/**
 *
 * @type {number}
 */
export const DELETE = 46;
/**
 *
 * @type {number}
 */
export const ENTER = 13;
/**
 *
 * @type {number}
 */
export const ESC = 27;
/**
 *
 * @type {number}
 */
export const CONTROL = 17;
/**
 *
 * @type {number}
 */
export const ALT = 18;
/**
 *
 * @type {number}
 */
export const SHIFT = 16;
/**
 *
 * @type {number}
 */
export const WINDOWS = 91;
/**
 *
 * @type {number}
 */
export const RIGHT_MOUSE_MENU = 93;
/**
 *
 * @type {number[]}
 */
export const NECESSARY_KEYS = [TAB, BACKSPACE, DELETE, ENTER, ESC, CONTROL, ALT, SHIFT, WINDOWS, RIGHT_MOUSE_MENU, ...ARROWS];
/**
 *
 * @type {(number|number)[]}
 */
export const NUMBERS_AND_SIGNS = [...NUMBERS, ...POINT, ...PLUS, ...MINUS, ...COMMA, ...NECESSARY_KEYS];
/**
 * @class {ASC2}
 */
export default class ASC2
{
	/**
	 * @constructor
	 */
	constructor()
	{
		throw new Error(`${this.constructor.name} must not be instantiated`);
	}
	/**
	 *
	 * @param event
	 * @returns {boolean}
	 */
	static checkSelection(event)
	{
		if(!event.ctrlKey && !event.shiftKey) return false;
		return ARROWS.indexOf(event.keyCode || event.which) !== -1;
	}
	/**
	 *
	 * @param event
	 * @returns {boolean}
	 */
	static checkCutCopyAndPaste(event)
	{
		return ASC2.checkCut(event) || ASC2.checkCopy(event) || ASC2.checkPaste(event);
	}
	/**
	 *
	 * @param event
	 * @returns {boolean}
	 */
	static checkCopyAndPaste(event)
	{
		return ASC2.checkCopy(event) || ASC2.checkPaste(event);
	}
	/**
	 *
	 * @param event
	 * @returns {boolean}
	 */
	static checkCutAndPaste(event)
	{
		return ASC2.checkCut(event) || ASC2.checkPaste(event);
	}
	/**
	 *
	 * @param event
	 * @returns {boolean}
	 */
	static checkCopy(event)
	{
		if(!event.ctrlKey) return false;
		return (event.keyCode || event.which) === C;
	}
	/**
	 *
	 * @param event
	 * @returns {boolean}
	 */
	static checkCut(event)
	{
		if(!event.ctrlKey) return false;
		return (event.keyCode || event.which) === X;
	}
	/**
	 *
	 * @param event
	 * @returns {boolean}
	 */
	static checkPaste(event)
	{
		if(!event.ctrlKey) return false;
		return (event.keyCode || event.which) === V;
	}
}
