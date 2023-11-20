@extends('templates.xgrow.main')

@push('jquery')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script src="{{ asset('xgrow-vendor/assets/js/confirmation-modal.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-maskmoney/3.0.2/jquery.maskMoney.min.js"></script>
    <script>
        $(function() {
            $.fn.dataTableExt.afnFiltering.push(
                function(oSettings, aData, iDataIndex) {
                    let dataReturn = true;
                    const iptValidRange = document.getElementById('ipt-valid-range');
                    if (!iptValidRange) return true;

                    const iptValidPeriod = iptValidRange.value;

                    if (iptValidPeriod) {
                        const [start, end] = iptValidPeriod.split('-');
                        const parsedDate = parseDatatablesDate(aData[2]);
                        const parsedStart = parseDatatablesDate(start);
                        const parsedEnd = parseDatatablesDate(end);
                        if (parsedDate >= parsedStart && parsedDate <= parsedEnd) {
                            dataReturn = true;
                        } else {
                            dataReturn = false;
                        }
                    }

                    return dataReturn;
                }
            );
        });
    </script>
@endpush

@push('after-styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="{{ asset('xgrow-vendor/assets/css/verify-alert.css') }}" rel="stylesheet">
@endpush

@push('before-scripts')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet">
@endpush

@push('after-scripts')
    <script src="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.0/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.html5.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.min.js"></script>

    <script>
        $(function() {
            $(document).ready(function() {
                const dateRangeOptions = {
                    autoUpdateInput: false,
                    'locale': {
                        'format': 'DD/MM/YYYY',
                        'separator': ' - ',
                        'applyLabel': 'Aplicar',
                        'cancelLabel': 'Cancelar',
                        'daysOfWeek': [
                            'Dom',
                            'Seg',
                            'Ter',
                            'Qua',
                            'Qui',
                            'Sex',
                            'Sab',
                        ],
                        'monthNames': [
                            'Janeiro',
                            'Fevereiro',
                            'Março',
                            'Abril',
                            'Maio',
                            'Junho',
                            'Julho',
                            'Agosto',
                            'Setembro',
                            'Outubro',
                            'Novembro',
                            'Dezembro',
                        ],
                        'customRangeLabel': 'Personalizar'
                    },
                    ranges: {
                        'Hoje': [moment(), moment()],
                        'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Ultimos 7 dias': [moment().subtract(6, 'days'), moment()],
                        'Ultimos 30 dias': [moment().subtract(29, 'days'), moment()],
                        'Este Mês': [moment().startOf('month'), moment().endOf('month')],
                        'Ultimo Mês': [moment().subtract(1, 'month').startOf('month'), moment()
                            .subtract(1, 'month')
                            .endOf('month')
                        ],
                        'Limpar': [null, null],
                    },
                };

                let datatable;
                datatable = $('#coupon-table').DataTable({
                    dom: '<"d-flex flex-wrap justify-content-center justify-content-xl-between justify-content-lg-center"' +
                        '<"title-table d-flex align-self-center justify-content-center me-1">' +
                        '<"d-flex flex-wrap align-items-center justify-content-xl-between justify-content-lg-center"' +
                        '<"d-flex flex-wrap align-items-center justify-content-center mb-2"<"global-search"><"filter-button">' +
                        '<"d-flex flex-wrap mt-2"<B><"create-button mb-2">>>>>' +
                        '<"filter-div mt-2"><"mt-2" rt>' +
                        '<"my-3 d-flex flex-wrap align-items-center justify-content-between"<"my-2"l><"my-2"p>>',
                    ajax: '/coupons/datatables',
                    processing: true,
                    serverSide: false,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        ['10 itens por página', '25 itens por página', '50 itens por página',
                            'Todos os registros'
                        ]
                    ],
                    'columnDefs': [{
                        'visible': false,
                        'searchable': false,
                    }],
                    language: {
                        url: '{{ asset('js/datatable-translate-pt-BR.json') }}',
                    },
                    columns: [{
                            data: 'code',
                            name: 'coupons.code',
                            render: function(data, type, row, meta) {
                                return '<a href="/coupons/' + row.id +
                                    '/edit" style="color: inherit"><strong>' + data
                                    .toUpperCase() || '-' + '</strong></a>';
                            },
                        },
                        {
                            data: 'plans',
                            render: function(data, type, row, meta) {
                                let plans = '';
                                data.forEach(plan => {
                                    const link =
                                        `<a style='color: inherit' href='/products/edit-plan-product/${plan.plan_id}'>${plan.plan_name}</a><br>`;
                                    plans += link;
                                });

                                return plans;
                            },
                        },
                        {
                            data: 'maturity',
                            name: 'coupons.maturity',
                            type: 'date',
                            render: function(data, type, row, meta) {
                                return (data != null) ? formatDatePTBR(data) : '-';
                            }
                        },
                        {
                            data: 'value',
                            name: 'coupons.value',
                            render: function(data, type, row, meta) {
                                let value = `${data}%`;
                                if (row.value_type === 'V') {
                                    value = formatCoin(value);
                                }

                                return value;
                            }
                        },
                        {
                            data: null,
                            searchable: false,
                            render: function(data, type, row) {
                                const params = {
                                    title: 'Excluir cupom',
                                    description: 'Você tem certeza que deseja excluir este cupom?',
                                    btnSave: 'Sim, excluir',
                                    btnCancel: 'Não, manter',
                                    success: 'O cupom selecionado foi excluído com sucesso',
                                    error: 'Não foi possível excluir o cupom: ',
                                    url: `/coupons/${row.id}`,
                                    method: 'POST',
                                    body: {
                                        '_token': "{{ csrf_token() }}",
                                        '_method': 'DELETE'
                                    },
                                    datatables: '#coupon-table'
                                };

                                const modal = window.btoa(JSON.stringify(params));
                                const url = '/coupons/' + row.id + '/edit';

                                const menu = `
                                    <div class="dropdown">
                                        <button class="xgrow-button table-action-button m-1" type="button" id="dropdownMenuButton${row.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu table-menu" aria-labelledby="dropdownMenuButton${row.id}">
                                            <li><a class="dropdown-item table-menu-item" href="javascript:void(0)" onclick="location.href='${url}'">Editar</a></li>
                                            <li><a class="dropdown-item table-menu-item" href="javascript:void(0)" onclick="openConfirmationModal('${modal}')">Excluir</a></li>
                                        </ul>
                                    </div>
                                `;

                                return '<div class="d-flex">' + menu + '';
                            },
                        },
                    ],
                    buttons: [{
                            extend: 'pdf',
                            text: '<button class="xgrow-button export-button me-1" title="Exportar em PDF">\n' +
                                '                  <i class="fas fa-file-pdf" style="color: red"></i>\n' +
                                '                </button>',
                            className: '',
                            exportOptions: {
                                modifier: {
                                    selected: true,
                                    page: 'all'
                                }
                            },
                        },
                        {
                            extend: 'csv',
                            text: '<button class="xgrow-button export-button me-1" title="Exportar em CSV">\n' +
                                '                  <i class="fas fa-file-csv" style="color: blue"></i>\n' +
                                '                </button>',
                            className: '',
                            exportOptions: {
                                modifier: {
                                    selected: true,
                                    page: 'all'
                                }
                            },
                        },
                        {
                            extend: 'excel',
                            text: '<button class="xgrow-button export-button me-1" title="Exportar em XLSX">\n' +
                                '                  <i class="fas fa-file-excel" style="color: green"></i>\n' +
                                '                </button>',
                            className: '',
                            exportOptions: {
                                modifier: {
                                    selected: true,
                                    page: 'all'
                                }
                            },
                        },
                    ],
                    initComplete: function(settings, json) {
                        $('.title-table').html(
                            '<h5 class="align-self-center">Cupons: <span id="spn-total-label"></span></h5>'
                        );
                        $('.buttons-csv').removeClass('dt-button buttons-csv');
                        $('.buttons-excel').removeClass('dt-button buttons-excel');
                        $('.buttons-pdf').removeClass('dt-button buttons-pdf');
                        $('.create-button').html(
                            '<button onclick="location.href=\'/coupons/create\'" class="xgrow-button" style="height:40px; width:128px"><i class="fa fa-plus"></i> Novo cupom</button>'
                        );
                        $('.dataTables_filter input').attr('placeholder', 'Buscar');
                        $('.create-label').html(
                            '<p class="xgrow-medium-bold me-2">Exportar em</p>');
                        $('.filter-button').html(`
                                    <div class="d-flex align-items-center py-2">
                                        <button type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-bs-expanded="false" aria-bs-controls="collapseExample" class="xgrow-button-filter xgrow-button export-button me-1" aria-expanded="true">
                                            <p>Filtros avançados <i class="fa fa-chevron-down" aria-hidden="true"></i></p>
                                        </button>
                                    </div>
                                `);
                        $('.global-search').html(`
                                    <div class="xgrow-input me-1 pt-0" style="background-color: var(--input-bg); height: 40px;" >
                                        <input id="ipt-global-filter" placeholder="Busque alguma coisa..." type="text" style="height: 40px;">
                                        <span class="xgrow-input-cancel"><i class="fa fa-search" aria-hidden="true"></i></span>
                                    </div>
                                `);
                        $('.filter-div').html(`
                            <div class="mb-3 collapse" id="collapseExample">
                                <div class="filter-container">
                                    <div class="p-2 px-3">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-3 mt-1">
                                                <div class="xgrow-form-control mb-2">
                                                    <select id="slc-plan-filter" class="xgrow-select w-100" name="plan-filter[]" id="product" multiple>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-3 mt-1">
                                                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                                                    <input type="text" class="form-control" id="ipt-valid-range"
                                                        style="border:none; outline:none; background-color: var(--input-bg); border-bottom: 1px solid var(--border-color);box-shadow: none; color: var(--contrast-green)"
                                                        autocomplete="off">
                                                    <label for="ipt-valid-range">Validade</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-3 mt-1">
                                                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                                                    <input type="text" class="mui--is-empty mui--is-untouched mui--is-pristine input-no-car-especial" id="ipt-discount"
                                                        autocomplete="off">
                                                    <label for="ipt-discount">Desconto</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-3 mt-1">
                                                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                                    <select class="xgrow-select" id="value_type" name="value_type">
                                                        <option value="V">R$</option>
                                                        <option value="P">%</option>
                                                    </select>
                                                    <label for="value_type">Tipo</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`);

                        $('#ipt-global-filter').on('keyup', function() {
                            datatable.search(this.value).draw();
                        });

                        axios.get("{{ URL::route('products.list') }}").then(response => {
                            let html = ""
                            response.data.products.sort(function(a, b) {
                                if (a.name > b.name) return 1;
                                if (a.name < b.name) return -1;
                                return 0;
                            });
                            response.data.products.forEach(item => html +=
                                `<option value="${item.name}">${item.name}</option>`
                                )
                            $('#slc-plan-filter').append(html)
                        });

                        $('#slc-plan-filter').select2({
                            allowClear: true,
                            placeholder: 'Produto'
                        });

                        $('#slc-plan-filter').on('change', function() {
                            const selected = $('#slc-plan-filter').val();
                            const filter = selected.join('|');
                            datatable.columns(1).search(filter, true, false).draw();
                        });

                        $('.xgrow-datepicker').datepicker({
                            format: 'dd/mm/yyyy',
                        });

                        $('#ipt-valid-range').daterangepicker(dateRangeOptions)
                            .on('apply.daterangepicker', function(ev, picker) {
                                if (!picker.startDate.isValid() && !picker.endDate
                                .isValid()) {
                                    return $(this).trigger('cancel.daterangepicker');
                                }
                                $(this).val(picker.startDate.format('DD/MM/YYYY') + '-' +
                                    picker.endDate
                                    .format('DD/MM/YYYY'));
                                $(this).removeClass('mui--is-empty');
                                $(this).addClass('mui--is-not-empty');
                                datatable.columns(2).search('').draw();
                            })
                            .on('cancel.daterangepicker', function(ev, picker) {
                                $(this).val('');
                                datatable.columns(2).search('').draw();
                            });

                        $('.input-no-car-especial').keyup(function(e) {
                            if ($("#value_type").val() == 'V') {
                                $(".input-no-car-especial").attr('maxlength', '');
                                $('.input-no-car-especial').mask("#0,00", {
                                    reverse: true
                                });
                            } else {
                                $('.input-no-car-especial').mask('00,00', {
                                    reverse: true
                                });
                            }

                            const type_discount = $('#value_type').val();
                            let discount = $('#ipt-discount').val();
                            let filter = "";

                            if (discount != '' && discount != null) {
                                if (type_discount == 'V') {
                                    if (discount.search(",") == -1) {
                                        discount += ",00";
                                    }
                                    filter = formatCoin(discount);
                                } else {
                                    filter = discount.replace(",00", "") + '%';
                                }
                            }
                            datatable.columns(3).search(filter).draw();
                        });

                        $('#value_type').change(function() {
                            var ctype = $(this).find('option:selected').attr('value');
                            if (ctype == 'V') {
                                $(".input-no-car-especial").attr('maxlength', '');
                                $(".input-no-car-especial").unmask().mask('#0,00', {
                                    reverse: true
                                });
                            } else {
                                $('.input-no-car-especial').unmask().mask('00,00', {
                                    reverse: true
                                });
                            }

                            const type_discount = $('#value_type').val();
                            let discount = $('#ipt-discount').val();
                            let filter = "";

                            if (discount != '' && discount != null) {
                                if (type_discount == 'V') {
                                    if (discount.search(",") == -1) {
                                        discount += ",00";
                                    }
                                    filter = formatCoin(discount);
                                } else {
                                    filter = discount.replace(",00", "") + '%';
                                }
                            }
                            datatable.columns(3).search(filter).draw();
                        });

                        setTotalLabel(json.recordsTotal);
                    },
                    drawCallback: function(settings) {
                        const total = datatable.page.info().recordsDisplay || 0;
                        setTotalLabel(total);
                    }
                });
            });
        });

        function setTotalLabel(total = 0) {
            let label = 'cupom';
            if (total !== 1) label = 'cupons';
            $('#spn-total-label').text(`${total} ${label}`);
        }
    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item mx-2"><span>Produtos</span></li>
            <li class="breadcrumb-item active mx-2"><span>Cupons</span></li>
        </ol>
    </nav>

    @if ($verifyDocument)
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12">
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <img src="{{ asset('xgrow-vendor/assets/img/documents/warning.svg') }}" style="margin-right: 1rem">
                    <div>
                        <h6>Atenção!</h6>
                        @if (!$recipientStatusMessage)
                        <p>Antes de realizar sua primeira venda, nós precisamos verificar a sua identidade.
                            <a style="color:inherit;font-weight:700" href="{{ route('documents') }}">Clique aqui para verificar.</a>
                        </p>
                        @else
                            <p>{{ $recipientStatusMessage }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="xgrow-card card-dark p-0">
        <div class="xgrow-card-body p-3 py-4">
            @include('elements.alert')
            <div class="table-responsive m-t-30">
                <table id="coupon-table"
                    class="xgrow-table table text-light table-responsive dataTable overflow-auto no-footer"
                    style="width:100%">
                    <thead>
                        <tr class="card-black" style="border: 4px solid var(--black-card-color)">
                            <th>Nome</th>
                            <th>Produtos</th>
                            <th>Validade</th>
                            <th>Desconto</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @include('elements.confirmation-modal')
    @include('elements.toast')
@endsection
