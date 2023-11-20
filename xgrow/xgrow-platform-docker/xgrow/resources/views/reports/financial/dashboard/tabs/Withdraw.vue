<template>
  <div class="xgrow-card card-dark">
    <Table :id="'subscriptions'">
      <template v-slot:title>
        <div class="w-100">
          <h5 class="mb-2">
            Saques: {{ pagination.totalResults }} saque{{ results.length > 1 ? "s" : "" }}
          </h5>
          <span class="subtitle">Veja todos os seus saques realizados.</span>
          <hr class="hr-line" />
        </div>
      </template>
      <template v-slot:header>
        <th>Transação</th>
        <th>Valor</th>
        <th>Data</th>
        <th>Status</th>
      </template>
      <template v-slot:body v-if="results.length > 0">
        <tr v-for="item in results" :key="item.created_at">
          <td>{{ item.id }}</td>
          <td>{{ formatBRLCurrency(item.amount / 100) }}</td>
          <td>{{ formatDateTimeSingleLine(item.created_at) }}</td>
          <td><Badge :status="item.status" /></td>
        </tr>
      </template>
      <template v-slot:body v-else>
        <tr>
          <td colspan="12" class="xgrow-no-content">Não há dados a serem exibidos.</td>
        </tr>
      </template>
      <template v-slot:footer>
        <Pagination
          :show-change-limit="false"
          :total-pages="pagination.totalPages"
          :total="pagination.totalResults"
          :current-page="pagination.currentPage"
          :limitItens="pagination.limit"
          @page-changed="onPageChange"
          @limit-changed="onLimitChange"
        >
        </Pagination>
      </template>
    </Table>
    <Loading :is-open="loading.active" status="loading" />
  </div>
</template>

<script>
import Table from "../../../../../js/components/Datatables/Table";
import Pagination from "../../../../../js/components/Datatables/Pagination";
import Badge from "../../../../../js/components/XgrowDesignSystem/Badge/StatusBadge";
import Loading from "../../../../../js/components/StatusModalComponent";

import formatBRLCurrency from "../../../../../js/components/XgrowDesignSystem/Mixins/formatBRLCurrency";
import formatDateTimeSingleLine from "../../../../../js/components/XgrowDesignSystem/Mixins/formatDateTimeSingleLine";

import axios from "axios";

export default {
  components: {
    Table,
    Pagination,
    Badge,
    Loading,
  },
  mixins: [formatBRLCurrency, formatDateTimeSingleLine],
  data() {
    return {
      loading: {
        active: false,
      },
      results: [],
      pagination: {
        totalPages: 1,
        totalResults: 0,
        currentPage: 1,
        limit: 25,
      },
    };
  },
  methods: {
    async getDataTableWithdraw() {
      this.results = [];

      const params = {
        length: this.pagination.limit,
        page: this.pagination.currentPage,
      };

      try {
        const { data } = await axios.get("/api/reports/financial/withdrawal-data", {
          params,
        });

        const response = data;
        this.results = response.data;
        this.pagination.totalResults = response.recordsTotal;
        this.pagination.totalPages = response.totalPages;
      } catch (error) {
        errorToast("Atenção!", "withdraw-data error");
      }
    },
    onPageChange: async function (page) {
      this.loading.active = true;

      this.pagination.currentPage = page;
      await this.getDataTableWithdraw();

      this.loading.active = false;
    },
    /** Limit by size itens */
    onLimitChange: async function (value) {
      this.loading.active = true;

      this.pagination.limit = parseInt(value);
      this.pagination.currentPage = 1;
      await this.getDataTableWithdraw();

      this.loading.active = false;
    },
  },
  async mounted() {
    await this.getDataTableWithdraw();
  },
};
</script>

<style>
td {
  height: 64px;
}

.subtitle {
  font-weight: 300;
}

.hr-line {
  margin-top: 2rem;
  margin-bottom: 0px;
}
</style>
