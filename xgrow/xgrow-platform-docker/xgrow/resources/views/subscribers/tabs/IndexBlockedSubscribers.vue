<template>
  <div class="xgrow-card card-dark py-4 h-100 import">
    <Table id="blockedSubscribersTable">
      <template v-slot:title>
        <div class="xgrow-table-header w-100">
          <h5 class="title">Alunos bloqueados: {{ pagination.totalResults }}</h5>

          <p>Veja todos os seus alunos bloqueados.</p>
          <hr />
        </div>
      </template>
      <template v-slot:filter>
        <div class="d-flex my-3 flex-wrap gap-3 align-items-end justify-content-between">
          <div class="d-flex gap-3 align-items-end flex-wrap">
            <Input
              style="margin: 0px; width: 400px"
              id="searchIpt"
              icon="<i class='fa fa-search'></i>"
              placeholder="Pesquise pelo nome ou e-mail do aluno..."
              v-model="filter.search"
              class="search-input"
            />
            <FilterButton target="advancedFilters" />
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
                  :options="filter.blocked.options"
                  v-model="filter.blocked.selected"
                  :searchable="true"
                  mode="tags"
                  placeholder="Digite ou selecione um status..."
                  :canClear="true"
                  @select="changeFilter"
                  @deselect="changeFilter"
                  @clear="changeFilter('plans')"
                >
                  <template v-slot:noresults>
                    <p class="multiselect-option" style="opacity: 0.5">
                      Status não encontrado...
                    </p>
                  </template>
                </Multiselect>
              </Col>
            </Row>
          </div>
        </div>
      </template>
      <template v-slot:header>
        <th>Nome</th>
        <th>Nº de bloqueios</th>
        <th>Data do bloqueio</th>
        <th>Situação</th>
        <th width="60px"></th>
      </template>
      <template v-if="subscribers.length > 0" v-slot:body>
        <tr :key="subscriber.id" v-for="subscriber in subscribers">
          <td>
            <a :href="`/subscribers/${subscriber.id}/edit`" style="color: inherit">
              <b>{{ subscriber.userName }}</b
              ><br />
              <span>{{ subscriber.userEmail }}</span>
            </a>
          </td>
          <td>{{ subscriber.totalTimesBlocked }} / {{ subscriber.blockedLimit }}</td>
          <td>
            <span v-html="formatDateTimeSingleLine(subscriber.createdAt)"></span>
          </td>
          <td>
            <StatusBadge :status="Boolean(subscriber.isLocked) ? 'blocked' : 'allowed'" />
          </td>
          <td>
            <DropdownButton
              :id="subscriber.id"
              :items="
                getActions(
                  subscriber.userId,
                  subscriber.userName,
                  Boolean(subscriber.isLocked),
                  subscriber.accesses
                )
              "
            />
          </td>
        </tr>
      </template>
      <template v-else v-slot:body>
        <tr>
          <td colspan="7" class="text-center" style="padding: 40px">
            Não há alunos cadastrados.
          </td>
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
    <StatusModal :is-open="loading" status="loading" />
    <QuestionModal
      :is-open="questionModal.open"
      :title="questionModal.title"
      :description="questionModal.description"
      :callback="questionModal.callback"
    />
    <Modal
      id="blocked"
      :is-open="blockedModal.open"
      modal-size="xl"
      @close="() => (blockedModal.open = false)"
    >
      <h5>Lista de bloqueios</h5>
      <Table id="blockedTable">
        <template v-slot:header>
          <th>Data do bloqueio</th>
          <th>IP</th>
          <th>Localização</th>
        </template>
        <template v-if="blockedModal.accesses" v-slot:body>
          <tr :key="block.createdAt" v-for="block in blockedModal.accesses">
            <td v-html="formatDateTimeSingleLine(block.createdAt)"></td>
            <td>{{ block.userIp }}</td>
            <td>
              {{ block.userLocation }}
            </td>
          </tr>
        </template>
      </Table>
    </Modal>
  </div>
</template>

<script>
import axios from "axios";
import moment from "moment";

