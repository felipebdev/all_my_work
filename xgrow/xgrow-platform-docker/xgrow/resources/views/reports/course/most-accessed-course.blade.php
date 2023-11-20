@push('after-scripts')
    <script>
        async function getMostViewedCoursesData(period) {
            let res = await axios.get('/api/reports/get-most-viewed-courses/', {
                params: {
                    period: period,
                    allDate: 0,
                },
            });
            let theme = await localStorage.getItem('theme');
            theme = (theme === 'dark-mode') ? 'light' : 'dark';
            const fontColor = (theme === 'dark') ? '#000000' : '#FFFFFF';

            let mostViewedCoursesChart = echarts.init(document.getElementById('mostViewedCoursesChart'), theme);
            const options = {
                textStyle: {
                    color: fontColor,
                    fontWeight: 500,
                    fontSize: 12,
                },
                backgroundColor: 'transparent',
                xAxis: {
                    type: 'category',
                    data: res.data.labels,
                },
                yAxis: {
                    type: 'value',
                    boundaryGap: [0, '100%'],
                },
                series: [{
                    data: res.data.data,
                    type: 'bar',
                    name: 'Acessos',
                    showBackground: false,
                    backgroundStyle: {
                        color: 'rgba(220, 220, 220, 0.8)',
                    },
                    itemStyle: {
                        color: 'rgb(35,120,247)',
                    },
                }],
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow',
                    },
                },
            };

            mostViewedCoursesChart.setOption(options);
            window.addEventListener('resize', function() {
                mostViewedCoursesChart.resize();
            });
        }

        $('.export').on('click', async (e) => {
            let res = await axios.get('/api/reports/get-most-viewed-courses/', {
                params: {
                    period: dateRange2,
                    allDate: limitDate2,
                },
            });
            const values = res.data.data;
            const labels = res.data.labels;
            const data = [];
            labels.map((item, index) => {
                data.push({
                    'name': item,
                    'value': values[index]
                });
            });

            const title = 'Cursos mais acessados';
            const filename = toKebabCase(title);
            let text = title + '.\n\n';
            if (e.target.dataset.type === 'pdf') {
                const header = ['Curso', 'Acessos'];
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
                text = 'curso,acessos\n';
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
                    '      <th>Acessos</th>\n' +
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

    </script>
@endpush

<div class="xgrow-card card-dark mb-2">
    <div class="xgrow-card-header">
        <p class="xgrow-card-title">Cursos mais acessados</p>
    </div>
    <div class="xgrow-card-body">
        <span id="mostViewedCoursesInfo"></span>
        <div id="mostViewedCoursesChart" style="height: 440px"></div>
    </div>
    <div class="xgrow-card-footer">
        <div class="d-flex align-items-center">
            <p class="xgrow-medium-bold me-2">Exportar em</p>
            <button class="xgrow-button export-button me-1 export" data-type="pdf" title="Exportar em PDF">
                <i class="fas fa-file-pdf" aria-hidden="true" data-type="pdf" style="color: red;"></i>
            </button>
            <button class="xgrow-button export-button me-1 export" data-type="csv" title="Exportar em CSV">
                <i class="fas fa-file-csv" aria-hidden="true" data-type="csv" style="color: blue;"></i>
            </button>
            <button class="xgrow-button export-button me-1 export" data-type="xls" title="Exportar em XLSX">
                <i class="fas fa-file-excel" aria-hidden="true" data-type="xls" style="color: green;"></i>
            </button>
        </div>
    </div>
</div>
