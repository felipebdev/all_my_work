@push('after-scripts')
    <script>
        async function getAgeGenderData(period, allDate) {
            const loadingCardTwo = document.getElementById('loadingCardTwo');
            loadingCardTwo.style.display = 'block';
            let res = await axios.get('/api/reports/age-gender/', {
                params: {
                    period: period,
                    allDate: allDate
                }
            });
            loadingCardTwo.style.display = 'none';
            let ageGenderChart = echarts.init(document.getElementById('ageGenderChart'));
            const options = {
                textStyle: {
                    color: 'gray',
                    fontWeight: 500,
                    fontSize: 12,
                },
                xAxis: {
                    type: 'value',
                    boundaryGap: [0, '100%'],
                },
                yAxis: {
                    type: 'category',
                    axisTick: {
                        show: false
                    },
                    data: res.data.response.labels,
                },
                series: [{
                        name: 'Feminino',
                        type: 'bar',
                        barGap: 0,
                        data: res.data.response.dataFemale,
                    },
                    {
                        name: 'Masculino',
                        type: 'bar',
                        data: res.data.response.dataMale,
                    },
                    {
                        name: 'Não Informado',
                        type: 'bar',
                        data: res.data.response.dataUndefined,
                    },
                ],
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow',
                    },
                },
                color: ['#e5323e', '#84a846', '#ff9c00'],
                legend: {
                    data: ['Feminino', 'Masculino', 'Não Informado'],
                    textStyle: {
                        color: 'gray',
                        fontWeight: 500,
                        fontSize: 12,
                    },
                },
            };

            ageGenderChart.setOption(options);
            window.addEventListener('resize', function() {
                ageGenderChart.resize();
            });

            let tabEl = document.querySelector('#age-gender-tab-card');
            tabEl.addEventListener('shown.bs.tab', function() {
                ageGenderChart.resize();
            });
        }

        $('.export-4').on('click', async (e) => {
            let res = await axios.get('/api/reports/age-gender/', {
                params: {
                    period: dateRange,
                    allDate: limitDate,
                },
            });
            const labels = res.data.response.labels;
            const female = res.data.response.dataFemale;
            const male = res.data.response.dataMale;
            const data = [];
            labels.map((item, index) => {
                data.push({
                    'age': item,
                    'female': female[index],
                    'male': male[index]
                });
            });

            const title = 'Quantidade de acessos por Idade/Gênero';
            const filename = toKebabCase(title);
            let text = title + '.\n\n';
            if (e.target.dataset.type === 'pdf') {
                const header = ['Idade', 'Feminino', 'Masculino'];
                pdfMakeWrapperDownloader(
                    title,
                    header,
                    data.map((item) => {
                        return [`${item.age}`, `${item.female}`, `${item.male}`];
                    }),
                    filename
                )
            }
            if (e.target.dataset.type === 'csv') {
                text = 'idade;feminino;masculino\n';
                data.map((item) => {
                    text += `${item.age};${item.female};${item.male}\n`;
                });
                downloadFile(text, 'data:text/csv;charset=utf-8,', filename + '.csv');
            }
            if (e.target.dataset.type === 'xls') {
                text = '<table>' +
                    '  <thead>' +
                    '    <tr>' +
                    '      <th>Idade</th>' +
                    '      <th>Feminino</th>' +
                    '      <th>Masculino</th>' +
                    '    </tr>' +
                    '  </thead>' +
                    '  <tbody>';

                data.map((item) => {
                    text += `<tr>
                                          <td>${item.age}</td>
                                          <td>${item.female}</td>
                                          <td>${item.male}</td>
                                        </tr>`;
                });
                text += '</tbody>' +
                    '</table>';

                downloadFile(text, 'data:application/vnd.ms-excel;charset=utf-8,', filename + '.xls');

            }
            if (e.target.dataset.type === 'txt') {
                data.map((item) => {
                    text += `Idade: ${item.age} | Feminino: ${item.female} | Masculino: ${item.male}\n`;
                });
                downloadFile(text, 'data:text/plain;charset=utf-8,', filename + '.txt');
            }
        });

    </script>
    <script>
        async function getGenderData(period, allDate) {
            let res = await axios.get('/api/reports/gender/', {
                params: {
                    period: period,
                    allDate: allDate
                }
            });
            let genderChart = echarts.init(document.getElementById('genderChart'));
            const options = {
                textStyle: {
                    color: 'gray',
                    fontWeight: 500,
                    fontSize: 12,
                },
                legend: {
                    data: ['Feminino', 'Masculino', 'Não Informado'],
                    textStyle: {
                        color: 'gray',
                        fontWeight: 500,
                        fontSize: 12,
                    },
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true,
                },
                xAxis: {
                    type: 'category',
                    data: [''],
                },
                yAxis: {
                    type: 'value',
                },
                series: [{
                    name: 'Feminino',
                    type: 'bar',
                    data: [res.data.response.feminino],
                    color: '#e5323e',
                    label: {
                        show: true,
                    },
                }, {
                    name: 'Masculino',
                    type: 'bar',
                    data: [res.data.response.masculino],
                    color: '#84a846',
                    label: {
                        show: true,
                    },
                }, {
                    name: 'Não Informado',
                    type: 'bar',
                    data: [res.data.response.naoInformado],
                    color: '#ff9c00',
                    label: {
                        show: true,
                    },
                },],
                tooltip: {},
            };

            genderChart.setOption(options);
            window.addEventListener('resize', function() {
                genderChart.resize();
            });

            let tabEl = document.querySelector('#only-gender-tab-card');
            tabEl.addEventListener('shown.bs.tab', function() {
                genderChart.resize();
            });
        }

        $('.export-5').on('click', async (e) => {
            let res = await axios.get('/api/reports/gender/', {
                params: {
                    period: dateRange,
                    allDate: limitDate,
                },
            });
            const labels = res.data.response.labels;
            const female = res.data.response.feminino;
            const male = res.data.response.masculino;

            const data = [];
            data.push({
                'gender': labels[0],
                'value': female
            });
            data.push({
                'gender': labels[1],
                'value': male
            });

            const title = 'Quantidade de acessos por Gênero';
            const filename = toKebabCase(title);
            let text = title + '.\n\n';
            if (e.target.dataset.type === 'pdf') {
                const header = ['Gênero', 'Acessos'];
                pdfMakeWrapperDownloader(
                    title,
                    header,
                    data.map((item) => {
                        return [`${item.gender}`, `${item.value ? item.value : 0}`];
                    }),
                    filename
                )
            }
            if (e.target.dataset.type === 'csv') {
                text = 'gênero;acessos\n';
                data.map((item) => {
                    text += `${item.gender};${item.value}\n`;
                });
                downloadFile(text, 'data:text/csv;charset=utf-8,', filename + '.csv');
            }
            if (e.target.dataset.type === 'xls') {
                text = '<table>' +
                    '  <thead>' +
                    '    <tr>' +
                    '      <th>Gênero</th>' +
                    '      <th>Acessos</th>' +
                    '    </tr>' +
                    '  </thead>' +
                    '  <tbody>';

                data.map((item) => {
                    text += `<tr>
                                          <td>${item.gender}</td>
                                          <td>${item.value}</td>
                                        </tr>`;
                });
                text += '</tbody>' +
                    '</table>';

                downloadFile(text, 'data:application/vnd.ms-excel;charset=utf-8,', filename + '.xls');

            }
            if (e.target.dataset.type === 'txt') {
                data.map((item) => {
                    text += `Gênero: ${item.gender} | Acessos: ${item.value}\n`;
                });
                downloadFile(text, 'data:text/plain;charset=utf-8,', filename + '.txt');
            }
        });

    </script>
