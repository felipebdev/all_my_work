@extends('templates.xgrow.main')

@push('after-styles')
    <link href="{{ asset('xgrow-vendor/assets/css/pages/report_access.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <style>
        .acess-card-width {
            width: auto;
        }

        .acess-daterange-width {
            width: auto;
        }

        @media screen and (max-width: 1094px) {
            .acess-card-width {
                width: 100%;
            }

            .acess-daterange-width {
                width: 100%;
            }
        }

    </style>
@endpush

@push('after-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/echarts@4.8.0/dist/echarts.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>

    <script type="text/javascript" src="{{ asset('/js/utils.js') }}"></script>
    <!--Script para o datapicker-->
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
                },
            }, function(start, end, label) {
                dateRange = start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY');
                limitDate = 0;
                applyDaterangeFilter();
            }).on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format(
                    'DD/MM/YYYY'));
                $(this).removeClass('mui--is-empty');
                $(this).addClass('mui--is-not-empty');
            });
        });

        function applyDaterangeFilter() {
            var start = $('input[name="daterange"]').data('daterangepicker').startDate.format('DD/MM/YYYY');
            var end = $('input[name="daterange"]').data('daterangepicker').endDate.format('DD/MM/YYYY');
            const period = `${start} - ${end}`;
            dateRange = period;
            limitDate = 0;
            getDataStats(period, 0);
        }

        $(function() {
            applyDaterangeFilter();
        });

        function getDataStats(period, allData) {
            // getTimeAccessAVG(period, allData); // top info
            getHitsPerHourData(period, allData); // Card 01 - Hits by Hour
            getHitsPerDayData(period, allData); // Card 01 - Hits by Day
            getHitsPerDayWeekData(period, allData); // Card 01 - Hits by Week
            getAgeGenderData(period, allData); // Card 02 - Access by Age and Gender
            getGenderData(period, allData); // Card 02 - Access by Gender
            getHitsByLocationData(period, allData); // Card 03 - Hits by location
        }

        async function getTimeAccessAVG(period, allDate) {
            let res = await axios.get('/api/reports/avg-time-access/', {
                params: {
                    period: period,
                    allDate: allDate
                }
            });
            $('#relMt').text(res.data.total_avg_time);
            $('#relMp').text(res.data.avg_time);

            const percentage = parseInt(res.data.percentage.toString().replace('%', '')) - 100;
            const absoluteVariation = Math.abs(percentage) + '%';
            if (percentage > 0) {
                $('#relPo').html(absoluteVariation + ' <i class=\'fa fa-arrow-up text-success\'></i>');
            } else if (percentage === 0) {
                $('#relPo').html(absoluteVariation + ' <i class=\'fa fa-arrows-h text-dark\'></i>');
            } else if (percentage < 0) {
                $('#relPo').html(absoluteVariation + ' <i class=\'fa fa-arrow-down text-danger\'></i>');
            }
        }

    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item"><span>Relatórios</span></li>
            <li class="breadcrumb-item active mx-2"><span>Acessos</span></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-sm-12">
            <div class="xgrow-card card-dark my-2 p-0" style="background: transparent;box-shadow: none;">
                <div class="xgrow-card-body p-0 m-0">
                    <div class="xgrow-check me-3 d-flex align-items-center">
                        <h5 class="align-self-center">Relatório de acessos na Plataforma</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
    // TODO: Adicionar as métricas quando as mesmas existirem
    @endphp
    {{-- <div class="flex-wrap-reverse pt-1 mb-2 d-flex justify-content-between card-dark"
        style="background: transparent;box-shadow: none;">
        <div class="acess-card-width">
           @include('reports.access.infos')
        </div>
        <div class="acess-daterange-width my-2">

        </div>
    </div> --}}

    <div class="row pb-3">
        <div class="col-sm-12">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-0">
                <input type="text" value="{{ $search['period'] ?? '' }}" class="form-control" name="daterange"
                    id="reportrange" autocomplete="off">
                <label for="daterange">Filtrar por data</label>
            </div>
        </div>
    </div>

    <div class="row pb-3">
        @include('reports.access.hits-access')
        @include('reports.access.gender')
    </div>

    <div class="row">
       @include('reports.access.location')
    </div>
@endsection
