<template>
  <div class="xgrow-card card-dark py-4 h-100 subscribers">
    <Table id="subscribersTable">
      <template v-slot:title>
        <div class="xgrow-table-header w-100">
          <div class="d-flex justify-content-between">
            <Title>Alunos: {{ pagination.totalResults }}</Title>
            <Button
              text="Novo aluno"
              icon="fa fa-plus"
              status="success"
              @click="modalNewSubscriber = true"
            />
          </div>
          <p>Veja todos os seus alunos cadastrados ou adicione novos.</p>
          <hr />
        </div>
      </template>
      <template v-slot:filter>
        <div class="d-flex my-3 flex-wrap gap-3 align-items-end justify-content-between">
          <div class="d-flex gap-3 align-items-end flex-wrap">
            <Input
              :is-search="true"
              style="margin: 0px; width: 400px"
              id="searchIpt"
              icon="<i class='fa fa-search'></i>"
              placeholder="Pesquise pelo nome ou e-mail do aluno..."
              v-model="filter.search"
              class="search-input"
            />
            <FilterButton target="advancedFilters" />
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
        <div class="mb-3 collapse collapse-card advancedFilters" id="advancedFilters">
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
              <Col sm="12" md="6" lg="6" xl="6" class="my-4">
                <Multiselect
                  :options="filter.plans.options"
                  v-model="filter.plans.selected"
                  :searchable="true"
                  mode="tags"
                  placeholder="Digite o nome de um produto ou selecione um..."
                  :canClear="true"
                  @select="changeFilter"
                  @deselect="changeFilter"
                  @clear="changeFilter('plans')"
                >
                  <template v-slot:noresults>
                    <p class="multiselect-option" style="opacity: 0.5">
                      Plano não encontrado...
                    </p>
                  </template>
                </Multiselect>
              </Col>
              <Col sm="12" md="6" lg="6" xl="6" class="my-4">
                <Multiselect
                  :options="filter.status.options"
                  v-model="filter.status.selected"
                  :searchable="true"
                  mode="tags"
                  placeholder="Digite a opção do status ou selecione um..."
                  :canClear="true"
                  @select="changeFilter"
                  @deselect="changeFilter"
                  @clear="changeFilter('status')"
                >
                  <template v-slot:noresults>
                    <p class="multiselect-option" style="opacity: 0.5">
                      Plano não encontrado...
                    </p>
                  </template>
                </Multiselect>
              </Col>
              <Col sm="12" md="6" lg="6" xl="6" class="my-4">
                <DatePicker
                  class="w-100"
                  v-model:value="filter.created_at.value"
                  format="DD/MM/YYYY"
                  :clearable="true"
                  type="date"
                  range
                  placeholder="Data de cadastro"
                  @change="(date) => searchByDate(date, 'created_at')"
                />
              </Col>
              <Col sm="12" md="6" lg="6" xl="6" class="my-4">
                <DatePicker
                  class="w-100"
                  v-model:value="filter.login.value"
                  format="DD/MM/YYYY"
                  :clearable="true"
                  type="date"
                  range
                  placeholder="Último acesso"
                  @change="(date) => searchByDate(date, 'login')"
                />
              </Col>
            </Row>
            <Row>
              <div
                class="col-sm-12 col-md-12 col-lg-6 mt-lg-1 mt-md-3 mt-sm-3 d-flex gap-3 align-items-center flex-sm-column flex-md-row flex-wrap"
              >
                <SwitchButton
                  id="neverAccessedFilter"
                  class="mb-3"
                  :model-value="filter.neverAccessedFilter"
                  v-on:update:model-value="
                    (res) => filterShow('neverAccessedFilter', res)
                  "
                >
                  Mostrar apenas alunos que nunca acessaram
                </SwitchButton>
              </div>
              <div class="col-sm-12 col-md-12 col-lg-6 my-3">
                <SwitchButton
                  id="emailWrongFilter"
                  class="mb-3"
                  :model-value="filter.emailWrongFilter"
                  v-on:update:model-value="(res) => filterShow('emailWrongFilter', res)"
                >
                  Mostrar apenas alunos com erros no e-mail
                </SwitchButton>
              </div>
            </Row>
          </div>
        </div>
      </template>
      <template v-slot:header>
        <th>Nome</th>
        <th>Telefone</th>
        <th>Cadastro</th>
        <th>Último acesso</th>
        <th>Produto</th>
        <th width="60px"></th>
      </template>
      <template v-if="subscribers.length > 0" v-slot:body>
        <tr :key="subscriber.id" v-for="subscriber in subscribers">
          <td>
            <router-link :to="`/subscribers/${subscriber.id}/edit/next`" style="color: inherit">
              <b>{{ subscriber.name }}</b
              ><br />
              <div class="d-flex align-items-center gap-1">
                <i
                  v-if="subscriber.email_bounce_id"
                  class="fas fa-exclamation-circle text-danger"
                  data-bs-toggle="tooltip"
                  data-bs-placement="right"
                  :title="subscriber.email_bounce_description"
                ></i>
                <span>{{ subscriber.email }}</span>
              </div>
            </router-link>
          </td>
          <td>{{ subscriber.cel_phone || subscriber.main_phone || "-" }}</td>
          <td v-html="formatDate(subscriber.created)"></td>
          <td>
            <span
              v-if="subscriber.login"
              v-html="formatDate(subscriber.login)"
            ></span>
            <span v-else>Nunca acessou</span>
          </td>
          <td
            v-html="
              getSubscriberProducts(subscriber.products_id, subscriber.products_name)
            "
          ></td>
          <td>
            <DropdownButton
              :id="subscriber.id"
              :items="getActions(subscriber.id, subscriber.name)"
            />
          </td>
        </tr>
      </template>
      <template v-else v-slot:body>
        <tr>
          <td colspan="7" class="text-center">Sem resultados.</td>
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
    <QuestionModal
      :is-open="questionModal.open"
      :title="questionModal.title"
      :description="questionModal.description"
      :callback="questionModal.callback"
    />
    <NewSubscriber
      :open="modalNewSubscriber"
      :closeModal="() => (modalNewSubscriber = false)"
      :get-subscribers="getSubscribers"
    />
  </div>
