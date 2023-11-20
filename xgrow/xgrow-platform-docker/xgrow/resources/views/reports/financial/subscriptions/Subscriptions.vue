<template>
  <div class="row mb-3">
    <div class="col-12 d-flex flex-column flex-lg-row gap-2 wrap overflow-auto py-2">
      <FinancialCard
        border-color="#93BC1E"
        :title="metrics.paid || formatBRLCurrency(0)"
        subtitle="Receita no período"
      >
        <XgrowTooltip
          id="subscription_paid"
          icon='<i class="fas fa-info-circle"></i>'
          tooltip="Total das assinaturas pagas no mês"
        />
      </FinancialCard>
      <FinancialCard
        border-color="#4DA2D1"
        :title="metrics.active || 0"
        subtitle="Ativas"
      >
        <XgrowTooltip
          id="subscription_active"
          icon='<i class="fas fa-info-circle"></i>'
          tooltip="Alunos que estão ativos"
        />
      </FinancialCard>
      <FinancialCard
        border-color="#F45858"
        :title="metrics.canceled || 0"
        subtitle="Canceladas"
      >
        <XgrowTooltip
          id="subscription_canceled"
          icon='<i class="fas fa-info-circle"></i>'
          tooltip="Alunos que não estão mais ativos"
        />
      </FinancialCard>
      <FinancialCard
        border-color="#F4E558"
        :title="metrics.churn || '0%'"
        subtitle="Churn"
      >
        <XgrowTooltip
          id="subscription_churn"
          icon='<i class="fas fa-info-circle"></i>'
          tooltip="Percentual de desistência no período"
        />
      </FinancialCard>
      <FinancialCard
        border-color="#93BC1E"
        :title="metrics.chargeback || formatBRLCurrency(0)"
        subtitle="Chargeback"
      >
        <XgrowTooltip
          id="subscription_chargeback"
          icon='<i class="fas fa-info-circle"></i>'
          tooltip="Pagamentos estornados direto pelo banco"
        />
      </FinancialCard>
    </div>
  </div>

  <div class="xgrow-card card-dark">
    <XgrowTable id="salesSubscriptionsTable">
      <template v-slot:title>
        <div
          class="d-flex align-items-center gap-1 flex-wrap justify-content-md-between w-100 slot-header"
        >
          <div class="xgrow-table-header">
            <h5 class="title">
              Assinaturas: {{ pagination.totalResults }}
            </h5>
            <span>Veja todos os seus assinantes cadastrados.</span>
          </div>
        </div>
        <div
          class="d-flex align-items-center justify-content-between py-2 gap-2 flex-wrap w-100"
        >
          <div class="d-flex gap-3 align-items-center flex-wrap">
            <Input
              id="search-field"
              placeholder="Pesquise nome ou e-mail..."
              icon="<i class='fas fa-search'></i>"
              iconColor="#93BC1E"
              v-model="filter.searchValue"
              style="min-width: 200px; max-width: 310px"
            />
            <FilterButton target="filterSalesSubscriptions" />
          </div>
          <div class="d-flex gap-3">
            <ExportLabel>
              <IconButton
                @click="exportReport('csv')"
                img-src="/xgrow-vendor/assets/img/reports/csv.svg"
                title="Exportar em CSV"
              />
              <IconButton
                @click="exportReport('xlsx')"
                img-src="/xgrow-vendor/assets/img/reports/xls.svg"
                title="Exportar em XLSX"
              />
            </ExportLabel>
          </div>
        </div>
      </template>
      <template v-slot:collapse>
        <div class="mb-3 collapse" id="filterSalesSubscriptions">
          <div class="filter-container">
            <div class="p-2 px-3">
              <div class="row">
                <div class="col-sm-12 col-md-12 mt-2 mb-3">
                  <p class="title-filter">
                    <i class="fas fa-filter"></i> Filtros avançados
                  </p>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-4 mb-3">
                  <div class="xgrow-form-control">
                    <xgrow-multiselect
                      :options="filter.product.options"
                      v-model="filter.product.selected"
                      :searchable="true"
                      mode="tags"
                      placeholder="Nome do produto"
                      @select="changeFilter"
                      :canClear="false"
                      @deselect="changeFilter"
                    >
                      <template v-slot:noresults>
                        <p class="multiselect-option" style="opacity: 0.5">
                          Produto não encontrado...
                        </p>
                      </template>
                    </xgrow-multiselect>
                  </div>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-4 mb-3">
                  <div class="xgrow-form-control mb-2">
                    <xgrow-multiselect
                      :options="filter.status.options"
                      v-model="filter.status.selected"
                      :searchable="true"
                      mode="tags"
                      placeholder="Status da assinatura"
                      @select="changeFilter"
                      :canClear="false"
                      @deselect="changeFilter"
                    >
                      <template v-slot:noresults>
                        <p class="multiselect-option" style="opacity: 0.5">
                          Status não encontrado...
                        </p>
                      </template>
                    </xgrow-multiselect>
                  </div>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-4 mb-3">
                  <div class="xgrow-form-control mb-2">
                    <xgrow-multiselect
                      :options="filter.method.options"
                      v-model="filter.method.selected"
                      :searchable="true"
                      mode="tags"
                      placeholder="Método de pagamento"
                      @select="changeFilter"
                      :canClear="false"
                      @deselect="changeFilter"
                    >
                      <template v-slot:noresults>
                        <p class="multiselect-option" style="opacity: 0.5">
                          Tipo não encontrado...
                        </p>
                      </template>
                    </xgrow-multiselect>
                  </div>
                </div>

                <div class="col-sm-12 col-md-12 col-lg-4 mb-3">
                  <xgrow-daterange-component
                    v-model:value="filter.datePickers.accession.value"
                    format="DD/MM/YYYY"
                    :clearable="true"
                    type="date"
                    range
                    placeholder="Período de adesão"
                    @change="changeDate($event, 'accession')"
                  />
                </div>

                <div class="col-sm-12 col-md-12 col-lg-4 mb-3">
                  <xgrow-daterange-component
                    v-model:value="filter.datePickers.canceled.value"
                    format="DD/MM/YYYY"
                    :clearable="true"
                    type="date"
                    range
                    placeholder="Período de cancelamento"
                    @change="changeDate($event, 'canceled')"
                  />
                </div>

                <div class="col-sm-12 col-md-12 col-lg-4 mb-3">
                  <xgrow-daterange-component
                    v-model:value="filter.datePickers.lastPayment.value"
                    format="DD/MM/YYYY"
                    :clearable="true"
                    type="date"
                    range
                    placeholder="Período do último pagamento"
                    @change="changeDate($event, 'lastPayment')"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>
      <template v-slot:header>
        <th>Cliente</th>
        <th>Produto</th>
        <th>Valor líquido</th>
        <th>Data adesão</th>
        <th>Data Cancel.</th>
        <th>Último pgto.</th>
        <th>Status</th>
        <th></th>
      </template>
      <template v-slot:body>
        <SubscriptionRow
          v-for="(subscription, index) in results"
          :key="index"
          :subscription="subscription"
          @loading="
            (isLoading) => {
              loading.active = isLoading;
            }
          "
          @subscriptionModal="subscriptionModal"
        />
        <tr v-if="results.length < 1">
          <td colspan="7" class="text-center">Não há dados a serem exibidos</td>
        </tr>
      </template>
      <template v-slot:footer>
        <Pagination
          class="mt-4"
          :total-pages="pagination.totalPages"
          :total="pagination.totalResults"
          :current-page="pagination.currentPage"
          @page-changed="onPageChange"
          @limit-changed="onLimitChange"
        >
        </Pagination>
      </template>
    </XgrowTable>
  </div>

  <SubscriptionsModalComponent
    :is-open="modal.active"
    :close-function="
      () => {
        modal.active = false;
        modal.data = {};
      }
    "
    :modal-data="modal.data"
  >
  </SubscriptionsModalComponent>

  <StatusModalComponent :is-open="loading.active" :status="loading.status" />
