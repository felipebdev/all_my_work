@extends('templates.xgrow.main')

@push('after-styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <link href="{{ asset('xgrow-vendor/assets/css/pages/report_access.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
          rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet">
@endpush

@push('jquery')
    <script>
        $(function() {
            $.fn.dataTableExt.afnFiltering.push(
                function(oSettings, aData, iDataIndex) {
                    let dataReturn = true;
                    const firstAccessRange = document.getElementById('course-first_access-range');
                    const lastAccessRange = document.getElementById('course-last_access-range');
                    if (!firstAccessRange || !lastAccessRange) return true;

                    const firstAccessPeriod = firstAccessRange.value;
                    const lastAccessPeriod = lastAccessRange.value;
                    if (!firstAccessPeriod && !lastAccessPeriod) return true;

                    if (firstAccessPeriod) {
                        const [start, end] = firstAccessPeriod.split('-');
                        const parsedDate = parseDatatablesDate(aData[3]);
                        const parsedStart = parseDatatablesDate(start.trim());
                        const parsedEnd = parseDatatablesDate(end.trim());
                        if (parsedDate >= parsedStart && parsedDate <= parsedEnd) {
                            dataReturn = true;
                        } else {
                            dataReturn = false;
                        }
                    }

                    if (lastAccessPeriod) {
                        const [start, end] = lastAccessPeriod.split('-');
                        const parsedDate = parseDatatablesDate(aData[4]);
                        const parsedStart = parseDatatablesDate(start.trim());
                        const parsedEnd = parseDatatablesDate(end.trim());
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

@push('after-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/echarts@4.8.0/dist/echarts.min.js"></script>
    <!--daterangepicker-->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script src="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.0/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.html5.min.js"></script>

    <script src="/js/percentageBars.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

    <script src="{{ asset('js/utils.js') }}"></script>
    <script type="text/javascript">
        const dateRangeOptions = {
            autoUpdateInput: false,
            'locale': {
                'format': 'DD/MM/YYYY',
                'separator': ' - ',
                'applyLabel': 'Aplicar',
                'cancelLabel': 'Limpar',
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
                'Ultimo Mês': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month')
                    .endOf('month')
                ],
            },
        };

        var dateRange2;
        var limitDate2;

        $(function () {
            $('input[name="daterange-2"]').daterangepicker(dateRangeOptions, function (start, end, label) {
                dateRange2 = start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY');
                limitDate2 = 0;

                applyDaterange2Filter();
            }).on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format(
                    'DD/MM/YYYY'));
                $(this).removeClass('mui--is-empty');
                $(this).addClass('mui--is-not-empty');

                table.columns(2).search('').draw();
            });
        });

        function applyDaterange2Filter() {
            var start = $('input[name="daterange-2"]').data('daterangepicker').startDate.format('DD/MM/YYYY');
            var end = $('input[name="daterange-2"]').data('daterangepicker').endDate.format('DD/MM/YYYY');
            const period = `${start} - ${end}`;
            const course = $('#courseSelect2 option:selected').val();
            dateRange2 = period;
            limitDate2 = 0;
            getDataStats(period, course);
        }

        $(function () {
            applyDaterange2Filter();
        });

        function getDataStats(period, course) {
            getMostViewedCoursesData(period)
            getMostViewedByCourseData(period, course)
        }

        $('#courseSelect2').on('change', (e) => {
            getMostViewedByCourseData(dateRange2, e.target.value);
        });
    </script>

@endpush

@section('content')

    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item"><span>Relatórios</span></li>
            <li class="breadcrumb-item active mx-2"><span>Pesquisa</span></li>
        </ol>
    </nav>

    <div class="flex-wrap-reverse pt-1 mb-2 d-flex justify-content-between">
        <div class="w-100 my-2">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-0">
                <input type="text" value="{{ $search['period'] ?? '' }}" class="form-control" name="daterange-2"
                    id="reportrange-2"  autocomplete="off">
                <label for="daterange-2">Filtrar por data</label>
            </div>
        </div>
    </div>



    <div class="row">
        <div class="col-xl-6 col-lg-6 col-md-12">
            @include('reports.course.most-accessed-course')
        </div>

        <div class="col-xl-6 col-lg-6 col-md-12">
            @include('reports.course.most-accessed-course-by-course')
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-xl-12 col-lg-12 col-md-12">
            <div class="xgrow-card card-dark mb-2">
                <div class="xgrow-card-header d-flex justify-content-between">
                    <p class="xgrow-card-title">Alunos ativos e inativos</p>
                </div>
                <div class="xgrow-card-body">

                    <ul class="activity-card-nav nav nav-tabs tabs-card-big flex-nowrap mb-4" id="myTabCard"
                        role="tablist">
                        <li class="activity-card-item nav-item tab-card-big">
                            <a class="activity-card-link nav-link active" id="hour-tab-card" data-bs-toggle="tab"
                               href="#overview" role="tab" aria-controls="hour-card" aria-selected="true">Visão
                                geral</a>
                        </li>
                        <li class="activity-card-item nav-item tab-card-big">
                            <a class="nav-link" id="day-tab-card" data-bs-toggle="tab" href="#withcourse" role="tab"
                               aria-controls="day-card" aria-selected="false">Alunos em curso</a>
                        </li>
                        <li class="activity-card-item nav-item tab-card-big">
                            <a class="nav-link" id="week-tab-card" data-bs-toggle="tab" href="#withoutcourse" role="tab"
                               aria-controls="week-card" aria-selected="false">Alunos sem curso</a>
                        </li>
                    </ul>

                    <div class="tab-content" id="myTabCardContent">

                        <div class="tab-pane fade show active" id="overview" role="tabpanel"
                             aria-labelledby="hour-card">
                            @include('reports.course.sections.tab-overview')
                        </div>

                        <div class="tab-pane fade" id="withcourse" role="tabpanel" aria-labelledby="day-tab-card">
                            @include('reports.course.sections.tab-with-course')
                        </div>

                        <div class="tab-pane fade" id="withoutcourse" role="tabpanel" aria-labelledby="week-tab-card">
                            @include('reports.course.sections.tab-without-course')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12">
            @include('reports.course.subscribers-by-course')
        </div>
    </div>

@endsection