</template>

<script>
import axios from "axios";
import moment from "moment";

import formatDateTimeSingleLine from "../../../js/components/XgrowDesignSystem/Mixins/formatDateTimeSingleLine";
import Title from "../../../js/components/XgrowDesignSystem/Typography/Title.vue";
import Table from "../../../js/components/Datatables/Table";
import Pagination from "../../../js/components/Datatables/Pagination";
import FilterButton from "../../../js/components/XgrowDesignSystem/Buttons/FilterButton";
import Input from "../../../js/components/XgrowDesignSystem/Form/Input";
import StatusBadge from "../../../js/components/XgrowDesignSystem/Badge/StatusBadge";
import Button from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import DropdownButton from "../../../js/components/XgrowDesignSystem/Buttons/DropdownButtonV2";
import Row from "../../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../../js/components/XgrowDesignSystem/Utils/Col";
import Subtitle from "../../../js/components/XgrowDesignSystem/Typography/Subtitle";
import QuestionModal from "../../../js/components/XgrowDesignSystem/Modals/QuestionModal";
import Loading from "../../../js/components/StatusModalComponent";
import SwitchButton from "../../../js/components/XgrowDesignSystem/Form/SwitchButton";
import ExportLabel from "../../../js/components/XgrowDesignSystem/Utils/ExportLabel";
import IconButton from "../../../js/components/XgrowDesignSystem/Buttons/IconButton";
import NewSubscriber from "../modal/NewSubscriber.vue";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";

import DatePicker from "vue-datepicker-next";
import "vue-datepicker-next/index.css";
import "vue-datepicker-next/locale/pt-br";

