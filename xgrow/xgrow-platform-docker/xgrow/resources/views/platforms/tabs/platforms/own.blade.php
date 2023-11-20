<xgrow-tab-content id="platformsOwn" :selected="activeScreen === 'platforms.own'">
    <div class="row">
        <header-component
            {{-- :button="{text: 'Nova plataforma', icon:'fas fa-plus', action: () => {}}" --}}
            :view-mode="viewMode"
            :change-view-mode="changeViewMode"
            label="Minhas plataformas"
            :total-results="pagination.platforms.totalResults">
            <template v-slot:search>
                <xgrow-input-component
                    id="search-field"
                    label="Pesquise..."
                    icon="<i class='fas fa-search'></i>"
                    v-model="filter.platforms.search"
                >
                </xgrow-input-component>
            </template>
        </header-component>
    </div>

    <div v-if="viewMode === 'grid'">
        <template v-if="platforms.length > 0">
            <div class="row" style="min-height: 50vh">
                <platform-card-component
                    v-for="platform in platforms"
                    :key="platform.id"
                    :title="platform.name"
                    :created-at="platform.created_at"
                    :learning-area-link="platform.url"
                    :platform-id="platform.id"
                    :image="platform.image"
                    :can-edit="true"
                    :pending-doc="!platform.verified"
                    @get-platform="modalEditPlatform"
                >
                </platform-card-component>
            </div>
            <xgrow-pagination-component
                class="col-12"
                :total-pages="pagination.platforms.totalPages"
                :total="pagination.platforms.totalResults"
                :current-page="pagination.platforms.currentPage"
                @page-changed="onPageChange"
                @limit-changed="onLimitChange">
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
        <template v-if="platforms.length > 0">
            <div class="row">
                <div class="col-12">
                    <div class="xgrow-card card-dark">
                        <xgrow-table-component id="platformsTable">
                            <template v-slot:header>
                                <th>[[platforms.length > 1 ? 'Plataformas' : 'Plataforma']]</th>
                                <th>Data de criação</th>
                                <th></th>
                                <th></th>
                            </template>
                            <template v-slot:body>
                                <platform-list-item-component
                                    v-for="platform in platforms"
                                    :key="platform.id"
                                    :title="platform.name"
                                    :created-at="platform.created_at"
                                    :learning-area-link="platform.url"
                                    :platform-id="platform.id"
                                    :image="platform.image"
                                    :can-edit="true"
                                    @get-platform="modalEditPlatform"
                                    :pending-doc="!platform.verified"
                                >
                                </platform-list-item-component>
                            </template>
                            <template v-slot:footer>
                                <xgrow-pagination-component
                                    class="mt-4"
                                    :total-pages="pagination.platforms.totalPages"
                                    :total="pagination.platforms.totalResults"
                                    :current-page="pagination.platforms.currentPage"
                                    @page-changed="onPageChange"
                                    @limit-changed="onLimitChange">
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

    <xgrow-modal-component :is-open="editModal.isOpen" @close="editModal.isOpen=false" modal-size="md">
        <template v-slot:content>
            <div class="row gap-3 text-center w-100" style="color:var(--gray1)">
                <div class="col-sm-12" style="margin-bottom: -52px;">
                    <h5 class="text-white" style="font-size: 22px">
                        <b>Imagem de capa da Plataforma</b>
                    </h5>
                    <p style="font-size: 16px;">
                        Imagem de identificação da plataforma. Tamanho: (1:1)
                    </p>

                    <upload-image
                        ref="iconUrl" title="" subtitle="" img-aspect-ratio="1x1"
                        refer="iconUrl" @send-image="receiveImage"
                        :style="'width:128px;height:128px;border-radius:10px;'">
                    </upload-image>
                </div>
            </div>
        </template>
        <template v-slot:footer="slotProps">
            <div class="w-100 d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-outline-light xgrow-button-cancel"
                        @click="slotProps.closeModal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-success" @click.prevent="saveThumb">
                    <i class="fas fa-check mr-2"></i> Salvar
                </button>
            </div>
        </template>
    </xgrow-modal-component>

</xgrow-tab-content>
