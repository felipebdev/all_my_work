<template>
  <div>
    <Breadcrumb :items="breadcrumbs" class="mb-3" />

    <Row class="pb-4">
      <Col sm="12" md="3" lg="3" xl=" 3">
        <Metric
          :title="
            Boolean(metrics.available)
              ? formatBRLCurrency(metrics.available / 100)
              : formatBRLCurrency(metrics.available)
          "
          subtitle="Disponível para saque"
          borderColor="#93BC1E"
        >
          <template v-slot:button>
            <Button
              style="padding: 6px 10px !important; margin-top: -23px"
              text="Sacar"
              status="success"
              :on-click="openWithdrawModal"
            />
          </template>
        </Metric>
      </Col>
      <Col sm="12" md="3" lg="3" xl="3">
        <Metric
          :title="
            Boolean(metrics.current)
              ? formatBRLCurrency(metrics.current / 100)
              : formatBRLCurrency(metrics.current)
          "
          id="pending"
          subtitle="Saldo atual"
          borderColor="#85F49E"
        />
      </Col>
      <Col sm="12" md="3" lg="3" xl="3">
        <Metric
          :title="
            Boolean(metrics.pending)
              ? formatBRLCurrency(metrics.pending / 100)
              : formatBRLCurrency(metrics.pending)
          "
          subtitle="A receber"
          borderColor="#E1BB32"
        />
      </Col>
    </Row>

    <Container>
      <template v-slot:content>
        <Table id="transactionsTable">
          <template v-slot:title>
            <div class="d-flex flex-column">
              <Row>
                <Title>Saques: {{ pagination.totalResults }}</Title>
              </Row>
              <Row>
                <Subtitle>Veja todos os seus saques realizados.</Subtitle>
              </Row>
            </div>
          </template>
          <template v-slot:header>
            <th colspan="2">Transação</th>
            <th v-for="header in ['Valor', 'Data', 'Status']" :key="header">
              {{ header }}
            </th>
          </template>
          <template v-slot:body v-if="Boolean(results[0]?.id)">
            <tr :key="`link-${item.id}`" v-for="item in results">
              <td colspan="2">{{ item.id }}</td>
              <td>{{ formatBRLCurrency(item.amount / 100) }}</td>
              <td v-html="formateDate(item.created_at)"></td>
              <td><Status :status="item.status" /></td>
            </tr>
          </template>
          <template v-else v-slot:body>
            <tr>
              <td colspan="11">Não há dados a serem exibidos.</td>
            </tr>
          </template>
          <template v-slot:footer>
            <Pagination
              :offset="this.pagination.offset"
              :totalPages="this.pagination.totalPages"
              :total="this.pagination.totalResults"
              :currentPage="this.pagination.currentPage"
              @limitChanged="onLimitChange"
              @pageChanged="onPageChange"
            />
          </template>
        </Table>
      </template>
    </Container>

    <Modal :is-open="modalWithdraw.isOpen" @close="closeWithdrawModal">
      <h3 class="modal__title">Realizar saque</h3>
      <hr class="modal__line" />
      <div class="d-flex flex-column">
        <p class="modal__subtitle">
          Confirme os dados bancários cadastrados no seu perfil e insira o valor que
          deseja sacar.
        </p>
        <div class="bank-data">
          <h4 class="bank-data__title bank-data__item">Dados bancários</h4>
          <div class="bank-data__item bank-data__item--dark">
            <p>Banco</p>
            <span> {{ recipient.account.bank }} </span>
          </div>
          <div class="bank-data__item">
            <p>Agência</p>
            <span>
              {{ recipient.account.branch_number }}
              {{
                recipient.account.branch_check_digit
                  ? "-" + recipient.account.branch_check_digit
                  : recipient.account.branch_check_digit
              }}
            </span>
          </div>
          <div class="bank-data__item bank-data__item--dark">
            <p>Conta</p>
            <span>
              {{ recipient.account.account_number }}-{{
                recipient.account.account_check_digit
              }}
            </span>
          </div>
          <div class="bank-data__item">
            <p>CPF/CNPJ</p>
            <span> {{ formatDocument(recipient.document) }} </span>
          </div>
        </div>

        <p id="withdraw-label" class="withdraw__available">
          Saldo disponível para saque:
          <span>{{
            Boolean(recipient.account.available)
              ? formatBRLCurrency(recipient.account.available / 100)
              : formatBRLCurrency(recipient.account.available)
          }}</span>
        </p>
        <Alert
            :title="'Saque indisponível'"
            status="warning"
            v-show="recipient.account.bank=='000'"
        >
            Sua conta bancária está em análise. Entre em contato com o suporte para solicitar a liberação.
        </Alert>
        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label w-100">
          <input
            id="withdraw_value"
            autocomplete="off"
            type="text"
            spellcheck="false"
            v-model="recipient.account.withdraw_value"
            v-maska="[
              '#,##',
              '##,##',
              '###,##',
              '#.###,##',
              '##.###,##',
              '###.###,##',
              '#.###.###,##',
              '##.###.###,##',
              '###.###.###,##',
            ]"
            @keyup="checkWithdrawAvailable()"
          />
          <label>Valor do saque</label>
        </div>

        <p class="withdraw__info">
          O valor estará disponível na conta no próximo dia útil após as 14h.
        </p>

        <hr class="modal__line mb-4" />
      </div>
      <div class="modal__actions">
        <button
          type="button"
          class="btn modal__actions--cancel"
          @click="closeWithdrawModal"
        >
          Cancelar
        </button>
        <button
          type="button"
          class="btn modal__actions--confirm"
          data-bs-dismiss="modal"
          aria-label="Close"
          :disabled="withdrawButtonDisabled"
          @click="openWithdrawConfirmationModal"
        >
          <i class="fa fa-check"> </i>
          Confirmar saque
        </button>
      </div>
    </Modal>

    <Modal
      :is-open="modalWithdrawConfirmation.isOpen"
      @close="closeWithdrawConfirmationModal"
    >
      <h3 class="modal__title">Confirmar saque</h3>
      <hr class="modal__line" />
      <div class="d-flex flex-column">
        <div class="bank-data">
          <h4 class="bank-data__title bank-data__item">Informações do saque</h4>
          <div class="bank-data__item bank-data__item--dark">
            <p>Valor requisitado</p>
            <span>
              {{ formatBRLCurrency(removeValueMask(recipient.account.withdraw_value)) }}
            </span>
          </div>
          <div class="bank-data__item">
            <p>Custo da transferência:</p>
            <span> {{ formatBRLCurrency(recipient.fee) }} </span>
          </div>
          <div class="bank-data__item bank-data__item--dark">
            <p>Valor a ser creditado na conta</p>
            <span>
              {{
                formatBRLCurrency(
                  removeValueMask(recipient.account.withdraw_value) - recipient.fee
                )
              }}
            </span>
          </div>
        </div>
      </div>
      <hr class="modal__line mt-2 mb-5" />
      <div class="modal__actions">
        <button
          type="button"
          class="btn modal__actions--cancel"
          @click="closeWithdrawConfirmationModal"
        >
          Cancelar
        </button>
        <button
          type="button"
          class="btn modal__actions--confirm"
          data-bs-dismiss="modal"
          aria-label="Close"
          @click="sendWithdraw"
        >
          <i class="fa fa-check"> </i>
          Efetuar saque
        </button>
      </div>
    </Modal>

    <Modal
      :is-open="modalWithdrawFeedback.isOpen"
      @close="closeWithdrawFeedbacktionModal"
    >
      <h3 class="modal__title">Saque realizado</h3>
      <hr class="modal__line" />
      <div class="d-flex flex-column">
        <p class="text-center" style="color: white; min-height: 119px">
          <span class="xgrow-large-bold">Eba!</span>
          O seu saque foi efetuado com sucesso. O valor estará disponível na sua conta no
          próximo dia útil a partir das 14:00 horas.
        </p>
      </div>
      <hr class="modal__line mb-4" />
      <div class="modal__actions">
        <button
          type="button"
          class="btn modal__actions--confirm"
          data-bs-dismiss="modal"
          aria-label="Close"
          @click="closeWithdrawFeedbacktionModal"
        >
          OK
        </button>
      </div>
    </Modal>

    <StatusModalComponent :is-open="loading" status="loading" />
  </div>
