@extends('templates.xgrow.main')

@push('after-styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="{{ asset('xgrow-vendor/assets/css/pages/report_access.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
        rel="stylesheet">
    <style>
        .media-list{
            height: 365px;
        }
        .outer-loading{
            height: 0;
            position: relative;
            top: 150px;
            left: 30%;
            width: fit-content;
            z-index: 1;
        }

        .inner-loading {
            background: rgba(0,0,0,.7);
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 20px;
            border-radius: 10px;
            z-index: 1;
        }
    </style>
@endpush

@push('after-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/echarts@4.8.0/dist/echarts.min.js"></script>
    <!--daterangepicker-->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>

    <script src="{{ asset('js/utils.js') }}"></script>
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

        async function getDataStats(period, allDate) {
            await getMostCommentedContentData(period, allDate, 'ASC', '#least-commented-table-inner'); // Card 01: Most commented
            await getMostCommentedContentData(period, allDate, 'DESC', '#most-commented-table-inner'); // Card 01: Less commented
            await getMostAccessedContentData(period, allDate); // Card 02: Most accessed content
            await getMostLikedContentData(period, allDate); // Card 02: Most liked content
            await getMostAccessedSectionData(period, allDate); // Card 03: Get most accessed section graph
            await getTotalViewedContentByAuthorData(period, allDate); // Card 04: Get most viewed by author

            // await getMostCommentedContentByAuthorData(period, allDate);
        }

    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb justify-content-between my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item ml-0"><a href="/">Início</a></li>
            <li class="breadcrumb-item"><span>Relatórios</span></li>
            <li class="breadcrumb-item active"><span>Conteúdos</span></li>
        </ol>
    </nav>

    <div>
        <div class="my-2">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-0">
                <input type="text" value="{{ $search['period'] ?? '' }}" class="form-control" name="daterange"
                    id="reportrange" autocomplete="off">
                <label for="daterange">Filtrar por data</label>
            </div>
        </div>
    </div>

    <div class="row">
        @include('reports.content.comments')
        @include('reports.content.accessed-liked')
    </div>

    <div class="row">
        @include('reports.content.most-accessed-section')
        @include('reports.content.total-viewed-content-by-author')
    </div>
@endsection
