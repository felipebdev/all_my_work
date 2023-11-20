@push('after-scripts')
    <script>
        async function getNoContact(period, filter = null) {
            document.getElementById('noContactInfo').innerText = 'Carregando...';
            $('#noContactChart').addClass('d-none');
            let res = await axios.get(`/callcenter/reports/dashboard/contactless/per-attendant/${period}/${filter}`);

            let theme = await localStorage.getItem('theme');
            theme = (theme === 'dark-mode') ? 'light' : 'dark';
            const fontColor = (theme === 'dark') ? '#000000' : '#FFFFFF';

            $('#cardNoContact').text(res.data.total);
            let legends = res.data.group.map(function(attendant) {
                return attendant.attendant.name;
            });
            let counted_info = res.data.group.map(function(attendant) {
                return attendant.counted_info;
            });

            let data = [];
            for (let i = 0; i < legends.length; i++) {
                data.push({value: counted_info[i], name: legends[i]});
            }

            if (res.data.group.length < 1) {
                $('#noContactChart').addClass('d-none');
                document.getElementById('noContactInfo').innerText = 'Não há dados para exibir no período selecionado';
            } else {
                $('#noContactChart').removeClass('d-none');
                document.getElementById('noContactInfo').innerText = '';
            }

            let noContactChart = echarts.init(document.getElementById('noContactChart'), theme);
            const options = {
                textStyle: {
                    color: fontColor,
                    fontWeight: 500,
                    fontSize: 12,
                },
                backgroundColor: 'transparent',
                tooltip: {
                    trigger: 'item'
                },
                legend: {
                    data: legends,
                    bottom: 20,
                    type: 'scroll',
                    orient: 'horizontal',
                    textStyle: {
                        color: fontColor,
                        fontWeight: 500,
                        fontSize: 12,
                    },
                },
                series: [
                    {
                        name: 'Sem contato',
                        type: 'pie',
                        radius: ['40%', '70%'],
                        center: ['50%', '40%'],
                        label: {
                            show: false,
                        },
                        labelLine: {
                            show: true
                        },
                        data: data
                    }
                ]
            };

            noContactChart.setOption(options);
            noContactChart.resize();

            window.addEventListener('resize', function() {
                noContactChart.resize();
            });
        }

        async function noContact(type){
            let [startDate, _, endDate] = $('input[name="daterange"]').val().split(' ');
            let period = startDate.split('/').reverse().join('-') + ' - ' + endDate.split('/').reverse().join('-');
            const audience = $("#slc-audience-filter option:selected").val();
            if (audience === '') {
                errorToast('Algum erro aconteceu!', 'Selecione um público');
                return;
            }
            let res = await axios.get(`/callcenter/reports/dashboard/contactless/per-attendant/${period}/${audience}`);
            const values = res.data.group;

            if (res.data.group.length < 1) {
                errorToast('Algum erro aconteceu!', 'Não há dados para exportar desse período');
                return;
            }

            let data = [];
            values.map((item, index) => {
                data.push({
                    'attendant': item.attendant.name,
                    'value': item.counted_info,
                });
            });

            const title = `Sem contato por Atendente no perído de ${$('input[name="daterange"]').val()}`;
            const filename = `sem-contato-por-atendente`;

            let text = title + '.\n\n';
            if (type === 'pdf') {
                const header = ['Atendente', 'Sem contato'];
                pdfMakeWrapperDownloader(
                    title,
                    header,
                    data.map((item) => {
                        return [`${item.attendant}`, `${item.value ?? 0}`];
                    }),
                    filename
                )
            }
            if (type === 'csv') {
                text = 'atendente;sem contato\n';
                data.map((item) => {
                    text += `${item.attendant};${item.value ? item.value : 0}\n`;
                });
                downloadFile(text, 'data:text/csv;charset=utf-8,', filename + '.csv');
            }
            if (type === 'xls') {
                text = "Atendente\tSem Contato\n";
                data.map((item) => {
                    text += `${item.attendant + ''}\t${item.value ? item.value : 0}\n`;
                });

                downloadFile(text, 'data:application/csv;charset=utf-8,', filename + '.xls');
            }
            if (type === 'txt') {
                data.map((item) => {
                    text += `Atendente: ${item.attendant} | Sem contato: ${item.value ? item.value : 0}\n`;
                });
                downloadFile(text, 'data:text/plain;charset=utf-8,', filename + '.txt');
            }
        }

    </script>
@endpush

<div class="xgrow-card card-dark mb-2 mt-2">
    <div class="xgrow-card-header">
        <p class="xgrow-card-title">Total de sem contato por atendente</p>
    </div>
    <div class="xgrow-card-body d-flex flex-column" style="min-height: 432px">
        <p id="noContactInfo" class="text-center my-auto">Selecione um público para mostrar as informações</p>
        <div id="noContactChart" class="d-none" style="height: 440px; width: 100% !important;"></div>
    </div>
    <div class="xgrow-card-footer">
        <div class="d-flex align-items-center">
            <p class="xgrow-medium-bold me-2">Exportar em</p>
            <button class="xgrow-button export-button me-1" onclick="noContact('pdf')" title="Exportar em PDF">
                <i class="fas fa-file-pdf" aria-hidden="true" style="color: red;"></i>
            </button>
            <button class="xgrow-button export-button me-1" onclick="noContact('csv')" title="Exportar em CSV">
                <i class="fas fa-file-csv" aria-hidden="true" style="color: blue;"></i>
            </button>
            <button class="xgrow-button export-button me-1" onclick="noContact('xls')" title="Exportar em XLSX">
                <i class="fas fa-file-excel" aria-hidden="true" style="color: green;"></i>
            </button>
        </div>
    </div>
</div>
