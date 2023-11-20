@push('after-scripts')
    <script type="text/javascript">
        const loadingCardOne = document.getElementById('loadingCardOne');
        const loadingCardOneTab2 = document.getElementById('loadingCardOneTab2');
        async function getMostCommentedContentData(period, allDate, order, divID) {
            loadingCardOne.style.display = 'block';
            loadingCardOneTab2.style.display = 'block';
            let res = await axios.get('/api/reports/count-commented-content/', {
                params: {
                    period: period,
                    allDate: allDate,
                    order: order,
                },
            });
            loadingCardOne.style.display = 'none';
            loadingCardOneTab2.style.display = 'none';

            let html = '';
            const resData = res.data.response.data;

            if (order == 'ASC') {
                resData.reverse(function (a, b) {
                    if (a.count_comments < b.count_comments) return -1;
                    if (a.count_comments > b.count_comments) return 1;
                    return 0;
                })
            } else {
                resData.sort(function (a, b) {
                    if (a.count_comments > b.count_comments) return -1;
                    if (a.count_comments < b.count_comments) return 1;
                    return 0;
                })
            }

            resData.map((item) => {
                const image = item.filename === null ? '/uploads/48ed8348-2564-4a22-a551-cb596b7dbd30.png' :
                    item.filename;
                html += `
                    <li class="d-flex align-items-center media">
                        <img width="48px" height="48px" src="${image}" alt="${item.title}" style="border-radius: 50%; object-fit: cover;">
                        <div class="media-body">
                            <p class="media-heading" style="color: var(--contrast-green3);">${item.title}</p>
                            <p>Comentários: ${item.count_comments}</p>
                        </div>
                    </li>`;
            });
            $(divID).html(resData.length > 0 ? html :
                '<li class="d-flex align-items-center media"><p>Não há dados para exibir.</p></li>');
        }

        $('.export').on('click', async (e) => {
            let res = await axios.get('/api/reports/count-commented-content/', {
                params: {
                    period: dateRange,
                    allDate: limitDate,
                    order: 'DESC',
                },
            });
            const data = res.data.response.data;
            const title = 'Top 20 conteúdos mais comentados';
            const filename = toKebabCase(title);
            let text = title + '.\n\n';
            data.sort(function (a, b) {
                if (a.count_comments > b.count_comments) return -1;
                if (a.count_comments < b.count_comments) return 1;
                return 0;
            });

            if (e.target.dataset.type === 'pdf') {
                const header = ['Conteúdo', 'Comentários'];
                pdfMakeWrapperDownloader(
                    title,
                    header,
                    data.map((item) => {
                        return [`${item.title}`, `${item.count_comments ?? 0}`];
                    }),
                    filename
                )
            }
            if (e.target.dataset.type === 'csv') {
                text = 'conteudo,comentarios\n';
                data.map((item) => {
                    text += `${item.title},${item.count_comments}\n`;
                });
                downloadFile(text, 'data:text/csv;charset=utf-8,', filename + '.csv');
            }
            if (e.target.dataset.type === 'xls') {
                text = '<table>\n' +
                    '  <thead>' +
                    '    <tr>' +
                    '      <th>Conteudo</th>' +
                    '      <th>Comentarios</th>\n' +
                    '    </tr>' +
                    '  </thead>' +
                    '  <tbody>';

                data.map((item) => {
                    text += `<tr>
                                <td>${item.title}</td>
                                <td>${item.count_comments}</td>
                            </tr>`;
                });
                text += '</tbody>' +
                    '</table>';

                downloadFile(text, 'data:application/vnd.ms-excel,', filename + '.xls');
            }
            if (e.target.dataset.type === 'txt') {
                data.map((item) => {
                    text += `Conteúdo: ${item.title} | Comentarios: ${item.count_comments}\n`;
                });
                downloadFile(text, 'data:text/plain;charset=utf-8,', filename + '.txt');
            }
        });

        $('.export-2').on('click', async (e) => {
            let res = await axios.get('/api/reports/count-commented-content/', {
                params: {
                    period: dateRange,
                    allDate: limitDate,
                    order: 'ASC',
                },
            });
            const data = res.data.response.data;
            const title = 'Top 20 conteúdos menos comentados';
            const filename = toKebabCase(title);
            let text = title + '.\n\n';
            data.reverse(function (a, b) {
                if (a.count_comments > b.count_comments) return -1;
                if (a.count_comments < b.count_comments) return 1;
                return 0;
            });

            if (e.target.dataset.type === 'pdf') {
                const header = ['Conteúdo', 'Comentários'];
                pdfMakeWrapperDownloader(
                    title,
                    header,
                    data.map((item) => {
                        return [`${item.title}`, `${item.count_comments ?? 0}`];
                    }),
                    filename
                )
            }
            if (e.target.dataset.type === 'csv') {
                text = 'conteudo,comentarios\n';
                data.map((item) => {
                    text += `${item.title},${item.count_comments}\n`;
                });
                downloadFile(text, 'data:text/csv;charset=utf-8,', filename + '.csv');
            }
            if (e.target.dataset.type === 'xls') {
                text = '<table>\n' +
                    '  <thead>' +
                    '    <tr>' +
                    '      <th>Conteudo</th>' +
                    '      <th>Comentarios</th>\n' +
                    '    </tr>' +
                    '  </thead>' +
                    '  <tbody>';

                data.map((item) => {
                    text += `<tr>
                                <td>${item.title}</td>
                                <td>${item.count_comments}</td>
                            </tr>`;
                });
                text += '</tbody>' +
                    '</table>';

                downloadFile(text, 'data:application/vnd.ms-excel,', filename + '.xls');
            }
            if (e.target.dataset.type === 'txt') {
                data.map((item) => {
                    text += `Conteúdo: ${item.title} | Comentarios: ${item.count_comments}\n`;
                });
                downloadFile(text, 'data:text/plain;charset=utf-8,', filename + '.txt');
            }
        });

    </script>
