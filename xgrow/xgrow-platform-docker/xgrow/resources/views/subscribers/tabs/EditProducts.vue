<template>
  <div>
    <Table id="subscriberProductsTable">
      <template v-slot:title>
        <div class="xgrow-table-header w-100">
          <h5 class="title mt-3">Produtos: {{ pagination.totalResults }}</h5>
          <p>Veja todos os produtos vinculados ao aluno ou adicione novos.</p>
        </div>
      </template>
      <template v-slot:header>
        <th>Pedido</th>
        <th>Produto</th>
        <th>Data de cadastro</th>
        <th>Data de cancelamento</th>
        <th>Status</th>
        <th></th>
      </template>
      <template v-if="products.length > 0" v-slot:body>
        <tr :key="product.payment_order_number" v-for="product in products">
          <td>{{ product.payment_order_number ?? '-' }}</td>
          <td>
            <a :href="`/products/${product.product_id}/plans`">
              <b>{{ product.product_name }}</b>
              <p>{{ product.plans_name }}</p>
            </a>
          </td>
          <td v-html="formatDateTimeDualLine(product.subscriptions_created_at)"></td>
          <td v-html="formatDateTimeDualLine(product.subscriptions_canceled_at)"></td>
          <td class="mt-2 d-flex gap-2 align-items-center">
            <StatusBadge :status="product.subscriptions_status" />
            <i
              v-show="product.subscriptions_status === 'canceled'"
              class="fas fa-info-circle"
              style="color: var(--green4)"
              :title="product.payment_cancellation_reason ?? 'Não foi informado o motivo do cancelamento.'"
            ></i>
          </td>
          <td>
            <DropdownButton
              :id="product.payment_order_number"
              :items="getActions(product)"
            />
          </td>
        </tr>
      </template>
      <template v-else v-slot:body>
        <tr>
          <td colspan="6" class="text-center">Sem resultados.</td>
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
    <ChangeProductStatusVue
      :is-open="changeStatusModal.isOpen"
      :subscription-id="changeStatusModal.subscriptionId"
      :plan-id="changeStatusModal.planId"
      :product-name="changeStatusModal.productName"
      :close="closeModal"
      :get-products="getProducts"
    />
    <CancelConfirm
      :is-open="cancelConfirmModal.isOpen"
      :product-name="cancelConfirmModal.productName"
      :subscription-id="cancelConfirmModal.subscriptionId"
      :subscription-type="cancelConfirmModal.subscriptionType"
      :subscriber-name="cancelConfirmModal.subscriberName"
      :product="cancelConfirmModal.product"
      :close="closeModal"
      :get-products="getProducts"
    />
    <CancelRefund
      :is-open="cancelRefundModal.isOpen"
      :product-name="cancelRefundModal.productName"
      :subscription-id="cancelRefundModal.subscriptionId"
      :subscriber-name="cancelRefundModal.subscriberName"
      :product="cancelRefundModal.product"
      :close="closeModal"
      :get-products="getProducts"
    />
    <Loading :is-open="loading" status="loading" />
  </div>
</template>

<script>
import Table from "../../../js/components/Datatables/Table";
import Loading from "../../../js/components/StatusModalComponent";
import Pagination from "../../../js/components/Datatables/Pagination";
import StatusBadge from "../../../js/components/XgrowDesignSystem/Badge/StatusBadge";
import DropdownButton from "../../../js/components/XgrowDesignSystem/Buttons/DropdownButtonV2";
import ChangeProductStatusVue from "../modal/ChangeProductStatus.vue";
import CancelConfirm from "../modal/CancelConfirm.vue";
import CancelRefund from "../modal/CancelRefund.vue";
import formatDateTimeDualLine from "../../../js/components/XgrowDesignSystem/Mixins/formatDateTimeDualLine";
import axios from "axios";

import formatTryCatchError from "../../../js/components/XgrowDesignSystem/Mixins/formatTryCatchError.js";