</template>

<script>
import FinancialCard from "../../../../js/components/XgrowDesignSystem/Cards/FinancialCard";
import StatusModalComponent from "../../../../js/components/StatusModalComponent";
import axios from "axios";
import Pagination from "../../../../js/components/Datatables/Pagination";
import XgrowTable from "../../../../js/components/Datatables/Table";
import FilterButton from "../../../../js/components/XgrowDesignSystem/Buttons/FilterButton";
import ExportLabel from "../../../../js/components/XgrowDesignSystem/Utils/ExportLabel";
import IconButton from "../../../../js/components/XgrowDesignSystem/Buttons/IconButton";
import Input from "../../../../js/components/XgrowDesignSystem/Input";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import DatePicker from "vue-datepicker-next";
import "vue-datepicker-next/index.css";
import "vue-datepicker-next/locale/pt-br";
import moment from "moment";
import SubscriptionRow from "./SubscriptionRow";
import SubscriptionsModalComponent from "./modal/SubscriptionsModalComponent";
import formatBRLCurrency from "../../../../js/components/XgrowDesignSystem/Mixins/formatBRLCurrency";
import XgrowTooltip from "../../../../js/components/XgrowDesignSystem/Tooltips/XgrowTooltip";

export default {
  name: "Subscriptions",
  mixins: [formatBRLCurrency],
  components: {
    XgrowTooltip,
    SubscriptionsModalComponent,
    SubscriptionRow,
    Input,
    "xgrow-multiselect": Multiselect,
    IconButton,
    ExportLabel,
    FilterButton,
    XgrowTable,
    Pagination,
    StatusModalComponent,
    FinancialCard,
    "xgrow-daterange-component": DatePicker,
  },
  data() {
    return {
      /** loading */
      loading: {
        active: true,
        status: "loading",
      },

      /** Metrics */
      metrics: {
        paid: 0,
        active: 0,
        canceled: 0,
        churn: 0,
        chargeback: 0,
      },

      /** Datatables and Pagination */
      results: [],
      pagination: {
        totalPages: 1,
        totalResults: 0,
        currentPage: 1,
        limit: 25,
      },
      filter: {
        searchValue: "",
        tags: [],
        datePickers: {
          accession: {
            value: [],
            formated: null,
          },
          canceled: {
            value: [],
            formated: null,
          },
          lastPayment: {
            value: [],
            formated: null,
          },
        },
        product: {
          options: {},
          selected: [],
        },
        method: {
          options: {
            boleto: "Boleto",
            credit_card: "Cartão de Crédito",
            pix: "Pix",
          },
          selected: [],
        },
        status: {
          options: {
            active: "Ativo",
            canceled: "Cancelado",
            pending: "Pendente",
            failed: "Falha no pagamento",
          },
          selected: [],
        },
      },

      /** Modal */
      modal: {
        active: false,
        data: {},
      },
    };
  },
  watch: {
    "filter.searchValue": async function () {
      await this.searchByTerm();
    },
  },
  methods: {
    /** On change page */
    onPageChange: async function (page) {
      this.pagination.currentPage = page;
      await this.getData();
    },
    /** Limit by size itens */
    onLimitChange: async function (value) {
      this.pagination.limit = value;
      await this.getData();
    },
    /** Search by term */
    searchByTerm: async function () {
      const term = this.filter.searchValue;
      setTimeout(async () => {
        if (term === this.filter.searchValue) {
          await this.getData();
        }
      }, 1000);
    },
    /** Subscriptions Data */
    async getData() {
      this.loading.active = true;
      try {
        const params = {
          productsId: this.filter.product.selected,
          typePayment: this.filter.method.selected,
          statusSubscription: this.filter.status.selected,
          periodAccession: this.filter.datePickers.accession.formated,
          periodCancel: this.filter.datePickers.canceled.formated,
          periodLastPayment: this.filter.datePickers.lastPayment.formated,
          searchTerm: this.filter.searchValue,
          offset: this.pagination.limit,
          page: this.pagination.currentPage,
        };

        const res = await axios.get(getSubscriptionsURL, {
          params,
        });

        const data = res.data.response[0].response;
        const rows = data.rows;

        this.updateMetrics(data.metrics);
        this.results = rows.data;
        this.pagination.totalPages = rows.last_page;
        this.pagination.totalResults = rows.total;
        this.pagination.currentPage = rows.current_page;
        this.pagination.limit = rows.per_page;
        this.loading.active = false;
      } catch (e) {
        this.loading.active = false;
        errorToast(
          "Algum erro aconteceu!",
          "Não foi possível carregar os dados das transações, entre em contato com o suporte."
        );
      }
    },
    /** Metric cards data **/
    updateMetrics: function (metrics) {
      this.metrics.paid = metrics.recipe_on_period;
      this.metrics.active = metrics.active;
      this.metrics.canceled = metrics.canceled;
      this.metrics.churn = metrics.churn;
      this.metrics.chargeback = metrics.chargeback;
    },
    /** Export Report Function */
    exportReport: async function (typeFile) {
      infoToast(
        "Gerando relatório...",
        "Acompanhe o status do arquivo em Relatórios > Listas Exportadas."
      );
      try {
        const params = {
          reportName: "subscription",
          typeFile: typeFile,
          productsId: this.filter.product.selected,
          typePayment: this.filter.method.selected,
          statusSubscription: this.filter.status.selected,
          periodAccession: this.filter.datePickers.accession.formated,
          periodCancel: this.filter.datePickers.canceled.formated,
          periodLastPayment: this.filter.datePickers.lastPayment.formated,
          searchTerm: this.filter.searchValue,
        };

        await axios.get(generateReportURL, {
          params,
        });
        successToast(
          "Relatório gerado!",
          "Você pode acessar o arquivo gerado em Relatórios > Listas Exportadas."
        );
      } catch (e) {
        errorToast(
          "Algum erro aconteceu!",
          "Ocorreu um erro inesperado, por favor contate o suporte."
        );
      }
    },
    /** Filters Data and Functions */
    getProducts: async function () {
      try {
        const res = await axios.get(getProductsList);
        if (res.data.status !== "success") {
          this.loading.active = false;
          errorToast(
            "Algum erro aconteceu!",
            "Não foi possível carregar a lista de produtors, entre em contato com o suporte."
          );
          return;
        }

        res.data.products.forEach((product) => {
          this.filter.product.options[product.id] = product.name;
        });
      } catch (e) {
        this.loading.active = false;
        errorToast(
          "Algum erro aconteceu!",
          "Não foi possível carregar a lista de produtors, entre em contato com o suporte."
        );
      }
    },
    /** Change Data on filter */
    changeFilter: async function () {
      this.pagination.currentPage = 1;
      await this.getData();
    },
    /** Change date */
    changeDate: async function (date, property) {
      date[0] !== null && date[1] !== null
        ? (this.filter.datePickers[property].formated = `${moment(date[0]).format(
            "DD/MM/YYYY"
          )}-${moment(date[1]).format("DD/MM/YYYY")}`)
        : (this.filter.datePickers[property].formated = null);
      this.pagination.currentPage = 1;
      await this.getData();
    },
    /** Open subscription modal */
    subscriptionModal: function (data) {
      this.modal.active = true;
      this.modal.data = data;
    },
  },
  async created() {
    this.loading.active = true;
    await this.getData();
    await this.getProducts();
    this.loading.active = false;
  },
};
</script>

<style lang="scss" scoped>
.slot-header {
  padding-bottom: 24px;
  border-bottom: 1px solid #414655;
}

.title-filter {
  font-size: 1rem;
  font-weight: 700;
  line-height: 1.625rem;

  .fas {
    color: #addf45;
  }
}
</style>
