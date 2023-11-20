@push('after-scripts')
    <script>
        async function getSubscriberActiveInativeData() {
            let res = await axios.get('/api/reports/subscriber-course/');

            let theme = await localStorage.getItem('theme');
            theme = (theme === 'dark-mode') ? 'light' : 'dark';
            const fontColor = (theme === 'dark') ? '#000000' : '#FFFFFF';

            let subscriberActiveInativeChart = echarts.init(document.getElementById('overviewChart'), theme);
            const options = {
                textStyle: {
                    color: fontColor,
                    fontWeight: 500,
                    fontSize: 12,
                },
                title: {
                    text: 'Visão geral do curso/alunos',
                    subtext: 'em curso X sem curso',
                    left: 'center',
                    textStyle: {
                        color: fontColor,
                        fontWeight: 500,
                        fontSize: 12,
                    }
                },
                backgroundColor: 'transparent',
                tooltip: {
                    trigger: 'item',
                    formatter: '{a} <br/>{b} : {c} ({d}%)',
                    textStyle: {
                        color: fontColor,
                        fontWeight: 500,
                        fontSize: 12,
                    }
                },
                legend: {
                    orient: 'vertical',
                    left: 'left',
                    data: res.data.labels,
                    textStyle: {
                        color: fontColor,
                        fontWeight: 500,
                        fontSize: 12,
                    }
                },
                series: [{
                    name: 'Total',
                    type: 'pie',
                    radius: '55%',
                    center: ['50%', '60%'],
                    data: res.data.status,
                    emphasis: {
                        itemStyle: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)',
                        },
                    },
                }, ],
            };
            subscriberActiveInativeChart.setOption(options);
            window.addEventListener('resize', function() {
                subscriberActiveInativeChart.resize();
            });
        }


        $('.export-3').on('click', async (e) => {
            let res = await axios.get('/api/reports/subscriber-course/');
            const values = res.data.status;
            const labels = res.data.labels;

            console.log(values);
            const data = [];
            labels.map((item, index) => {
                data.push({
                    'name': item,
                    'value': values[index].value
                });
            });

            const title = 'Visão geral do curso/alunos';
            const filename = toKebabCase(title);
            let text = title + '.\n\n';
            if (e.target.dataset.type === 'pdf') {
                const header = ['Em curso', 'Sem curso'];
                pdfMakeWrapperDownloader(
                    title,
                    header,
                    data.map((item) => {
                        return [`${item.name}`, `${item.value ?? 0}`];
                    }),
                    filename
                )
            }
            if (e.target.dataset.type === 'csv') {
                text = 'emcurso,semcurso\n';
                data.map((item) => {
                    text += `${item.name};${item.value}\n`;
                });
                downloadFile(text, 'data:text/csv;charset=utf-8,', filename + '.csv');
            }
            if (e.target.dataset.type === 'xls') {
                text = '<table>\n' +
                    '  <thead>' +
                    '    <tr>' +
                    '      <th>Em curso</th>' +
                    '      <th>Sem curso</th>\n' +
                    '    </tr>' +
                    '  </thead>' +
                    '  <tbody>';

                data.map((item) => {
                    text += `<tr>
                                                    <td>${item.name}</td>
                                                    <td>${item.value}</td>
                                                </tr>`;
                });
                text += '</tbody>' +
                    '</table>';

                downloadFile(text, 'data:application/vnd.ms-excel;charset=utf-8,', filename + '.xls');

            }
            if (e.target.dataset.type === 'txt') {
                data.map((item) => {
                    text += `Curso: ${item.name} | Acessos: ${item.value}\n`;
                });
                downloadFile(text, 'data:text/plain;charset=utf-8,', filename + '.txt');
            }
        });

        getSubscriberActiveInativeData();

    </script>
@endpush

<div id="overviewChart" style="height: 400px"></div>
<div class="xgrow-card-footer">
    <div class="d-flex align-items-center">
        <p class="xgrow-medium-bold me-2">Exportar em</p>
        <button class="xgrow-button export-button me-1 export-3" data-type="pdf" title="Exportar em PDF">
            <i class="fas fa-file-pdf" aria-hidden="true" data-type="pdf" style="color: red;"></i>
        </button>
        <button class="xgrow-button export-button me-1 export-3" data-type="csv" title="Exportar em CSV">
            <i class="fas fa-file-csv" aria-hidden="true" data-type="csv" style="color: blue;"></i>
        </button>
        <button class="xgrow-button export-button me-1 export-3" data-type="xls" title="Exportar em XLSX">
            <i class="fas fa-file-excel" aria-hidden="true" data-type="xls" style="color: green;"></i>
        </button>
    </div>
</div>
