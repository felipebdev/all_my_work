@push('after-scripts')
    <script type="text/javascript">
        async function getMostCommentedContentByAuthorData(period, allDate) {
            let res = await axios.get('/api/reports/most-accessed-content-by-author/', {
                params: {
                    period: period,
                    allDate: allDate,
                },
            });
            let MostCommentedContentByAuthorChart = echarts.init(document.getElementById(
                'MostCommentedContentByAuthorChart'));
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
                    data: res.data.labels,
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
                    data: res.data.data,
                    emphasis: {
                        itemStyle: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)',
                        },
                    },
                    label: {
                        position: 'inner',
                        formatter: '{d}%',
                    },
                }, ],
            };

            MostCommentedContentByAuthorChart.setOption(options);
            window.addEventListener('resize', function() {
                MostCommentedContentByAuthorChart.resize();
            });
        }

        $('.export-6').on('click', async (e) => {
            let res = await axios.get('/api/reports/most-accessed-content-by-author/', {
                params: {
                    period: dateRange,
                    allDate: limitDate,
                },
            });
            const data = res.data.data;
            const title = 'Top conteúdos mais comentados por autor';
            const filename = toKebabCase(title);
            let text = title + '.\n\n';
            if (e.target.dataset.type === 'pdf') {
                alert('Em implementação. Aguarde!');
            }
            if (e.target.dataset.type === 'csv') {
                text = 'conteudo,visualizacoes\n';
                data.map((item) => {
                    text += `${item.name},${item.value}\n`;
                });
                downloadFile(text, 'data:text/csv;charset=utf-8,', filename + '.csv');
            }
            if (e.target.dataset.type === 'xls') {
                text = '<table>' +
                    '  <thead>' +
                    '    <tr>' +
                    '      <th>Autor/Conteudo</th>' +
                    '      <th>Visualizacoes</th>' +
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

                downloadFile(text, 'data:application/vnd.ms-excel,', filename + '.xls');
            }
            if (e.target.dataset.type === 'txt') {
                data.map((item) => {
                    text += `${item.name} | Visualizações: ${item.value}\n`;
                });
                downloadFile(text, 'data:text/plain;charset=utf-8,', filename + '.txt');
            }
        });

    </script>
@endpush

<div class="col-xl-12 mt-1">

    <div class="xgrow-card card-dark mb-3">
        <div class="xgrow-card-header">
            <p class="xgrow-card-title">Top conteúdos mais visualizados por autor</p>
        </div>

        <div class="xgrow-card-body">
            <div id="MostCommentedContentByAuthorChart" style="height: 400px"></div>

            <div class="xgrow-card-footer">
                <div class="d-flex align-items-center">
                    <p class="xgrow-medium-bold me-2">Exportar em</p>
                    <button class="xgrow-button export-button me-1 export-6" data-type="pdf">
                        <i class="fas fa-file-pdf" aria-hidden="true" data-type="pdf"></i>
                    </button>
                    <button class="xgrow-button export-button me-1 export-6" data-type="xls">
                        <i class="fas fa-file-csv" aria-hidden="true" data-type="xls"></i>
                    </button>
                    <button class="xgrow-button export-button me-1 export-6" data-type="csv">
                        <i class="fas fa-file-excel" aria-hidden="true" data-type="csv"></i>
                    </button>
                </div>
            </div>

        </div>

        <div class="xgrow-card-footer"></div>
    </div>
</div>