export default {
  components: {
    Title,
    NewSubscriber,
    Table,
    Pagination,
    FilterButton,
    Input,
    StatusBadge,
    Button,
    DropdownButton,
    Row,
    Col,
    Multiselect,
    Subtitle,
    DatePicker,
    QuestionModal,
    Loading,
    SwitchButton,
    ExportLabel,
    IconButton,
  },
  mixins: [formatDateTimeSingleLine],
  data() {
    return {
      loading: false,
      questionModal: {
        open: false,
        title: "",
        description: "",
        callback: () => {},
      },
      subscribers: [],
      pagination: {
        totalPages: 1,
        totalResults: 0,
        currentPage: 1,
        limit: 25,
      },
      filter: {
        search: "",
        neverAccessedFilter: false,
        emailWrongFilter: false,
        created_at: {
          value: "",
          formated: "",
        },
        login: {
          value: "",
          formated: "",
        },
        plans: {
          options: [],
          selected: [],
        },
        status: {
          options: [
            { value: "active", label: "Ativo" },
            { value: "canceled", label: "Cancelado" },
            { value: "failed", label: "Falha no Pagamento" },
            { value: "pending_payment", label: "Pagamento Pendente" },
            { value: "pending", label: "Pendente" },
          ],
          selected: [],
        },
      },
      modalNewSubscriber: false,
    };
  },
  watch: {
    "filter.search": function () {
      this.searchByTerm();
    },
  },
  async mounted() {
    await this.getProducts();
    await this.getSubscribers();
  },
  methods: {
    formatDate(date) {
      return moment(date).format("DD/MM/YYYY [<br>][ às ]HH:mm");
    },
    async getSubscribers() {
      this.loading = true;
      const res = await axios.get(subscriberRoute, {
        params: {
          page: this.pagination.currentPage,
          offset: this.pagination.limit,
          searchTermFilter: this.filter.search,
          createdPeriodFilter: this.filter.created_at.formated,
          lastAccessPeriodFilter: this.filter.login.formated,
          plansFilter: this.filter.plans.selected,
          statusFilter: this.filter.status.selected,
          neverAccessedFilter: this.filter.neverAccessedFilter,
          emailWrongFilter: this.filter.emailWrongFilter,
        },
      });

      this.subscribers = res.data.response.subscribers.data;

      this.pagination.totalPages = res.data.response.subscribers.last_page;
      this.pagination.totalResults = res.data.response.subscribers.total;
      this.pagination.currentPage = res.data.response.subscribers.current_page;
      this.loading = false;
    },
    async paginationChange(type, page) {
      this.pagination[type] = parseInt(page);
      await this.getSubscribers();
    },
    async searchByTerm() {
      this.pagination.currentPage = 1;
      const term = this.filter.search;
      setTimeout(async () => {
        if (term === this.filter.search) {
          await this.getSubscribers();
        }
      }, 1000);
    },
    async changeFilter(clear = false) {
      if (["created_at", "plans"].includes(clear)) {
        this.filter[clear].selected = [];
      }
      this.pagination.currentPage = 1;
      await this.getSubscribers();
    },
    getActions(id, name) {
      const result = [
        {
          name: "Editar",
          ico: "fa fa-pencil",
          url: `/subscribers/${id}/edit/next`,
          isVueRouter: true,
        },
        {
          name: "Reenviar dados de acesso",
          ico: "fa-solid fa-paper-plane",
          url: "#",
          callback: () =>
            this.openModal(
              `Deseja reenviar os dados de acesso para este aluno?`,
              `Caso você reenvie os dados do aluno ${name} essa ação não poderá ser desfeita`,
              () => this.resendAccessData(id)
            ),
        },
        {
          name: "Excluir",
          ico: "fa-solid fa-xmark red",
          url: "#",
          callback: () =>
            this.openModal(
              `Deseja excluir este aluno?`,
              `Caso você exclua o aluno ${name} essa ação não poderá ser desfeita`,
              () => this.delete(id)
            ),
        },
      ];

      return result;
    },
    async searchByDate(date, field) {
      if (!date[0]) {
        this.filter[field].formated = null;
      } else {
        const startDate = moment(this.filter[field].value[0]).format("DD/MM/YYYY");
        const finalDate = moment(this.filter[field].value[1]).format("DD/MM/YYYY");
        this.filter[field].formated = [startDate, finalDate];
      }
      await this.changeFilter();
    },
    async delete(id) {
      this.loading = true;
      try {
        const url = deleteUserURL.replace(/:id/g, id);
        await axios.delete(url);

        this.questionModal.open = false;
        successToast("Sucesso!", "O aluno selecionado foi excluído.");
        await this.getSubscribers();
      } catch (error) {
        errorToast("Algo aconteceu!", error.message);
      }
      this.loading = false;
    },
    openModal(title, description, callback) {
      this.questionModal.open = true;
      this.questionModal.title = title;
      this.questionModal.description = description;
      this.questionModal.callback = callback;
    },
    async resendAccessData(id) {
      this.loading = true;
      const url = resendUserDataURL.replace(/:id/g, id);
      try {
        await axios.post(url);
        successToast("Sucesso!", `Dados enviados com sucesso!`);
      } catch (error) {
        errorToast("Algum erro aconteceu!", `${error.response.data.message}`);
      }
      this.questionModal.open = false;
      this.loading = false;
    },
    async filterShow(type, res) {
      this.filter[type] = res;
      await this.changeFilter();
    },
    async exportReport(typeFile) {
      successToast(
        "Iniciando download!",
        "Seu arquivo foi adicionado a fila de downloads. Para ver o andamento, click emListas exportadas no menu lateral."
      );
      await axios.post(exportRoute, {
        searchTerm: this.filter.search,
        plansFilter: this.filter.plans.selected,
        statusFilter: this.filter.status.selected,
        createdPeriodFilter: this.filter.created_at.formated,
        lastAccessPeriodFilter: this.filter.login.formated,
        neverAccessedFilter: this.filter.neverAccessedFilter,
        emailWrongFilter: this.filter.emailWrongFilter,
        typeFile: typeFile, // || xlsx
        reportName: "subscriber-users",
      });
    },
    async getProducts() {
      this.loading = true;
      const res = await axios.get(productsRoute);
      this.filter.plans.options = res.data.products.map((item) => {
        return {
          value: item.id,
          label: item.name,
        };
      });

      this.loading = false;
    },
    getSubscriberProducts(ids, names) {
      if (ids !== null) {
        const idList = ids.split(",");
        const namesList = names.split(",");

        let products = idList.map((item, index) => {
          return `<li><a href="/products/${item}/plans">${namesList[index]}</a></li>`;
        });

        return `<ul class="subscribers__products">${products.join('')}</ul>`;
      }

      return '-';
    },
  },
};
</script>

<style lang="scss">
#advancedFilters {
  border-top: 1px solid rgba(255, 255, 255, 0.25);
  border-bottom: 1px solid rgba(255, 255, 255, 0.25);
  background: rgba(0, 0, 0, 0.2);
}

.create-button:hover {
  color: #fff !important;
}

.subscribers {
  &__products {
    list-style: disc !important;
    margin-bottom: 0px;

    li {
      list-style: disc !important;
    }

    a {
      color: #fff;
    }
  }
}
</style>
