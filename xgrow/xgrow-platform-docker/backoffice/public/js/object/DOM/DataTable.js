import DOMComponent from './DOMComponent.js';
import StringUtil from '../Util/StringUtil.js';

/**
 *
 * @type {string}
 */
$.fn.dataTable.ext.errMode = 'none';
/**
 *
 * @type {string}
 */
const DEFAULT_VALUE = '-';
/**
 *
 * @param columns
 * @returns {*[]}
 */
const setupColumns = (columns = []) =>
{
	let i;
	const total = columns.length,
	config = [];

	for (i = 0; i < total; ++i)
	{
		const column = columns[i],
		typeData = {...{ prefix: '', suffix: ''}, ...(column.typeData || {}) },
		columnConfig = { data: column.name, render: (data) => { return data; } },
		prefix = typeData.prefix || '',
		suffix = typeData.suffix || '';

		if(!column.data) column.data = column.name;

		switch (column.type)
		{
			case 'name':

				columnConfig.render = (data) =>
				{
					const value = data || '';
					if(!value) return DEFAULT_VALUE;
					return `<span class="name">${prefix}${value}${suffix}</span>`;
				};

				break;

			case 'email':

				columnConfig.render = (data) =>
				{
					const value = data || '';
					if(!value) return DEFAULT_VALUE;
					return `<a href="mailto:${value}">${prefix}${value}${suffix}</a>`;
				};

				break;

			case 'document':

				columnConfig.render = (data) =>
				{
					const value = data || '';
					if(!value) return DEFAULT_VALUE;
					if (value.length === 11) return prefix + StringUtil.formatCPF(value) + suffix;
					if (value.length === 14) return prefix + StringUtil.formatCNPJ(value) + suffix;

					return value;
				};

				break;

			case 'cpf':

				columnConfig.render = (data) =>
				{
					const value = data || '';
					if(!value) return DEFAULT_VALUE;
					return prefix + StringUtil.formatCPF(value) + suffix;
				};

				break;

			case 'cnpj':

				columnConfig.render = (data) =>
				{
					const value = data || '';
					if(!value) return DEFAULT_VALUE;
					return prefix + StringUtil.formatCNPJ(value) + suffix;
				};

				break;

			case 'date':

				columnConfig.render = (data) =>
				{
					const value = data || '';
					if(!value) return DEFAULT_VALUE;
					return prefix + StringUtil.dateToPTBRFormat(value) + suffix;
				};

				break;

			case 'list':

				columnConfig.render = (data) =>
				{
					return prefix + (data || DEFAULT_VALUE) + suffix;
				};

				break;

			case 'number':

				columnConfig.render = (data) =>
				{
					return prefix + (Number(data) || DEFAULT_VALUE) + suffix;
				};

				break;

			case 'float':

				columnConfig.render = (data) =>
				{
					if(!data) return DEFAULT_VALUE;
					return prefix + parseFloat(data).toFixed(typeData.decimals || 2) + suffix;
				};

				break;

			case 'currency':

				columnConfig.render = (data) =>
				{
					const value = data || '';
					if(!value) return DEFAULT_VALUE;
					return prefix + StringUtil.formatCurrency(value) + suffix;
				};

				break;


			case 'object':

				columnConfig.render = (data) =>
				{
					const value = data || '';
					if(!value) return DEFAULT_VALUE;
					return '<span class="status">' + prefix + (typeData[value] || value) + suffix + '</span>';
				};

				break;

			default:

				columnConfig.render = (data) =>
				{
					if(!data) return DEFAULT_VALUE;
					return prefix + data + suffix;
				};

				break;
		}

		config.push(columnConfig);
	}

	return config;
};
/**
 *
 * @returns {Promise<any>}
 */
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
/**
 *
 * @param type
 * @returns {Promise<void>}
 */
const exportAll = async (type) =>
{
	const data = await requestData();
	//console.log(data);
	hideLoader();
	clearInterval(_exportTimeInterval);

	if (type === 'excel') exportExcel(null, null, data);

	console.log(`Total time to export: ${_exportTime} seconds`);
};
/**
 *
 * @param name
 * @param fileName
 * @param data
 * @param meta
 */
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

/**
 *
 * @param s
 * @returns {ArrayBuffer}
 */
