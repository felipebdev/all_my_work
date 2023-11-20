<template>
    <div>
        <template v-if="platforms.length > 0">
            <div class="row">
                <HeaderComponent :view-mode="viewMode" :change-view-mode="changeViewMode" label="Minhas afiliações"
                    :total-results="pagination.totalResults">
                </HeaderComponent>
            </div>
            <div v-if="viewMode === 'grid'" class="platforms platforms--cards">
                <div :key="platform_id" v-for="{ platform_id, platform_name, platform_cover } in platforms"
                    class="platforms__card">
                    <div class="platforms__container">
                        <img class="platforms__img" :src="
                            platform_cover ??
                            'https://las.xgrow.com/background-default.png'
                        " />
                        <h3>{{ platform_name }}</h3>
                    </div>
                    <button class="platforms__link" @click.prevent="setPlatform(platform_id, platform_name)">
                        <i class="fas fa-arrow-circle-right" aria-hidden="true"></i>
                        Acessar plataforma
                    </button>
                </div>
            </div>
            <div v-else-if="viewMode === 'list'" class="row">
                <div class="col-12">
                    <div class="xgrow-card card-dark">
                        <Table id="platformsTable">
                            <template v-slot:header>
                                <th>Plataforma</th>
                                <th></th>
                            </template>
                            <template v-slot:body>
                                <tr :key="platform_id" v-for="{
                                    platform_id,
                                    platform_name,
                                    platform_cover,
                                } in platforms">
                                    <td>
                                        <img class="platforms__img platforms__img--small" width="50" height="50" :src="
                                            platform_cover ??
                                            'https://las.xgrow.com/background-default.png'
                                        " />
                                        <span>{{ platform_name }}</span>
                                    </td>
                                    <td>
                                        <button class="platforms__link"
                                            @click.prevent="setPlatform(platform_id, platform_name)">
                                            <i class="fas fa-arrow-circle-right" aria-hidden="true"></i>
                                            Acessar plataforma
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </Table>
                    </div>
                </div>
            </div>
            <Pagination class="mt-4" :total-pages="pagination.totalPages" :total="pagination.totalResults"
                :current-page="pagination.currentPage" @page-changed="onPageChange" @limit-changed="onLimitChange" />
        </template>
        <div v-else class="xgrow-card card-dark">
            <div class="row my-3">
                <h4 class="text-center">Nenhuma afiliação encontrada...</h4>
            </div>
        </div>
        <StatusModalComponent :is-open="loading" status="loading" />
    </div>
</template>

<script>
import HeaderComponent from "../../../../js/components/HeaderComponent.vue";
import Table from "../../../../js/components/Datatables/Table";
import StatusModalComponent from "../../../../js/components/StatusModalComponent";
import Pagination from "../../../../js/components/Datatables/Pagination";
import axios from "axios";

export default {
    name: "AffiliatePlatforms",
    components: {
        HeaderComponent,
        Pagination,
        StatusModalComponent,
        Table,
    },
    props: {
        env: { required: false },
    },
    data() {
        return {
            viewMode: "grid",
            platforms: [],
            pagination: {
                totalPages: 1,
                totalResults: 0,
                currentPage: 1,
                limit: 25,
            },
            loading: false,
        };
    },
    methods: {
        /** Show transaction detail */
        async getPlatforms() {
            this.loading = true;
            const res = await axios.get(affiliatePlatformsUrl, {
                params: {
                    page: this.pagination.currentPage,
                    offset: this.pagination.limit,
                },
            });

            const { platforms } = res.data.response;

            this.platforms = platforms.data;

            this.pagination.totalPages = platforms.last_page;
            this.pagination.totalResults = platforms.total;
            this.pagination.currentPage = platforms.current_page;
            this.pagination.limit = platforms.per_page;
            this.loading = false;
        },
        changeViewMode(mode) {
            this.viewMode = mode;
        },
        onPageChange: async function (page) {
            this.pagination.currentPage = page;
            await this.getPlatforms();
        },
        /** Limit by size itens */
        onLimitChange: async function (value) {
            this.pagination.limit = parseInt(value);
            await this.getPlatforms();
        },
        setPlatform(platform_id, platform_name) {
            localStorage.setItem('affiliates-platform_id', platform_id)
            localStorage.setItem('affiliates-platform_name', platform_name)

            this.$router.push('/affiliations/products')
        },
    },
    async mounted() {
        localStorage.removeItem('affiliates-platform_id')
        localStorage.removeItem('affiliates-platform_name')
        await this.getPlatforms();
    },
};
</script>

<style lang="scss" scoped src="./styles.scss"></style>
<style lang="scss">
.info {
    padding-bottom: 0 !important;
    border-bottom: 0 !important;
}

.header {
    flex-wrap: inherit !important;
}

.header .functions {
    margin-top: 0 !important;
}

@media screen and (max-width: 500px) {
    .header {
        flex-wrap: wrap !important;
    }

    .header .functions .view {
        margin-top: 0px !important;
    }
}

@media screen and (max-width: 1024px) {
    .header .functions {
        flex-wrap: nowrap;
    }
}
</style>
