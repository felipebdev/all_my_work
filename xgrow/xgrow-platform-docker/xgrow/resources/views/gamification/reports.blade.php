@extends('templates.xgrow.main')

@push('after-scripts')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script src="{{ asset('js/bundle/gamification-reports.js') }}"></script>
@endpush

@push('after-styles')
    <link href="{{ asset('xgrow-vendor/assets/css/pages/gamification.css') }}" rel="stylesheet">
@endpush

@push('jquery')
    <script src="{{ asset('xgrow-vendor/assets/js/confirmation-modal.js') }}"></script>
@endpush

@section('content')
    <div id="reports">

        <nav class="xgrow-breadcrumb mt-3" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Início</a></li>
                <!-- <li class="breadcrumb-item"><a href="/gamification">Gamificação</a></li> -->
                <li class="breadcrumb-item active mx-2"><span>Relatórios</span></li>
            </ol>
        </nav>

        <status-modal-component :is-open="statusLoading" :status="status"></status-modal-component>

        <div class="xgrow-card card-dark" style="min-height: 500px">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <xgrow-table-component :id="'content-table'">
                        <template v-slot:title>
                            <div
                                class="d-flex align-items-center gap-1 flex-wrap justify-content-center justify-content-md-between w-100 ">
                                <h5 class="mb-0">Total de [[results.length]] aluno[[results.length > 1 ? 's' : '']]
                                </h5>
                                <div>
                                    <div class="d-flex align-items-center py-2 gap-2 flex-wrap">
                                        <div class="xgrow-input me-1 xgrow-input-search">
                                            <input id="ipt-global-filter" placeholder="Pesquisa um desafio..." type="text"
                                                style="height: 40px;" v-model="filter.searchValue">
                                            <span class="xgrow-input-cancel"><i class="fa fa-search"
                                                    aria-hidden="true"></i></span>
                                        </div>
                                        <button type="button" data-bs-toggle="collapse" data-bs-target="#collapseDiv"
                                            aria-bs-expanded="false" aria-bs-controls="collapseDiv"
                                            class="xgrow-button-filter xgrow-button export-button me-1"
                                            aria-expanded="true">
                                            <p>Filtros avançados <i class="fa fa-chevron-down" aria-hidden="true"></i></p>
                                        </button>
                                        <div class="export-buttons">
                                            <button class="xgrow-button export-button me-1" title="Exportar em CSV">
                                                <img src="/xgrow-vendor/assets/img/reports/txt.svg" alt="Exportar em CSV">
                                            </button>
                                            <button class="xgrow-button export-button me-1" title="Exportar em XLSX">
                                                <img src="/xgrow-vendor/assets/img/reports/xls.svg" alt="Exportar em XLSX">
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template v-slot:collapse>
                            <div class="mb-3 collapse" id="collapseDiv">
                                <div class="filter-container">
                                    <div class="p-2 px-3">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 my-2">
                                                <h5>Filtros avançados</h5>
                                            </div>

                                            <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                                                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                                    {!! Form::text('subscriberName', null, ['id' => 'subscriberName', 'class' => 'xgrow-input mui--is-empty mui--is-untouched mui--is-pristine', 'v-model' => 'filter.subscriberName']) !!}
                                                    {!! Form::label('subscriberName', 'Nome do aluno') !!}
                                                </div>
                                            </div>

                                            <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                                                <xgrow-daterange-component v-model:value="filter.dateRangeValue"
                                                    format="DD/MM/YYYY" :clearable="false" type="date" range
                                                    placeholder="Data de cadastro" @change="changePeriodFilter" />
                                            </div>

                                            <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                                                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                                    <select id="actionValue" class="xgrow-select"
                                                        v-model="filter.actionValue" @change="void(0)">
                                                        <option value="" selected disabled>Selecione uma opção</option>
                                                        <option v-for="actionValue in filter.actionOptions"
                                                            :value="actionValue.id" :key="actionValue.id">
                                                            [[ actionValue.name ]]
                                                        </option>
                                                    </select>
                                                    <label for="actionValue">Ação</label>
                                                    <span class="caret"></span>
                                                </div>
                                            </div>

                                            <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                                                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                                    <select id="levelValue" class="xgrow-select"
                                                        v-model="filter.levelValue" @change="void(0)">
                                                        <option value="" selected disabled>Selecione uma opção</option>
                                                        <option v-for="levelValue in filter.levelOptions"
                                                            :value="levelValue.id" :key="levelValue.id">
                                                            [[ levelValue.name ]]
                                                        </option>
                                                    </select>
                                                    <label for="levelValue">Fase</label>
                                                    <span class="caret"></span>
                                                </div>
                                            </div>

                                            <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                                                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                                    <select id="challengeValue" class="xgrow-select"
                                                        v-model="filter.challengeValue" @change="void(0)">
                                                        <option value="" selected disabled>Selecione uma opção</option>
                                                        <option v-for="challengeValue in filter.challengeOptions"
                                                            :value="challengeValue.id" :key="challengeValue.id">
                                                            [[ challengeValue.name ]]
                                                        </option>
                                                    </select>
                                                    <label for="challengeValue">Desafio</label>
                                                    <span class="caret"></span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template v-slot:header>
                            <th>Nome</th>
                            <th>Ação</th>
                            <th>Data de cadastro</th>
                            <th>Fase</th>
                            <th>Pontuação</th>
                            <th style="width:40px"></th>
                        </template>
                        <template v-slot:body>
                            <tr v-if="results.length > 0" v-for="(item, index) in results" :key="item._id">
                                <td>[[item.name]]</td>
                                <td>[[item.action]]</td>
                                <td>[[item.createdAt]]</td>
                                <td>[[item.level]]</td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <img src="/xgrow-vendor/assets/img/gamification/coin.svg" alt="Xcoin">
                                        <span>[[item.score]] Xcoin[[item.score > 1 ? 's' : '']]</span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="xgrow-button table-action-button m-1" type="button"
                                            :id="'dropdownMenuButton'+[[item._id]]" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu table-menu xgrow-dropdown-menu"
                                            :aria-labelledby="'dropdownMenuButton'+[[item._id]]">
                                            <li>
                                                <a class="dropdown-item table-menu-item" href="javascript:void(0)"
                                                    @click="void(0)">
                                                    Ver detalhes
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr v-else>
                                <td colspan="4" class="xgrow-no-content">
                                    Não há dados a serem exibidos.
                                </td>
                            </tr>
                        </template>
                        <template v-slot:footer>
                            <xgrow-pagination-component :total-pages="paginationTotalResults" :total="paginationTotal"
                                :current-page="paginationCurrentPage" @page-changed="onPageChange"
                                @limit-changed="onLimitChange">
                            </xgrow-pagination-component>
                        </template>
                    </xgrow-table-component>
                </div>

            </div>
        </div>
        @include('elements.confirmation-modal')
        @include('elements.toast')
    </div>
@endsection
