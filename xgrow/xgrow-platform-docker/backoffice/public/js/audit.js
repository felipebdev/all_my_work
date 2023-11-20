'use strict';
import BackOfficeAPI from './object/Net/BackOfficeAPI.js';
import DataTable from './object/DOM/DataTable.js';
import DatePicker from './object/DOM/DatePicker.js';
import Select2 from './object/DOM/Select2.js';
import Loader from './object/DOM/Loader.js';
import Form from './object/DOM/Form.js';
import StringUtil from './object/Util/StringUtil.js';

const
	log = console.log,
	TEST = false,
	api = new BackOfficeAPI(),
	defaultDataTableOptions =
	{
		url:`audit/read/`,
		initHandler:(e, settings, data) =>
		{

		},
		preXhrHandler:(e, settings, data) =>
		{

		},
		xhrHandler:(e, settings, data) =>
		{

		},
		orderHandler:(e, settings, data) =>
		{

		}
	};

const start = () =>
{
	const
		getFormData = (options = {}) =>
		{
			return {
				...{
					test: false,
					client_ids:inputClients.ids,
					date_start:StringUtil.dateToMySQLFormat(inputDateStart.value),
					date_end:StringUtil.dateToMySQLFormat(inputDateEnd.value)
				},
				...options
			};
		},
		rowGroup =
		{
			startRender: function(rows, group) { return `ID do registro: ${group}`; },
			endRender: null,
			dataSrc: ['id']
		},
		form = new Form('#report-form', { validate:false }),
		inputClients = new Select2('#input-clients'),
		inputDateStart = new DatePicker('#input-date-start'),
		inputDateEnd = new DatePicker('#input-date-end'),
		inputTables = new Select2('#input-tables'),
		clientTable = new DataTable
		(
			'#client-table',
			{
				...defaultDataTableOptions,
				...{
					columns:COLUMNS['client'],
					getURLParams:() => { return { ...getFormData({ table:'client' }) } },
					rowGroup
				}
			}
		).hide(),
		userTable = new DataTable
		(
			'#user-table',
			{
				...defaultDataTableOptions,
				...{
					columns:COLUMNS['user'],
					getURLParams:() => { return { ...getFormData({ table:'user' }) } },
					rowGroup
				}
			}
		).hide(),
		platformTable = new DataTable
		(
			'#platform-table',
			{
				...defaultDataTableOptions,
				columns:COLUMNS['platform'],
				getURLParams:() => { return { ...getFormData({ table:'platform' }) } },
				rowGroup
			}
		).hide();

	let
		_selectedDataBaseTables = [];

	$('.container-fluid').addClass('full');

	inputClients.on('change', (e) =>
	{
		update();
	});

	inputDateStart.on('change', (e) =>
	{
		update();
	});

	inputDateEnd.on('change', (e) =>
	{
		update();
	});

	inputTables.on('change', (e) =>
	{
		_selectedDataBaseTables = inputTables.ids;
		update();
	});

	Loader.create();

	$('#report-form').on('submit', (e) =>
	{
		e.preventDefault();

		if(!_selectedDataBaseTables || !_selectedDataBaseTables.length)
		{
			alert('Selecione uma tabela para auditar');
			return;
		}

		if(_selectedDataBaseTables.indexOf('client') > -1) clientTable.show();
		else clientTable.hide();

		if(_selectedDataBaseTables.indexOf('user') > -1) userTable.show();
		else userTable.hide();

		if(_selectedDataBaseTables.indexOf('platform') > -1) platformTable.show();
		else platformTable.hide();
	});
}

const update = () =>
{

}

$(document).ready(() => { start(); });
