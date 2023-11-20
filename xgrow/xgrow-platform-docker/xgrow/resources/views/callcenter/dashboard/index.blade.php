@extends('templates.xgrow.main')

@push('after-styles')
    <link href="{{ asset('xgrow-vendor/assets/css/pages/dashboard_index.css') }}" rel="stylesheet">
    <link href="{{ asset('xgrow-vendor/assets/css/pages/callcenter_dashboard_attendants.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet">
    <style>
        .callcenter-cards {
            width: auto;
            padding: 0;
            height: auto;
            overflow-x: auto;
        }

        .callcenter-daterange {
            padding: 8px 0;
        }

        @media only screen and (max-width: 1283px) {
            .callcenter-cards {
                width: 100%;
            }

            .callcenter-daterange {
                width: 100%;
            }
        }


    </style>
@endpush

@push('after-scripts')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/echarts@4.8.0/dist/echarts.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script type="text/javascript" src="{{ asset('/js/utils.js') }}"></script>
    <script type="text/javascript">
        var dateRange;
        var limitDate;
        $(function() {
            $('input[name="daterange"]').daterangepicker({
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
                    'Ultimo Mês': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')],
                    'Limpar': [null, null],
                },
            }, function(start, end, label) {
                dateRange = start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY');
                limitDate = 0;
            }).on('apply.daterangepicker', function(ev, picker) {
                if (!picker.startDate.isValid() && !picker.endDate.isValid()) {
                    return $(this).trigger('cancel.daterangepicker');
                }
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format(
                    'DD/MM/YYYY'));
                $(this).removeClass('mui--is-empty');
                $(this).addClass('mui--is-not-empty');

                let period =  picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD');
                const selected = $("#slc-audience-filter option:selected").text();

                if (selected.length < 1){
                    errorToast('Atenção', 'Selecione pelo menos um público');
                    return;
                }

                const filter = selected;

                getTotalLeads(filter);
                getTotalPending(filter);
                getEarnings(period, filter);
                getLosses(period, filter);
                getNoContact(period, filter);
                getAttendantsList(filter);
            });

            // $('#slc-audience-filter').select2({
            //     allowClear: true,
            //     placeholder: 'Público'
            // });

            $('#slc-audience-filter').on('change', function() {
                const selected = $("#slc-audience-filter option:selected").text();

                if (selected.length < 1){
                    errorToast('Atenção', 'Selecione pelo menos um público');
                    return;
                }

                const filter = selected;

                let period =  $('input[name="daterange"]').data('daterangepicker').startDate.format('YYYY-MM-DD') + ' - ' + $('input[name="daterange"]').data('daterangepicker').endDate.format('YYYY-MM-DD');
                getTotalLeads(filter);
                getTotalPending(filter);
                getEarnings(period, filter);
                getLosses(period, filter);
                getNoContact(period, filter);
                getAttendantsList(filter);
            });
        });

        async function getTotalLeads(filter = null) {
            let res = await axios.get(`/callcenter/reports/dashboard/get-total-leads/${filter}`);
            $('#cardLeads').text(res.data);
        }

        async function getTotalPending(filter = null) {
            let res = await axios.get(`/callcenter/reports/dashboard/get-total-pending/${filter}`);
            $('#cardPending').text(res.data);
        }
    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item active mx-2"><span>Call Center</span></li>
        </ol>
    </nav>

    @include('callcenter.dashboard.header')

    <div class="row">
        <div class="col-xl-6 col-lg-6 col-md-12">
            @include('callcenter.dashboard.charts.earnings')
        </div>
        <div class="col-xl-6 col-lg-6 col-md-12">
            @include('callcenter.dashboard.charts.losses')
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6 col-lg-6 col-md-12">
            @include('callcenter.dashboard.charts.nocontact')
        </div>
        <div class="col-xl-6 col-lg-6 col-md-12">
            @include('callcenter.dashboard.attendants')
        </div>
    </div>

    @include('elements.toast')
@endsection
