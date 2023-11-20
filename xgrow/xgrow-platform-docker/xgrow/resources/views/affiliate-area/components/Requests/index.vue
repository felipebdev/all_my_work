<template>
    <div class="links xgrow-card card-dark py-4">
        <div v-if="hasFailed" class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <img :src="imgWarning" style="margin-right: 1rem">
                <div>
                    <h6>Atenção!</h6>
                    <p>Ocorreu um erro na verificação do seu documento.
                        <a style="color:inherit;font-weight:700" href="https://ajuda.xgrow.com/pt-br/"
                            target="_blank">Clique aqui</a> para entrar
                        em contato com o suporte e começar a receber comissões.
                    </p>
                </div>
            </div>
        </div>
        <Table id="eventsTable">
            <template v-slot:title>
                <div class="d-flex flex-column">
                    <Row>
                        <Title>Pedidos pendentes: {{ pagination.totalResults }}</Title>
                    </Row>
                    <Row>
                        <Subtitle>Veja os detalhes de todos os pedidos de afiliações pendentes.</Subtitle>
                    </Row>
                </div>
            </template>
            <template v-slot:filter>
                <div class='d-flex my-3 flex-wrap gap-3 align-items-end justify-content-between'>
                    <div class='d-flex gap-3 align-items-end flex-wrap'>
                        <Input id='searchIpt' icon="<i class='fa fa-search'></i>"
                            placeholder='Pesquise pelo nome do produto...' v-model='filter.search' class="search-input" />
                        <FilterButton target='advancedFilters' v-if="false" />
                    </div>
                </div>
            </template>
            <template v-slot:collapse>
                <div class='mb-3 collapse collapse-card advancedFilters' id='advancedFilters'>
                    <div class='p-2 px-3' style="border-radius: inherit">
                        <Row>
                            <Col classes='mt-2 mb-4 d-flex gap-2 align-items-center'>
                            <Subtitle><i class='fa fa-filter advancedFilters__icon'></i> Filtros Avançados</Subtitle>
                            </Col>
                        </Row>
                        <Row>
                            <!-- <Col sm="12" md="4" lg="4" xl="4" class="my-4">
                  <Multiselect
                    :options="options.eventType"
                    v-model="filter.eventType"
                    :searchable="true"
                    mode="tags"
                    placeholder="Digite o tipo de evento ou selecione um..."
                    :canClear="true"
                  >
                    <template v-slot:noresults>
                      <p class="multiselect-option" style="opacity: 0.5">Produto não
                        encontrado...
                      </p>
                    </template>
                  </Multiselect>
                </Col> -->
                        </Row>
                    </div>
                </div>
            </template>
            <template v-slot:header>
                <th v-for="header in ['Produto', 'Comissão', 'Data de afiliação', 'Plataforma', 'Produtor', 'Status']"
                    :key="header">
                    {{ header }}
                </th>
            </template>
            <template v-slot:body v-if="results.length">
                <tr :key="`link-${item.platform_users_id}`" v-for="item in results">
                    <td>
                        <img class="product__image" :src="
                            item.files_filename ??
                            'https://las.xgrow.com/background-default.png'
                        " alt="product image" />
                        {{ item.products_name }}
                    </td>
                    <td>{{ item.producer_products_percent ? `${formatPercent(item.producer_products_percent)}%` : '0%' }}</td>
                    <td>{{ item.producer_products_created_at ? formatDateSingleLine(item.producer_products_created_at) : '' }}
                    </td>
                    <td>{{ item.platform_name }}</td>
                    <td>{{ item.platform_users_name }}</td>
                    <td>
                        <Status :status="item.producer_products_status" />
                    </td>
                </tr>
            </template>
            <template v-else v-slot:body>
                <tr>
                    <td colspan="11">Não há pedidos pendentes ou recusados</td>
                </tr>
            </template>
            <template v-slot:footer>
                <Pagination :offset="this.pagination.offset" :totalPages="this.pagination.totalPages"
                    :total="this.pagination.totalResults" :currentPage="this.pagination.currentPage"
                    @limitChanged="onLimitChange" @pageChanged="onPageChange" />
            </template>
        </Table>
        <StatusModalComponent :is-open="loading" status="loading" />
    </div>
