@push('after-scripts')
    <script>
        async function getHitsByLocationData(period, allDate) {
            const loadingCardThree = document.getElementById('loadingCardThree');
            loadingCardThree.children[0].style.left = '45%';
            loadingCardThree.style.display = 'block';
            let res = await axios.get('/api/reports/hits-by-location/', {
                params: {
                    period: period,
                    allDate: allDate
                }
            });
            loadingCardThree.style.display = 'none';
            let hitsByLocationChart = echarts.init(document.getElementById('hitsByLocationChart'));
            const options = {
                textStyle: {
                    color: 'gray',
                    fontWeight: 500,
                    fontSize: 12,
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow',
                    },
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true,
                },
                xAxis: {
                    type: 'value',
                    boundaryGap: [0, 0.01],
                },
                yAxis: {
                    type: 'category',
                    data: res.data.response.labels,
                },
                series: [{
                    name: 'Acessos',
                    type: 'bar',
                    data: res.data.response.data,
                    color: ['#84a846'],
                }, ],
            };
            hitsByLocationChart.setOption(options);
            window.addEventListener('resize', function() {
                hitsByLocationChart.resize();
            });
        }

        $('.export-6').on('click', async (e) => {
            let res = await axios.get('/api/reports/hits-by-location/', {
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
                    'location': item,
                    'value': values[index]
                });
            });

            const title = 'Quantidade de acessos por localidade';
            const filename = toKebabCase(title);
            let text = title + '.\n\n';
            if (e.target.dataset.type === 'pdf') {
                const header = ['Localidade', 'Acessos'];
                pdfMakeWrapperDownloader(
                    title,
                    header,
                    data.map((item) => {
                        return [`${item.location ?? 'Indefinido' }`, `${item.value ?? 0}`];
                    }),
                    filename
                )
            }
            if (e.target.dataset.type === 'csv') {
                text = 'localidade;acessos\n';
                data.map((item) => {
                    text += `${item.location};${item.value}\n`;
                });
                downloadFile(text, 'data:text/csv;charset=utf-8,', filename + '.csv');
            }
            if (e.target.dataset.type === 'xls') {
                text = '<table>' +
                    '  <thead>' +
                    '    <tr>' +
                    '      <th>Localidade</th>' +
                    '      <th>Acessos</th>' +
                    '    </tr>' +
                    '  </thead>' +
                    '  <tbody>';

                data.map((item) => {
                    text += `<tr>
                                      <td>${item.location}</td>
                                      <td>${item.value}</td>
                                    </tr>`;
                });
                text += '</tbody>' +
                    '</table>';

                downloadFile(text, 'data:application/vnd.ms-excel;charset=utf-8,', filename + '.xls');

            }
            if (e.target.dataset.type === 'txt') {
                data.map((item) => {
                    text += `Localidade: ${item.location} | Acessos: ${item.value}\n`;
                });
                downloadFile(text, 'data:text/plain;charset=utf-8,', filename + '.txt');
            }
        });

    </script>
@endpush

<div class="col-xl-12 col-lg-12 col-md-12">
    <div class="xgrow-card card-dark mb-2">
        <div class="xgrow-card-header">
            <div class="d-flex flex-column">
                <p class="xgrow-card-title">Por localidade</p>
            </div>
        </div>

        <div class="xgrow-card-body">

            <span id="loadingCardThree">
                <div class="loading outer-loading">
                    <span class="inner-loading">
                        <i class="fa fa-spinner fa-pulse fa-2x fa-fw primary"
                        style="color: var(--contrast-green3);"></i> Carregando...
                    </span>
                </div>
            </span>

            <div id="hitsByLocationChart" style="height: 400px"></div>
        </div>

        <div class="xgrow-card-footer">
            <div class="d-flex align-items-center">
                <p class="xgrow-medium-bold me-2">Exportar em</p>
                <button class="xgrow-button export-button me-1 export-6" data-type="pdf" title="Exportar em PDF">
                    <i class="fas fa-file-pdf" aria-hidden="true" data-type="pdf" style="color: red;"></i>
                </button>
                <button class="xgrow-button export-button me-1 export-6" data-type="csv" title="Exportar em CSV">
                    <i class="fas fa-file-csv" aria-hidden="true" data-type="csv" style="color: blue;"></i>
                </button>
                <button class="xgrow-button export-button me-1 export-6" data-type="xls" title="Exportar em XLSX">
                    <i class="fas fa-file-excel" aria-hidden="true" data-type="xls" style="color: green;"></i>
                </button>
            </div>
        </div>
    </div>
</div>
