<div class="tab-pane fade show" id="progressAll"
    :class="{'active': activeScreen.toString() === 'progress.all'}">
    
    <xgrow-table-component id="simplified-progress-table">
        <template v-slot:title>
            <div class="d-flex align-items-center gap-1 flex-wrap justify-content-center justify-content-md-between w-100">
                <div class="xgrow-table-header">
                    <h5 class="title">Alunos: [[ pagination.totalResults ]]</h5>
                    <span class="subtitle">Veja todos os detalhes do progresso de seus alunos.</span>
                </div>
                <div>
                    <div class="d-flex align-items-center py-2 gap-2 flex-wrap w-100">
                        <div class="xgrow-input me-1 xgrow-input-search" style="height: 48px !important;">
                            <input id="ipt-global-filter" placeholder="Pesquise pelo nome ou email do aluno..." type="text"
                                v-model="filter.searchValue" style="width: 90% !important;">
                            <span class="xgrow-input-cancel">
                                <i class="fa fa-search" aria-hidden="true"></i>
                            </span>
                        </div>
                        <button type="button" data-bs-toggle="collapse" data-bs-target="#filterCoproductions"
                                aria-bs-expanded="false" aria-bs-controls="filterCoproductions"
                                class="xgrow-button-filter xgrow-button export-button me-1" aria-expanded="true">
                            <span>Filtros avançados
                                <i class="fa fa-chevron-down" aria-hidden="true"></i>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </template>
        <template v-slot:collapse>
            <div class="mb-3 collapse" id="filterCoproductions">
                <div class="filter-container">
                    <div class="p-2 px-3">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 my-2">
                                <p class="title-filter">Filtros avançados</p>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-4">
                                <div class="xgrow-form-control mb-2">
                                    <multiselect-component
                                        v-model="filter.course"
                                        :options="filter.courseOptions"
                                        :searchable="true"
                                        mode="single"
                                        @select="null"
                                        @clear="null"
                                        placeholder="Digite ou selecione um curso..."
                                    >
                                        <template v-slot:noresults>
                                            <p class="multiselect-option" style="opacity: 0.5">Curso não encontrado...</p>
                                        </template>
                                    </multiselect-component>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template v-slot:header>
            <th>Nome</th>
            <th>Email</th>
            <th>Telefone</th>
            <th>Cadastro</th>
            <th></th>
        </template>
        <template v-slot:body>
            <tr v-for="item in results" v-if="results.length > 0">
                <td>[[ item.name ]]</td>
                <td>[[ item.email ]]</td>
                <td>[[ item.phone || item.cellphone || '--' ]]</td>
                <td>[[ $util.formatDateTimeBR(item.created_at) ]]</td>
                <td>
                    <div class="dropdown">
                        <button
                            class="xgrow-button table-action-button m-1" type="button"
                            :id="`dropdownMenuButton${item.id}`" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fas fa-ellipsis-v" aria-hidden="true"></i>
                        </button>
                        <ul class="dropdown-menu table-menu" :aria-labelledby="`dropdownMenuButton${item.id}`"
                            style="margin: 0px;">
                            <li>
                                <a class="dropdown-item table-menu-item" href="javascript:void(0)"
                                    @click.prevent="getSubscriberDetails(item.id)">
                                    Ver detalhes
                                </a>
                            </li>
                        </ul>
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
                class="mt-4"
                :total-pages="pagination.totalPages"
                :total="pagination.totalResults"
                :current-page="pagination.currentPage"
                @page-changed="onPageChange"
                @limit-changed="onLimitChange">
            </xgrow-pagination-component>
        </template>
    </xgrow-table-component>
</div>
