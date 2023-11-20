@push('after-styles')
    <style>
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
    <script>
        const loadingCardOne = document.getElementById('loadingCardOne');

        async function getHitsPerHourData(period, allDate) {
            loadingCardOne.style.display = 'block';
            let res = await axios.get('/api/reports/hits-per-hour/', {
                params: {period: period, allDate: allDate}
            });
            loadingCardOne.style.display = 'none';
            let hitsPerHourChart = echarts.init(document.getElementById('hitsPerHourChart'));
            const options = {
                textStyle: {
                    color: 'gray',
                    fontWeight: 500,
                    fontSize: 12,
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: res.data.response.labels,
                },
                yAxis: {
                    type: 'value',
                    boundaryGap: [0, '100%'],
                },
                series: [{
                    data: res.data.response.data,
                    name: 'Acessos',
                    type: 'line',
                    symbol: 'none',
                    sampling: 'average',
                    itemStyle: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--green1'),
                    },
                    areaStyle: [],
                }],
                tooltip: {
                    trigger: 'axis'
                },

            };
            hitsPerHourChart.setOption(options);
            window.addEventListener('resize', function() {
                hitsPerHourChart.resize();
            });

            let tabEl = document.querySelector('#hour-tab-card');
            tabEl.addEventListener('shown.bs.tab', function() {
                hitsPerHourChart.resize();
            });
        }

        $('.export').on('click', async (e) => {
            console.log(e.target);
            console.log(e.target.dataset.type);

            let res = await axios.get('/api/reports/hits-per-hour/', {
                params: {
                    period: dateRange,
                    allDate: limitDate,
                },
            });
            const values = res.data.response.data;
            const labels = res.data.response.labels;
            const data = [];
            labels.map((item, index) => {
                data.push({
                    'hour': item,
                    'value': values[index]
                });
            });

            const title = 'Horário de pico por hora';
            const filename = toKebabCase(title);
            let text = title + '.\n\n';
            if (e.target.dataset.type === 'pdf') {
                const header = ['Horário', 'Acessos'];
                pdfMakeWrapperDownloader(
                    title,
                    header,
                    data.map((item) => {
                        return [`${item.hour}:00`, `${item.value ?? 0}`];
                    }),
                    filename
                )
            }
            if (e.target.dataset.type === 'csv') {
                text = 'horário;acessos\n';
                data.map((item) => {
                    text += `${item.hour}:00;${item.value ? item.value : 0}\n`;
                });
                downloadFile(text, 'data:text/csv;charset=utf-8,', filename + '.csv');
            }
            if (e.target.dataset.type === 'xls') {
                text = '<table>' +
                    '  <thead>' +
                    '    <tr>' +
                    '      <th>Horário</th>' +
                    '      <th>Acessos</th>' +
                    '    </tr>' +
                    '  </thead>' +
                    '  <tbody>';

                data.map((item) => {
                    text += `<tr>
                                                                      <td>${item.hour + ':00'}</td>
                                                                      <td>${item.value ? item.value : 0}</td>
                                                                    </tr>`;
                });
                text += '</tbody>' +
                    '</table>';

                downloadFile(text, 'data:application/vnd.ms-excel;charset=utf-8,', filename + '.xls');

            }
            if (e.target.dataset.type === 'txt') {
                data.map((item) => {
                    text += `Horário: ${item.hour}:00 | Acessos: ${item.value ? item.value : 0}\n`;
                });
                downloadFile(text, 'data:text/plain;charset=utf-8,', filename + '.txt');
            }
        });
    </script>

    <script>
        async function getHitsPerDayData(period, allDate) {
            let res = await axios.get('/api/reports/hits-per-day/', {
                params: {
                    period: period,
                    allDate: allDate
                }
            });
            let hitsPerDayChart = echarts.init(document.getElementById('hitsPerDayChart'));
            const options = {
                textStyle: {
                    color: 'gray',
                    fontWeight: 500,
                    fontSize: 12,
                },
                xAxis: {
                    type: 'category',
                    data: res.data.response.labels,
                },
                yAxis: {
                    type: 'value',
                    boundaryGap: [0, '100%'],
                },
                series: [{
                    data: res.data.response.data,
                    type: 'bar',
                    name: 'Acessos',
                    showBackground: false,
                    backgroundStyle: {
                        color: 'rgba(220, 220, 220, 0.8)',
                    },
                    itemStyle: {
                        color: '#84a846',
                    },
                }],
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow',
                    },
                },
            };

            hitsPerDayChart.setOption(options);
            window.addEventListener('resize', function() {
                hitsPerDayChart.resize();
            });

            let tabEl = document.querySelector('#day-tab-card');
            tabEl.addEventListener('shown.bs.tab', function() {
                hitsPerDayChart.resize();
            });
        }

        $('.export-2').on('click', async (e) => {
            let res = await axios.get('/api/reports/hits-per-day/', {
                params: {
                    period: dateRange,
                    allDate: limitDate,
                },
            });
            const values = res.data.response.data;
            const labels = res.data.response.labels;
            const data = [];
            labels.map((item, index) => {
                data.push({
                    'date': item,
                    'value': values[index]
                });
            });

            const title = 'Data de pico por dia';
            const filename = toKebabCase(title);
            let text = title + '.\n\n';
            if (e.target.dataset.type === 'pdf') {
                const header = ['Data', 'Acessos'];
                pdfMakeWrapperDownloader(
                    title,
                    header,
                    data.map((item) => {
                        return [`${item.date}`, `${item.value ?? 0}`];
                    }),
                    filename
                )
            }
            if (e.target.dataset.type === 'csv') {
                text = 'data;acessos\n';
                data.map((item) => {
                    text += `${item.date};${item.value}\n`;
                });
                downloadFile(text, 'data:text/csv;charset=utf-8,', filename + '.csv');
            }
            if (e.target.dataset.type === 'xls') {
                text = '<table>' +
                    '  <thead>' +
                    '    <tr>' +
                    '      <th>Data</th>' +
                    '      <th>Acessos</th>' +
                    '    </tr>' +
                    '  </thead>' +
                    '  <tbody>';

                data.map((item) => {
                    text += `<tr>
                                                                      <td>${item.date}</td>
                                                                      <td>${item.value}</td>
                                                                    </tr>`;
                });
                text += '</tbody>' +
                    '</table>';

                downloadFile(text, 'data:application/vnd.ms-excel;charset=utf-8,', filename + '.xls');

            }
            if (e.target.dataset.type === 'txt') {
                data.map((item) => {
                    text += `Data: ${item.date} | Acessos: ${item.value}\n`;
                });
                downloadFile(text, 'data:text/plain;charset=utf-8,', filename + '.txt');
            }
        });
    </script>

    <script>
        async function getHitsPerDayWeekData(period, allDate) {
            let res = await axios.get('/api/reports/hits-per-day-week/', {
                params: {
                    period: period,
                    allDate: allDate
                }
            });
            let hitsPerDayWeekChart = echarts.init(document.getElementById('hitsPerDayWeekChart'));
            const options = {
                textStyle: {
                    color: 'gray',
                    fontWeight: 500,
                    fontSize: 12,
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: res.data.response.labels,
                },
                yAxis: {
                    type: 'value',
                    boundaryGap: [0, '100%'],
                },
                series: [{
                    data: res.data.response.data,
                    name: 'Acessos',
                    type: 'line',
                    symbol: 'none',
                    sampling: 'average',
                    itemStyle: {
                        color: '#84a846',
                    },
                    areaStyle: [],
                }],
                tooltip: {
                    trigger: 'axis'
                },

            };
            hitsPerDayWeekChart.setOption(options);
            window.addEventListener('resize', function() {
                hitsPerDayWeekChart.resize();
            });

            let tabEl = document.querySelector('#week-tab-card');
            tabEl.addEventListener('shown.bs.tab', function() {
                hitsPerDayWeekChart.resize();
            });
        }

        $('.export-3').on('click', async (e) => {
            let res = await axios.get('/api/reports/hits-per-day-week/', {
                params: {
                    period: dateRange,
                    allDate: limitDate,
                },
            });
            const values = res.response.data.data;
            const labels = res.response.data.labels;
            const data = [];
            labels.map((item, index) => {
                data.push({
                    'day': item,
                    'value': values[index]
                });
            });

            const title = 'Horário de pico por dia da semana';
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
<div class="col-xl-6 col-lg-6 col-md-12">
    <div class="xgrow-card card-dark mb-2" style="min-height: 488px">
        <div class="xgrow-card-header d-flex justify-content-between">
            <p class="xgrow-card-title">Pico de acessos</p>
        </div>
        <div class="xgrow-card-body">

            <ul class="activity-card-nav nav nav-tabs tabs-card-big flex-nowrap" id="myTabCard" role="tablist">
                <li class="activity-card-item nav-item tab-card-big">
                    <a class="activity-card-link nav-link active" id="hour-tab-card" data-bs-toggle="tab"
                        href="#hourcard" role="tab" aria-controls="hour-card" aria-selected="true">Por hora</a>
                </li>
                <li class="activity-card-item nav-item tab-card-big">
                    <a class="nav-link" id="day-tab-card" data-bs-toggle="tab" href="#daycard" role="tab"
                        aria-controls="day-card" aria-selected="false">Por dia</a>
                </li>
                <li class="activity-card-item nav-item tab-card-big">
                    <a class="nav-link" id="week-tab-card" data-bs-toggle="tab" href="#weekcard" role="tab"
                        aria-controls="week-card" aria-selected="false">Por semana</a>
                </li>
            </ul>

            <div class="tab-content" id="myTabCardContent">
                <div class="tab-pane fade show active" id="hourcard" role="tabpanel" aria-labelledby="hour-card">

                    <span id="loadingCardOne">
                        <div class="loading outer-loading">
                            <span class="inner-loading">
                                <i class="fa fa-spinner fa-pulse fa-2x fa-fw primary"
                                style="color: var(--contrast-green3);"></i> Carregando...
                            </span>
                        </div>
                    </span>

                    <div id="hitsPerHourChart" style="height: 400px"></div>
                    <div class="xgrow-card-footer">
                        <div class="d-flex align-items-center">
                            <p class="xgrow-medium-bold me-2">Exportar em</p>
                            <button class="xgrow-button export-button me-1 export" data-type="pdf"
                                title="Exportar em PDF">
                                <i class="fas fa-file-pdf" aria-hidden="true" data-type="pdf" style="color: red;"></i>
                            </button>
                            <button class="xgrow-button export-button me-1 export" data-type="csv"
                                title="Exportar em CSV">
                                <i class="fas fa-file-csv" aria-hidden="true" data-type="csv" style="color: blue;"></i>
                            </button>
                            <button class="xgrow-button export-button me-1 export" data-type="xls"
                                title="Exportar em XLSX">
                                <i class="fas fa-file-excel" aria-hidden="true" data-type="xls"
                                    style="color: green;"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="daycard" role="tabpanel" aria-labelledby="day-tab-card">
                    <div id="hitsPerDayChart" style="height: 400px"></div>
                    <div class="xgrow-card-footer">
                        <div class="d-flex align-items-center">
                            <p class="xgrow-medium-bold me-2">Exportar em</p>

                            <button class="xgrow-button export-button me-1 export-2" data-type="pdf"
                                title="Exportar em PDF">
                                <i class="fas fa-file-pdf" aria-hidden="true" data-type="pdf" style="color: red;"></i>
                            </button>
                            <button class="xgrow-button export-button me-1 export-2" data-type="csv"
                                title="Exportar em CSV">
                                <i class="fas fa-file-csv" aria-hidden="true" data-type="csv" style="color: blue;"></i>
                            </button>
                            <button class="xgrow-button export-button me-1 export-2" data-type="xls"
                                title="Exportar em XLSX">
                                <i class="fas fa-file-excel" aria-hidden="true" data-type="xls"
                                    style="color: green;"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="weekcard" role="tabpanel" aria-labelledby="week-tab-card">
                    <div id="hitsPerDayWeekChart" style="height: 400px"></div>
                    <div class="xgrow-card-footer">
                        <div class="d-flex align-items-center">
                            <p class="xgrow-medium-bold me-2">Exportar em</p>
                            <button class="xgrow-button export-button me-1 export-3" data-type="pdf"
                                title="Exportar em PDF">
                                <i class="fas fa-file-pdf" aria-hidden="true" data-type="pdf" style="color: red;"></i>
                            </button>
                            <button class="xgrow-button export-button me-1 export-3" data-type="csv"
                                title="Exportar em CSV">
                                <i class="fas fa-file-csv" aria-hidden="true" data-type="csv" style="color: blue;"></i>
                            </button>
                            <button class="xgrow-button export-button me-1 export-3" data-type="xls"
                                title="Exportar em XLSX">
                                <i class="fas fa-file-excel" aria-hidden="true" data-type="xls"
                                    style="color: green;"></i>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
            {{-- <div class="xgrow-card-footer"></div> --}}
        </div>
    </div>
</div>
