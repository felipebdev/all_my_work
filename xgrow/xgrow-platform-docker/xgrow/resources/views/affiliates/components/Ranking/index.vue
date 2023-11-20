<template>
  <div class="xgrow-card card-dark py-4">
    <Table id="salesTransactionsTable">
      <template v-slot:title>
        <div class="xgrow-table-header w-100">
          <h5 class="title">Ranking dos afiliados: {{ pagination.totalResults }}</h5>
          <p>Veja em detalhes o ranking de afiliados.</p>
          <hr />
        </div>
      </template>
      <template v-slot:filter>
        <div class="d-flex my-3 flex-wrap gap-3 align-items-end justify-content-between">
          <div class="d-flex gap-3 align-items-end flex-wrap">
            <Input
              id="searchIpt"
              icon="<i class='fa fa-search'></i>"
              placeholder="Pesquise pelo nome ou e-mail do afiliado..."
              v-model="filter.search"
              class="search-input"/>
          </div>
        </div>
      </template>
      <template v-slot:header>
        <th>Posição</th>
        <th>Nome</th>
        <th>Nº de vendas</th>
        <th>Valor em comissões</th>
        <th style="width: 60px;"></th>
      </template>
      <template v-if="affiliates.length > 0" v-slot:body>
        <tr
          :key="affiliate.affiliate_id"
          v-for="(affiliate, index) in affiliates"
        >
          <td>{{ affiliate.ranking }}º</td>
          <td>
            <b
              ><u>{{ affiliate.affiliate_name }}</u></b
            >
            <br />{{ affiliate.affiliate_email }}
          </td>

          <td>
            {{ affiliate.number_transactions }}
          </td>
          <td>
            {{ formatBRLCurrency(affiliate.commission_amount) }}
          </td>
          <td>
            <div class="dropdown x-dropdown">
              <button
                class="xgrow-button xgrow-button-action table-action-button m-1"
                type="button"
                id="dropdownMenuButton${row.id}"
                data-bs-toggle="dropdown"
              >
                <i class="fas fa-ellipsis-v"></i>
              </button>
              <ul
                class="dropdown-menu table-menu"
                aria-labelledby="dropdownMenuButton${row.id}"
              >
                <li role="button" class="dropdown-item table-menu-item"
                  @click="showUser(affiliate.affiliate_id)">
                  <i class="affiliate-icon fa fa-eye"></i>
                  Ver dados do afiliado
                </li>
              </ul>
            </div>
          </td>
        </tr>
      </template>
      <template v-else v-slot:body>
        <tr>
          <td colspan="5" class="text-center">Não há registros.</td>
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
        />
      </template>
    </Table>

  </div>
</template>

<script>
import formatBRLCurrency from '../../../../js/components/XgrowDesignSystem/Mixins/formatBRLCurrency'
import Subtitle from "../../../../js/components/XgrowDesignSystem/Typography/Subtitle";
import Modal from "../../../../js/components/XgrowDesignSystem/Modals/Modal.vue";
import Input from "../../../../js/components/XgrowDesignSystem/Form/Input";
import Pagination from "../../../../js/components/Datatables/Pagination";
import Table from "../../../../js/components/Datatables/Table";
import axios from "axios";


export default {
  name: "AffiliatesActive",
  components: {
    Table,
    Pagination,
    Modal,
    Input,
    Subtitle,
  },
  props: {
    showUser: {
      type: Function,
      default: () => {},
    },
  },
  mixins: [formatBRLCurrency],
  data() {
    return {
      pagination: {
        totalPages: 1,
        totalResults: 0,
        currentPage: 1,
        limit: 25,
      },
      loading: false,
      affiliates: [],
      filter: {
        search: "",
      },
    };
  },
  watch: {
    "filter.search": function () {
      this.searchByTerm();
    },
  },
  methods: {
    async getData() {
      this.$store.commit("setLoading", true);

      const response = await axios.get(urlRanking, {
        params: {
          page: this.pagination.currentPage,
          offset: this.pagination.limit,
          search: this.filter.search,
        },
      });

      this.affiliates = response.data.response.data;
      this.pagination.totalPages = response.data.response.last_page;
      this.pagination.totalResults = response.data.response.total;
      this.pagination.currentPage = response.data.response.current_page;

      this.$store.commit("setLoading", false);
    },
    onPageChange: async function (page) {
      this.pagination.currentPage = page;
      await this.getData();
    },
    onLimitChange: async function (value) {
      this.pagination.limit = parseInt(value);
      this.pagination.currentPage = 1;
      await this.getData();
    },
    async searchByTerm() {
      const term = this.filter.search;
      setTimeout(async () => {
        if (term === this.filter.search) {
          this.pagination.currentPage = 1;
          await this.getData();
        }
      }, 1000);
    },
  },
  async mounted() {
    await this.getData();
  },
};
</script>

<style lang="scss" src="./styles.scss" scoped></style>
<style lang="scss">
#advancedFilters {
  border-top: 1px solid rgba(255, 255, 255, 0.25);
  border-bottom: 1px solid rgba(255, 255, 255, 0.25);
  background: rgba(0, 0, 0, 0.2);
}

.cta {
  display: flex;
  justify-content: space-between;
  width: 100%;
}

.cancel {
  background: none;
  border: 2px solid;
}

.question {
  height: 77px;
  background: #9b9b9b;
  padding: 15px 30px;
  border-radius: 50%;
  color: #2a2e39;
  margin-bottom: 20px;
}

#searchIpt {
  width: 350px;
  height: 48px;
}

.form-group {
  margin: 0px !important;
}
</style>
