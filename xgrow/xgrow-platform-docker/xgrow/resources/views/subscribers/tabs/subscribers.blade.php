@push('after-styles')
    <style>
        .border-email {
            text-align: justify;
            margin: 14px 0;
            border: 1px solid var(--green4);
            padding: 14px;
        }

        .border-email-title {
            font-weight: 600;
            color: var(--green4);
        }
    </style>
@endpush

@push('jquery')
    <script>
        $(function() {
            $.fn.dataTableExt.afnFiltering.push(
                function(oSettings, aData, iDataIndex) {
                    let dataReturn = true;
                    const iptCreatedRange = document.getElementById("ipt-created-range");
                    const iptLastAccessRange = document.getElementById("ipt-last-access-range");

                    if (!iptCreatedRange || !iptLastAccessRange) return true;

                    const iptCreatedPeriod = iptCreatedRange.value;
                    const iptLastAccessPeriod = iptLastAccessRange.value;

                    if (iptCreatedPeriod) {
                        const [start, end] = iptCreatedPeriod.split("-");
                        const parsedDate = parseDatatablesDate(aData[5]);
                        const parsedStart = parseDatatablesDate(start);
                        const parsedEnd = parseDatatablesDate(end);
                        dataReturn = parsedDate >= parsedStart && parsedDate <= parsedEnd;
                    }

                    if (iptLastAccessPeriod) {
                        const [start, end] = iptLastAccessPeriod.split("-");
                        const parsedDate = parseDatatablesDate(aData[7]);
                        const parsedStart = parseDatatablesDate(start);
                        const parsedEnd = parseDatatablesDate(end);
                        dataReturn = parsedDate >= parsedStart && parsedDate <= parsedEnd;
                    }

                    return dataReturn;
                }
            );
        });
    </script>
@endpush

