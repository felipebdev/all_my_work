@push('after-scripts')
    <script type="text/javascript">
        const loadingCardTwo = document.getElementById('loadingCardTwo');
        async function getMostAccessedContentData(period, allDate) {
            loadingCardTwo.style.display = 'block';
            let res = await axios.get('/api/reports/most-accessed-content/', {
                params: {
                    period: period,
                    allDate: allDate,
                },
            });
            loadingCardTwo.style.display = 'none';
            let html = '';
            const resData = res.data.response.data;
            resData.sort(function (a, b) {
                if (a.amount > b.amount) return -1;
                if (a.amount < b.amount) return 1;
                return 0;
            })

            resData.map((item) => {
                const image = item.filename === null ? '/xgrow-vendor/assets/img/big-file.png' :
                    item.filename;
                html += `
                    <li class="d-flex align-items-center media">
                        <img width="48px" height="48px" src="${image}" alt="${item.title}" style="border-radius: 50%; object-fit: cover;" onerror="this.src='/xgrow-vendor/assets/img/big-file.png';"/>
                        <div class="media-body">
                            <p class="media-heading" style="color: var(--contrast-green3);">${item.title}</p>
                            <p>Visualizações: ${item.amount}</p>
                        </div>
                    </li>
                `;
            });
            $('#most-accessed-table-inner').html(resData.length > 0 ? html :
                '<li class="d-flex align-items-center media"><p>Não há dados para exibir.</p></li>');
        }

        $('.export-3').on('click', async (e) => {
            let res = await axios.get('/api/reports/most-accessed-content/', {
                params: {
                    period: dateRange,
                    allDate: limitDate,
                },
            });
            const data = res.data.response.data;
            const title = 'Top 20 conteúdos mais acessados';
            const filename = toKebabCase(title);
            let text = title + '.\n\n';

            data.sort(function (a, b) {
                if (a.amount > b.amount) return -1;
                if (a.amount < b.amount) return 1;
                return 0;
            });

            if (e.target.dataset.type === 'pdf') {
                const header = ['Conteúdo', 'Visualizações'];
                pdfMakeWrapperDownloader(
                    title,
                    header,
                    data.map((item) => {
                        return [`${item.title}`, `${item.amount ?? 0}`];
                    }),
                    filename
                )
            }
            if (e.target.dataset.type === 'csv') {
                text = 'conteudo,visualizacoes\n';
                data.map((item) => {
                    text += `${item.title},${item.amount}\n`;
                });
                downloadFile(text, 'data:text/csv;charset=utf-8,', filename + '.csv');
            }
            if (e.target.dataset.type === 'xls') {
                text = '<table>\n' +
                    '  <thead>' +
                    '    <tr>' +
                    '      <th>Conteúdo</th>' +
                    '      <th>Visualizações</th>\n' +
                    '    </tr>' +
                    '  </thead>' +
                    '  <tbody>';

                data.map((item) => {
                    text += `<tr>
                                <td>${item.title}</td>
                                <td>${item.amount}</td>
                            </tr>`;
                });
                text += '</tbody>' +
                    '</table>';

                downloadFile(text, 'data:application/vnd.ms-excel;charset=utf-8,', filename + '.xls');
            }
            if (e.target.dataset.type === 'txt') {
                data.map((item) => {
                    text += `Conteúdo: ${item.title} | Visualizações: ${item.amount}\n`;
                });
                downloadFile(text, 'data:text/plain;charset=utf-8,', filename + '.txt');
            }
        });
    </script>

    <script type="text/javascript">
        const loadingCardTwoTab2 = document.getElementById('loadingCardTwoTab2');
        async function getMostLikedContentData(period, allDate) {
            loadingCardTwoTab2.style.display = 'block';
            let res = await axios.get('/api/reports/most-liked-content/', {
                params: {
                    period: period,
                    allDate: allDate,
                },
            });
            loadingCardTwoTab2.style.display = 'none';
            let html = '';
            const resData = res.data.response.data;
            resData.sort(function (a, b) {
                if (a.amount > b.amount) return -1;
                if (a.amount < b.amount) return 1;
                return 0;
            })

            resData.map((item) => {
                const image = item.filename === null ? '/uploads/48ed8348-2564-4a22-a551-cb596b7dbd30.png' :
                    item.filename;
                html += `
                    <li class="d-flex align-items-center media">
                        <img width="48px" height="48px" src="${image}" alt="${item.title}" onerror="this.src='/xgrow-vendor/assets/img/big-file.png';" />
                        <div class="media-body">
                            <p class="media-heading" style="color: var(--contrast-green3);">${item.title}</p>
                            <p>Curtidas: ${item.likes}</p>
                        </div>
                    </li>
                `;
            });

            $('#most-liked-table-inner').html(resData.length > 0 ? html :
                '<li class="d-flex align-items-center media"><p>Não há dados para exibir.</p></li>');
        }

        $('.export-4').on('click', async (e) => {
            let res = await axios.get('/api/reports/most-liked-content/', {
                params: {
                    period: dateRange,
                    allDate: limitDate,
                },
            });
            const data = res.data.response.data;
            const title = 'Top 20 conteúdos mais curtidos';
            const filename = toKebabCase(title);
            let text = title + '.\n\n';
            data.sort(function (a, b) {
                if (a.amount > b.amount) return -1;
                if (a.amount < b.amount) return 1;
                return 0;
            })

            if (e.target.dataset.type === 'pdf') {
                const header = ['Conteúdo', 'Curtidas'];
                pdfMakeWrapperDownloader(
                    title,
                    header,
                    data.map((item) => {
                        return [`${item.title}`, `${item.amount ?? 0}`];
                    }),
                    filename
                )
            }
            if (e.target.dataset.type === 'csv') {
                text = 'conteudo,curtidas\n';
                data.map((item) => {
                    text += `${item.title},${item.likes}\n`;
                });
                downloadFile(text, 'data:text/csv;charset=utf-8,', filename + '.csv');
            }
            if (e.target.dataset.type === 'xls') {
                text = '<table>\n' +
                    '  <thead>' +
                    '    <tr>' +
                    '      <th>Conteudo</th>' +
                    '      <th>Curtidas</th>\n' +
                    '    </tr>' +
                    '  </thead>' +
                    '  <tbody>';

                data.map((item) => {
                    text += `<tr>
                                <td>${item.title}</td>
                                <td>${item.likes}</td>
                            </tr>`;
                });
                text += '</tbody>' +
                    '</table>';

                downloadFile(text, 'data:application/vnd.ms-excel,', filename + '.xls');
            }
            if (e.target.dataset.type === 'txt') {
                data.map((item) => {
                    text += `Conteúdo: ${item.title} | Curtidas: ${item.likes}\n`;
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
                <p class="xgrow-card-title">Top 20: Engajamento no conteúdos</p>
            </div>
        </div>

        <ul class="activity-card-nav nav nav-tabs tabs-card-big" id="myTabCard2" role="tablist">
            <li class="activity-card-item nav-item tab-card-big">
                <a class="activity-card-link nav-link active" id="most-accessed-tab-card" data-bs-toggle="tab"
                    href="#most-accessedcard" role="tab" aria-controls="most-accessed-card" aria-selected="true">Mais
                    acessados</a>
            </li>
            <li class="activity-card-item nav-item tab-card-big">
                <a class="nav-link" id="most-liked-tab-card" data-bs-toggle="tab" href="#most-likedcard" role="tab"
                    aria-controls="most-liked-card" aria-selected="false">Mais curtidos</a>
            </li>
        </ul>

        <div class="tab-content" id="myTabCardContent">
            <div class="tab-pane fade active show" id="most-accessedcard" role="tabpanel"
                aria-labelledby="most-accessed-card">
                <span id="loadingCardTwo">
                    <div class="loading outer-loading">
                        <span class="inner-loading">
                            <i class="fa fa-spinner fa-pulse fa-2x fa-fw primary"
                            style="color: var(--contrast-green3);"></i> Carregando...
                        </span>
                    </div>
                </span>
                <ul class="media-list" id="most-accessed-table-inner"></ul>

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
                            <i class="fas fa-file-excel" aria-hidden="true" data-type="xls" style="color: green;"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="most-likedcard" role="tabpanel" aria-labelledby="most-liked-tab-card">
                <span id="loadingCardTwoTab2">
                    <div class="loading outer-loading">
                        <span class="inner-loading">
                            <i class="fa fa-spinner fa-pulse fa-2x fa-fw primary"
                            style="color: var(--contrast-green3);"></i> Carregando...
                        </span>
                    </div>
                </span>
                <ul class="media-list" id="most-liked-table-inner"></ul>

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
        </div>
    </div>
</div>
