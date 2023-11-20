import DynamicObject from '../DynamicObject.js';
/**
 * @class Tabs
 */
export default class Tabs extends DynamicObject
{
	/**
	 * Tabs constructor
	 * @param {string|HTMLElement} selector
	 * @param {Object} [options = {}]
	 */
	constructor(selector, options = {  })
	{
		super(selector, options);

		this._$tab = this._$element;
		this._$tabContent = $(`${this._selector}-content`);
		this._currentTab = 0;
		this._lastHash = window.location.hash;

		this._$tabLinks = this._$tab.find('.nav-item');
		this._totalTabs = this._$tabLinks.length;
		this._tabsCounter = 0;

		this._$tab.on('click', (e) => { window.location.hash = $(e.target).attr('href'); });
		if (!window.location.hash) window.location.hash = $(this._$tab.find('.nav-link')[0]).attr('href');

		$(window).on('hashchange', (e) => { this.update(); });

		console.log('this._totalTabs', this._totalTabs);
	}
	update()
	{
		const hash = window.location.hash;

		if (hash === '#undefined')
		{
			window.location.hash = this._lastHash;
			return;
		}

		this._$tab.find('.active').removeClass('active show');
		this._$tabContent.find('.active').removeClass('active show');

		$(`[href="${hash}"]`).addClass('active show');

		this._currentTab = $(hash).addClass('active show').index();

		this._$tab.find('.selected').removeClass('selected');
		this._$tab.find('.active').closest('li').addClass('selected');

		this._lastHash = hash.replace('#', '');

		this._tabsCounter = this._currentTab;

		this._change.dispatch(this._currentTab, this._lastHash);
	}
	previous()
	{
		if(--this._tabsCounter < 0) this._tabsCounter = this._totalTabs -1;
		console.log('previous tab', this._tabsCounter);
		$(this._$tabLinks[this._tabsCounter]).find('a').trigger({ type:'click' });
	}
	next()
	{
		if(++this._tabsCounter >= this._totalTabs) this._tabsCounter = 0;
		console.log('next tab', this._tabsCounter);
		$(this._$tabLinks[this._tabsCounter]).find('a').trigger({ type:'click' });
	}

	get currentIndex()
	{
		return this._tabsCounter;
	}
}
