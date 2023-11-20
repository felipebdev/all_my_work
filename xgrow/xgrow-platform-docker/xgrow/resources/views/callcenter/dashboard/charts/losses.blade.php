@push('after-scripts')
    <script>
        async function getLosses(period, filter = null) {
            document.getElementById('lossesInfo').innerText = 'Carregando...';
            $('#lossesChart').addClass('d-none');
            let res = await axios.get(`/callcenter/reports/dashboard/lost/per-attendant/${period}/${filter}`);

            let theme = await localStorage.getItem('theme');
            theme = (theme === 'dark-mode') ? 'light' : 'dark';
            const fontColor = (theme === 'dark') ? '#000000' : '#FFFFFF';

            $('#cardLosses').text(res.data.total);
            let legends = res.data.group.map(function(attendant) {
                return attendant.attendant.name;
            });
            let data = res.data.group.map(function(attendant) {
                return attendant.counted_info;
            });

            if (res.data.group.length < 1) {
                $('#lossesChart').addClass('d-none');
                document.getElementById('lossesInfo').innerText = 'Não há dados para exibir no período selecionado';
            } else {
                $('#lossesChart').removeClass('d-none');
                document.getElementById('lossesInfo').innerText = '';
            }

            let lossesChart = echarts.init(document.getElementById('lossesChart'), theme);
            const options = {
                textStyle: {
                    color: fontColor,
                    fontWeight: 500,
                    fontSize: 12,
                },
                backgroundColor: 'transparent',
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    }
                },
                legend: {
                    data: legends,
                    type: 'scroll',
                    orient: 'horizontal',
                    textStyle: {
                        color: fontColor,
                        fontWeight: 500,
                        fontSize: 12,
                    },
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: {
                    minInterval: 1,
                    type: 'value',
                    boundaryGap: [0, 0.01]
                },
                yAxis: {
                    type: 'category',
                    data: legends
                },
                series: [
                    {
                        name: 'Perdidos',
                        type: 'bar',
                        itemStyle: {
                            color: '#D04646'
                        },
                        data: data
                    },
                ]
            };

            lossesChart.setOption(options);
            lossesChart.resize();

            window.addEventListener('resize', function() {
                lossesChart.resize();
            });
        }

        async function exportLosses(type){
            let [startDate, _, endDate] = $('input[name="daterange"]').val().split(' ');
            let period = startDate.split('/').reverse().join('-') + ' - ' + endDate.split('/').reverse().join('-');
            const audience = $("#slc-audience-filter option:selected").val();

            if (audience === '') {
                errorToast('Algum erro aconteceu!', 'Selecione um público');
                return;
            }
            let res = await axios.get(`/callcenter/reports/dashboard/lost/per-attendant/${period}/${audience}`);
            const values = res.data.group;

            if (res.data.group.length < 1) {
                errorToast('Algum erro aconteceu!', 'Não há dados para exportar desse período');
                return;
            }

            let data = [];
            values.map((item, index) => {
                data.push({
                    'attendant': item.attendant.name,
                    'value': item.counted_info,
                });
            });

            const title = `Perdidos por Atendente no perído de ${$('input[name="daterange"]').val()}`;
            const filename = `perdidos-por-atendente`;

            let text = title + '.\n\n';
            if (type === 'pdf') {
                const header = ['Atendente', 'Perdidos'];
                pdfMakeWrapperDownloader(
                    title,
                    header,
                    data.map((item) => {
                        return [`${item.attendant}`, `${item.value ?? 0}`];
                    }),
                    filename
                )
            }
            if (type === 'csv') {
                text = 'atendente;perdidos\n';
                data.map((item) => {
                    text += `${item.attendant};${item.value ? item.value : 0}\n`;
                });
                downloadFile(text, 'data:text/csv;charset=utf-8,', filename + '.csv');
            }
            if (type === 'xls') {
                text = "Atendente\tPerdidos\n";
                data.map((item) => {
                    text += `${item.attendant + ''}\t${item.value ? item.value : 0}\n`;
                });

                downloadFile(text, 'data:application/csv;charset=utf-8,', filename + '.xls');
            }
            if (type === 'txt') {
                data.map((item) => {
                    text += `Atendente: ${item.attendant} | Perdidos: ${item.value ? item.value : 0}\n`;
                });
                downloadFile(text, 'data:text/plain;charset=utf-8,', filename + '.txt');
            }
        }
    </script>
@endpush

<div class="xgrow-card card-dark mb-2 mt-2">
    <div class="xgrow-card-header">
        <p class="xgrow-card-title">Total de perdidos por atendente</p>
    </div>
    <div class="xgrow-card-body d-flex flex-column" style="min-height: 432px">
        <p id="lossesInfo" class="text-center my-auto">Selecione um público para mostrar as informações</p>
        <div id="lossesChart" class="d-none" style="height: 440px; width: 100% !important;"></div>
    </div>
    <div class="xgrow-card-footer">
        <div class="d-flex align-items-center">
            <p class="xgrow-medium-bold me-2">Exportar em</p>
            <button class="xgrow-button export-button me-1" onclick="exportLosses('pdf')" title="Exportar em PDF">
                <i class="fas fa-file-pdf"aria-hidden="true"style="color: red;"></i>
            </button>
            <button class="xgrow-button export-button me-1" onclick="exportLosses('csv')" title="Exportar em CSV">
                <i class="fas fa-file-csv" aria-hidden="true" style="color: blue;"></i>
            </button>
            <button class="xgrow-button export-button me-1" onclick="exportLosses('xls')" title="Exportar em XLSX">
                <i class="fas fa-file-excel" aria-hidden="true" style="color: green;"></i>
            </button>
        </div>
    </div>
</div>
