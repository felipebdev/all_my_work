@push('after-scripts')
    <script type="text/javascript">
        const loadingCardFour = document.getElementById('loadingCardFour');
        async function getTotalViewedContentByAuthorData(period, allDate) {
            loadingCardFour.style.display = 'block';
            let res = await axios.get('/api/reports/total-viewed-content-by-author/', {
                params: {
                    period: period,
                    allDate: allDate,
                },
            });
            loadingCardFour.style.display = 'none';

            const info = document.getElementById('totalViewedContentByAuthorInfo');
            if (res.data.response.data.length < 1) {
                info.innerText = 'Não há dados para exibir no período selecionado.';
            } else {
                info.innerText = '';
            }

            let totalViewedContentByAuthorChart = echarts.init(document.getElementById(
                'totalViewedContentByAuthorChart'));
            const options = {
                textStyle: {
                    color: 'gray',
                    fontWeight: 500,
                    fontSize: 12,
                },
                tooltip: {
                    trigger: 'item',
                    formatter: '{a}: {c} ({d}%)',
                },
                legend: {
                    orient: 'vertical',
                    left: 'left',
                    data: res.data.response.labels,
                    textStyle: {
                        color: 'gray',
                        fontWeight: 500,
                        fontSize: 12,
                    },
                },
                series: [{
                    name: 'Visualizações',
                    type: 'pie',
                    radius: '55%',
                    center: ['50%', '60%'],
                    data: res.data.response.data,
                    emphasis: {
                        itemStyle: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)',
                        },
                    },
                    label: {
                        formatter: '{d}%',
                    },
                    labelLine: {
                        show: true,
                    },
                }, ],
            };

            totalViewedContentByAuthorChart.setOption(options);
            window.addEventListener('resize', function() {
                totalViewedContentByAuthorChart.resize();
            });
        }

        $('.export-6').on('click', async (e) => {
            let res = await axios.get('/api/reports/total-viewed-content-by-author/', {
                params: {
                    period: dateRange,
                    allDate: limitDate,
                },
            });
            const data = res.data.response.data;
            const title = 'Top conteúdos mais comentados por autor';
            const filename = toKebabCase(title);
            let text = title + '.\n\n';
            const total = data.reduce((start, data) => start + data.value, 0);

            if (e.target.dataset.type === 'pdf') {
                const header = ['Autor', 'Visualizações', 'Porcentagem'];
                pdfMakeWrapperDownloader(
                    title,
                    header,
                    data.map((item) => {
                        return [`${item.name}`, `${item.value ?? 0}`,
                            `${((item.value / total) * 100).toFixed(2)}`
                        ];
                    }),
                    filename
                )
            }
            if (e.target.dataset.type === 'csv') {
                text = 'autor,visualizacoes,porcentagem\n';
                data.map((item) => {
                    text += `${item.name},${item.value},${((item.value / total) * 100).toFixed(2)}%\n`;
                });
                downloadFile(text, 'data:text/csv;charset=utf-8,', filename + '.csv');
            }
            if (e.target.dataset.type === 'xls') {
                text = '<table>' +
                    '  <thead>' +
                    '    <tr>' +
                    '      <th>Autor</th>' +
                    '      <th>Visualizacoes</th>' +
                    '      <th>Porcentagem</th>' +
                    '    </tr>' +
                    '  </thead>' +
                    '  <tbody>';

                data.map((item) => {
                    text += `<tr>
                                      <td>${item.name}</td>
                                      <td>${item.value}</td>
                                      <td>${((item.value / total) * 100).toFixed(2)}%</td>
                                    </tr>`;
                });
                text += '</tbody>' +
                    '</table>';

                downloadFile(text, 'data:application/vnd.ms-excel,', filename + '.xls');
            }
            if (e.target.dataset.type === 'txt') {
                data.map((item) => {
                    text +=
                        `${item.name} | Visualizações: ${item.value} | Porcentagem: ${((item.value / total) * 100).toFixed(2)}%\n`;
                });
                downloadFile(text, 'data:text/plain;charset=utf-8,', filename + '.txt');
            }
        });

    </script>
@endpush

<div class="col-xl-6 my-3">
    <div class="xgrow-card card-dark mb-3" style="height: 566px">
        <div class="xgrow-card-header">
            <p class="xgrow-card-title">Conteúdos mais populares por autor</p>
        </div>
        <div class="xgrow-card-body">
            <span id="totalViewedContentByAuthorInfo"></span>
            <span id="loadingCardFour">
                <div class="loading outer-loading">
                    <span class="inner-loading">
                        <i class="fa fa-spinner fa-pulse fa-2x fa-fw primary"
                        style="color: var(--contrast-green3);"></i> Carregando...
                    </span>
                </div>
            </span>
            <div id="totalViewedContentByAuthorChart" style="height: 400px"></div>
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
</div>
