@push('after-scripts')
    <script type="text/javascript">
        const loadingCardThree = document.getElementById('loadingCardThree');
        async function getMostAccessedSectionData(period, allDate) {
            loadingCardThree.style.display = 'block';
            let res = await axios.get('/api/reports/most-accessed-section/', {
                params: {
                    period: period,
                    allDate: allDate,
                },
            });
            loadingCardThree.style.display = 'none';

            const info = document.getElementById('mostAccessedSectionInfo');
            if (res.data.response.data.length < 1) {
                info.innerText = 'Não há dados para exibir no período selecionado.';
            } else {
                info.innerText = '';
            }
            let mostAccessedSectionChart = echarts.init(document.getElementById('mostAccessedSectionChart'));
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
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true,
                },
                series: [{
                    name: 'Acessos',
                    type: 'bar',
                    data: res.data.response.data,
                    barWidth: 50,
                }, ],
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow',
                    },
                },
                color: ['#84a846'],
            };

            mostAccessedSectionChart.setOption(options);
            window.addEventListener('resize', function() {
                mostAccessedSectionChart.resize();
            });

        }

        $('.export-5').on('click', async (e) => {
            let res = await axios.get('/api/reports/most-accessed-section/', {
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
                    'name': item,
                    'value': values[index]
                });
            });

            const title = 'Seções mais acessadas';
            const filename = toKebabCase(title);
            let text = title + '.\n\n';

            data.sort(function (a, b) {
                if (a.value > b.value) return -1;
                if (a.value < b.value) return 1;
                return 0;
            })

            if (e.target.dataset.type === 'pdf') {
                const header = ['Seção', 'Acessos'];
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
                text = 'seção,acessos\n';
                data.map((item) => {
                    text += `${item.name},${item.value}\n`;
                });
                downloadFile(text, 'data:text/csv;charset=utf-8,', filename + '.csv');
            }
            if (e.target.dataset.type === 'xls') {
                text = '<table>' +
                    '  <thead>' +
                    '    <tr>' +
                    '      <th>Seção</th>' +
                    '      <th>Acessos</th>' +
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
                    text += `Seção: ${item.name} | Acessos: ${item.value}\n`;
                });
                downloadFile(text, 'data:text/plain;charset=utf-8,', filename + '.txt');
            }
        });

    </script>
@endpush


<div class="col-xl-6 my-3">
    <div class="xgrow-card card-dark mb-3" style="height: 566px">
        <div class="xgrow-card-header">
            <p class="xgrow-card-title">Seções mais acessadas</p>
        </div>
        <div class="xgrow-card-body">
            <span id="mostAccessedSectionInfo"></span>
            <span id="loadingCardThree">
                <div class="loading outer-loading">
                    <span class="inner-loading">
                        <i class="fa fa-spinner fa-pulse fa-2x fa-fw primary"
                        style="color: var(--contrast-green3);"></i> Carregando...
                    </span>
                </div>
            </span>
            <div id="mostAccessedSectionChart" style="height: 400px"></div>
            <div class="xgrow-card-footer">
                <div class="d-flex align-items-center">
                    <p class="xgrow-medium-bold me-2">Exportar em</p>
                    <button class="xgrow-button export-button me-1 export-5" data-type="pdf" title="Exportar em PDF">
                        <i class="fas fa-file-pdf" aria-hidden="true" data-type="pdf" style="color: red;"></i>
                    </button>
                    <button class="xgrow-button export-button me-1 export-5" data-type="csv" title="Exportar em CSV">
                        <i class="fas fa-file-csv" aria-hidden="true" data-type="csv" style="color: blue;"></i>
                    </button>
                    <button class="xgrow-button export-button me-1 export-5" data-type="xls" title="Exportar em XLSX">
                        <i class="fas fa-file-excel" aria-hidden="true" data-type="xls" style="color: green;"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
