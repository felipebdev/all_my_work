<template>
  <div class="xgrow-card card-dark py-4">
    <Table id="salesTransactionsTable" key="salesTransactionsTable">
      <template v-slot:title>
        <div class="xgrow-table-header w-100">
          <h5 class="title">Solicitações recusadas, bloqueadas, canceladas ou falhas: {{ pagination.totalResults }}</h5>
          <p>Veja todos as suas solicitações recusadas, bloqueadas, canceladas ou falhas.</p>
          <hr />
        </div>
      </template>
      <template v-slot:filter>
        <div
          class="
            d-flex
            my-3
            flex-wrap
            gap-3
            align-items-end
            justify-content-between
          "
        >
          <div class="d-flex gap-3 align-items-end flex-wrap">
            <Input
              id="searchIpt"
              icon="<i class='fa fa-search'></i>"
              placeholder="Pesquise pelo id, nome ou e-mail do afiliado..."
              v-model="filter.search"
              class="search-input"
            />
            <FilterButton target="advancedFilters" />
          </div>
        </div>
      </template>
      <template v-slot:collapse>
        <div
          class="mb-3 collapse collapse-card advancedFilters"
          id="advancedFilters"
        >
          <div class="p-2 px-3" style="border-radius: inherit">
            <Row>
              <Col classes="mt-2 mb-4 d-flex gap-2 align-items-center">
                <Subtitle
                  ><i class="fa fa-filter advancedFilters__icon"></i> Filtros
                  Avançados</Subtitle
                >
              </Col>
            </Row>
            <Row>
              <Col sm="12" md="6" lg="6" xl="6" class="mb-4">
                <Multiselect
                  :options="$store.state.advancedFilters.products"
                  v-model="filter.products"
                  :searchable="true"
                  mode="tags"
                  placeholder="Digite o nome de um produto ou selecione um..."
                  :canClear="true"
                  @select="changeFilter"
                  @deselect="changeFilter"
                  @clear="changeFilter('products')"
                >
                  <template v-slot:noresults>
                    <p class="multiselect-option" style="opacity: 0.5">
                      Produto não encontrado...
                    </p>
                  </template>
                </Multiselect>
              </Col>
              <Col sm="12" md="6" lg="6" xl="6" class="mb-4">
                <DatePicker
                  v-model:value="filter.date"
                  format="DD/MM/YYYY"
                  :clearable="true"
                  type="date"
                  range
                  placeholder="Data do evento"
                  @change="searchByDate"
                />
              </Col>
            </Row>
          </div>
        </div>
      </template>
      <template v-slot:header>
        <th
          v-for="header in ['Id','Nome', 'Produto', 'Data de afiliação', 'Comissão', 'Status']"
          :key="header"
        >
          {{header}}
        </th>
        <th style="width: 60px;"></th>
      </template>
      <template v-if="affiliates.length > 0" v-slot:body>
        <tr
          :key="affiliate.producer_products_id"
          v-for="affiliate in affiliates"
        >
          <td>{{ affiliate.producers_id }}</td>
          <td>
            <b
              ><u>{{ affiliate.platform_users_name }}</u></b
            >
            <br />{{ affiliate.platform_users_email }}
          </td>
          <td>{{ affiliate.products_name }}</td>
          <td>
            {{ formatDate(affiliate.producer_products_created_at) }} <br />
            às {{ formatTime(affiliate.producer_products_created_at) }}
          </td>
          <td>
            {{ String(affiliate.producer_products_percent).replace(".", ",") }}%
          </td>
          <td>
            <Status :status="affiliate.producer_products_status" />
          </td>
          <td>
            <div class="dropdown x-dropdown">
              <button
                class="xgrow-button xgrow-button-action table-action-button m-1"
                type="button"
                id="dropdownMenuButton${row.id}"
                data-bs-toggle="dropdown"
                v-if="affiliate.producer_products_status != 'canceled' && affiliate.producer_products_status != 'recipient_failed'"
              >
                <i class="fas fa-ellipsis-v"></i>
              </button>
              <ul
                class="dropdown-menu table-menu"
                aria-labelledby="dropdownMenuButton${row.id}"
              >
                <li
                  role="button"
                  class="dropdown-item table-menu-item"
                  @click.prevent="
                    confirmationModal(
                      affiliate.producer_products_id,
                      affiliate.platform_users_name,
                      'unblock'
                    )
                  "
                >
                  <i
                    class="affiliate-icon affiliate-icon--green fa-solid fa-check"
                  ></i>
                  Desbloquear afiliado
                </li>
              </ul>
            </div>
          </td>
        </tr>
      </template>
      <template v-else v-slot:body>
        <tr>
          <td colspan="5" class="text-center">Não há afiliados com os status: Bloqueado, Cancelado ou Recusado.</td>
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
    <Modal :is-open="modal.open" modalSize="md"
      class="confirm-modal"
      @close="modal.open = false">
      <i class="fa fa-question question"></i>
      <h5>
        Deseja
        <span class="lowercase">{{
          modal.actionTexts[modal.actionType].button
        }}</span>
        afiliação?
      </h5>
      <p>
        Confirma o {{modal.actionTexts[modal.actionType].text }} do afiliado: {{ modal.user }}?
      </p>
      <div class="cta">
        <button class="xgrow-button cancel" @click="modal.open = false">
          Voltar
        </button>
        <button class="xgrow-button" @click="unblockAffiliate">
          {{ modal.actionTexts[modal.actionType].button }} afiliação
        </button>
      </div>
    </Modal>
  </div>
