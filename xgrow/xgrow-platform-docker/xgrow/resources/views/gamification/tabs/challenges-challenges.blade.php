<div class="tab-pane fade show" id="challengesChallenges"
    :class="{'active': activeScreen.toString() === 'challenges.challenges'}">
    <div class="row">

        <div class="pane-header-grid">
            <div class="gamification__header border-bottom pb-2 mb-4">
                <div>
                    <div class="tab-pane-title pb-0">
                        <h5 class="mb-3">Desafios: [[paginationTotal]]</h5>
                    </div>
                    <p class="mb-3">
                        Veja todos os seus desafios cadastrados ou crie um novo.
                    </p>
                </div>

                <button class="xgrow-button" style="height:40px; width:150px"
                    @click="createChallenge">
                    <i class="fa fa-plus" style="margin-right: 10px;"></i> Novo Desafio
            </button>
            </div>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12">
            <xgrow-table-component :id="'content-table'">
                <template v-slot:title>
                    <div
                        class="gamification__tools-bar">
                        <div class="gamification__filter-container">
                            <xgrow-input
                                id="search-field"
                                class="gamification__search-field"
                                placeholder="Pesquise pelo nome do desafio..."
                                icon="<i class='fas fa-search'></i>"
                                icon-color="#93BC1E"
                                v-model="search">
                            </xgrow-input>
                            <button type="button" data-bs-toggle="collapse" data-bs-target="#collapseDiv"
                                aria-bs-expanded="false" aria-bs-controls="collapseDiv"
                                class="xgrow-button-filter xgrow-button export-button me-1 gamification__advanced-filter" aria-expanded="true">
                                <p>Filtros avançados <i class="fa fa-chevron-down" aria-hidden="true"></i></p>
                            </button>
                        </div>
                        <export-label>
                            <button class="xgrow-button export-button me-1" title="Exportar em XLSX">
                                <img src="/xgrow-vendor/assets/img/reports/xls.svg" alt="Exportar em XLSX">
                            </button>
                            <button class="xgrow-button export-button me-1" title="Exportar em CSV">
                                <img src="/xgrow-vendor/assets/img/reports/csv.svg" alt="Exportar em CSV">
                            </button>
                        </export-label>
                    </div>
                </template>
                <template v-slot:collapse>
                    <div class="mb-3 collapse" id="collapseDiv">
                        <div class="filter-container">
                            <div class="p-2 px-3">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 mb-2">
                                        <h5>Filtros avançados</h5>
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <div class="xgrow-form-control mb-2">
                                            <multiselect-component
                                                v-model="challengeValue"
                                                :options="challengeOptions"
                                                @select="null"
                                                @clear="null"
                                                placeholder="Desafio"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <xgrow-daterange-component v-model:value="dateRangeValue" format="DD/MM/YYYY"
                                            :clearable="false" type="date" range placeholder="Selecione o período"
                                            @change="changePeriodFilter" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <template v-slot:header>
                    <th>Ordem</th>
                    <th>Nome do desafio</th>
                    <th>Alunos que completaram</th>
                    <th>Último a completar</th>
                    <th></th>
                </template>
                <template v-slot:body>
                    <tr v-if="challenges.length > 0" v-for="item in challenges" :key="item._id">
                        <td>[[item.order]]</td>
                        <td>[[item.title]]</td>
                        <td>-</td> {{-- completed --}}
                        <td>-</td> {{-- lastCompleted --}}
                        <td>
                            <div class="dropdown gamification__dropdown">
                                <button class="xgrow-button table-action-button m-1" type="button"
                                    :id="'dropdownMenuButton'+[[item._id]]" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu table-menu"
                                    :aria-labelledby="'dropdownMenuButton'+[[item._id]]">
                                    <li><a class="dropdown-item table-menu-item" href="javascript:void(0)"
                                        @click="editChallenge(item._id)">
                                        <i class="fa fa-pencil gamification__icons gamification__edit" aria-hidden="true"></i>
                                            Editar desafio</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item table-menu-item" href="javascript:void(0)"
                                            @click="openShowReplyModal(item._id)">
                                            <i class="fa fa-eye gamification__icons gamification__answer" aria-hidden="true"></i>
                                            Ver resposta do aluno
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item table-menu-item" href="javascript:void(0)"
                                           @click="callDeleteModal(item._id)">
                                           <i class="fa fa-trash gamification__icons gamification__delete" aria-hidden="true"></i>
                                           Excluir desafio
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

<xgrow-modal-component :is-open="showReplyModal" @close="showReplyModal=false" modal-size="xl">
    <template v-slot:title>
        Resposta do desafio de: <u style="color:var(--green4)" v-text="challengeReply.userName"></u>
    </template>
    <template v-slot:content>
        <xgrow-table-component :id="'show-reply-table'">
            <template v-slot:header>
                <th>Nome do desafio</th>
                <th>Resposta</th>
                <th>Respondido em</th>
                <th>Links</th>
            </template>
            <template v-slot:body>
                <tr v-if="1 == 1">
                    <td><span v-text="challengeReply.challenge"></span></td>
                    <td>
                        <span v-text="challengeReply.reply"></span>
                    </td>
                    <td><span v-text="challengeReply.createdAt"></span></td>
                    <td>
                        <a href="#" style="color:#ffffff"><span v-text="challengeReply.link"></span></a>
                    </td>
                </tr>
                <tr v-else>
                    <td colspan="4" class="xgrow-no-content">
                        Não há dados a serem exibidos.
                    </td>
                </tr>
            </template>
        </xgrow-table-component>

    </template>
    <template v-slot:footer="slotProps">
        <button type="button" class="btn btn-success" @click="slotProps.closeModal">
            Voltar
        </button>
    </template>
</xgrow-modal-component>
