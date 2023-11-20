<template>
    <div>
        <template v-if="products.length > 0">
            <div class="row">
                <HeaderComponent :view-mode="viewMode" :change-view-mode="changeViewMode" :label="platformName"
                    :total-results="pagination.totalResults">
                </HeaderComponent>
            </div>
            <div v-if="viewMode === 'grid'" class="platforms platforms--cards">
                <div :key="product_id" v-for="{
                    product_id,
                    name,
                    filename,
                    status,
                    affiliate_creation
                } in products" class="platforms__card">
                    <div class="platforms__container">
                        <img class="platforms__img" :src="
                            filename ??
                            'https://las.xgrow.com/background-default.png'
                        " />
                        <div>
                            <h3>{{ name }}</h3>
                            <p class="pb-2">
                                <Status :status="status" />
                            </p>
                            <p>Afiliado desde: {{ formatDateTimeBR(affiliate_creation) }}</p>
                        </div>
                    </div>
                    <button class="platforms__link" @click.prevent="setProduct(product_id, name)">
                        <i class="fas fa-arrow-circle-right" aria-hidden="true"></i>
                        Acessar produto
                    </button>
                </div>
            </div>
            <div v-else-if="viewMode === 'list'" class="row">
                <div class="col-12">
                    <div class="xgrow-card card-dark">
                        <Table id="platformsTable">
                            <template v-slot:header>
                                <th>Produto</th>
                                <th>Status</th>
                                <th>Afiliado desde</th>
                                <th></th>
                            </template>
                            <template v-slot:body>
                                <tr :key="product_id" v-for="{
                                    product_id,
                                    name,
                                    filename,
                                    status,
                                    affiliate_creation
                                } in products">
                                    <td>
                                        <img class="platforms__img platforms__img--small" width="50" height="50" :src="
                                            filename ??
                                            'https://las.xgrow.com/background-default.png'
                                        " />
                                        <span>{{ name }}</span>
                                    </td>
                                    <td>
                                        <p>
                                            <Status :status="status" />
                                        </p>
                                    </td>
                                    <td>{{ formatDateTimeBR(affiliate_creation) }}</td>
                                    <td>
                                        <button class="platforms__link" @click.prevent="setProduct(product_id, name)">
                                            <i class="fas fa-arrow-circle-right" aria-hidden="true"></i>
                                            Acessar produto
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
import Status from "../../../../js/components/XgrowDesignSystem/Badge/StatusBadge";
import axios from "axios";
import moment from "moment"

export default {
    name: "CoproductionsOwner",
    components: {
        HeaderComponent,
        Pagination,
        StatusModalComponent,
        Table,
        Status
    },
    props: {
        env: { required: false },
    },
    data() {
        return {
            platformId: localStorage.getItem("affiliates-platform_id"),
            platformName: localStorage.getItem("affiliates-platform_name"),
            viewMode: "list",
            products: [],
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
        async getProducts() {
            this.loading = true;

            const productsUrl = affiliateProductsUrl.replace(
                "platform_id",
                this.platformId
            );

            const response = await axios.get(productsUrl, {
                params: {
                    page: this.pagination.currentPage,
                    offset: this.pagination.limit,
                },
            });
            const products = response.data.data;

            this.products = products.data;

            this.pagination = {
                totalPages: products.last_page,
                totalResults: products.total,
                currentPage: products.current_page,
                limit: products.per_page,
            }

            this.loading = false;
        },
        changeViewMode(mode) {
            this.viewMode = mode;
        },
        onPageChange: async function (page) {
            this.pagination.currentPage = page;
            await this.getProducts()
        },
        /** Limit by size itens */
        onLimitChange: async function (value) {
            this.pagination.limit = parseInt(value);
            await this.getProducts()
        },
        formatDateTimeBR(value) {
            return moment(value).format("DD/MM/YYYY");
        },
        setProduct(product_id, name) {
            localStorage.setItem('affiliates-product_id', product_id)
            localStorage.setItem('affiliates-product_name', name)

            this.$router.push('/affiliations/products/resume')
        },
    },
    async mounted() {

        if (!this.platformId) {
            this.$router.push('/affiliations')
        }

        await this.getProducts();
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
