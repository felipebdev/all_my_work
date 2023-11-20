'use strict';
import BackOfficeAPI from './object/Net/BackOfficeAPI.js';

const
log = console.log,
TEST = false,
URL = `client-transactions/read/`,
LOCALE = 'pt-br',
CURRENCY_OPTIONS = {style: 'currency', currency: 'BRL'},
lengthMenu = [[100, 250, 500, 1000, 2500, 5000, 10000], [100, 250, 500, 1000, 2500, 5000, 10000]],
api = new BackOfficeAPI();

let
$dataTable = null,
$studentNameInput = null,
$studentEmailInput = null,
$studentDocumentInput = null,
$studentCardInput = null,
$studentLastLoginInput = null,
$clientCPFInput = null,
$clientCNPJInput = null,
$clientsNamesInput = null,
$clientPlatformInput = null,
$clientProductInput = null,
$paymentStatusInput = null,
$paymentLastDateInput = null,
$paymentLastValueInput = null,
$loader = null,
$body = null,
_exportTime = 0,
_exportTimeInterval = 0,
_totalRecords = 0,
_currentCount = 0,
_exportData = [],
_isStarted = false,
_isFirstLoadedAborted = false;

const getURLParams = () =>
{
	return {
		itemsPerPage: Number($dataTable ? $dataTable.page.len() : lengthMenu[0]),
		page: Number($dataTable ? $dataTable.page() : 0) + 1,
		test: TEST,

		subscriber_name: sanitize($studentNameInput.val()),
		subscriber_email: sanitize($studentEmailInput.val()),
		subscriber_document_number: sanitize(removeSpecialChars($studentDocumentInput.val())),
		subscriber_last_access: sanitize(dateToMySQLFormat($studentLastLoginInput.val())),
		subscriber_credit_cards_last_four_digits: sanitize($studentCardInput.val()),

		client_cpf: sanitize(removeSpecialChars($clientCPFInput.val())),
		client_cnpj: sanitize(removeSpecialChars($clientCNPJInput.val())),
		clients_names: ($clientsNamesInput.val()),
		client_platform: ($clientPlatformInput.val()),
		client_product: ($clientProductInput.val()),

		payment_status: ($paymentStatusInput.val()),
		payment_date: (dateToMySQLFormat($paymentLastDateInput.val())),
		payment_value: (sanitizeCurrency($paymentLastValueInput.val()))
	};
};

const showLoader = (duration = 500) =>
{
	$body.addClass('no-interaction');
	$loader.fadeIn(duration);
};

const hideLoader = (duration = 500) =>
{
	$body.removeClass('no-interaction');
	$loader.fadeOut(duration);
};

const requestData = async () =>
{
	_exportTimeInterval = setInterval(() =>
	{
		++_exportTime;
		log(`time elapsed: ${_exportTime} seconds`);
	}, 1000);
	showLoader();
	const response = await fetch(`${URL}?` + $.param({...getURLParams(), ...{all: true, test: false, result_type: 'values'}}));
	return response.json();
};

const exportAll = async (type) =>
{
	const data = await requestData();
	//console.log(data);
	hideLoader();
	clearInterval(_exportTimeInterval);

	if (type === 'excel') exportExcel(null, null, data);

	console.log(`Total time to export: ${_exportTime} seconds`);
};

const exportExcel = (name, fileName, data, meta) =>
{
	const wb = XLSX.utils.book_new();

	wb.Props = {
		Title: 'Report',
		Subject: 'Report',
		Author: 'XGrow',
		CreatedDate: (new Date()).getDate()
	};

	wb.SheetNames.push('Test Sheet');

	var ws_data = data.data;  //a row with 2 columns

	var ws = XLSX.utils.aoa_to_sheet(ws_data);

	wb.Sheets['Test Sheet'] = ws;

	var wbout = XLSX.write(wb, {bookType: 'xlsx', type: 'binary'});

	saveAs(new Blob([s2ab(wbout)], {type: 'application/octet-stream'}), 'relatorio.xlsx');
};

function s2ab(s)
{
	var buf = new ArrayBuffer(s.length); //convert s to arrayBuffer
	var view = new Uint8Array(buf);  //create uint8array as viewer
	for (var i = 0; i < s.length; i++) view[i] = s.charCodeAt(i) & 0xFF; //convert to octet
	return buf;
}

const parseChunk = () =>
{

};

const sanitize = (value = null) =>
{
	return String(value || '').trim();
};

const removeSpecialChars = (value = null) =>
{
	return String(value || '').replace(/[^a-zA-Z0-9]/g, '');
};

const sanitizeCurrency = (value = null) =>
{
	return String(value || '').replaceAll(',', '.');
};

const dateToMySQLFormat = (date = null) =>
{
	if (!date) return '';
	date = date.split('/');
	return `${String(date[2] || '').trim()}-${String(date[1] || '').trim()}-${String(date[0] || '').trim()}`;
};