</template>

<script>
import axios from "axios";

import formatBRLCurrency from "../../js/components/XgrowDesignSystem/Mixins/formatBRLCurrency";
import formatDateTimeDualLine from "../../js/components/XgrowDesignSystem/Mixins/formatDateTimeDualLine";

import Container from "../../js/components/XgrowDesignSystem/Cards/Container";
import Metric from "../../js/components/XgrowDesignSystem/Cards/FinancialCard";
import Modal from "../../js/components/XgrowDesignSystem/Modals/Modal";
import Title from "../../js/components/XgrowDesignSystem/Typography/Title";
import Subtitle from "../../js/components/XgrowDesignSystem/Typography/Subtitle";
import Row from "../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../js/components/XgrowDesignSystem/Utils/Col";
import FilterButton from "../../js/components/XgrowDesignSystem/Buttons/FilterButton";
import Input from "../../js/components/XgrowDesignSystem/Form/Input";
import Button from "../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import Status from "../../js/components/XgrowDesignSystem/Badge/StatusBadge";
import Table from "../../js/components/Datatables/Table";
import Pagination from "../../js/components/Datatables/Pagination";
import ButtonDetail from "../../js/components/Datatables/ButtonDetail";
import StatusModalComponent from "../../js/components/StatusModalComponent";
import Breadcrumb from "../../js/components/XgrowDesignSystem/Breadcrumb/Breadcrumb";
import Alert from "../../js/components/XgrowDesignSystem/Alert/Alert";