@push('after-scripts')
    <script>
        let btnTypeX, eX, dtX, nodeX, configX;

        $(document).ready(function() {
            const dateRangeOptions = {
                autoUpdateInput: false,
                "locale": {
                    "format": "DD/MM/YYYY",
                    "separator": " - ",
                    "applyLabel": "Aplicar",
                    "cancelLabel": "Cancelar",
                    "daysOfWeek": [
                        "Dom",
                        "Seg",
                        "Ter",
                        "Qua",
                        "Qui",
                        "Sex",
                        "Sab"
                    ],
                    "monthNames": [
                        "Janeiro",
                        "Fevereiro",
                        "Março",
                        "Abril",
                        "Maio",
                        "Junho",
                        "Julho",
                        "Agosto",
                        "Setembro",
                        "Outubro",
                        "Novembro",
                        "Dezembro"
                    ],
                    "customRangeLabel": "Personalizar"
                },
                ranges: {
                    "Hoje": [moment(), moment()],
                    "Ontem": [moment().subtract(1, "days"), moment().subtract(1, "days")],
                    "Ultimos 7 dias": [moment().subtract(6, "days"), moment()],
                    "Ultimos 30 dias": [moment().subtract(29, "days"), moment()],
                    "Este Mês": [moment().startOf("month"), moment().endOf("month")],
                    "Ultimo Mês": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month")
                        .endOf("month")
                    ],
                    "Limpar": [null, null]
                }
            };

            let createdAtRange = "";
            let lastAccessRange = "";
            let datatable;

            datatable = $("#subscriber-table").DataTable({
                dom: "<\"d-flex flex-wrap justify-content-center justify-content-xl-between justify-content-lg-center\"" +
                    "<\"title-table subs d-flex align-self-center justify-content-center me-1\">" +
                    "<\"d-flex flex-wrap align-items-center justify-content-xl-between justify-content-lg-center\"" +
                    "<\"d-flex flex-wrap align-items-center justify-content-center mb-2\"<\"global-search subs\"><\"filter-button subs\">" +
                    "<\"d-flex flex-wrap mt-2\"<B><\"create-button subs mb-2\">>>>>" +
                    "<\"filter-div subs mt-2\"><\"mt-2\" rt>" +
                    "<\"my-3 d-flex flex-wrap align-items-center justify-content-between\"<\"my-2\"l><\"my-2\"p>>",

                ajax: {
                    url: '{{ route('subscribers.user.index') }}',
                    data: function(d) {
                        d.searchTerm = $("#ipt-global-filter").val();
                        d.plansFilter = $("#slc-plan-filter option:selected").map(function() {
                            return this.value;
                        }).get();
                        d.statusFilter = $("#slc-status-filter option:selected").map(function() {
                            return this.value;
                        }).get();
                        d.createdPeriodFilter = $("#ipt-created-range").val();
                        d.lastAccessPeriodFilter = $("#ipt-last-access-range").val();

                        d.neverAccessedFilter = $("#swt-not-acess").is(":checked");
                        d.emailWrongFilter = $("#swt-email-error").is(":checked");
                    }
                },

                processing: true,
                serverSide: true,
                lengthMenu: [
                    [10, 25, 50, -1],
                    ["10 itens por página", "25 itens por página", "50 itens por página",
                        "Todos os registros"
                    ]
                ],
                language: {
                    "url": "{{ asset('js/datatable-translate-pt-BR.json') }}"
                },
                "columnDefs": [{
                    visible: false,
                    searchable: false
                }],
                order: [
                    [3, "desc"]
                ],
                columns: [{
                        data: "name",
                        render: function(data, type, row) {
                            return "<a href=\"/subscribers/" + row.id +
                                "/edit\" style=\"color: inherit\">" + data + "</a>";
                        }
                    },
                    {
                        data: "email",
                        render: function(data, type, row) {
                            let bounceIcon = "";
                            if (row.email_bounce_id) bounceIcon =
                                `<i class="fas fa-exclamation-circle text-danger" data-bs-toggle="tooltip" data-bs-placement="right" title="${row.email_bounce_description}"></i>`;
                            return bounceIcon + " " + data;
                        }
                    },
                    {
                        data: "cel_phone",
                        orderable: false,
                        visible: true,
                        render: function(data, type, row) {
                            return (data) ? data : row.main_phone || "";
                        }
                    },
                    {
                        data: "created",
                        name: "subscribers.created_at",
                        type: "date",
                        render: function(data, type) {
                            if (type === "sort") {
                                return data;
                            }
                            return (data != null) ? moment(data).format("DD/MM/YYYY HH:mm:ss") : "";
                        }
                    },
                    {
                        data: "status",
                        name: "subscriptions.status",
                        visible: false
                    },
                    {
                        data: "login",
                        render: function(data, type, row) {
                            if (type === "sort") {
                                return data;
                            }

                            const timeZone = Intl.DateTimeFormat('pt-BR', {
                                timeZone: "America/Sao_Paulo",
                                dateStyle: 'short',
                                timeStyle: 'short'
                            });

                            let hasProblemAccess = row.has_problem_access;
                            if (hasProblemAccess === null) hasProblemAccess = "";
                            if (hasProblemAccess === 0) hasProblemAccess = ",não teve problemas";
                            if (hasProblemAccess === 1) hasProblemAccess = ",teve problemas";

                            if (data != null) {
                                return timeZone.format(new Date(data))
                            } else {
                                return "Nunca acessou" + hasProblemAccess;
                            }
                        }
                    },
                    {
                        data: "products_name",
                        name: "products.name",
                        render: function(data) {
                            return data ? data.split(",").join(",<br>") : "";
                        }
                    },
                    {
                        data: "product_id",
                        orderable: false,
                        searchable: false,
                        visible: false
                    },
                    {
                        data: null,
                        searchable: false,
                        render: function(data, type, row) {
                            const url = "/subscribers/" + row.id + "/edit";

                            const params = {
                                title: "Excluir aluno",
                                description: "Você tem certeza que deseja excluir este aluno?",
                                btnSave: "Sim, excluir",
                                btnCancel: "Não, manter",
                                success: "O aluno selecionado foi excluído com sucesso",
                                error: "Não foi possível excluir o aluno: ",
                                url: "/subscribers/delete",
                                method: "POST",
                                body: {
                                    "id": row.id,
                                    "_token": "{{ csrf_token() }}"
                                },
                                datatables: "#subscriber-table"
                            };

                            const modal = window.btoa(JSON.stringify(params));

                            const menu = `
                                    <div class="dropdown">
                                        <button class="xgrow-button table-action-button m-1" type="button" id="dropdownMenuButton${row.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu table-menu" aria-labelledby="dropdownMenuButton${row.id}">
                                            <li><a class="dropdown-item table-menu-item" href="${url}">Editar</a></li>
                                            <li><a class="dropdown-item table-menu-item" href="javascript:void(0)" onclick="openConfirmationModal('${modal}')">Excluir</a></li>
                                            <li><a class="dropdown-item table-menu-item" href="javascript:void(0)" onclick="resendAccessData('/subscribers/${row.id}/resend-date')">Reenviar dados de acesso</a></li>
                                        </ul>
                                    </div>`;

                            return "<div class=\"d-flex\">" + menu + "";
                        }
                    }
                ],

                buttons: [
                    @can('report')
                        {
                            extend: "csv",
                            text: "<button class=\"xgrow-button export-button me-1\" title=\"Exportar em CSV\">\n" +
                                " <i class=\"fas fa-file-csv\" style=\"color: blue\"></i>\n" +
                                " </button>",
                            className: "",
                            action: function(e, dt, node, config) {
                                successToast("Iniciando download!",
                                    "Seu arquivo foi adicionado a fila de downloads. Para ver o andamento, click emListas exportadas no menu lateral."
                                    );
                                axios.post("{{ route('report.download.subscribers.user') }}", {
                                    searchTerm: $("#ipt-global-filter").val(),
                                    plansFilter: $("#slc-plan-filter option:selected").map(
                                        function() {
                                            return this.value;
                                        }).get(),
                                    statusFilter: $("#slc-status-filter option:selected")
                                        .map(function() {
                                            return this.value;
                                        }).get(),
                                    createdPeriodFilter: $("#ipt-created-range").val() ??
                                        null,
                                    lastAccessPeriodFilter: $("#ipt-last-access-range")
                                    .val() ?? null,
                                    neverAccessedFilter: $("#swt-not-acess").is(":checked"),
                                    emailWrongFilter: $("#sswt-email-error").is(":checked"),
                                    typeFile: "csv",
                                    reportName: "subscriber-users"
                                });
                            }
                        }, {
                            extend: "excel",
                            text: "<button class=\"xgrow-button export-button me-1\" title=\"Exportar em XLSX\">\n" +
                                " <i class=\"fas fa-file-excel\" style=\"color: green\"></i>\n" +
                                " </button>",
                            className: "",
                            action: function(e, dt, node, config) {
                                successToast("Iniciando download!",
                                    "Seu arquivo foi adicionado a fila de downloads. Para ver o andamento, click em Listas exportadas no menu lateral."
                                    );
                                axios.post("{{ route('report.download.subscribers.user') }}", {
                                    searchTerm: $("#ipt-global-filter").val(),
                                    plansFilter: $("#slc-plan-filter option:selected").map(
                                        function() {
                                            return this.value;
                                        }).get(),
                                    statusFilter: $("#slc-status-filter option:selected")
                                        .map(function() {
                                            return this.value;
                                        }).get(),
                                    createdPeriodFilter: $("#ipt-created-range").val() ??
                                        null,
                                    lastAccessPeriodFilter: $("#ipt-last-access-range")
                                    .val() ?? null,
                                    neverAccessedFilter: $("#swt-not-acess").is(
                                        ":checked") ?? null,
                                    emailWrongFilter: $("#sswt-email-error").is(
                                        ":checked") ?? null,
                                    typeFile: "xlsx",
                                    reportName: "subscriber-users"
                                });
                            }
                        },
                    @endcan
                ],

                initComplete: function(settings, json) {
                    $(".title-table.subs").html(
                        "<h5 class=\"align-self-center\">Alunos: <span id=\"spn-total-label\"></span></h5>"
                    );
                    $(".buttons-csv").removeClass("dt-button buttons-csv");
                    $(".buttons-excel").removeClass("dt-button buttons-excel");
                    $(".buttons-pdf").removeClass("dt-button buttons-pdf");
                    $(".create-button.subs").html(
                        "<button onclick=\"location.href='/subscribers/create'\" class=\"xgrow-button\" style=\"height:40px; width:128px\"><i class=\"fa fa-plus\"></i> Novo aluno </button>"
                    );
                    $(".dataTables_filter input").attr("placeholder", "Buscar");
                    $(".filter-button.subs").html(`
                                                                                <div class="d-flex align-items-center py-2">
                                                                                    <button type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-bs-expanded="false" aria-bs-controls="collapseExample" class="xgrow-button-filter xgrow-button export-button me-1" aria-expanded="true">
                                                                                    <p>Filtros avançados <i class="fa fa-chevron-down" aria-hidden="true"></i></p>
                                                                                    </button>
                                                                                </div>
                                                                            `);
                    $(".global-search.subs").html(`
                                                                                <div class="xgrow-input me-1 pt-0" style="background-color: var(--input-bg); height: 40px;" >
                                                                                    <input id="ipt-global-filter" placeholder="Busque alguma coisa..." type="text" style="height: 40px;">
                                                                                    <span class="xgrow-input-cancel"><i class="fa fa-search" aria-hidden="true"></i></span>
                                                                                </div>
                                                                            `);
                    $(".filter-div.subs").html(`
                                        <div class="mb-3 collapse" id="collapseExample">
                                            <div class="filter-container">
                                                <div class="p-2 px-3">
                                                    <div class="row align-items-center">
                                                        <div class="col-sm-12 col-md-6 col-lg-3 mt-1">
                                                            <div class="xgrow-form-control mb-2">
                                                                <select id="slc-plan-filter" class="xgrow-select w-100" name="plan-filter[]" id="product" multiple>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12 col-md-6 col-lg-3 mt-1">
                                                            <div class="xgrow-form-control mb-2">
                                                                <select id="slc-status-filter" class="xgrow-select w-100" multiple>
                                                                    <option value="active">Ativo</option>
                                                                    <option value="canceled">Cancelado</option>
                                                                    <option value="failed">Falha no Pagamento</option>
                                                                    <option value="pending_payment">Pagamento Pendente</option>
                                                                    <option value="pending">Pendente</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12 col-md-6 col-lg-3 mt-1">
                                                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                                                                <input type="text" class="form-control" id="ipt-created-range"
                                                                    style="border:none; outline:none; background-color: var(--input-bg); border-bottom: 1px solid var(--border-color);box-shadow: none; color: var(--contrast-green)"
                                                                    autocomplete="off">
                                                                <label for="ipt-created-range">Data de cadastro</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12 col-md-6 col-lg-3 mt-1">
                                                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                                                                <input type="text" class="form-control" id="ipt-last-access-range"
                                                                    style="border:none; outline:none; background-color: var(--input-bg); border-bottom: 1px solid var(--border-color);box-shadow: none; color: var(--contrast-green)"
                                                                    autocomplete="off">
                                                                <label for="ipt-last-access-range">Último acesso</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12 col-md-12 col-lg-6 mt-lg-1 mt-md-3 mt-sm-3 d-flex gap-3 align-items-center flex-sm-column flex-md-row flex-wrap">
                                                            <div class="form-check form-switch">
                                                                <input id="swt-not-acess" class="form-check-input" type="checkbox" value="only_null"/>
                                                                <label for="swt-not-acess">Mostrar apenas alunos que nunca acessaram</label>
                                                            </div>
                                                            <button class="xgrow-button" id="btnResendModal" style="height:40px;width:max-content;margin-top: 5px;padding:0 20px;display:none" data-bs-toggle="modal" data-bs-target="#modal-send-email-not-accessed-courses">
                                                                <i class="fa fa-mail-forward"></i> Reenviar dados de acesso
                                                            </button>
                                                        </div>
                                                        <div class="col-sm-12 col-md-12 col-lg-6 my-3">
                                                            <div class="form-check form-switch">
                                                                <input id="swt-email-error" class="form-check-input" type="checkbox" value="wrong_email"/>
                                                                <label for="swt-email-error">Mostrar apenas alunos com erro no email</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>`);

                    axios.get("{{ URL::route('products.list') }}").then(response => {
                        let html = "";
                        response.data.products.sort(function(a, b) {
                            if (a.name > b.name) return 1;
                            if (a.name < b.name) return -1;
                            return 0;
                        });
                        response.data.products.forEach(item => html +=
                            `<option value="${item.id}">${item.name}</option>`);
                        $("#slc-plan-filter").append(html);
                    });


                    $(".xgrow-datepicker").datepicker({
                        format: "dd/mm/yyyy"
                    });
                    $("#slc-plan-filter").select2({
                        allowClear: true,
                        placeholder: "Produto"
                    });
                    $("#slc-status-filter").select2({
                        allowClear: true,
                        placeholder: "Status"
                    });

                    $("#ipt-global-filter").on("keyup", function() {
                        datatable.search(this.value).draw();
                    });

                    $("#slc-plan-filter").on("change", function() {
                        const selected = $("#slc-plan-filter").val();
                        const filter = selected.join("|");
                        datatable.columns(7).search(filter, true, false).draw();
                    });

                    $("#slc-status-filter").on("change", function() {
                        const selected = $("#slc-status-filter").val();
                        const filter = selected.join("|");
                        datatable.columns(4).search(filter, true, false).draw();
                    });

                    $("#swt-not-acess").on("change", function() {
                        datatable.columns(5).search("").draw();
                    });

                    $("#swt-email-error").on("change", function() {
                        datatable.columns(2).search("").draw();
                    });

                    $("#swt-not-acess").change(function() {
                        $(this).prop("checked") ? $("#btnResendModal").show() :
                            $("#btnResendModal").hide();
                    });

                    $("#btnResendModal").click(function() {
                        $("#spnTotalUser").text(datatable.page.info().recordsDisplay);
                    });

                    $("#btnResendEmail").click(function() {
                        successToast("Ação realizada com sucesso.",
                            "Essa ação pode levar algum tempo para disparar todos os emails."
                        );
                        axios.post(@json(route('subscribers.notification.never-access-course')))
                            .then(function(response) {
                                successToast("Ação realizada com sucesso.", response.data
                                    .response.toString());
                            })
                            .catch(function(error) {
                                errorToast("Ocorreu um erro.", error.response.data.response
                                    .toString());
                            });
                    });

                    $("#ipt-created-range").daterangepicker(dateRangeOptions)
                        .on("apply.daterangepicker", function(ev, picker) {
                            if (!picker.startDate.isValid() && !picker.endDate.isValid()) {
                                return $(this).trigger("cancel.daterangepicker");
                            }
                            $(this).val(picker.startDate.format("DD/MM/YYYY") + "-" + picker.endDate
                                .format("DD/MM/YYYY"));
                            $(this).removeClass("mui--is-empty");
                            $(this).addClass("mui--is-not-empty");
                            datatable.columns(3).search("").draw();
                        })
                        .on("cancel.daterangepicker", function(ev, picker) {
                            $(this).val("");
                            datatable.columns(3).search("").draw();
                        });

                    $("#ipt-last-access-range").daterangepicker(dateRangeOptions)
                        .on("apply.daterangepicker", function(ev, picker) {
                            if (!picker.startDate.isValid() && !picker.endDate.isValid()) {
                                return $(this).trigger("cancel.daterangepicker");
                            }
                            $(this).val(picker.startDate.format("DD/MM/YYYY") + "-" + picker.endDate
                                .format("DD/MM/YYYY"));
                            $(this).removeClass("mui--is-empty");
                            $(this).addClass("mui--is-not-empty");
                            datatable.columns(5).search("").draw();
                        })
                        .on("cancel.daterangepicker", function(ev, picker) {
                            $(this).val("");
                            datatable.columns(5).search("").draw();
                        });
                    datatable.columns(1).search("").draw();

                    let tooltipTriggerList = [].slice.call(document.querySelectorAll(
                        "[data-bs-toggle=\"tooltip\"]"));
                    let tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                },
                drawCallback: function(settings) {
                    const total = datatable.page.info().recordsDisplay || 0;
                    setTotalLabel(total, "#spn-total-label");
                }
            });
        });
    </script>
