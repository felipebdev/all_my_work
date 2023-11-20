<template>
  <div class="row mb-3">
    <div class="col-12 d-flex flex-column flex-lg-row gap-2 wrap overflow-auto py-2">
      <FinancialCard
        border-color="#adff2f"
        :title="metrics.paid || formatBRLCurrency(0)"
        subtitle="Pagos"
      >
        <XgrowTooltip
          id="nolimitpaid"
          icon='<i class="fas fa-info-circle"></i>'
          tooltip="Total das parcelas pagas"
        />
      </FinancialCard>

      <FinancialCard
        border-color="#24cefe"
        :title="metrics.to_receive || formatBRLCurrency(0)"
        subtitle="A receber"
      >
        <XgrowTooltip
          id="nolimitto_receive"
          icon='<i class="fas fa-info-circle"></i>'
          tooltip="Total das parcelas futuras a receber"
        />
      </FinancialCard>

      <FinancialCard
        border-color="#ffff00"
        :title="metrics.late || formatBRLCurrency(0)"
        subtitle="Atrasados"
      >
        <XgrowTooltip
          id="nolimitlate"
          icon='<i class="fas fa-info-circle"></i>'
          tooltip="Total das parcelas que ainda não foram pagas"
        />
      </FinancialCard>

      <FinancialCard
        border-color="#e22222"
        :title="metrics.canceled || formatBRLCurrency(0)"
        subtitle="Cancelados"
      >
        <XgrowTooltip
          id="nolimitcanceled"
          icon='<i class="fas fa-info-circle"></i>'
          tooltip="Pagamentos futuros sem limite que foram canceladas"
        />
      </FinancialCard>

      <FinancialCard
        border-color="#f5f6fa"
        :title="metrics.chargeback || formatBRLCurrency(0)"
        subtitle="Chargeback"
      >
        <XgrowTooltip
          id="nolimitchargeback"
          icon='<i class="fas fa-info-circle"></i>'
          tooltip="Pagamentos estornados direto pelo banco"
        />
      </FinancialCard>
    </div>
  </div>

  <div class="xgrow-card card-dark">
    <XgrowTable id="salesNolimiteTable">
      <template v-slot:title>
        <div
          class="d-flex align-items-center gap-1 flex-wrap justify-content-md-between w-100 slot-header"
        >
          <div class="xgrow-table-header">
            <h5 class="title">
              Sem limite: {{ pagination.totalResults }}
              <span v-show="filterTitle">| </span>
              <span class="filters" v-html="filterTitle" /><br />
            </h5>
            <span>Veja todas as suas recorrências sem limite.</span>
          </div>
        </div>
        <div
          class="d-flex align-items-center justify-content-between py-2 gap-2 flex-wrap w-100"
        >
          <div class="d-flex gap-3 align-items-center flex-wrap">
            <xgrow-input
              id="search-field"
              placeholder="Pesquise nome ou e-mail..."
              icon="<i class='fas fa-search'></i>"
              iconColor="#93BC1E"
              v-model="filter.searchValue"
              style="min-width: 200px; max-width: 310px"
            />
            <FilterButton target="filterNoLimiteSales" />
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
        <div class="mb-3 collapse" id="filterNoLimiteSales">
          <div class="filter-container">
            <div class="p-2 px-3">
              <div class="row">
                <div class="col-sm-12 col-md-12 my-2">
                  <p class="title-filter">
                    <i class="fas fa-filter"></i> Filtros avançados
                  </p>
                </div>
                <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                  <div class="xgrow-form-control mb-2">
                    <xgrow-multiselect
                      :options="filter.product.options"
                      v-model="filter.product.selected"
                      :searchable="true"
                      mode="tags"
                      placeholder="Produto"
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
                <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                  <div class="xgrow-form-control mb-2">
                    <xgrow-multiselect
                      :options="filter.statusSubscription.options"
                      v-model="filter.statusSubscription.selected"
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
                <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                  <div class="xgrow-form-control mb-2">
                    <xgrow-multiselect
                      :options="filter.statusPayment.options"
                      v-model="filter.statusPayment.selected"
                      :searchable="true"
                      mode="tags"
                      placeholder="Status do pagamento"
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
                <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                  <xgrow-daterange-component
                    v-model:value="filter.daterangeAccession.value"
                    format="DD/MM/YYYY"
                    :clearable="true"
                    type="date"
                    range
                    placeholder="Data de adesão"
                    @change="searchByDate($event, 'daterangeAccession')"
                  />
                </div>
                <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                  <xgrow-daterange-component
                    v-model:value="filter.daterangeCancel.value"
                    format="DD/MM/YYYY"
                    :clearable="true"
                    type="date"
                    range
                    placeholder="Data de cancelamento"
                    @change="searchByDate($event, 'daterangeCancel')"
                  />
                </div>
                <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                  <xgrow-daterange-component
                    v-model:value="filter.daterangeLast.value"
                    format="DD/MM/YYYY"
                    :clearable="true"
                    type="date"
                    range
                    placeholder="Data do último pagamento"
                    @change="searchByDate($event, 'daterangeLast')"
                  />
                </div>
                <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                  <xgrow-daterange-component
                    v-model:value="filter.daterangeBilling.value"
                    format="DD/MM/YYYY"
                    :clearable="true"
                    type="date"
                    range
                    placeholder="Data de cobrança"
                    @change="searchByDate($event, 'daterangeBilling')"
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
        <th>Parcelas pagas</th>
        <th>Dt. adesão</th>
        <th>Dt. cobrança</th>
        <th>Dt. cancelamento</th>
        <th>Último pgto</th>
        <th>Status da assinatura</th>
        <th>Status do pagamento</th>
        <th></th>
      </template>
      <template v-slot:body>
        <NoLimitTableRowComponent
          v-for="(transaction, idx) in results"
          :key="idx"
          :transaction="transaction"
          :modal-function="openModal"
          @cancel-recurrence="cancelRecurrence"
        >
        </NoLimitTableRowComponent>
        <tr v-if="results.length < 1">
          <td colspan="13" class="text-center">Não há dados a serem exibidos</td>
        </tr>
      </template>
      <template v-slot:footer>
        <xgrow-pagination-component
          class="mt-4"
          :total-pages="pagination.totalPages"
          :total="pagination.totalResults"
          :current-page="pagination.currentPage"
          @page-changed="onPageChange"
          @limit-changed="onLimitChange"
        >
        </xgrow-pagination-component>
      </template>
    </XgrowTable>
  </div>

  <NoLimitModalComponent
    :is-open="isOpen && modal === null"
    :close-function="closeModal"
    :modal-data="modalData"
  >
  </NoLimitModalComponent>
  <cancel-recurrence-modal
    :is-open="isOpen && modal === 'cancel-recurrence'"
    :close-function="closeRModal"
    :modal-data="modalData"
  >
  </cancel-recurrence-modal>
