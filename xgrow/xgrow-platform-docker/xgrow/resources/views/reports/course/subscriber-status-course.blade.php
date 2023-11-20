@push('after-scripts')
    <script>
        async function getSubscriberActiveInativeData() {
            let res = await axios.get('/api/reports/subscriber-course/');
            let subscriberActiveInativeChart = echarts.init(document.getElementById('subscriberActiveInativeChart'));
            const options = {
                title: {
                    text: 'Visão geral do curso/alunos',
                    subtext: 'em curso X sem curso',
                    left: 'center',
                },
                tooltip: {
                    trigger: 'item',
                    formatter: '{a} <br/>{b} : {c} ({d}%)',
                },
                legend: {
                    orient: 'vertical',
                    left: 'left',
                    data: res.data.labels,
                },
                backgroundColor: 'transparent',
                series: [{
                    name: 'Total',
                    type: 'pie',
                    radius: '55%',
                    center: ['50%', '60%'],
                    data: res.data.status,
                    emphasis: {
                        itemStyle: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)',
                        },
                    },
                }, ],
            };
            subscriberActiveInativeChart.setOption(options);
            window.addEventListener('resize', function() {
                subscriberActiveInativeChart.resize();
            });
        }
        getSubscriberActiveInativeData();

    </script>
@endpush

<div class="row">
    <div class="col-sm-12">
        <ul class="nav nav-tabs nav-bordered customtab mb-4">
            <li class="nav-item">
                <a href="#home-b1" data-toggle="tab" aria-expanded="false" class="nav-link active">
                    <i class="mdi mdi-home-variant d-lg-none d-block mr-1"></i>
                    <span class="d-none d-lg-block">Visão geral</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#profile-b1" data-toggle="tab" aria-expanded="true" class="nav-link">
                    <i class="mdi mdi-account-circle d-lg-none d-block mr-1"></i>
                    <span class="d-none d-lg-block">Alunos em curso</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#settings-b1" data-toggle="tab" aria-expanded="false" class="nav-link">
                    <i class="mdi mdi-settings-outline d-lg-none d-block mr-1"></i>
                    <span class="d-none d-lg-block">Alunos sem curso</span>
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="home-b1" style="min-height: 450px">
                <div id="subscriberActiveInativeChart" style="height: 400px"></div>
            </div>
            <div class="tab-pane show" id="profile-b1" style="min-height: 450px">
                @include('reports.course.sections.tab-with-course')
            </div>
            <div class="tab-pane" id="settings-b1" style="min-height: 450px">
                @include('reports.course.sections.tab-without-course')
            </div>
        </div>
    </div>
</div>
