@extends('templates.xgrow.main')

@push('jquery')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script src="{{ asset('xgrow-vendor/assets/js/confirmation-modal.js') }}"></script>
@endpush

@push('after-styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="{{ asset('xgrow-vendor/assets/css/pages/subscribers_index.css') }}" rel="stylesheet">
    <style>
        .has-error {
            border-bottom-color: red;
        }
    </style>
@endpush

@push('after-scripts')
    <script src="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
    <script src=" https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>

    <!-- end - This is for export functionality only -->

    <script>
        $(document).ready(function() {
                    const conditionsConfig = {
                        'products.id': {
                            conditions: {
                                1: 'igual a',
                                2: 'diferente de',
                            },
                            template: '#plan-input-template'
                        },
                        'subscribers.status': {
                            conditions: {
                                1: 'igual a',
                                2: 'diferente de',
                            },
                            template: '#status-input-template'
                        },
                        'subscribers.created_at': {
                            conditions: {
                                1: 'na data de',
                                3: 'depois de',
                                5: 'antes de',
                            },
                            template: '#date-input-template'
                        },
                        'subscribers.last_acess': {
                            conditions: {
                                1: 'na data de',
                                3: 'depois de',
                                5: 'antes de',
                                7: 'nunca acessou',
                            },
                            template: '#date-input-template'
                        },
                        'subscribers.gender': {
                            conditions: {
                                1: 'igual a',
                                2: 'diferente de',
                            },
                            template: '#gender-input-template'
                        },
                        'subscribers.birthday': {
                            conditions: {
                                1: 'na data de',
                                3: 'depois de',
                                5: 'antes de',
                            },
                            template: '#date-input-template'
                        },
                        'subscribers.address_state': {
                            conditions: {
                                1: 'igual a',
                                2: 'diferente de',
                            },
                            template: '#states-input-template'
                        },
                        'subscribers.address_city': {
                            conditions: {
                                1: 'igual a',
                                2: 'diferente de',
                            },
                            template: '#text-input-template'
                        },
                        'subscribers.document_type': {
                            conditions: {
                                1: 'igual a',
                                2: 'diferente de',
                            },
                            template: '#person-input-template'
                        },
                        'subscriber_status_lead': {
                            conditions: {
                                1: 'igual a',
                                2: 'diferente de',
                            },
                            template: '#person-input-subscriber-type'
                        },
                        'payments.type_payment': {
                            conditions: {
                                1: 'igual a',
                                2: 'diferente de',
                            },
                            template: '#payment_method-input-template'
                        },
                        'payment_singlesale_status': {
                            conditions: {
                                1: 'igual a',
                                2: 'diferente de',
                            },
                            template: '#person-input-single-sale'
                        },
                        'payment_subscription_status': {
                            conditions: {
                                1: 'igual a',
                                2: 'diferente de',
                            },
                            template: '#person-input-subscription'
                        },
                        'payment_nolimit_status': {
                            conditions: {
                                1: 'igual a',
                                2: 'diferente de',
                            },
                            template: '#person-input-nolimit'
                        },
                    }


                    const route = @json(route('condition.datatables'));

                    let conditions;
                    let datatable;
                    datatable = $('#subscriber-table').DataTable({
                                dom: '<"d-flex flex-wrap justify-content-center justify-content-xl-between justify-content-lg-center"' +
                                    '<"title-table d-flex align-self-center justify-content-center me-1">' +
                                    '<"d-flex flex-wrap align-items-center justify-content-xl-between justify-content-lg-center"' +
                                    '<"d-flex flex-wrap align-items-center justify-content-center mb-2"<"global-search"><"filter-button">' +
                                    '<"d-flex flex-wrap"<B>>>>>' +
                                    '<"filter-div mt-2"><"mt-2" rt>' +
                                    '<"my-3 d-flex flex-wrap align-items-center justify-content-between"<"my-2"l><"my-2"p>>',
                                ajax: {
                                    url: route,
                                    type: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data: function(data) {
                                        data.conditions = conditions;
                                        return data;
                                    },
                                },
                                processing: true,
                                serverSide: true,
                                lengthMenu: [
                                    [10, 25, 50, -1],
                                    ['10 itens por página', '25 itens por página', '50 itens por página',
                                        'Todos os registros'
                                    ]
                                ],
                                language: {
                                    'url': "{{ asset('js/datatable-translate-pt-BR.json') }}",
                                },
                                'columnDefs': [{
                                    'visible': false,
                                    'searchable': false,
                                }],
                                order: [
                                    [3, 'desc'],
                                ],
                                columns: [{
                                        data: 'name',
                                        render: function(data, type, row) {
                                            const route = @json(route('subscribers.edit', ':id'));
                                            const url = route.replace(/:id/g, row.id);
                                            return '<a href="' + url + '" style="color: inherit">' + data + '</a>';
                                        },
                                    },
                                    {
                                        data: 'product_name',
                                        name: 'products.name',
                                        render: function(data) {
                                            return data;
                                        },
                                    },

                                    {
                                        data: 'subscriber_status',
                                        name: 'subscribers.status',
                                        render: function(data, type, row) {
                                            const subscriberStatus = {
                                                'active': 'Ativo',
                                                'trial': 'Trial',
                                                'canceled': 'Cancelado',
                                                'lead': 'Lead',
                                                'pending_payment': 'Pagamento pendente',
                                            };
                                            return subscriberStatus[data] || '-';
                                        }
                                    },
                                    {
                                        data: 'created_at',
                                        name: 'subscribers.created_at',
                                        type: 'date',
                                        render: function(data, type) {
                                            if (type === 'sort') {
                                                return data;
                                            }
                                            return (data != null) ? formatDatePTBR(data) : '';
                                        },
                                    },
                                    {
                                        data: 'last_acess',
                                        render: function(data, type) {
                                            if (type === 'sort') {
                                                return data;
                                            }
                                            return (data != null) ? formatDatePTBR(data) : 'Nunca acessou';
                                        },
                                    },
                                    {
                                        data: 'payment_type',
                                        render: function(data, type) {
                                            const paymentType = {
                                                'P': 'Venda única',
                                                'R': 'Assinatura',
                                                'U': 'Sem limite',
                                            };
                                            return paymentType[data] || '-';
                                        },
                                    },
                                    {
                                        data: 'payment_method',
                                        name: 'payments.type_payment',
                                        render: function(data, type) {
                                            const paymentMethod = {
                                                'credit_card': 'Cartão de Crédito',
                                                'boleto' : 'Boleto',
                                                'pix': 'Pix',
                                                'paypal' : 'Paypal',
                                            };

                                            return paymentMethod[data] || '-';
                                        },
                                    },
                                    {
                                        data: 'payment_status',
                                        name: 'payment.status',
                                        render: function(data, type) {
                                            const paymentStatus = {
                                                'paid':  'Pago',
                                                'pending': 'Pendente',
                                                'canceled': 'Cancelado',
                                                'failed': 'Atrasado',
                                            };
                                            return paymentStatus[data] || '-';
                                        },
                                    },
                                ],
                                buttons: [
                                    // {
                                        // extend: 'pdf',
                                        // text: '<button class="xgrow-button export-button me-1" title="Exportar em PDF">' +
                                        //     '<i class="fas fa-file-pdf" style="color: red"></i>' +
                                        //     '</button>',
                                        // className: '',
                                        // exportOptions: {
                                        //     columns: [':visible:not(.no-export)'],
                                        //     modifier: {
                                        //         selected: true,
                                        //         page: 'all'
                                        //     }
                                        // },
                                    // },
                                    {
                                        extend: 'csv',
                                        text: '<button class="xgrow-button export-button me-1" title="Exportar em CSV">' +
                                            '<i class="fas fa-file-csv" style="color: blue"></i>' +
                                            '</button>',
                                        className: '',
                                        action: function (e, dt, node, config) {
                                            axios({
                                                url: @json(route('condition.export-csv')),
                                                method: 'post',
                                            }).then((response) => {
                                                successToast('Iniciando download!', 'Seu arquivo foi adicionado a fila de downloads. Para ver o andamento, click em Listas exportadas no menu lateral.');
                                            }).catch((err) => {
                                                errorToast('Falha ao gerar CSV!', 'Por favor entre em contato com o suporte');
                                            });
                                        }
                                    },
                                    {
                                        extend: 'excel',
                                        text: '<button class="xgrow-button export-button me-1" title="Exportar em XLSX">' +
                                            '<i class="fas fa-file-excel" style="color: green"></i>' +
                                            '</button>',
                                        className: '',
                                        action: function (e, dt, node, config) {
                                            axios({
                                                url: @json(route('condition.export-xlsx')),
                                                method: 'post',
                                            }).then((response) => {
                                                successToast('Iniciando download!', 'Seu arquivo foi adicionado a fila de downloads. Para ver o andamento, click em Listas exportadas no menu lateral.');
                                            }).catch((err) => {
                                                errorToast('Falha ao gerar XLSX!', 'Por favor entre em contato com o suporte');
                                            });
                                        }
                                    },
                                ],
                                initComplete: function(settings, json) {
                                        $('.title-table').html(
                                            '<h5 class="align-self-center">Clientes da campanha: <span id="spn-total-label">{{ $totalLabel ?? '' }}</span></h5>'
                                        );
                                        $('.buttons-csv').removeClass('dt-button buttons-csv');
                                        $('.buttons-excel').removeClass('dt-button buttons-excel');
                                        $('.buttons-pdf').removeClass('dt-button buttons-pdf');

                                        $('.dataTables_filter input').attr('placeholder', 'Buscar');
                            //             $('.filter-button').html(`
                            //     <div class="d-flex align-items-center py-2">
                            //         <button type="button"
                            //             data-bs-toggle="collapse" data-bs-target="#collapseExample"
                            //             class="xgrow-button-filter xgrow-button export-button me-1"
                            //             aria-expanded="false" aria-controls="collapseExample">
                            //         <p>Filtros <i class="fa fa-chevron-down" aria-hidden="true"></i></p>
                            //         </button>
                            //     </div>
                            // `);
                                        $('.filter-div').html($('#filter-template').html());

                                        $('#apply-filter').click(function() {
                                            applyFilter();
                                        });

                                        function getAndValidateData() {
                                            const name = $('#name').val().trim();
                                            const description = $('#description').val().trim();
                                            const errors = [];

                                            if (name === '') {
                                                errors.push('Nome obrigatório');
                                            }

                                            if (description === '') {
                                                errors.push('Descrição obrigatória');
                                            }

                                            let conditions;
                                            try {
                                                conditions = evaluateConditions();
                                            } catch (e) {
                                                errors.push(e.message);
                                            }

                                            if (errors.length > 0) {
                                                return errorToast('Algum erro foi encontrado!', errors.join('\n'));
                                            }

                                            return {
                                                name: name,
                                                description: description,
                                                conditions: conditions,
                                            };
                                        }

                                        $('#create-audience').click(function(e) {
                                                    e.preventDefault();

                                                    const storeUrl = @json(route('audience.store'));
                                                    const successUrl = @json(route('audience.index'));

                                                    const data = getAndValidateData();
                                                    if (!data) {
                                                        return false;
                                                    }

                                                    axios.post(storeUrl, data)
                                                        .then(response => {
                                                            successToast('Público criado!',
                                                                "Alterações salvas com sucesso!");
                                                            window.location.replace(successUrl);
                                                        })
                                                        .catch(error => {
                                                            errorToast('Algum erro aconteceu!',
                                                                `${error.response.data}`);
                                                        })
                            });

                            $('#update-audience').click(function (e) {
                                e.preventDefault();

                                const updateUrl = @json(route('audience.update', $audience->id ?? ':id'));
                                const successUrl = @json(route('audience.index'));

                                const data = getAndValidateData();
                                if (!data) {
                                    return false;
                                }

                                axios.put(updateUrl, data)
                                    .then(response => {
                                        successToast('Público alterado!', "Alterações editadas com sucesso.");
                                        window.location.replace(successUrl);
                                    })
                                    .catch(error => {
                                        errorToast('Algum erro aconteceu!', error.response.data);
                                    });
                            });

                            $('#add-condition').click(function () {
                                const $template = $('#additional-condition-template').html();
                                const index = Math.floor(Math.random() * 999999999999) + 1;
                                const html = $template.replace(/{%index%}/g, index)

                                $('#dynamic-additional-conditions').append(html);
                            });

                            function applyFilter() {
                                try {
                                    conditions = evaluateConditions();
                                    datatable.ajax.reload()
                                } catch (e) {
                                    errorToast('Algum erro aconteceu!', e.message);
                                }
                            }

                            function renderConditionRow($row, field, currentOperator = '', currentValue = '') {
                                const config = conditionsConfig[field];
                                const $operator = $row.find('.operator-condition');
                                const operators = config.conditions;
                                $operator.empty();
                                for (var key in operators) {
                                    if (operators.hasOwnProperty(key)) {
                                        const value = operators[key];
                                        $operator.append($("<option></option>").attr("value", key).text(value));
                                    }
                                }
                                if (currentOperator) {
                                    $operator.val(currentOperator)
                                }

                                const template = config.template
                                const html = $(template).html();
                                $row.find('.value-condition-replacement').html(html)

                                if (currentOperator == 7) {
                                    $row.find('.value-condition').val('').prop( "disabled", true);
                                } else if (currentValue) {
                                    $row.find('.value-condition').val(currentValue).prop( "disabled", false);
                                }
                            }

                            // initial rendering
                            $.when($('.condition-row').each(function () {
                                const $row = $(this);
                                const field = $row.find('.field-condition :selected').val()
                                const operator = $row.find('.operator-condition :selected').val()
                                const value = tryToGetValue(this)

                                if (!conditionsConfig[field]) {
                                    return;
                                }

                                renderConditionRow($row, field, operator, value)
                            })).then(function () {
                                @if ($type == 'edit')
                                applyFilter();
                                @endif
                            });

                            $('.condition-row').on('change', '.operator-condition', function (e) {
                                const $row = $(this).closest('.row'); // .css( "background", "yellow" );
                                const val = $(this).val();
                                const disable = (val == 7);
                                $row.find('.value-condition').val('').prop( "disabled", disable);
                            });

                            // Update row when field changes
                            $('.filter-container').on('change', '.field-condition', function (e) {
                                const field = e.target.value;
                                if (!conditionsConfig[field]) {
                                    return;
                                }
                                const $row = $(this).closest('.row');

                                renderConditionRow($row, field);
                            });

                            evaluateTotal(datatable);
                        },
                        drawCallback: function (settings) {
                            evaluateTotal(datatable)
                        }
                    });
                });

                function evaluateTotal(datatable) {
                    const total = datatable.page.info().recordsDisplay || 0;
                    const label = (total > 1) ? 'totais' : 'total';
                    $('#spn-total-label').text(`${total} ${label}`);
                }

                function tryToGetValue(obj) {
                    const text = $(obj).find('.value-condition').val();
                    if (text) {
                        return text;
                    }
                    const option = $(obj).find('.value-condition :selected').val()
                    if (option) {
                        return option;
                    }
                    return;
                }

                function tryToGetText(obj) {
                    const option = $(obj).find('.value-condition :selected').text()
                    if (option) {
                        return option;
                    }
                    const text = $(obj).find('.value-condition').text();
                    if (text) {
                        return text;
                    }
                    const value = $(obj).find('.value-condition').val();
                    if (value) {
                        return value;
                    }

                    return '';
                }

                function trimNewlineAndWhitespace(text) {
                    return text.replace(/^\s+|\s+$/g, '');
                }

                function evaluateConditions() {
                    let conditions = [];
                    let errors = [];

                    $('.condition-row').each(function (i, obj) {
                        let $field = $(obj).find('.field-condition')
                        let $fieldSelected = $field.find(':selected');

                        $field.removeClass('has-error');
                        if (!$fieldSelected.val()) {
                            $field.addClass('has-error');
                            errors.push('Condição ' + (i + 1) + ', campo obrigatório');
                        }

                        let $operator = $(obj).find('.operator-condition');
                        let $operatorSelected = $operator.find(':selected');

                        $operator.removeClass('has-error');
                        if (!$operatorSelected.val()) {
                            $operator.addClass('has-error');
                            errors.push('Condição ' + (i + 1) + ', condição obrigatória');
                        }

                        let operator = $operatorSelected.val()

                        let value = tryToGetValue(obj);

                        $(obj).find('.value-condition').removeClass('has-error')
                        if (!value && operator != 7) {
                            $(obj).find('.value-condition').addClass('has-error')
                            errors.push('Condição ' + (i + 1) + ', valor obrigatório');
                        }

                        if (operator == 7) {
                            value = 'null';
                        }

                        const condition = {
                            field: $fieldSelected.val(),
                            field_text: trimNewlineAndWhitespace($fieldSelected.text()),
                            operator: operator,
                            operator_text: trimNewlineAndWhitespace($operatorSelected.text()),
                            value: value,
                            value_text: trimNewlineAndWhitespace(tryToGetText(obj)),
                            condition_type: $(obj).find('input[type="radio"]:checked').val() || '1', // AND default
                        };

                        conditions.push(condition);
                    });

                    if (errors.length > 0) {
                        throw new Error(errors.join('\n'));
                    }

                    return conditions;
                }

    </script>

@endpush

@section('content')

    <template id="filter-template">
{{--        <div class="mb-3 collapse" id="collapseExample">--}}
            <div class="filter-container">
                <div class="p-2 px-3">
                    <div class="xgrow-form-control mb-2">
                        @include('audience.form')
                    </div>
                </div>


                <div class="p-2 px-3">
                    <div class="d-flex justify-content-end">
                        <div class="xgrow-form-control mb-2 ">
                            <button id="apply-filter" class="xgrow-button" style="height:40px; width:128px">
                                Aplicar filtro
                            </button>
                        </div>
                    </div>
                </div>

            </div>


{{--        </div>--}}
    </template>

    @include('audience.components.templates')

    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Engajamento</li>
            <li class="breadcrumb-item"><a href="{{ route('audience.index') }}">Públicos</a></li>

            <li class="breadcrumb-item active"><span>
                    @if ($type == 'create')
                        Novo público
                    @else
                        Editar público
                    @endif
                </span></li>
        </ol>
    </nav>

    <div class="xgrow-card card-dark p-0">
        <div class="xgrow-card-body p-3 py-4">
            <div class="table-responsive m-t-30">
                @if ($errors->any())
                    @include('elements.alert')
                @endif

                <table id="subscriber-table"
                    class="xgrow-table table text-light table-responsive dataTable overflow-auto no-footer"
                    style="width:100%">
                    <thead>
                        <tr class="card-black" style="border: 2px solid var(--black-card-color)">
                            <th>Nome</th>
                            {{-- <th>Plano ID</th> --}}
                            <th>Produto</th>
                            <th>Status do aluno</th>
                            <th>Cadastro</th>
                            <th>Útimo Acesso</th>
                            <th>Tipo produto</th>
                            <th>Método pgto.</th>
                            <th>Pagamento</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="xgrow-card-footer p-3 border-top mt-4">

            @if ($type == 'create')
                <input id="create-audience" class="xgrow-button" type="submit" value="Criar">
            @else
                <input id="update-audience" class="xgrow-button" type="submit" value="Salvar">
            @endif
        </div>
    </div>
    @include('elements.confirmation-modal')
    @include('elements.toast')
@endsection
