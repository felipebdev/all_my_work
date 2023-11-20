@push('after-scripts')
    <script>
        async function getMostViewedByCourseData(period, course) {

            let params = {
                period: period,
                allDate: 0,
            };

            if (course) {
                params.course = course;
            }

            let res = await axios.get('/api/reports/get-most-viewed-courses-by-course/', {
                params: params,
            });

            let theme = await localStorage.getItem('theme');
            theme = (theme === 'dark-mode') ? 'light' : 'dark';
            const fontColor = (theme === 'dark') ? '#000000' : '#FFFFFF';

            let mostViewedByCourseChart = echarts.init(document.getElementById('mostViewedByCourseChart'), theme);
            const options = {
                textStyle: {
                    color: fontColor,
                    fontWeight: 500,
                    fontSize: 12,
                },
                backgroundColor: 'transparent',
                grid: {
                    top: 25,
                    height: 290,
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: res.data.labels,
                },
                yAxis: {
                    type: 'value',
                    boundaryGap: [0, '100%'],
                },
                series: [{
                    data: res.data.data,
                    name: 'Acessos',
                    type: 'line',
                    symbol: 'none',
                    sampling: 'average',
                    itemStyle: {
                        color: 'rgb(255, 70, 131)',
                    },
                    areaStyle: [],
                }],
                tooltip: {
                    trigger: 'axis'
                },

            };
            mostViewedByCourseChart.setOption(options);
            window.addEventListener('resize', function() {
                mostViewedByCourseChart.resize();
            });
        }

        $('.export-2').on('click', async (e) => {
            let res = await axios.get('/api/reports/get-most-viewed-courses-by-course/', {
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
                    'day': item,
                    'value': values[index]
                });
            });

            const title = 'Cursos mais acessados por dia da semana';
            const filename = toKebabCase(title);
            let text = title + '.\n\n';
            if (e.target.dataset.type === 'pdf') {
                const header = ['Dia da semana', 'Acessos'];
                pdfMakeWrapperDownloader(
                    title,
                    header,
                    data.map((item) => {
                        return [`${item.day}`, `${item.value ?? 0}`];
                    }),
                    filename
                )
            }
            if (e.target.dataset.type === 'csv') {
                text = 'dia da semana;acessos\n';
                data.map((item) => {
                    text += `${item.day};${item.value}\n`;
                });
                downloadFile(text, 'data:text/csv;charset=utf-8,', filename + '.csv');
            }
            if (e.target.dataset.type === 'xls') {
                text = '<table>' +
                    '  <thead>' +
                    '    <tr>' +
                    '      <th>Dia da Semana</th>' +
                    '      <th>Acessos</th>' +
                    '    </tr>' +
                    '  </thead>' +
                    '  <tbody>';

                data.map((item) => {
                    text += `<tr>
                                          <td>${item.day}</td>
                                          <td>${item.value}</td>
                                        </tr>`;
                });
                text += '</tbody>' +
                    '</table>';

                downloadFile(text, 'data:application/vnd.ms-excel;charset=utf-8,', filename + '.xls');

            }
            if (e.target.dataset.type === 'txt') {
                data.map((item) => {
                    text += `Dia da Semana: ${item.day} | Acessos: ${item.value}\n`;
                });
                downloadFile(text, 'data:text/plain;charset=utf-8,', filename + '.txt');
            }
        });

    </script>
@endpush

<div class="xgrow-card card-dark mb-2">
    <div class="xgrow-card-header">
        <div class="d-flex flex-column">
            <p class="xgrow-card-title">Cursos mais acessados</p>
            <p class="xgrow-card-subtitle">Por dia da semana</p>
        </div>
        <div class="xgrow-form-control d-sm-flex d-none">
            <select name="course" class="perfomance-select xgrow-select" id="courseSelect2">
                <option value="">Selecione o curso...</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="xgrow-card-body">
        <span id="mostViewedByCourseInfo"></span>
        <div id="mostViewedByCourseChart" style="height: 400px"></div>
    </div>
    <div class="xgrow-card-footer">
        <div class="d-flex align-items-center">
            <p class="xgrow-medium-bold me-2">Exportar em</p>
            <button class="xgrow-button export-button me-1 export-2" data-type="pdf" title="Exportar em PDF">
                <i class="fas fa-file-pdf" aria-hidden="true" data-type="pdf" style="color: red;"></i>
            </button>
            <button class="xgrow-button export-button me-1 export-2" data-type="csv" title="Exportar em CSV">
                <i class="fas fa-file-csv" aria-hidden="true" data-type="csv" style="color: blue;"></i>
            </button>
            <button class="xgrow-button export-button me-1 export-2" data-type="xls" title="Exportar em XLSX">
                <i class="fas fa-file-excel" aria-hidden="true" data-type="xls" style="color: green;"></i>
            </button>
        </div>
    </div>
</div>