import formatDateTimeSingleLine from "../../../js/components/XgrowDesignSystem/Mixins/formatDateTimeSingleLine";

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
import Modal from "../../../js/components/XgrowDesignSystem/Modals/Modal";
import QuestionModal from "../../../js/components/XgrowDesignSystem/Modals/QuestionModal";
import StatusModal from "../../../js/components/StatusModalComponent";
import SwitchButton from "../../../js/components/XgrowDesignSystem/Form/SwitchButton";

import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";

import DatePicker from "vue-datepicker-next";
import "vue-datepicker-next/index.css";
import "vue-datepicker-next/locale/pt-br";

export default {
  components: {
    Modal,
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
    StatusModal,
    SwitchButton,
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
      blockedModal: {
        open: false,
        accesses: [],
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
        blocked: {
          options: [
            {label: 'Liberado', value: 0},
            {label: 'Bloqueado', value: 1}
          ],
          selected: [],
        },
      },
    };
  },
  watch: {
    "filter.search": function () {
      this.searchByTerm();
    },
  },
  async mounted() {
    await this.getSubscribers();
  },
  methods: {
    async getSubscribers() {
      this.loading = true;
      const res = await axios.get(blockedSubscriberRoute, {
        params: {
          page: this.pagination.currentPage,
          offset: this.pagination.limit,
          nameFilter: this.filter.search,
          blocked: this.filter.blocked.selected,
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
    getActions(id, name, isLocked, accesses) {
      const result = [
        {
          name: "Ver lista de bloqueios",
          ico: "fa-solid fa-user-lock",
          url: "#",
          callback: () => {
            this.blockedModal.open = true;
            this.blockedModal.accesses = accesses;
          },
        },
      ];

      if (isLocked) {
        result.push({
          name: "Liberar acesso",
          ico: "fa-solid fa-check",
          url: "#",
          callback: () =>
            this.openModal(
              `Liberar acesso do aluno`,
              `Deseja realmente liberar o acesso do aluno ${name}?`,
              () => this.updateSub(id, 0)
            ),
        });
      } else {
        result.push({
          name: "Banir acesso",
          ico: "fa-solid fa-ban red",
          url: "#",
          callback: () =>
            this.openModal(
              `Banir acesso do aluno`,
              `Deseja realmente banir o acesso do aluno ${name}?`,
              () => this.updateSub(id, 1)
            ),
        });
      }

      return result;
    },
    async searchByDate(date, field) {
      if (!date[0]) {
        this.filter[field].formated = null;
      } else {
        this.filter[field].value = date;

        const startDate = moment(this.filter[field].value[0]).format("YYYY-MM-DD");
        const finalDate = moment(this.filter[field].value[1]).format("YYYY-MM-DD");
        this.filter[field].formated = [startDate, finalDate];
      }
      await this.changeFilter();
    },
    async delete(id) {
      this.loading = true;
      try {
        const res = await axios.post("/subscribers/delete", {
          id,
        });

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
      try {
        await axios.get(`/subscribers/${id}/resend-date`);
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
    async updateSub(userId, action) {
      this.loading = true;
      try {
        await axios.put(updateStatusURL, {
          userId: userId,
          action: action,
        });

        await this.getSubscribers()

        successToast(
          "Sucesso!",
          `Usuário ${action == 0 ? "liberado" : "bloqueado"} com sucesso!`
        );
      } catch (error) {
        errorToast("Algum erro aconteceu!", `${error.response.data.message}`);
      }
      this.questionModal.open = false
      this.loading = false
    },
  },
};
</script>

<style scoped lang="scss">
.import {
  &__instructions {
    margin-bottom: 30px;
  }

  &__container {
    display: flex;
    gap: 60px;
  }

  &__file {
    p {
      font-size: 12px;
      font-style: italic;
      font-weight: 400;
      line-height: 19px;
      letter-spacing: 0em;
      text-align: left;
      margin-bottom: 20px;
    }
  }
}
</style>
<style lang="scss">
#advancedFilters {
  border-top: 1px solid rgba(255, 255, 255, 0.25);
  border-bottom: 1px solid rgba(255, 255, 255, 0.25);
  background: rgba(0, 0, 0, 0.2);
}

#blocked {
  h5 {
    text-align: center;
  }
  .modal__content {
    padding: 40px;
  }
}

.create-button:hover {
  color: #fff !important;
}
</style>