export default {
  components: {
    CancelRefund,
    Loading,
    Table,
    Pagination,
    StatusBadge,
    DropdownButton,
    ChangeProductStatusVue,
    CancelConfirm
  },
  mixins: [formatDateTimeDualLine],
  data() {
    return {
      loading: false,
      changeStatusModal: {
        isOpen: false,
        subscriptionId: 0,
        planId: 0,
        productName: "",
      },
      cancelConfirmModal: {
        isOpen: false,
        productName: "",
        subscriptionId: 0,
        subscriptionType: ''
      },
      cancelRefundModal: {
        isOpen: false,
        productName: "",
        subscriptionId: 0,
      },
      products: [],
      pagination: {
        totalPages: 1,
        totalResults: 0,
        currentPage: 1,
        limit: 25,
      },
    };
  },
  methods: {
    async getProducts() {
      this.loading = true;
      const url = getUserProductsURL.replace(":id", this.$route.params.id);

      try {
        const { data } = await axios.get(url, {
          params: {
            page: this.pagination.currentPage,
            offset: this.pagination.limit,
          },
        });

        this.products = data.response.data.data;
        this.pagination.totalPages = data.response.data.last_page;
        this.pagination.totalResults = data.response.data.total;
        this.pagination.currentPage = data.response.data.current_page;
        this.loading = false;
      } catch (error) {
        errorToast("Atenção!", formatTryCatchError(error));
        this.loading = false;
      }
    },
    async paginationChange(type, page) {
      this.pagination[type] = parseInt(page);
      await this.getProducts();
    },
    openModal(product) {
      this.changeStatusModal.isOpen = true;
      this.changeStatusModal.planId = product.plan_id;
      this.changeStatusModal.subscriptionId = product.subscriptions_id;
      this.changeStatusModal.productName = product.plans_name;
    },
    openCancelModal(product) {
      const name = document.getElementById('user_name').value

      this.cancelConfirmModal.isOpen = true;
      this.cancelConfirmModal.productName = product.product_name;
      this.cancelConfirmModal.subscriptionId = product.subscriptions_id,
      this.cancelConfirmModal.subscriptionType = product.payment_type;
      this.cancelConfirmModal.subscriberName = name;
      this.cancelConfirmModal.product = product;
    },
    openCancelRefundModal(product) {
      const name = document.getElementById('user_name').value

      this.cancelRefundModal.isOpen = true;
      this.cancelRefundModal.productName = product.product_name;
      this.cancelRefundModal.subscriptionId = product.subscriptions_id,
      this.cancelRefundModal.subscriberName = name;
      this.cancelRefundModal.product = product;
    },

    closeModal() {
      this.changeStatusModal.isOpen = false;
      this.cancelRefundModal.isOpen = false;
      this.cancelConfirmModal.isOpen = false;
    },
    getActions(product) {
      const rules = this.getRules(product);

      return [
        {
          name: "Alterar Status",
          ico: "fa-solid fa-arrow-right-arrow-left",
          url: "#",
          callback: () =>
            this.openModal(product),
        },
        { name: "Cancelar", ico: "fa-solid fa-xmark red", url: "#", callback:() => this.openCancelModal(product), hide: !rules.showCancelProduct },
        { name: "Cancelar", ico: "fa-solid fa-xmark red", url: "#", callback:() => this.openCancelModal(product), hide: !rules.showCancelSignature },
        {
          name: "Cancelar e estornar",
          ico: "fa-solid fa-clock-rotate-left red",
          url: "#",
          callback: () => this.openCancelRefundModal(product),
          hide: !rules.showCancelRevert,
        },
        {
          name: "Reenviar comprovante de compra",
          ico: "fas fa-paper-plane",
          url: "#",
          callback: () => this.resendProofPurchase(product.payment_id),
          hide: !rules.showProofPurchase,
        },

        {
          name: "Reenviar boleto",
          ico: "fas fa-paper-plane",
          url: "#",
          callback: () => this.resendBillet(product.payment_id),
          hide: !rules.showReSendBillet,
        },

        {
          name: "Reenviar comprovante de estorno",
          ico: "fas fa-paper-plane",
          url: "#",
          callback: () => this.resendReversalReceipt(product.payment_plan_id),
          hide: !rules.showReversalReceipt,
        },
        {
          name: "Baixar comprovante de estorno",
          ico: "fa-solid fa-download",
          url: "#",
          callback: () => this.downloadReversalReceipt(product.payment_plan_id),
          hide: !rules.showReversalReceipt,
        },
      ];
    },
    getRules(product) {
      const cancelRule =
        product.subscriptions_canceled_at === null &&
        (product.payment_type === "P" || product.payment_type === "U") &&
        product.payment_status === "paid";

      return {
        showCancelProduct: cancelRule,
        showCancelSignature: product.payment_type === "R" && !product.subscriptions_canceled_at,

        showCancelRevert: cancelRule && product.payment_order_number !== null,

        showProofPurchase: product.payment_status === "paid",
        showReSendBillet: product.payment_type_payment === "boleto",

        showReversalReceipt:
          product.payment_type_payment === "credit_card" &&
          product.subscriptions_status === "canceled",
      };
    },
    async resendReversalReceipt(paymentPlanId) {
      try {
        this.loading = true;
        const url = resendReversalReceiptURL.replace(":id", paymentPlanId);

        const res = await axios.get(url);
        this.loading = false;
        successToast("Sucesso!", res.data.message);
      } catch (error) {
        this.loading = false;
        errorToast("Atenção!", formatTryCatchError(error));
      }
    },
    async resendProofPurchase(paymentId) {
      try {
        this.loading = true;
        const url = resendProofPurchaseURL.replace(":paymentId", paymentId);

        const res = await axios.get(url);
        this.loading = false;
        successToast("Sucesso!", res.data.message);
      } catch (error) {
        this.loading = false;
        errorToast("Atenção!", formatTryCatchError(error));
      }
    },
    async resendBillet(paymentId) {
      try {
        this.loading = true;
        const url = resendBilletURL.replace(":id", paymentId);

        const res = await axios.get(url);
        this.loading = false;
        successToast("Sucesso!", res.data.message);
      } catch (error) {
        this.loading = false;
        errorToast("Atenção!", formatTryCatchError(error));
      }
    },
    async downloadReversalReceipt(paymentPlanId) {
      try {
        this.loading = true;
        const url = downloadReversalReceiptURL.replace(":id", paymentPlanId);

        const res = await axios.get(url);
        this.loading = false;
        successToast("Sucesso!", res.data.message);
      } catch (error) {
        this.loading = false;
        errorToast("Atenção!", formatTryCatchError(error));
      }
    },
  },
  async mounted() {
    await this.getProducts();
  },
};
</script>

<style lang="scss">
.table-responsive {
  overflow-x: hidden !important;
}

#subscriberProductsTable {
  a {
    text-decoration: none !important;
    color: inherit;
  }
}
</style>
