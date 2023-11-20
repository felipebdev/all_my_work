<template>
  <Table id="sendingEmailTable">
    <template v-slot:title>
      <div class="xgrow-table-header w-100">
        <h5 class="title mt-3">Histórico de pagamentos: {{ pagination.totalResults }}</h5>
        <p>Veja os detalhes dos pagamentos realizados pelo aluno.</p>
      </div>
    </template>
    <template v-slot:header>
      <th>Pedido</th>
      <th>Transação</th>
      <th>Produto</th>
      <th>Dt. Cobrança</th>
      <th>Método</th>
      <th>Origem</th>
      <th>Valor total</th>
      <th>Valor líquido</th>
      <th>Status</th>
      <th></th>
    </template>
    <template v-if="payments.length > 0" v-slot:body>
      <tr :key="payment.payment_order_number" v-for="payment in payments">
        <td>{{ payment.payment_order_number }}</td>
        <td>{{ payment.payment_order_code }}</td>
        <td>
          <b>{{ payment.products_name }}</b> <br />
          {{ payment.plans_name }}
        </td>
        <td>{{ formatDateSingleLine(payment.payment_date) }}</td>
        <td>{{ payment.payment_type }}</td>
        <td>{{ payment.payment_source ?? "-" }}</td>
        <td>{{ formatBRLCurrency(payment.payment_plan_plan_value) }}</td>
        <td>{{ formatBRLCurrency(payment.payment_plan_customer_value) }}</td>
        <td>
          <StatusBadge :status="payment.payment_status" />
        </td>
        <td>
          <DropdownButton :id="payment.payment_order_number" :items="getActions()" />
        </td>
      </tr>
    </template>
    <template v-else v-slot:body>
      <tr>
        <td colspan="9" class="text-center">Sem resultados.</td>
      </tr>
    </template>
    <template v-slot:footer>
      <Pagination
        class="mt-4"
        :total-pages="pagination.totalPages"
        :total="pagination.totalResults"
        :current-page="pagination.currentPage"
        @page-changed="(page) => paginationChange('currentPage', page)"
        @limit-changed="(page) => paginationChange('limit', page)"
      />
    </template>
  </Table>
  <Loading :is-open="loading" status="loading" />
</template>

<script>
import Table from "../../../js/components/Datatables/Table";
import Loading from "../../../js/components/StatusModalComponent";
import Pagination from "../../../js/components/Datatables/Pagination";
import StatusBadge from "../../../js/components/XgrowDesignSystem/Badge/StatusBadge";
import DropdownButton from "../../../js/components/XgrowDesignSystem/Buttons/DropdownButtonV2";

import formatDateSingleLine from "../../../js/components/XgrowDesignSystem/Mixins/formatDateSingleLine";
import formatTryCatchError from "../../../js/components/XgrowDesignSystem/Mixins/formatTryCatchError.js";
import formatBRLCurrency from "../../../js/components/XgrowDesignSystem/Mixins/formatBRLCurrency.js";
import axios from "axios";

export default {
  components: {
    Table,
    Loading,
    Pagination,
    StatusBadge,
    DropdownButton,
  },
  mixins: [formatDateSingleLine, formatBRLCurrency],
  data() {
    return {
      loading: false,
      payments: [],
      pagination: {
        totalPages: 1,
        totalResults: 0,
        currentPage: 1,
        limit: 25,
      },
    };
  },
  methods: {
    async getPayments() {
      try {
        this.loading = true;

        const url = getUserPaymentHistoryURL.replace(":id", this.$route.params.id);
        const res = await axios.get(url, {
          params: {
            page: this.pagination.currentPage,
            offset: this.pagination.limit,
          }
        });
        const {payments} = res.data.response;
        this.payments = payments.data;

        this.pagination.totalPages = payments.last_page;
        this.pagination.totalResults = payments.total;
        this.pagination.currentPage = payments.current_page;

        this.loading = false;
      } catch (error) {
        this.loading = false;
        errorToast('Atenção!', formatTryCatchError(error))
      }
    },
    async paginationChange(type, page) {
      this.pagination[type] = parseInt(page);
      await this.getPayments();
    },
    getActions() {
      const result = [
        {
          name: "Estornar apenas este pagamento",
          ico: "fa-solid fa-clock-rotate-left red",
          url: "#",
        },
        {
          name: "Estornar pagamento",
          ico: "fa-solid fa-clock-rotate-left red",
          url: "#",
        },
        {
          name: "Estornar todos pagamentos da recorrência",
          ico: "fa-solid fa-clock-rotate-left red",
          url: "#",
        },
        {
          name: "Reenviar comprovante de estorno",
          ico: "fas fa-paper-plane",
          url: "#",
        },
        {
          name: "Baixar comprovante de estorno",
          ico: "fa-solid fa-download",
          url: "#",
        },
        {
          name: "Reenviar comprovante de compra",
          ico: "fas fa-paper-plane",
          url: "#",
        },
        {
          name: "Reenviar boleto",
          ico: "fas fa-paper-plane",
          url: "#",
        },
      ];

      return result;
    },
  },
  async mounted() {
    await this.getPayments();
  },
};
</script>

<style>
.table-responsive {
  overflow-x: hidden !important;
}
</style>