@endpush

<div class="col-xl-6 col-lg-6 col-md-12">
    <div class="xgrow-card card-dark mb-2" style="min-height: 488px">
        <div class="xgrow-card-header d-flex justify-content-between">
            <p class="xgrow-card-title">Por idade/gênero</p>
        </div>

        <ul class="activity-card-nav nav nav-tabs tabs-card-big flex-nowrap" id="myTabCard" role="tablist">
            <li class="activity-card-item nav-item tab-card-big">
                <a class="activity-card-link nav-link active" id="age-gender-tab-card" data-bs-toggle="tab"
                    href="#age-gendercard" role="tab" aria-controls="age-gender-card"
                    aria-selected="true">Idade/Gênero</a>
            </li>
            <li class="activity-card-item nav-item tab-card-big">
                <a class="nav-link" id="only-gender-tab-card" data-bs-toggle="tab" href="#only-gendercard" role="tab"
                    aria-controls="only-gender-card" aria-selected="false">Só Gênero</a>
            </li>
        </ul>

        <div class="tab-content" id="myTabCardContent">
            <div class="tab-pane fade show active" id="age-gendercard" role="tabpanel" aria-labelledby="age-gender-card">

                <span id="loadingCardTwo">
                    <div class="loading outer-loading">
                        <span class="inner-loading">
                            <i class="fa fa-spinner fa-pulse fa-2x fa-fw primary"
                            style="color: var(--contrast-green3);"></i> Carregando...
                        </span>
                    </div>
                </span>

                <div id="ageGenderChart" style="height: 400px"></div>

                <div class="xgrow-card-footer">
                    <div class="d-flex align-items-center">
                        <p class="xgrow-medium-bold me-2">Exportar em</p>
                        <button class="xgrow-button export-button me-1 export-4" data-type="pdf"
                            title="Exportar em PDF">
                            <i class="fas fa-file-pdf" aria-hidden="true" data-type="pdf" style="color: red;"></i>
                        </button>
                        <button class="xgrow-button export-button me-1 export-4" data-type="csv"
                            title="Exportar em CSV">
                            <i class="fas fa-file-csv" aria-hidden="true" data-type="csv" style="color: blue;"></i>
                        </button>
                        <button class="xgrow-button export-button me-1 export-4" data-type="xls"
                            title="Exportar em XLSX">
                            <i class="fas fa-file-excel" aria-hidden="true" data-type="xls" style="color: green;"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="only-gendercard" role="tabpanel" aria-labelledby="only-gender-tab-card">
                <div id="genderChart" style="height: 400px"></div>

                <div class="xgrow-card-footer">
                    <div class="d-flex align-items-center">
                        <p class="xgrow-medium-bold me-2">Exportar em</p>
                        <button class="xgrow-button export-button me-1 export-5" data-type="pdf"
                            title="Exportar em PDF">
                            <i class="fas fa-file-pdf" aria-hidden="true" data-type="pdf" style="color: red;"></i>
                        </button>
                        <button class="xgrow-button export-button me-1 export-5" data-type="csv"
                            title="Exportar em CSV">
                            <i class="fas fa-file-csv" aria-hidden="true" data-type="csv" style="color: blue;"></i>
                        </button>
                        <button class="xgrow-button export-button me-1 export-5" data-type="xls"
                            title="Exportar em XLSX">
                            <i class="fas fa-file-excel" aria-hidden="true" data-type="xls" style="color: green;"></i>
                        </button>
                    </div>
                </div>
            </div>
            {{-- <div class="xgrow-card-footer"></div> --}}
        </div>
    </div>
</div>