import moment from "moment";
export default {
  name: "Index",
  components: {
    Breadcrumb,
    Container,
    Title,
    Subtitle,
    Row,
    Col,
    FilterButton,
    Input,
    Table,
    Pagination,
    Status,
    ButtonDetail,
    Metric,
    StatusModalComponent,
    Button,
    Modal,
    Alert,
  },
  mixins: [formatBRLCurrency, formatDateTimeDualLine],
  data() {
    return {
      breadcrumbs: [
        { title: "Área do afiliado", link: "/affiliations", isVueRouter: true },
        { title: "Produtos", link: "/affiliations/products", isVueRouter: true },
        { title: "Saques", link: "#", isVueRouter: true },
      ],
      loading: false,
      platformId: localStorage.getItem("affiliates-platform_id"),
      productId: localStorage.getItem("affiliates-product_id"),
      productName: localStorage.getItem("affiliates-product_name"),
      metrics: {
        available: 0,
        pending: 0,
        current: 0,
      },
      pagination: {
        totalPages: 1,
        totalResults: 0,
        currentPage: 1,
        offset: 25,
      },
      results: [],
      filter: {
        search: "",
      },
      recipient: {
        account: {
          branch_number: "",
          branch_check_digit: "",
          bank: "",
          account_check_digit: "",
          account_number: "",
          available: 0,
          withdraw_value: ""
        },
        document: "",
        type: "individual",
        fee: 3.67,
      },
      modalWithdraw: {
        isOpen: false,
      },
      modalWithdrawConfirmation: {
        isOpen: false,
      },
      modalWithdrawFeedback: {
        isOpen: false,
      },
      withdrawButtonDisabled: true,
    };
  },
  async mounted() {
    this.setMenu();

    if (!this.productId) {
      if (!this.platformId) {
        return this.$router.push("/affiliates");
      }

      this.$router.push("/affiliates/products");
    }

    await this.getData();
    await this.getBalance();
    await this.getRecipientInfo();
  },
  methods: {
    formateDate(date) {
      return moment(date).format("DD/MM/YYYY [<br>][ às ] HH:mm")
    },
    async getData() {
      this.loading = true;

      const url = affiliateWithdraws.replace("platform_id", this.platformId);
      const params = {
        offset: this.pagination.offset,
        page: this.pagination.currentPage,
      };

      try {
        const { data } = await axios.get(url, { params });
        const { response } = data;

        const transactions = response.data;

        this.results = transactions.data.map((transaction) => transaction);

        const totalPages = Math.ceil(transactions.total / transactions.per_page);

        this.pagination.totalPages = totalPages > 0 ? totalPages : 1;
        this.pagination.totalResults = transactions.total;
        this.pagination.currentPage = transactions.current_page;
        this.pagination.offset = transactions.per_page;
      } catch (e) {
        errorToast(
          "Atenção!",
          `Error na operação (getWithdraws) contate a equipe de suporte!`
        );
      } finally {
        this.loading = false;
      }
    },
    async getBalance() {
      this.loading = true;

      const url = affiliateBallance.replace("platform_id", this.platformId);

      try {
        const { data } = await axios.get(url);

        const { available, current, pending } = data.response.data;

        this.metrics = { available: available || 0, current: current || 0, pending: pending || 0 };
        this.recipient.account.available = available || 0
      } catch (e) {
        errorToast(
          "Atenção!",
          `Error na operação (getBalance) contate a equipe de suporte!`
        );
      } finally {
        this.loading = false;
      }
    },
    getRecipientInfo: async function () {
      try {
        const url = urlBankInformation.replace(/:platformId/g, this.platformId);
        const res = await axios.get(url);
        const bankDetails = res.data.response.data;

        this.recipient.document = bankDetails.document;
        this.recipient.account.account_check_digit = bankDetails.account_check_digit;
        this.recipient.account.account_number = bankDetails.account;
        this.recipient.account.bank = bankDetails.bank;
        this.recipient.account.branch_check_digit = bankDetails.branch_check_digit;
        this.recipient.account.branch_number = bankDetails.branch;
      } catch (e) {
        errorToast(
          "Atenção!",
          `Error na operação (getRecipientInfo) contate a equipe de suporte!`
        );
      }
    },
    async sendWithdraw() {
      this.loading = true;
      this.closeWithdrawConfirmationModal();

      const valueMinusTax = this.removeValueMask(this.recipient.account.withdraw_value) - this.recipient.fee;
      const totalWithdraw = Math.trunc(valueMinusTax * 100);

      try {
        const url = affiliateDoWithdraw.replace("platform_id", this.platformId);
        await axios.post(url, { amount: totalWithdraw });
        this.openWithdrawFeedbacktionModal();
        await this.getData();
        await this.getBalance();
        await this.getRecipientInfo();
      } catch (error) {
        errorToast("Atenção!", error.response.data.message);
      }
      this.recipient.account.withdraw_value = "";
      this.withdrawButtonDisabled = true;
      this.loading = false;
    },
    onLimitChange(limit) {
      this.pagination.offset = limit;
      this.getData();
    },
    onPageChange(page) {
      this.pagination.currentPage = page;
      this.getData();
    },
    removeValueMask: function (value) {
      if (value == "") return 0;

      try {
        value = value.replaceAll(".", "");
        value = value.replace(",", ".");
        return parseFloat(value);
      } catch {
        return 0;
      }
    },
    formatDocument(item) {
      if (!item) return;

      if (item.length > 11) {
        return item.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, "$1.$2.$3/$4-$5");
      }

      return item.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
    },
    openWithdrawModal: function () {
      this.modalWithdraw.isOpen = true;
    },
    closeWithdrawModal: function () {
      this.modalWithdraw.isOpen = false;
    },
    openWithdrawConfirmationModal: function () {
      this.closeWithdrawModal();
      this.modalWithdrawConfirmation.isOpen = true;
    },
    closeWithdrawConfirmationModal: function () {
      this.modalWithdrawConfirmation.isOpen = false;
    },
    openWithdrawFeedbacktionModal: function () {
      this.modalWithdrawFeedback.isOpen = true;
    },
    closeWithdrawFeedbacktionModal: function () {
      this.modalWithdrawFeedback.isOpen = false;
    },
    checkWithdrawAvailable () {
      const account = this.recipient.account;

      if (account.bank == '000') {
          this.withdrawButtonDisabled = true;
          return;
      }

      let [withdraw_value, available] = [
        account.withdraw_value,
        account.available,
      ];

      if (available === 0) {
        errorToast(
          "Atenção!",
          `Você não tem nenhum valor disponível para saque!`
        );
        this.withdrawButtonDisabled = true;
        return;
      }

      withdraw_value = this.removeValueMask(withdraw_value);

      if (
        withdraw_value === "" ||
        withdraw_value === undefined ||
        withdraw_value < 4.67
      ) {
        this.withdrawButtonDisabled = true;
        return;
      }

      if (Math.trunc(withdraw_value * 100) > available) {
        errorToast(
          "Atenção!",
          `O valor solicitado ultrapassa o valor disponível para saque!`
        );
        this.withdrawButtonDisabled = true;
        return;
      }

      this.withdrawButtonDisabled = false;
      return;
    },
    setMenu() {
      document.getElementById("coProducerButton").style.display = "none";
      document.getElementById("platforms-link").style.display = "none";
      document.getElementById("affiliations-link").style.display = "none";
      document.getElementById("documents-link").style.display = "none";

      document.getElementById("affiliate-link-1").classList.remove("active");
      document.getElementById("affiliate-link-2-transactions").classList.remove("active");
      document.getElementById("affiliate-link-2-withdraw").classList.add("active");
      document.getElementById("affiliate-link-1").style.display = "block";
      document.getElementById("affiliate-link-2").style.display = "block";
      document.getElementById("affiliate-link-2-content").style.display = "block";
    },
  },
};
</script>