const dateToPTBRFormat = (date = null) =>
{
	if (!date) return '';
	date = date.split(' ');
	const days = (date[0] || '').split('-');
	date = String(days[2] || '').trim() + '/' + String(days[1] || '').trim() + '/' + String(days[0] || '').trim();

	return `<span class="date" data-value="${date}">${date}</span>`;
};

const formatCPF = (value = null) =>
{
	if (!value) return '';
	value = (value).replace(/[^\d]/g, '').replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
	return `<span class="document cpf" data-value="${value}">${value}</span>`;
};

const formatCNPJ = (value = null) =>
{
	if (!value) return '';
	value = value.replace(/[^\d]/g, '').replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
	return `<span class="document cnpj" data-value="${value}">${value}</span>`;
};

const formatCurrency = (value = null) =>
{
	if (!value) return '';
	value = String(value);
	const symbol = value.indexOf('R$') === -1 && value.indexOf('R$ ') === -1 ? '<span class="currency-symbol">R$</span> ' : '';
	value = String(Number(value).toFixed(2)).toLocaleString(LOCALE, CURRENCY_OPTIONS).replace('.', ',').replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
	return '<span class="currency">' + (value ? symbol + `<span class="currency-value" data-value="${value}">${value}</span>` : '') + '</span>';
};

const initElements = () =>
{
	$('#export-button').on('click', (e) => { exportAll('excel'); });

	$('.container-fluid').addClass('full');
	$('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1');

	$('#report-form').on('submit', (e) =>
	{
		e.preventDefault();
		$dataTable.ajax.reload();
	});

	$('select.select-2').addClass('select2-fix').select2();

	$('#clients-names').on('change', async (e) =>
	{
		//const data = e.params.data;
		//console.log(data);

		const response = await api.getPlatformsAndProductsByClient($clientsNamesInput.val());
		const data = response.data;

		$clientPlatformInput.select2().empty().select2({ data:data.platforms });
		$clientProductInput.select2().empty().select2({ data:data.products });
	});

	$('.select2-selection').addClass('shadow-sm');

	$('.datepicker').datepicker({
		todayBtn: true,
		clearBtn: true,
		language: 'pt-BR',
		multidate: false,
		calendarWeeks: true,
		autoclose: true,
		todayHighlight: true
	});

};

const setupColumns = () =>
{
	let i;
	const total = COLUMNS.length,
	config = [];

	for (i = 0; i < total; ++i)
	{
		const column = COLUMNS[i],
		typeData = {...{prefix: '', suffix: ''}, ...(column.typeData || {})},
		columnConfig = {data: column.name, render: (data) => { return data; }},
		prefix = typeData.prefix || '',
		suffix = typeData.suffix || '';

		switch (column.type)
		{
			case 'name':

				columnConfig.render = (data) =>
				{
					const value = data || '';
					return `<span class="name">${prefix}${value}${suffix}</span>`;
				};

				break;

			case 'email':

				columnConfig.render = (data) =>
				{
					const value = data || '';
					return `<a href="mailto:${value}">${prefix}${value}${suffix}</a>`;
				};

				break;

			case 'document':

				columnConfig.render = (data) =>
				{
					const value = data || '';
					if (value.length === 11) return prefix + formatCPF(value) + suffix;
					if (value.length === 14) return prefix + formatCNPJ(value) + suffix;

					return value;
				};

				break;

			case 'cpf':

				columnConfig.render = (data) =>
				{
					const value = data || '';
					return prefix + formatCPF(value) + suffix;
				};

				break;

			case 'cnpj':

				columnConfig.render = (data) =>
				{
					const value = data || '';
					return prefix + formatCNPJ(value) + suffix;
				};

				break;

			case 'date':

				columnConfig.render = (data) =>
				{
					const value = data || '';
					return prefix + dateToPTBRFormat(value) + suffix;
				};

				break;

			case 'list':

				columnConfig.render = (data) =>
				{
					return prefix + data + suffix;
				};

				break;

			case 'number':

				columnConfig.render = (data) =>
				{
					return prefix + Number(data) + suffix;
				};

				break;

			case 'float':

				columnConfig.render = (data) =>
				{
					return prefix + parseFloat(data).toFixed(typeData.decimals || 2) + suffix;
				};

				break;

			case 'currency':

				columnConfig.render = (data) =>
				{
					const value = data || '';
					return prefix + formatCurrency(value) + suffix;
				};

				break;


			case 'object':

				columnConfig.render = (data) =>
				{
					const value = data || '';
					return '<span class="status">' + prefix + (typeData[value] || value) + suffix + '</span>';
				};

				break;

			default:

				columnConfig.render = (data) =>
				{
					return prefix + data + suffix;
				};

				break;
		}

		config.push(columnConfig);
	}

	return config;
};