</template>

<script>
import Pagination from "../../../../js/components/Datatables/Pagination.vue";
import Input from "../../../../js/components/XgrowDesignSystem/Input.vue";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import DatePicker from "vue-datepicker-next";
import "vue-datepicker-next/index.css";
import "vue-datepicker-next/locale/pt-br";
import NoLimitTableRowComponent from "./NoLimitTableRowComponent.vue";
import NoLimitModalComponent from "./modal/NoLimitModalComponent.vue";
import axios from "axios";
import moment from "moment";
import CancelRecurrenceModal from "./modal/tabs/CancelRecurrenceModal";
import FinancialCard from "../../../../js/components/XgrowDesignSystem/Cards/FinancialCard";
import XgrowTable from "../../../../js/components/Datatables/Table";
import FilterButton from "../../../../js/components/XgrowDesignSystem/Buttons/FilterButton";
import ExportLabel from "../../../../js/components/XgrowDesignSystem/Utils/ExportLabel";
import IconButton from "../../../../js/components/XgrowDesignSystem/Buttons/IconButton";
import formatBRLCurrency from "../../../../js/components/XgrowDesignSystem/Mixins/formatBRLCurrency";
import XgrowTooltip from "../../../../js/components/XgrowDesignSystem/Tooltips/XgrowTooltip";