function s2ab(s)
{
	var buf = new ArrayBuffer(s.length); //convert s to arrayBuffer
	var view = new Uint8Array(buf);  //create uint8array as viewer
	for (var i = 0; i < s.length; i++) view[i] = s.charCodeAt(i) & 0xFF; //convert to octet
	return buf;
}

/**
 *
 * @type {{exportOptions: {modifier: {search: string, page: string}}}}
 */
const exportOptions = { exportOptions: { modifier: {page: 'all', search: 'none'} } };
/**
 *
 */
const initHandler = () =>
{
	//this.options.initHandler();
	//updateSort();
	//hideLoader();
};
/**
 *
 * @param e
 */
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
/**
 *
 * @type {*[]}
 * @private
 */
const _order =
[

];
/**
 *
 */
$(document).on('click', '.sort-column', (e) =>
{
	e.preventDefault();
	e.stopImmediatePropagation();
	e.stopPropagation();

	updateSort(e);
});
/**
 *
 * @type {number[][]}
 */
const lengthMenu = [[100, 250, 500, 1000, 2500, 5000, 10000], [100, 250, 500, 1000, 2500, 5000, 10000]];
/**
 * @class {DataTable}
 */
export default class DataTable extends DOMComponent
{
	/**
	 * @inheritDoc
	 */
	constructor(id, options = {})
	{
		super(id, { ...{ getURLParams:() => { return {}; } }, ...options});

		this._$tableContainer = this.$selector.closest('.table-container');
		this._$loader = this._$tableContainer.find('.table-loader');

		const COLUMNS_DATA = options.columns;
		delete options.columns;

		this._$dataTable = $(id)
		.on('init.dt', (e, settings, data) =>
		{
			console.log(`DataTables ${id} inited`);
		})
		.on('preXhr.dt', (e, settings, data) =>
		{
			this.showLoader();
		})
		.on('xhr.dt', (e, settings, data) =>
		{
			this.hideLoader();
		})
		.on('order.dt', (e, settings, data) =>
		{

		})
		.on('error.dt', (e, settings, techNote, message) =>
		{
			console.log(`DataTables ${id} error`);
			console.warn(message);
		})
		.DataTable
		(
			{
				...{
					/*dom: 'l<br />Bfrtip',*/
					orderMulti: true,
					columnDefs:
					[
						{
							defaultContent:'-',
							targets: '_all',
						}
					],
					order: [],
					columns: setupColumns(COLUMNS_DATA),
					buttons: [],
					language: { url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json' },
					ajax:
					{
						url: this.options.url,
						data: (data) =>
						{
							const vars =
							{
								...{
									itemsPerPage: Number(this._$dataTable ? this._$dataTable.page.len() : lengthMenu[0]),
									page: Number(this._$dataTable ? this._$dataTable.page() : 0) + 1
								},
								...{ ...this.options.getURLParams(), ...data }
							}
							//console.log('urlParams', vars);
							return vars;
						}
					},
					searching: false,
					processing: false,
					serverSide: true,
					searchDelay: 1500,
					lengthMenu
				},
				...(options || {})
			}
		);

		//console.log('OPTIONS');
		//console.log(this.options);
	}
	/**
	 *
	 * @returns {DataTable}
	 */
	showLoader()
	{
		this._$loader.fadeIn(500);
		return this;
	}
	/**
	 *
	 * @returns {DataTable}
	 */
	hideLoader()
	{
		this._$loader.fadeOut(500);
		return this;
	}
	/**
	 *
	 * @returns {DataTable}
	 */
	show(reload = true, completeCallBack = () => {})
	{
		if(reload) this.reloadRequest(() => { this.hideLoader(); completeCallBack(); });
		this.$selector.closest('.table-container').removeClass('hidden').removeAttr('hidden');
		return this;
	}
	/**
	 *
	 * @returns {DataTable}
	 */
	hide()
	{
		this.abortRequest();
		this.$selector.closest('.table-container').addClass('hidden').attr('hidden', 'hidden');
		return this;
	}
	/**
	 *
	 * @param completeCallBack
	 * @returns {DataTable}
	 */
	reloadRequest(completeCallBack = () => {})
	{
		this._$dataTable.ajax.reload(completeCallBack);
		return this;
	}
	/**
	 *
	 * @returns {DataTable}
	 */
	abortRequest()
	{
		try { this._$dataTable.settings()[0].jqXHR.abort(); } catch (e) {}
		return this;
	}
}