<style lang="scss" scoped>
.xgrow-button-action,
.dropdown-menu,
.table-menu-item {
  background: #222429;
  cursor: pointer;
}

.dropdown-item:hover {
  color: var(--contrast-green3);
}

.client-info {
  &__name {
    font-weight: 700;
  }
  &__email {
    font-weight: 400;
  }
}

.blur {
  user-select: none;
  filter: blur(3px);
}

.modal__content {
  padding: 40px 52px;
}

@media screen and (max-width: 768px) {
  .modal__content {
    padding: 20px 26px;
  }
}

.modal__title {
  color: #fff;
  font-size: 18px;
  padding-bottom: 4px;
}

.modal__subtitle {
  color: #fff;
  font-size: 16px;
  font-weight: 400;
  margin-bottom: 18px;
}

.modal__line {
  background-color: #c4c4c4;
}

.bank-data {
  display: flex;
  flex-direction: column;
  margin-bottom: 36px;
}

.bank-data__item {
  font-weight: 600;
  font-size: 14px;
  color: #fff;
  padding: 10px 12px;
  background: #333844;
  display: grid;
  grid-template-columns: 1fr 1fr;
}

.bank-data__item span {
  font-weight: 400;
}

.bank-data__item--dark {
  background: #252932;
}

.bank-data__title {
  margin-bottom: 0px;
  font-weight: 700;
}

.withdraw__available {
  margin-bottom: 20px;
  color: #fff;
}

.withdraw__info {
  font-weight: 400;
  color: #fff;
  margin-bottom: 24px;
}

.withdraw__available span {
  color: var(--font-color);
  font-weight: 700;
}

.modal__actions {
  display: flex;
  justify-content: center;
  gap: 20px;
}

@media screen and (max-width: 768px) {
  .modal__actions {
    flex-direction: column-reverse;
  }
}

.modal__actions--cancel {
  border-color: #fff;
  color: #fff;
  min-width: 175px;
}

.modal__actions--confirm {
  background: #93bc1e;
  color: #fff;
  min-width: 175px;
}

.modal__actions--confirm:hover,
.modal__actions--cancel:hover {
  color: #fff;
}

.fa-check {
  font-size: 12px;
  margin-right: 5px;
}
</style>