@endpush


<div class="col-xl-6 col-lg-6 col-md-12">
    <div class="xgrow-card card-dark my-3" style="height: 566px">
        <div class="xgrow-card-header">
            <div class="d-flex flex-column">
                <p class="xgrow-card-title">Top 20: Engajamento em comentários</p>
            </div>
        </div>

        <ul class="activity-card-nav nav nav-tabs tabs-card-big" id="myTabCard" role="tablist">
            <li class="activity-card-item nav-item tab-card-big">
                <a class="activity-card-link nav-link active" id="most-commented-tab-card" data-bs-toggle="tab"
                    href="#most-commentedcard" role="tab" aria-controls="most-commented-card" aria-selected="true">Mais
                    comentados</a>
            </li>
            <li class="activity-card-item nav-item tab-card-big">
                <a class="nav-link" id="less-commented-tab-card" data-bs-toggle="tab" href="#less-commentedcard"
                    role="tab" aria-controls="less-commented-card" aria-selected="false">Menos comentados</a>
            </li>
        </ul>

        <div class="tab-content" id="myTabCardContent">
            <div class="tab-pane fade active show" id="most-commentedcard" role="tabpanel"
                aria-labelledby="most-commented-card">
                <span id="loadingCardOne">
                    <div class="loading outer-loading">
                        <span class="inner-loading">
                            <i class="fa fa-spinner fa-pulse fa-2x fa-fw primary"
                            style="color: var(--contrast-green3);"></i> Carregando...
                        </span>
                    </div>
                </span>
                <ul class="media-list" id="most-commented-table-inner"></ul>
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
                            <i class="fas fa-file-excel" aria-hidden="true" data-type="xls" style="color: green;"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="less-commentedcard" role="tabpanel"
                aria-labelledby="less-commented-tab-card">
                <span id="loadingCardOneTab2">
                    <div class="loading outer-loading">
                        <span class="inner-loading">
                            <i class="fa fa-spinner fa-pulse fa-2x fa-fw primary"
                            style="color: var(--contrast-green3);"></i> Carregando...
                        </span>
                    </div>
                </span>
                <ul class="media-list" id="least-commented-table-inner"></ul>

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
                            <i class="fas fa-file-excel" aria-hidden="true" data-type="xls" style="color: green;"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
