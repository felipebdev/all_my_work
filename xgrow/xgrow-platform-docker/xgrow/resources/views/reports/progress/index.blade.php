@extends('templates.xgrow.main')

@push('after-styles')
<link href="{{asset('xgrow-vendor/assets/css/pages/report_search.css')}}" rel="stylesheet">
<style>

</style>
@endpush

@push('after-scripts')
<script src="{{asset('xgrow-vendor/assets/js/toast-config.js')}}"></script>
<script src=" https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script>
    const getAPIResult = @json(route('reports.get.progress.api'));
    const getAPICourses = @json(route('reports.get.progress.courses.api'));
</script>
<script src="{{ asset('js/bundle/progressReport.js') }}"></script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item"><span>Relatórios</span></li>
            <li class="breadcrumb-item active mx-2"><span>Pesquisa de conteúdos</span></li>
        </ol>
    </nav>

    <div class="xgrow-card card-dark p-0" id="progressReport">
        <status-modal-component :is-open="statusLoading" :status="status"></status-modal-component>
        <div class="xgrow-card-body p-3 py-4">
            <xgrow-table-component :id="'content-table'">
                <template v-slot:title>
                    <div>
                        <h5>Registros: [[total == null ? '0' : total]] registro<span v-if="total > 1">s</span></h5>
                    </div>
                    <div class="d-flex align-items-center gap-1 flex-wrap justify-content-sm-center ">
                        <div class="xgrow-input me-1" style="background-color: var(--input-bg); height: 40px;" >
                            <input id="ipt-global-filter" placeholder="Busque alguma coisa..." type="text" style="height: 40px;" v-model="search" >
                            <span class="xgrow-input-cancel"><i class="fa fa-search" aria-hidden="true"></i></span>
                        </div>
                        {{-- <div class="export-buttons">
                            <button class="xgrow-button export-button me-1" title="Exportar em CSV">
                                <i class="fas fa-file-csv" style="color: blue" aria-hidden="true"></i>
                            </button>
                            <button class="xgrow-button export-button me-1" title="Exportar em XLSX">
                                <i class="fas fa-file-excel" style="color: green" aria-hidden="true"></i>
                            </button>
                        </div> --}}
                        <div class="d-flex align-items-center py-2">
                            <button type="button" data-bs-toggle="collapse" data-bs-target="#collapseDiv" aria-bs-expanded="false" aria-bs-controls="collapseDiv" class="xgrow-button-filter xgrow-button export-button me-1" aria-expanded="true">
                                <p>Filtros avançados <i class="fa fa-chevron-down" aria-hidden="true"></i></p>
                            </button>
                        </div>
                    </div>
                </template>
                <template v-slot:collapse>
                    <div class="mb-3 collapse" id="collapseDiv">
                        <div class="filter-container">
                            <div class="p-2 px-3">
                                <div class="row">
                                    <div class="col-sm-12 col-md-4">
                                        <div class="xgrow-form-control mb-2">
                                            <multiselect-component
                                            v-model="course"
                                            :options="courses"
                                            @select="getData"
                                            @clear="clearCourse"
                                            placeholder="Por Curso"
                                          />
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <xgrow-daterange-component
                                            v-model:value="firstAccessFilter"
                                            format="DD/MM/YYYY"
                                            {{-- :clearable="false" --}}
                                            type="date"
                                            range
                                            placeholder="Filtrar por data do primeiro acesso"
                                            @change="firstAccessConvert"/>
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <xgrow-daterange-component
                                            v-model:value="lastAccessFilter"
                                            format="DD/MM/YYYY"
                                            {{-- :clearable="false" --}}
                                            type="date"
                                            range
                                            placeholder="Filtrar por data do último acesso"
                                            @change="lastAccessConvert"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <template v-slot:header>
                    <th>Curso</th>
                    <th>Aluno</th>
                    <th>Primeiro acesso</th>
                    <th>Último acesso</th>
                    <th>Status</th>
                    <th>Progresso</th>
                </template>
                <template v-slot:body>
                    <tr v-if="total" v-for="(res, index) in results" :key="index">
                        <td>[[res.courseName]]</td>
                        <td>[[res.userName]]</td>
                        <td>[[res.firstAccess]]</td>
                        <td>[[res.lastAccess]]</td>
                        <td>[[parseInt(res.percentCourseCompleted.slice(0, -1)) > 0 ? 'Em andamento' : 'Não iniciado']]</td>
                        <td>
                            <div class="progress position-relative">
                                <div class="progress-bar progress-bar-striped bg-success" role="progressbar"
                                    :style="'width:' + [[res.percentCourseCompleted]] + ';background-color:var(--breadcrumb-bg-active)!important'"
                                    :aria-valuenow="parseInt(res.percentCourseCompleted.slice(0, -1))"
                                    aria-valuemin="0" aria-valuemax="100">
                                </div>
                                <small class="justify-content-center d-flex position-absolute w-100" style="color:#000;font-weight:600;">
                                    [[res.percentCourseCompleted]]
                                </small>
                            </div>
                        </td>
                    </tr>
                    <tr v-else>
                        <td colspan="6" class="xgrow-no-content">
                            Não há dados a serem exibidos.
                        </td>
                    </tr>
                </template>
                <template v-slot:footer>
                    <xgrow-pagination-component
                    :total-pages="totalResults"
                    :total="total"
                    :current-page="currentPage"
                    @page-changed="onPageChange"
                    @limit-changed="onLimitChange"
                    ></xgrow-pagination-component>
                </template>
            </xgrow-table-component>
        </div>
    </div>
    @include('elements.confirmation-modal')
    @include('elements.toast')
@endsection
