<xgrow-tab-content id="platformsCollaboration" :selected="activeScreen === 'platforms.collaboration'">
    <div class="row">
        <header-component
            :view-mode="viewMode"
            :change-view-mode="changeViewMode"
            label="Minhas colaborações"
            :total-results="pagination.collaborations.totalResults">
            <template v-slot:search>
                <xgrow-input-component
                    id="search-field"
                    label="Pesquise..."
                    icon="<i class='fas fa-search'></i>"
                    v-model="filter.collaborations.search"
                >
                </xgrow-input-component>
            </template>
        </header-component>
    </div>

    <div v-if="viewMode === 'grid'">
        <template v-if="collaborations.length > 0">
            <div class="row" style="min-height: 50vh">
                <platform-card-component
                    v-for="platform in collaborations"
                    :key="platform.id"
                    :title="platform.name"
                    :created-at="platform.created_at"
                    :learning-area-link="platform.url"
                    :platform-id="platform.id"
                    :pending-doc="false"
                    :image="platform.image"
                    :can-edit="false">
                </platform-card-component>
            </div>
            <xgrow-pagination-component
                class="col-12"
                :total-pages="pagination.collaborations.totalPages"
                :total="pagination.collaborations.totalResults"
                :current-page="pagination.collaborations.currentPage"
                @page-changed="onPageChangeCollaborations"
                @limit-changed="onLimitChangeCollaborations">
            </xgrow-pagination-component>
        </template>
        <template v-else>
            <div class="text-white text-center not-found">
                <img src="/xgrow-vendor/assets/img/no-platforms.svg" alt="Imagem da plataforma não encontrada">
                <h1>Nenhuma plataforma encontrada!</h1>
                <p>Não há plataformas cadastradas ou sua pesquisa não foi satisfeita.</p>
            </div>
        </template>
    </div>

    <div v-else-if="viewMode === 'list'">
        <template v-if="collaborations.length > 0">
            <div class="row">
                <div class="col-12">
                    <div class="xgrow-card card-dark">
                        <xgrow-table-component id="platformsTable">
                            <template v-slot:header>
                                <th>[[collaborations.length > 1 ? 'Plataformas' : 'Platforma']]</th>
                                <th>Data de criação</th>
                                <th></th>
                                <th></th>
                            </template>
                            <template v-slot:body>
                                <platform-list-item-component
                                    v-for="platform in collaborations"
                                    :key="platform.id"
                                    :title="platform.name"
                                    :created-at="platform.created_at"
                                    :learning-area-link="platform.url"
                                    :platform-id="platform.id"
                                    :pending-doc="false"
                                    :image="platform.image"
                                    :can-edit="false">
                                </platform-list-item-component>
                            </template>
                            <template v-slot:footer>
                                <xgrow-pagination-component
                                    class="mt-4"
                                    :total-pages="pagination.collaborations.totalPages"
                                    :total="pagination.collaborations.totalResults"
                                    :current-page="pagination.collaborations.currentPage"
                                    @page-changed="onPageChangeCollaborations"
                                    @limit-changed="onLimitChangeCollaborations">
                                </xgrow-pagination-component>
                            </template>
                        </xgrow-table-component>
                    </div>
                </div>
            </div>
        </template>
        <template v-else>
            <div class="text-white text-center not-found">
                <img src="/xgrow-vendor/assets/img/no-platforms.svg" alt="Imagem da plataforma não encontrada">
                <h1>Nenhuma plataforma encontrada!</h1>
                <p>Não há plataformas cadastradas ou sua pesquisa não foi satisfeita.</p>
            </div>
        </template>
    </div>
</xgrow-tab-content>