const start = () =>
{
	if (_isStarted) return;
	_isStarted = true;

	$studentNameInput = $('#student-name');
	$studentEmailInput = $('#student-email');
	$studentDocumentInput = $('#student-document-number');
	$studentCardInput = $('#student-card-number');
	$studentLastLoginInput = $('#student-last-login');
	$clientCPFInput = $('#client-cpf');
	$clientCNPJInput = $('#client-cnpj');
	$clientsNamesInput = $('#clients-names');
	$clientPlatformInput = $('#client-platform');
	$clientProductInput = $('#client-product');
	$paymentStatusInput = $('#payment-status');
	$paymentLastDateInput = $('#payment-last-date');
	$paymentLastValueInput = $('#payment-last-value');
	$loader = $('<div id="report-loader"><div class="loader-container"><div class="loader"></div></div></div>');
	$body = $('body');

	//showLoader();

	let _isToPreventRequest = false;

	const exportOptions = {exportOptions: {modifier: {page: 'all', search: 'none'}}};

	const abortRequest = () =>
	{
		$dataTable.settings()[0].jqXHR.abort();
	};

	const reloadRequest = () =>
	{
		$dataTable.ajax.reload();
	};

	const abortFirstRequest = () =>
	{
		if(_isFirstLoadedAborted) return;

		try
		{
			abortRequest();
			hideLoader();
			_isFirstLoadedAborted = true;
			log('first request aborted successfully');
		}
		catch (e)
		{

		}
	}

	const initHandler = () =>
	{
		abortFirstRequest();
		updateSort();
		hideLoader();
	};

	const preXhrHandler = (e, settings, data) =>
	{
		abortFirstRequest();
		if(_isFirstLoadedAborted) showLoader();
	};

	const xhrHandler = (e, settings, data) =>
	{
		hideLoader();
	};

	const orderHandler = (e, settings, data) =>
	{

	};

	const updateSort = (e) =>
	{
		if (e && e.currentTarget)
		{
			const index = $(e.currentTarget).data('index');
			const existentIndex = _order.map(x => x[0]).indexOf(index);

			if (existentIndex > -1)
			{
				++_order[existentIndex][2];

				if (_order[existentIndex][2] === 1) _order[existentIndex][1] = 'asc';
				else _order.splice(existentIndex, 1);
			}
			else
			{
				_order.unshift([index, 'desc', 0]);
			}
		}

		let counter = 0;

		$(`.badge-sort`).addClass('hidden');
		_order.forEach((e, i) =>
		{
			$(`.badge-sort[data-index=${_order[i][0]}]`).text(++counter).removeClass('hidden');
		});

		if (e && e.currentTarget) $dataTable.order(_order);
	};

	const _order =
	[
		[COLUMNS.map(x => x['name']).indexOf('payment_date'), 'desc', 0],
		[COLUMNS.map(x => x['name']).indexOf('client_full_name'), 'desc', 0],
		[COLUMNS.map(x => x['name']).indexOf('subscriber_name'), 'desc', 0],
		[COLUMNS.map(x => x['name']).indexOf('client_product'), 'desc', 0]
	];

	//console.log('setupColumns()', setupColumns())

	$dataTable = $('#report-table').on('init.dt', initHandler).on('preXhr.dt', preXhrHandler).on('xhr.dt', xhrHandler).on('order.dt', orderHandler).DataTable
	({
		dom: 'l<br />Bfrtip',
		orderMulti: true,
		columnDefs:
		[
			{
				targets: '_all',
				createdCell: function (td, cellData, rowData, row, col)
				{
					$(td).addClass(COLUMNS[col].owner);
				}
			}
		],
		order: _order.slice(),
		columns: setupColumns(),
		buttons:
		[
			{extend: 'copy', text: 'Copiar', ...exportOptions},
			{extend: 'csv', ...exportOptions},
			{extend: 'excel', ...exportOptions},
			{extend: 'pdf', ...exportOptions},
			{extend: 'print', text: 'Imprimir', ...exportOptions},
			{
				text: 'excel (todos os registros)',
				action: function (e, dt, node, config)
				{
					exportAll('excel');
				}
			}
		],
		language: {url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json'},
		ajax: {url: URL, data: (data) => { return { ...getURLParams(), ...data}; }},
		searching: false,
		processing: false,
		serverSide: true,
		searchDelay: 1500,
		lengthMenu
	});

	$body.append($loader);
	hideLoader(0);

	initElements();

	if (TEST) log('APP RUNNING IN TEST MODE');

	$(document).on('click', '.sort-column', (e) =>
	{
		e.preventDefault();
		e.stopImmediatePropagation();
		e.stopPropagation();

		updateSort(e);
	});

	let intervalCounter = 0;
	const interval = setInterval(() =>
	{
		abortFirstRequest();
		if(_isFirstLoadedAborted || (++intervalCounter > 99)) clearInterval(interval);
	}, 100);

};

$(document).ready(() => { start(); });