@endpush

<div class="tab-pane fade show active" id="nav-subscriber" role="tabpanel" aria-labelledby="nav-subscriber-tab">
    <div class="xgrow-card card-dark p-0">
        <div class="xgrow-card-body p-3 py-4">
            @if (count($plans) > 0)
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
                                <th>E-mail</th>
                                <th>Telefone</th>
                                <th>Cadastro</th>
                                <th>Status</th>
                                <th>Útimo Acesso</th>
                                <th>Produto</th>
                                <th>Pid</th>
                                <th class="no-export"></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @else
                <div class="alert alert-warning">
                    Antes de criar um aluno é necessário criar um plano. Acesse o menu "Alunos > Planos"
                    ou clique <a href="/plans/create">aqui</a>.
                </div>
            @endif
        </div>
    </div>
</div>

{{-- <-- MODAL ENVIAR EMAIL --> --}}
<div class="modal-sections modal fade" tabindex="-1" id="modal-send-email-not-accessed-courses" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="d-flex w-100 justify-content-end p-3 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-header">
                <p class="modal-title">Confirmar envio de acesso</p>
            </div>
            <div class="modal-body" style="display:unset;padding:30px">
                <div class="row">
                    <p>
                        Tem certeza que deseja reenviar os dados de acesso para cada um dos <b><span
                                id="spnTotalUser"></span></b> alunos da lista?
                    </p>
                    <div class="border-email">
                        <p class="border-email-title">Template de email a ser enviado:</p>
                        <p><span class="border-email-title">Remetente:</span> {{ $emailTemplate->from ?? '' }}</p>
                        <p><span class="border-email-title">Assunto:</span> {{ $emailTemplate->subject ?? '' }}</p>
                        <p><span class="border-email-title">Mensagem:</span> {!! $emailTemplate->message ?? '' !!}</p>
                        <p class="mt-3 border-email-title text-start">
                            Obs.: as variáveis do corpo da mensagem serão substituídas pelos dados do aluno.
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success" data-bs-dismiss="modal" id="btnResendEmail">Sim, enviar
                </button>
                <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal">Não cancelar</button>
            </div>
        </div>
    </div>
</div>
