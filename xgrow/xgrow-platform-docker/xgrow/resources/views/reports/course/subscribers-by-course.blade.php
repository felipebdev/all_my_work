@push('after-scripts')
    <script>
        async function getSubscriberByCourseData() {
            let res = await axios.get('/api/reports/subscriber-by-course/');

            let theme = await localStorage.getItem('theme');
            theme = (theme === 'dark-mode') ? 'light' : 'dark';
            const fontColor = (theme === 'dark') ? '#000000' : '#FFFFFF';

            let subscriberByCourseChart = echarts.init(document.getElementById('subscribersByCourseChart'), theme);

            const options = {
                textStyle: {
                    color: fontColor,
                    fontWeight: 500,
                    fontSize: 12,
                },
                tooltip: {
                    trigger: 'axis',
                    formatter: 'Curso: {b}<br/>Quantidade de alunos: {c}',
                    axisPointer: {
                        type: 'shadow'
                    }
                },
                backgroundColor: 'transparent',
                xAxis: {
                    type: 'category',
                    data: res.data.labels
                },
                yAxis: {
                    type: 'value',
                    minInterval: 1
                },
                series: [{
                    data: res.data.data,
                    type: 'bar',
                    showBackground: false,
                    color: 'greenyellow',
                    textStyle: {
                        color: fontColor,
                        fontWeight: 500,
                        fontSize: 12,
                    },
                    label: {
                        show: true,
                        textStyle: {
                            color: '#2a2e39',
                            fontWeight: 500,
                            fontSize: 12,
                        },
                    }
                }]
            };
            subscriberByCourseChart.setOption(options);
            window.addEventListener('resize', function() {
                subscriberByCourseChart.resize();
            });
        }


        $('.export-4').on('click', async (e) => {
            let res = await axios.get('/api/reports/subscriber-by-course/');
            const values = res.data.data;
            const labels = res.data.labels;

            const data = [];
            labels.map((item, index) => {
                data.push({
                    'name': item,
                    'value': values[index]
                });
            });

            const title = 'Quantidade de alunos por curso';
            const filename = toKebabCase(title);
            let text = title + '.\n\n';
            if (e.target.dataset.type === 'pdf') {
                const header = ['Curso', 'Quantidade de alunos'];
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
                text = 'curso,quantidade_de_alunos\n';
                data.map((item) => {
                    text += `${item.name};${item.value}\n`;
                });
                downloadFile(text, 'data:text/csv;charset=utf-8,', filename + '.csv');
            }
            if (e.target.dataset.type === 'xls') {
                text = '<table>\n' +
                    '  <thead>' +
                    '    <tr>' +
                    '      <th>Curso</th>' +
                    '      <th>Quantidade de alunos</th>\n' +
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
                    text += `Curso: ${item.name} | Quantidade de alunos: ${item.value}\n`;
                });
                downloadFile(text, 'data:text/plain;charset=utf-8,', filename + '.txt');
            }
        });
        getSubscriberByCourseData();

    </script>
@endpush

<div class="xgrow-card card-dark mt-2">
    <div class="xgrow-card-header d-flex justify-content-between">
        <p class="xgrow-card-title">Quantidade de alunos por curso</p>
    </div>
    <div class="xgrow-card-body">

        <div id="subscribersByCourseChart" style="height: 400px"></div>

        <div class="xgrow-card-footer">
            <div class="d-flex align-items-center">
                <p class="xgrow-medium-bold me-2">Exportar em</p>
                <button class="xgrow-button export-button me-1 export-4" data-type="pdf" title="Exportar em PDF">
                    <i class="fas fa-file-pdf" aria-hidden="true" data-type="pdf" style="color: red;"></i>
                </button>
                <button class="xgrow-button export-button me-1 export-4" data-type="csv" title="Exportar em CSV">
                    <i class="fas fa-file-csv" aria-hidden="true" data-type="csv" style="color: blue;"></i>
                </button>
                <button class="xgrow-button export-button me-1 export-4" data-type="xls" title="Exportar em XLSX">
                    <i class="fas fa-file-excel" aria-hidden="true" data-type="xls" style="color: green;"></i>
                </button>
            </div>
        </div>
    </div>
</div>