</template>

<script>
import Subtitle from "../../../../js/components/XgrowDesignSystem/Typography/Subtitle";
import Modal from "../../../../js/components/XgrowDesignSystem/Modals/Modal.vue";
import Table from "../../../../js/components/Datatables/Table";
import Row from "../../../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../../../js/components/XgrowDesignSystem/Utils/Col";
import FilterButton from "../../../../js/components/XgrowDesignSystem/Buttons/FilterButton";
import Input from "../../../../js/components/XgrowDesignSystem/Form/Input";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import Pagination from "../../../../js/components/Datatables/Pagination";
import axios from "axios";
import moment from "moment";
import DatePicker from "vue-datepicker-next";
import Status from "../../../../js/components/XgrowDesignSystem/Badge/StatusBadge";
import "vue-datepicker-next/index.css";
import "vue-datepicker-next/locale/pt-br";


export default {
  name: "AffiliatesDifferentStatus",
  components: {
    Table,
    Pagination,
    Modal,
    Row,
    Col,
    FilterButton,
    Input,
    Multiselect,
    Subtitle,
    DatePicker,
    Status
  },
  props: {
    showUser: {
      type: Function,
      default: () => {},
    }
  },
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
      affiliatesCancelUrl,
      affiliatesBlockUrl,
      modal: {
        open: false,
        actionType: "unblock",
        user: "",
        route: "",
        actionTexts: {
          unblock: {
            text: "desbloqueio",
            button: "Desbloquear",
            title: "desbloqueada",
          },
        },
      },
      filter: {
        search: "",
        names: [],
        date: "",
        dateFormated: "",
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

      const params = {
        page: this.pagination.currentPage,
        offset: this.pagination.limit,
        products: this.filter.products,
        affiliation_status: ['blocked', 'canceled', 'refused', 'recipient_failed'],
        created_period_filter: this.filter.dateFormated,
        search: this.filter.search,
      }

      const response = await axios.get(affiliatesActiveUrl, { params });

      this.affiliates = response.data.response.affiliates.data;
      this.pagination.totalPages = response.data.response.affiliates.last_page;
      this.pagination.totalResults = response.data.response.affiliates.total;
      this.pagination.currentPage = response.data.response.affiliates.current_page;

      this.$store.commit("setLoading", false);
    },
    onPageChange: async function (page) {
      this.pagination.currentPage = page;
      await this.getData();
    },
    onLimitChange: async function (value) {
      this.pagination.limit = parseInt(value);
      await this.getData();
    },
    confirmationModal(producer_product_id, user, actionType) {
      this.modal.open = true;
      this.modal.route = affiliatesUnblockUrl.replace(
        "producer_product_id",
        producer_product_id
      );

      this.modal.actionType = actionType;
      this.modal.user = user;
    },
    async unblockAffiliate() {
      this.$store.commit("setLoading", true);
      this.modal.open = false;

      try {
        await axios.post(this.modal.route);
        await this.getData();
        successToast(
          `Afiliação ${this.modal.actionTexts[this.modal.actionType].title}!`,
          `A afiliação de ${this.modal.user} foi ${
            this.modal.actionTexts[this.modal.actionType].title
          } com sucesso.`
        );
      } catch (e) {
        errorToast(
          `Falha ao ${
            this.modal.actionTexts[this.modal.actionType].button
          } afiliação!`,
          `Ocorreu um erro inesperado ao ${
            this.modal.actionTexts[this.modal.actionType].button
          } a afiliação de ${this.modal.user}, tente novamente mais tarde.`
        );
      }
    },
    formatDate(value) {
      return moment(value).format("DD/MM/YYYY");
    },
    formatTime(value) {
      return moment(value).format("H:m");
    },
    async changeFilter(clear = false) {
      if (["products"].includes(clear)) {
        this.filter[clear] = [];
      }
      this.pagination.currentPage = 1;
      await this.getData();
    },
    async searchByDate() {
      this.pagination.currentPage = 1;
      if (this.filter.date[0] !== null) {
        this.filter.dateFormated =
          this.formatDate(this.filter.date[0]) +
          "-" +
          this.formatDate(this.filter.date[1]);
      } else {
        this.filter.dateFormated = null;
      }
      await this.getData();
    },
    async searchByTerm() {
      const term = this.filter.search;
      setTimeout(async () => {
        if (term === this.filter.search) {
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
.confirm-modal {
  .modal__content {
    padding: 15px;
    max-width: 350px;
    display: flex;
    flex-direction: column;
    align-items: center;
    min-height: 0px !important;

    p {
      text-align: center;
      margin-bottom: 20px;
    }
  }

  .modal-dialog {
    display: flex;
    justify-content: center;
  }
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
  width: 400px;
  height: 48px;
}

.form-group {
  margin: 0px !important;
}
</style>
