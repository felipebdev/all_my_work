<template>
  <div>
    <template v-if="activeScreen.toString() === 'listCoproducer'">
      <Table :id="'content-table'">
        <template v-slot:title>
          <div
            class="d-flex align-items-center gap-1 flex-wrap justify-content-center justify-content-md-between w-100"
          >
            <div>
              <p class="xgrow-card-title mb-2">Coprodutores</p>
              <span>Veja todos os coprodutores cadastrados e convide novos.</span>
            </div>
            <div>
              <div class="d-flex align-items-center py-2 gap-2 flex-wrap w-100">
                <div class="xgrow-input me-1 xgrow-input-search">
                  <input
                    id="ipt-global-filter"
                    placeholder="Pesquisa um coprodutor..."
                    type="text"
                    style="height: 40px"
                    v-model="filter.searchValue"
                  />
                  <span class="xgrow-input-cancel"
                    ><i class="fa fa-search" aria-hidden="true"></i
                  ></span>
                </div>
                <button
                  type="button"
                  data-bs-toggle="collapse"
                  data-bs-target="#collapseDiv"
                  aria-bs-expanded="false"
                  aria-bs-controls="collapseDiv"
                  class="xgrow-button-filter xgrow-button export-button me-1"
                  aria-expanded="true"
                >
                  <span
                    >Filtros avançados
                    <i class="fa fa-chevron-down" aria-hidden="true"></i
                  ></span>
                </button>
                <div class="export-buttons d-none">
                  <button class="xgrow-button export-button me-1" title="Exportar em CSV">
                    <img
                      src="/xgrow-vendor/assets/img/reports/txt.svg"
                      alt="Exportar em CSV"
                    />
                  </button>
                  <button
                    class="xgrow-button export-button me-1"
                    title="Exportar em XLSX"
                  >
                    <img
                      src="/xgrow-vendor/assets/img/reports/xls.svg"
                      alt="Exportar em XLSX"
                    />
                  </button>
                </div>
                <div>
                  <button
                    class="xgrow-button xgrow-button-datatables"
                    @click="newCoproducer"
                  >
                    <i class="fa fa-plus" aria-hidden="true"></i> Convidar coprodutor
                  </button>
                </div>
              </div>
            </div>
          </div>
        </template>
        <template v-slot:collapse>
          <div class="mb-3 collapse" id="collapseDiv">
            <div class="filter-container">
              <div class="p-2 px-3">
                <div class="row">
                  <div class="col-sm-12 col-md-12 my-2">
                    <h5>Filtros avançados</h5>
                  </div>
                  <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                    <div
                      class="xgrow-floating-input mui-textfield mui-textfield--float-label"
                    >
                      <select
                        id="statusValue"
                        class="xgrow-select"
                        v-model="filter.statusValue"
                        @change="search"
                      >
                        <option value="" selected disabled>Selecione uma opção</option>
                        <option
                          v-for="statusValue in filter.statusOptions"
                          :value="statusValue.id"
                          :key="statusValue.id"
                        >
                          {{ statusValue.name }}
                        </option>
                      </select>
                      <label for="levelValue">Status do contrato</label>
                      <span class="caret"></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </template>
        <template v-slot:header>
          <th>Data</th>
          <th>Coprodutor</th>
          <th>Comissão</th>
          <th>Vencimento</th>
          <th>Status</th>
          <th style="width: 40px"></th>
        </template>
        <template v-slot:body v-if="results.length > 0">
          <tr v-for="item in results" :key="item.id">
            <td>{{ formatDateBR(item.created_at) }}</td>
            <td>{{ item.name }}<br />{{ item.email }}</td>
            <td>{{ item.percent }}%</td>
            <td>
              {{ item.contract_limit ? formatDateBR(item.contract_limit) : "Ilimitado" }}
            </td>
            <td v-html="modifyStatus(item.status)" style="vertical-align: middle"></td>
            <td class="text-end">
              <div class="dropdown">
                <button
                  class="xgrow-button table-action-button m-1"
                  type="button"
                  :id="'dropdownMenuButton' + item.id"
                  data-bs-toggle="dropdown"
                  aria-expanded="false"
                >
                  <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul
                  class="dropdown-menu table-menu xgrow-dropdown-menu"
                  :aria-labelledby="'dropdownMenuButton' + item.id"
                >
                  <li>
                    <a
                      class="dropdown-item table-menu-item"
                      href="javascript:void(0)"
                      @click="editCoproducer(item.id)"
                    >
                      Editar
                    </a>
                  </li>
                  <li v-if="item.status !== 'canceled'">
                    <a
                      class="dropdown-item table-menu-item"
                      href="javascript:void(0)"
                      @click="openCancelContract(item.ppId)"
                    >
                      Cancelar contrato
                    </a>
                  </li>
                  <li class="d-none">
                    <a
                      class="dropdown-item table-menu-item"
                      href="javascript:void(0)"
                      @click="editCoproducer(item.id)"
                    >
                      Excluir coprodutor
                    </a>
                  </li>
                </ul>
              </div>
            </td>
          </tr>
        </template>
        <template v-slot:body v-else>
          <tr>
            <td colspan="6" class="xgrow-no-content">Não há dados a serem exibidos.</td>
          </tr>
        </template>
        <template v-slot:footer>
          <Pagination
            class="mt-4"
            :total-pages="paginationTotalPages"
            :total="paginationTotal"
            :current-page="paginationCurrentPage"
            @page-changed="onPageChange"
            @limit-changed="onLimitChange"
          >
          </Pagination>
        </template>
      </Table>

      <Modal :is-open="cancelContractModal" @close="cancelContractModal = false">
        <template v-slot:content>
          <div class="row gap-3 text-center w-100" style="color: var(--gray1)">
            <i
              aria-hidden="true"
              class="fas custom-alert-symbol fa-question-circle fa-5x"
            ></i>
            <h5 class="m-0 p-0" style="color: #ffffff">
              <b>Deseja realmente cancelar este contrato?</b>
            </h5>
            <span>Não há maneira de recuperar esse item após seu cancelamento.</span>
          </div>
        </template>
        <template v-slot:footer="slotProps">
          <button
            type="button"
            class="btn btn-outline-light mr-2 xgrow-button-cancel"
            @click="slotProps.closeModal"
          >
            Voltar
          </button>
          <button type="button" class="btn btn-success" @click.prevent="cancelContract">
            <i class="fas fa-check mr-2"></i> Sim, cancelar
          </button>
        </template>
      </Modal>
    </template>
    <template v-else>
      <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 mb-4">
          <div class="tab-pane-title pb-0">
            <h5 class="mb-3">Convidar coprodutor</h5>
          </div>
          <p class="xgrow-medium-regular">
            Preencha os campos abaixo para convidar um novo coprodutor.
          </p>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 py-3">
          <p><b>Dados do coprodutor</b></p>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12">
          <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
            <input
              type="text"
              name="name"
              id="name"
              class="xgrow-input mui--is-touched mui--is-dirty mui--is-not-empty"
              v-model="coproducer.name"
              :disabled="method === 'edit'"
            />
            <label for="name">Nome</label>
          </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12">
          <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
            <input
              type="text"
              name="email"
              id="email"
              class="xgrow-input mui--is-touched mui--is-dirty mui--is-not-empty"
              v-model="coproducer.email"
              :disabled="method === 'edit'"
            />
            <label for="email">E-mail</label>
          </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12">
          <DatePicker
            class="w-100"
            v-model:value="coproducer.due"
            format="DD/MM/YYYY"
            :clearable="false"
            type="date"
            @change="void 0"
            :disabled="coproducer.status === 'canceled'"
          >
          </DatePicker>
          <label for="due" class="xgrow-ds-mx-datepicker-label"
            >Limite do contrato (Opcional)</label
          >
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12">
          <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
            <input
              type="text"
              name="commission"
              id="commission"
              class="xgrow-input mui--is-not-empty mui--is-untouched mui--is-pristine"
              v-model="coproducer.commission"
              :disabled="coproducer.status === 'canceled'"
              v-maska="['##', '##.##']"
              min="1"
              max="80"
            />
            <label for="commission">Comissão</label>
          </div>
          <small class="xgrow-ds-input-hint"
            ><i>A comissão deve ser maior de 1% e menor que 80%.</i></small
          >
        </div>

      <div class="col-lg-12 col-md-12 col-sm-12 mt-3">
            <div class="d-flex align-items-center my-2">
                <div class="form-check form-switch">
                    <input type="checkbox" name="splitInvoice" id="splitInvoice" class="form-check-input" v-model="coproducer.splitInvoice" :disabled="coproducer.status === 'canceled'">
                    <label class="form-check-label" for="splitInvoice">Dividir a responsabilidade de emissão de notas fiscais com esse coprodutor? (Somente Wisenotas)</label>
                </div>
            </div>
        </div>
      </div>

      <div class="border-top border-secondary mt-5">
        <div class="d-flex py-4 px-0 justify-content-between flex-wrap gap-3">
          <button
            class="btn xgrow-button-secondary button-cancel"
            @click="backCoproducer"
          >
            Voltar
          </button>
          <template v-if="method === 'create'">
            <button class="xgrow-button xgrow-button-custom" @click="sendInvite">
              Enviar convite
            </button>
          </template>
          <template v-else>
            <button class="xgrow-button xgrow-button-custom" @click="sendInvite">
              Atualizar dados
            </button>
          </template>
        </div>
      </div>
    </template>
    <StatusModalComponent :is-open="loading" :status="status"></StatusModalComponent>
  </div>