export default {
  name: "no-limit-component",
  mixins: [formatBRLCurrency],
  components: {
    XgrowTooltip,
    IconButton,
    ExportLabel,
    FilterButton,
    XgrowTable,
    FinancialCard,
    NoLimitTableRowComponent,
    "cancel-recurrence-modal": CancelRecurrenceModal,
    "xgrow-pagination-component": Pagination,
    "xgrow-input": Input,
    "xgrow-multiselect": Multiselect,
    "xgrow-daterange-component": DatePicker,
    NoLimitModalComponent,
  },
  emits: ["startLoading", "endLoading"],
  computed: {

    filterTitle() {
      let filterTitle = "";

      const filters = [
        { id: "product", name: "Produtos", type: "list"},
        { id: "statusSubscription", name: "Status da assinatura", type: "list" },
        { id: "statusPayment", name: "Status do Pagamento", type: "list" },
        { id: "daterangeAccession", name: "Data de adesão", type: "date" },
        { id: "daterangeCancel", name: "Data de cancelamento", type: "date" },
        { id: "daterangeLast", name: "Data do último pagamento", type: "date" },
        { id: "daterangeBilling", name: "Data de cobrança", type: "date" },
      ];

      filters.map((filter) => {
        const selectedFilter = this.filter[filter.id];

        if (filter.type === "list" && selectedFilter.selected.length > 0) {
          const items = selectedFilter.selected.map((value) => {
            return ` <span class="filters-tags">${selectedFilter.options[value]}</span>`;
          });

          filterTitle = filterTitle.length > 0 ? filterTitle + " | " : filterTitle;
          filterTitle += `${filter.name}: ${items.join(" ")}`;
        } else if (filter.type === "date" && selectedFilter.formated) {
          const dateRange = ` <span class="filters-tags">${selectedFilter.formated}</span>`;

          filterTitle = filterTitle.length > 0 ? filterTitle + " | " : filterTitle;
          filterTitle += `${filter.name}: ${dateRange}`;
        }
      });

      if (filterTitle.length > 0) {
        filterTitle = '<i class="fas fa-filter" style="color: #addf45;"></i> ' + filterTitle;
      }

      return filterTitle;
    },
  },
  data() {
    return {
      /** Modal */
      isOpen: false,
      modalData: {},

      /** Metrics */
      metrics: {},

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
        product: {
          options: {},
          selected: [],
        },
        statusSubscription: {
          options: {
            active: "Ativo",
            canceled: "Cancelado",
            pending: "Pendente",
            failed: "Falha no pagamento",
          },
          selected: []
        },
        statusPayment: {
          options: {
            paid: 'Pago',
            pending: 'Pendente',
            canceled: 'Cancelado',
            failed: 'Falha no pagamento',
            chargeback: 'Chargeback',
            expired: 'Expirado',
            refunded: 'Estornado',
            pending_refund: 'Estorno pendente',
          },
          selected: []
        },
        method: {
          options: {
            boleto: "Boleto",
            credit_card: "Cartão de Crédito",
            pix: "Pix",
          },
          selected: [],
        },
        daterangeAccession: {
          value: null,
          formated: null,
        },
        daterangeCancel: {
          value: null,
          formated: null,
        },
        daterangeLast: {
          value: null,
          formated: null,
        },
        daterangeBilling: {
          value: null,
          formated: null,
        },
        showCouponOnly: false,
      },

      /** Mark if component already load */
      alreadyLoaded: false,
      modal: null,
    };
  },
  watch: {
    "filter.searchValue": function () {
      this.searchByTerm();
    },
  },
  methods: {
    /** Modal Functions */
    async openModal(nolimit) {
      const { subscribers_id, plan_id, payment_order_number } = nolimit;
      let requestURL = `${getNoLimitURL}/${subscribers_id}/${plan_id}/${payment_order_number}`;

      this.$emit("startLoading");

      try {
        const res = await axios.get(requestURL);

        if (res.data.error === true) {
          this.$emit("endLoading");
          errorToast(
            "Algum erro aconteceu!",
            "Não foi possível carregar os dados da transação, entre em contato com o suporte."
          );
          return;
        }

        this.modalData = res.data.response[0].response;
        this.$emit("endLoading");
        this.isOpen = true;
      } catch (error) {
        this.$emit("endLoading");
        errorToast(
          "Algum erro aconteceu!",
          "Não foi possível carregar os dados da transação, entre em contato com o suporte."
        );
      }
    },
    closeModal: function () {
      this.modalData = {};
      this.isOpen = false;
    },
    closeRModal: function () {
      this.modalData = {};
      this.isOpen = false;
      this.modal = null;
    },
    /** On change page */
    onPageChange: async function (page) {
      this.pagination.currentPage = page;

      this.$emit("startLoading");
      await this.getNoLimit();
      this.$emit("endLoading");
    },
    /** Limit by size itens */
    onLimitChange: async function (value) {
      this.pagination.currentPage = 1;
      this.pagination.limit = value;

      this.$emit("startLoading");
      await this.getNoLimit();
      this.$emit("endLoading");
    },

    /** No Limit Data */
    async getNoLimit() {
      let url = getNoLimitURL;

      const params = {
        productsId: this.filter.product.selected,
        statusSubscription: this.filter.statusSubscription.selected,
        statusPayment: this.filter.statusPayment.selected,
        typePayment: this.filter.method.selected,
        searchTerm: this.filter.searchValue,
        offset: this.pagination.limit,
        page: this.pagination.currentPage,
        periodAccession: this.filter.daterangeAccession.formated,
        periodCancel: this.filter.daterangeCancel.formated,
        periodLastPayment: this.filter.daterangeLast.formated,
        periodBillingDate: this.filter.daterangeBilling.formated,
      };

      try {
        const res = await axios.get(url, { params });

        if (res.data.error === true) {
          this.$emit("endLoading");
          errorToast(
            "Algum erro aconteceu!",
            "Não foi possível carregar os dados das transações, entre em contato com o suporte."
          );
          return;
        }

        const data = res.data.response[0].response;

        /** Populate metrics */
        this.metrics = data.metrics;

        /** Pagination */
        this.pagination.totalPages = data.rows.last_page;
        this.pagination.totalResults = data.rows.total;
        this.pagination.currentPage = data.rows.current_page;
        this.pagination.limit = data.rows.per_page;

        /** Populate results */
        this.results = data.rows.data;
      } catch (error) {
        this.$emit("endLoading");
        errorToast(
          "Algum erro aconteceu!",
          "Não foi possível carregar os dados das transações, entre em contato com o suporte."
        );
      }
    },

    /** Filters Data and Functions */
    getProducts: async function () {
      await axios
        .get(getProductsList)
        .then((res) => {
          if (res.data.status !== "success") {
            this.$emit("endLoading");
            errorToast(
              "Algum erro aconteceu!",
              "Não foi possível carregar a lista de produtors, entre em contato com o suporte."
            );
            return;
          }

          res.data.products.forEach((product) => {
            this.filter.product.options[product.id] = product.name;
          });
        })
        .catch((error) => {
          this.$emit("endLoading");
          errorToast(
            "Algum erro aconteceu!",
            "Não foi possível carregar a lista de produtors, entre em contato com o suporte."
          );
        });
    },
    changeFilter: async function (value, property) {
      this.$emit("startLoading");
      this.pagination.currentPage = 1;
      await this.getNoLimit();
      this.$emit("endLoading");
    },
    searchByTerm: async function () {
      const term = this.filter.searchValue;
      setTimeout(async () => {
        if (term === this.filter.searchValue) {
          this.$emit("startLoading");
          await this.getNoLimit();
          this.$emit("endLoading");
        }
      }, 1000);
    },
    searchByDate: async function (date, property) {
      if (date[0] !== null && date[1] !== null) {
        this.filter[property].formated = `${moment(date[0]).format(
          "DD/MM/YYYY"
        )}-${moment(date[1]).format("DD/MM/YYYY")}`;
      } else {
        this.filter[property].formated = null;
      }

      this.$emit("startLoading");
      this.pagination.currentPage = 1;
      await this.getNoLimit();
      this.$emit("endLoading");
    },

    /** Execute every time a component is loaded */
    async loadData() {
      this.$emit("startLoading");
      await this.getProducts();
      await this.getNoLimit();
      this.$emit("endLoading");
    },

    /** Export Report Function */
    async exportReport(typeFile) {
      infoToast(
        "Gerando relatório...",
        "Acompanhe o status do arquivo em Relatórios > Listas Exportadas."
      );
      let url = generateReportURL;

      try {
        const res = await axios.get(url, {
          params: {
            reportName: "nolimit",
            typeFile: typeFile,
            productsId: this.filter.product.selected,
            statusSubscription: this.filter.statusSubscription.selected,
            statusPayment: this.filter.statusPayment.selected,
            typePayment: this.filter.method.selected,
            searchTerm: this.filter.searchValue,
            periodAccession: this.filter.daterangeAccession.formated,
            periodCancel: this.filter.daterangeCancel.formated,
            periodLastPayment: this.filter.daterangeLast.formated,
            periodBillingDate: this.filter.daterangeBilling.formated,
          },
        });

        successToast(
          "Relatório gerado!",
          "Você pode acessar o arquivo gerado em Relatórios > Listas Exportadas."
        );
      } catch (error) {
        errorToast(
          "Algum erro aconteceu!",
          "Ocorreu um erro inesperado, por favor contate o suporte."
        );
      }
    },
    /** Cancel recurrence */
    cancelRecurrence(transaction) {
      this.isOpen = true;
      this.modal = "cancel-recurrence";
      this.modalData = transaction;
    },
  },
};
</script>
<style>
.filters-tags {
  background: var(--green1) !important;
  display: inline-block;
  font-size: 14px;
  padding: 2px 4px;
  background: var(--green1);
  border-radius: 4px;
  font-weight: 400;
}
</style>
<style scoped lang="scss">
:deep(.form-group) {
  #search-field {
    height: 40px;
  }

  span {
    top: 7px !important;
  }
}

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

.filters {
  font-size: 16px;
}
</style>