</template>

<script>
import imgWarning from '../../../../../public/xgrow-vendor/assets/img/documents/warning.svg'
import HeaderComponent from "../../../../js/components/HeaderComponent.vue";
import Table from "../../../../js/components/Datatables/Table";
import StatusModalComponent from "../../../../js/components/StatusModalComponent";
import Pagination from "../../../../js/components/Datatables/Pagination";
import Title from "../../../../js/components/XgrowDesignSystem/Typography/Title";
import Subtitle from "../../../../js/components/XgrowDesignSystem/Typography/Subtitle";
import Row from "../../../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../../../js/components/XgrowDesignSystem/Utils/Col";
import Status from "../../../../js/components/XgrowDesignSystem/Badge/StatusBadge";
import FilterButton from "../../../../js/components/XgrowDesignSystem/Buttons/FilterButton";
import Input from "../../../../js/components/XgrowDesignSystem/Form/Input";
import formatDateSingleLine from "../../../../js/components/XgrowDesignSystem/Mixins/formatDateSingleLine";
import axios from "axios";

export default {
    name: "AffiliateRequests",
    mixins: [formatDateSingleLine],
    components: {
        HeaderComponent,
        Pagination,
        StatusModalComponent,
        Table,
        Title,
        Subtitle,
        Row,
        Col,
        FilterButton,
        Input,
        Status
    },
    props: {
        env: { required: false },
    },
    data() {
        return {
            imgWarning,
            hasFailed: false,
            viewMode: "grid",
            results: [],
            pagination: {
                totalPages: 1,
                totalResults: 0,
                currentPage: 1,
                limit: 25,
            },
            loading: false,
            filter: {
                search: ''
            }
        };
    },
    methods: {
        async getData() {
            this.loading = true;

            try {
                const params = {
                    search: this.filter.search,
                    affiliation_status: ["pending", "refused", "recipient_failed"],
                    page: this.pagination.currentPage,
                    offset: this.pagination.limit,
                };

                const res = await axios.get(affiliationsByStatus, { params });

                const { affiliates } = res.data.response;

                this.results = affiliates.data;

                this.hasFailed = Boolean(this.results.find(item => item.producer_products_status === 'recipient_failed'));

                this.pagination.totalPages = affiliates.last_page;
                this.pagination.totalResults = affiliates.total;
                this.pagination.currentPage = affiliates.current_page;
                this.pagination.limit = affiliates.per_page;
            } catch (e) {
                console.log(e);
            } finally {
                this.loading = false;
            }
        },
        changeViewMode(mode) {
            this.viewMode = mode;
        },
        async onPageChange(page) {
            this.pagination.currentPage = page;
            await this.getData();
        },
        async onLimitChange(value) {
            this.pagination.limit = parseInt(value);
            this.pagination.currentPage = 1;
            await this.getData();
        },
        async search() {
            let term = this.filter.search
            setTimeout(async () => {
                if (term === this.filter.search) {
                    this.pagination.currentPage = 1;
                    await this.getData();
                }
            }, 1000);
        },
        formatPercent(value) {
            return parseFloat(value).toFixed(2).replace('.', ',');
        }
    },
    async mounted() {
        await this.getData();
    },
    watch: {
        'filter.search': async function () {
            await this.search();
        },
    },
};
</script>
<style>
.alert-warning {
    border: none;
    border-radius: 3px;
    color: #ffb200;
    border-left: 4px solid #ffb200;
    background-color: #3D3736;
}
</style>
<style lang="scss" scoped src="./styles.scss"></style>