</template>

<script>
import axios from "axios";
import StatusModalComponent from "../../../../js/components/StatusModalComponent";
import Table from "../../../../js/components/Datatables/Table";
import Pagination from "../../../../js/components/Datatables/Pagination";

import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import Input from "../../../../js/components/XgrowDesignSystem/Form/Input";
import moment from "moment";

import DatePicker from "vue-datepicker-next";
import "vue-datepicker-next/index.css";
import "vue-datepicker-next/locale/pt-br";

import Modal from "../../../../js/components/ModalComponent";
import { maska } from "maska";

export default {
  components: {
    StatusModalComponent,
    Multiselect,
    Input,
    DatePicker,
    Pagination,
    Table,
    Modal,
  },
  directives: { maska },
  data() {
    return {
      method: "create",
      loading: false,
      status: "loading",
      activeScreen: "listCoproducer",

      /** Pagination */
      paginationCurrentPage: 1, // Current Page
      paginationLimit: 25, // Limit by page
      paginationTotal: 0, // Total Results
      paginationTotalPages: 0, //Total Pages

      /** Coproducer Data */
      results: [],

      /** Filters */
      filter: {
        searchValue: "",
        statusValue: "",
        statusOptions: [
          { id: "", name: "Todos os status" },
          { id: "active", name: "Ativo" },
          { id: "canceled", name: "Cancelado" },
          { id: "pending", name: "Pendente" },
        ],
      },

      /** Coproducer Data */
      currentId: null,
      currentProductProducerId: null,
      coproducer: {
        id: 0,
        name: "",
        email: "",
        due: null,
        commission: 0,
        status: "",
        splitInvoice: false,
      },

      cancelContractModal: false,
    };
  },
  watch: {
    "filter.searchValue": function () {
      this.search();
    },
  },
  methods: {
    formatDateBR(value) {
      return moment(value).format("DD/MM/YYYY");
    },
    modifyStatus(value) {
      if (value === "active")
        return '<span class="xgrow-ds-badge xgrow-ds-badge-success">ativo</span>';
      if (value === "canceled")
        return '<span class="xgrow-ds-badge xgrow-ds-badge-danger">cancelado</span>';
      if (value === "pending")
        return '<span class="xgrow-ds-badge xgrow-ds-badge-warning">pendente</span>';
    },
    /** Used for search with timer */
    search: async function () {
      let term = this.filter.searchValue;
      setTimeout(() => {
        if (term === this.filter.searchValue) {
          this.loading = true;
          axios
            .get(getAllCoproducers, {
              params: {
                offset: this.paginationLimit,
                search: this.filter.searchValue,
                status: this.filter.statusValue,
              },
            })
            .then((res) => {
              const producers = res.data.response.producers;
              this.results = producers.data;
              this.paginationCurrentPage = producers.current_page;
              this.paginationTotal = producers.total;
              this.loading = false;
            })
            .catch((err) => console.log(err));
        }
      }, 1000);
    },
    /** Change screen by value */
    changePage: function (screen) {
      this.activeScreen = screen.toString();
    },
    /** Get Total de Pages */
    totalPages: function () {
      const qty = Math.ceil(this.paginationTotal / this.paginationLimit);
      this.paginationTotalPages = qty <= 1 ? 1 : qty;
    },
    /** On change page */
    onPageChange: async function (page) {
      this.paginationCurrentPage = page;
    },
    /** Limit by size itens */
    onLimitChange: async function (value) {
      this.paginationLimit = parseInt(value);
      await this.search();
    },
    /** Send Invite for new Producer */
    sendInvite: async function () {
      if (this.verifyFields()) return true;
      this.loading = true;
      this.status = "saving";

      if (this.method === "create") {
        const formData = new FormData();
        formData.append("name", this.coproducer.name);
        formData.append("email", this.coproducer.email);
        formData.append("commission", this.coproducer.commission);
        formData.append("split_invoice", this.coproducer.splitInvoice);
        if (this.coproducer.due) {
          formData.append("due", moment(this.coproducer.due).format("YYYY-MM-DD"));
        }
        try {
          const res = await axios.post(saveCoproducer, formData);
          successToast("Ação realizada com sucesso", res.data.message.toString());
          this.loading = false;
          this.status = "loading";
          await this.reloadData();
          this.changePage("listCoproducer");
        } catch (e) {
          this.loading = false;
          this.status = "loading";
          errorToast("Falha ao realizar ação", e.response.data.message.toString());
          if (e.response.status >= 500) {
            errorToast("Falha ao realizar ação", e.response.statusText);
          }
          if (e.response.status === 422) {
            const error = e.response.data.errors;
            this.isKeyExists(error, "name");
            this.isKeyExists(error, "email");
            this.isKeyExists(error, "due");
            this.isKeyExists(error, "commission");
          }
        }
      }
      if (this.method === "edit") {
        const due = this.coproducer.due
          ? moment(this.coproducer.due).format("YYYY-MM-DD")
          : null;
        try {
          const res = await axios.post(updateCoproducer, {
            producer: this.coproducer.id,
            commission: this.coproducer.commission,
            due: due,
            split_invoice: this.coproducer.splitInvoice,
            _method: "put",
          });
          successToast("Ação realizada com sucesso", res.data.message.toString());
          this.loading = false;
          this.status = "loading";
          await this.reloadData();
          this.changePage("listCoproducer");
        } catch (e) {
          this.loading = false;
          this.status = "loading";
          errorToast("Falha ao realizar ação", e.response.data.message.toString());
          if (e.response.status >= 500) {
            errorToast("Falha ao realizar ação", e.response.statusText);
          }
          if (e.response.status === 422) {
            const error = e.response.data.errors;
            this.isKeyExists(error, "commission");
          }
        }
      }
    },
    /** Verify if has error */
    verifyFields: function () {
      if (this.coproducer.name.trim() === "") {
        errorToast("Algum erro aconteceu!", "O nome é obrigatório.");
        return true;
      }
      if (this.coproducer.email.trim() === "") {
        errorToast("Algum erro aconteceu!", "O e-mail é obrigatório.");
        return true;
      }
      if (!this.emailRegex(this.coproducer.email)) {
        errorToast("Algum erro aconteceu!", "O e-mail informado é inválido.");
        return true;
      }
      if (this.coproducer.commission < 1 || this.coproducer.commission > 80) {
        errorToast(
          "Algum erro aconteceu!",
          "A comissão deve ser maior de 1% e menor que 80%."
        );
        return true;
      }
    },
    /** Verify error on backend */
    isKeyExists: function (obj, key) {
      if (obj.hasOwnProperty(key)) {
        errorToast("Falha ao realizar ação", obj[key][0].toString());
      }
      return false;
    },
    /** Regex for valid email */
    emailRegex: function (val) {
      // const expression = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$/;
      const expression = /\S+@\S+\.\S+/;
      const regex = new RegExp(expression);
      return val.match(regex);
    },
    /** New Producer */
    newCoproducer: async function () {
      this.clearFields();
      this.method = "create";
      this.changePage("editCoproducer");
    },
    /** Edit Producer */
    editCoproducer: async function (id) {
      this.clearFields();
      this.currentId = id;
      this.method = "edit";
      const producer = this.results.filter((item) => item.id === id)[0];
      this.coproducer.id = producer.id;
      this.coproducer.name = producer.name;
      this.coproducer.email = producer.email;
      this.coproducer.due = producer.contract_limit
        ? moment(producer.contract_limit).toDate()
        : null;
      this.coproducer.commission = producer.percent;
      this.coproducer.status = producer.status;
      this.coproducer.splitInvoice = !!producer.split_invoice;
      this.changePage("editCoproducer");
    },
    /** Open Cancel Producer Contract Modal */
    openCancelContract: async function (id) {
      this.currentProductProducerId = id;
      this.cancelContractModal = true;
    },
    /** Cancel Producer Contract */
    cancelContract: async function () {
      this.cancelContractModal = false;
      this.loading = true;
      this.status = "saving";
      const formData = new FormData();
      formData.append("productProducerId", this.currentProductProducerId);
      const res = await axios.post(cancelContract, formData);
      successToast("Ação realizada com sucesso", res.data.message.toString());
      this.loading = false;
      this.status = "loading";
      await this.reloadData();
    },
    /** Back to List Producer */
    backCoproducer: async function () {
      await this.reloadData();
      this.changePage("listCoproducer");
    },
    /** Clear Fields */
    clearFields: function () {
      this.currentId = this.currentProductProducerId = this.coproducer.due = null;
      this.coproducer.name = this.coproducer.status = this.coproducer.email = "";
      this.coproducer.commission = 20;
      this.coproducer.splitInvoice = false;
    },
    /** Hack for load button */
    loadTabButtons: function () {
      this.clickTab("nav-plans-tab");
      this.clickTab("nav-links-tab");
      this.clickTab("nav-delivery-tab");
      this.clickTab("nav-configs-tab");
    },
    /** Hack for click button */
    clickTab: function (idEl) {
      document.getElementById(idEl).onclick = async () => {
        await this.reloadData();
        await new Promise((resolve) => setTimeout(resolve, 500));
        this.changePage("listCoproducer");
      };
    },
    /** Get All Co-Producers */
    getAllCoproducers: async function () {
      const req = await axios.get(getAllCoproducers);
      const producers = req.data.response.producers;
      this.results = producers.data;
      this.paginationCurrentPage = producers.current_page;
      this.paginationTotal = producers.total;
    },
    /** Get All Co-Producers */
    reloadData: async function () {
      await this.getAllCoproducers();
      this.totalPages();
      this.loadTabButtons();
    },
  },
  /** Created lifecycle */
  async created() {
    await this.reloadData();
  },
};
</script>

<style>
.xgrow-ds-badge {
  text-transform: uppercase;
  padding: 10px 20px;
  font-weight: 500;
  font-size: 0.85rem;
  width: 94px;
}

.xgrow-ds-badge-success {
  background-color: rgba(123, 186, 74, 0.1);
  color: #7cbb4b;
}

.xgrow-ds-badge-danger {
  background-color: rgba(187, 75, 75, 0.1);
  color: #bb4b4b;
}

.xgrow-ds-badge-warning {
  background-color: rgba(187, 146, 75, 0.1);
  color: #bb924b;
}

.xgrow-button-datatables {
  height: 40px;
  width: fit-content;
  padding: 0 20px;
}

.xgrow-ds-input-hint {
  color: rgb(193, 197, 207);
  top: -1rem;
  position: relative;
  font-weight: 300;
}

.xgrow-ds-mx-datepicker-label {
  color: var(--ds2-action-dark) !important;
  font-weight: 600 !important;
  z-index: 1;
  position: relative;
  left: 10px;
  top: -60px;
}

.mx-input {
  font-size: 16px !important;
  padding-top: 32px !important;
}

.xgrow-input[type="text"]:not(:focus).mui--is-empty ~ label {
  top: 0 !important;
}
</style>
