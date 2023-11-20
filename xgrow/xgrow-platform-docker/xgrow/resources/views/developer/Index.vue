<template>
  <div>
    <nav class="xgrow-breadcrumb mt-3" aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <RouterLink to="/">
            <span>Ínicio</span>
          </RouterLink>
        </li>
        <li class="breadcrumb-item">
          <span>Configurações</span>
        </li>
        <li class="breadcrumb-item active">
          <span>Desenvolvedor</span>
        </li>
      </ol>
    </nav>
    <div class="xgrow-card card-dark py-4">
      <Table class="tokens">
        <template v-slot:title>
          <div class="xgrow-table-header w-100 d-flex justify-content-between">
            <div>
              <h5 class="title">Tokens: {{ results.length }}</h5>
              <p>Veja todos os seus tokens cadastrados ou adicione novos.</p>
            </div>
            <Button
              text="Gerar token"
              status="success"
              icon="fa fa-plus"
              :onClick="openTokenModal"
            />
          </div>
          <hr />
        </template>
        <!-- <template v-slot:filter>
          <div
            class="d-flex mb-3 flex-wrap gap-3 align-items-end justify-content-between"
          >
            <div class="d-flex gap-3 align-items-end flex-wrap">
              <Input
                id="searchIpt"
                icon="<i class='fa fa-search'></i>"
                placeholder="Pesquise pelo domínio..."
                v-model="filter.search"
                autocomplete="chrome-off"
                class="token__search"
              />
            </div>
          </div>
        </template> -->
        <template v-slot:header>
          <th>API Token</th>
          <th>Domínio</th>
          <th>Dt. criação</th>
          <th style="width: 60px"></th>
        </template>
        <template v-if="results.length > 0" v-slot:body>
          <tr :key="item.id" v-for="(item, index) in results">
            <td>
              <span v-if="item.show" class="token__key">
                {{ item.key }}
              </span>
              <span v-else class="token__key"> ************** </span>
              <button
                v-if="item.show"
                class="token__actions"
                @click="showToken(item)"
                title="Ocultar token"
              >
                <i class="fas fa-eye-slash"></i>
              </button>
              <button
                v-else
                class="token__actions"
                @click="showToken(item)"
                title="Mostrar token"
              >
                <i class="fas fa-eye"></i>
              </button>
              <div class="token__link">
                <div class="token__copy-message" v-show="copyMessageIndex === index">
                  <i class="fas fa-check"></i>Link copiado para área de transferência
                </div>
                <button class="token__actions" @click="copyLink(item.key, index)">
                  <i class="far fa-copy"></i>
                </button>
              </div>
            </td>
            <td>{{ item.domain }}</td>
            <td>{{ formatDateSingleLine(item.created_at) }}</td>
            <td>
              <div class="dropdown x-dropdown">
                <button
                  class="xgrow-button xgrow-button-action table-action-button m-1"
                  type="button"
                  id="dropdownMenuButton"
                  data-bs-toggle="dropdown"
                >
                  <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul
                  class="dropdown-menu table-menu"
                  aria-labelledby="dropdownMenuButton$"
                >
                  <li role="button" class="dropdown-item table-menu-item" @click="openDeleteConfirmationModal(item)">
                    <i class="icon icon--red fa fa-trash"></i>
                    Excluir token
                  </li>
                </ul>
              </div>
            </td>
          </tr>
        </template>
        <template v-else v-slot:body>
          <tr>
            <td colspan="5" class="text-center">Nenhum Token encontrado.</td>
          </tr>
        </template>
        <!-- <template v-slot:footer>
          <Pagination
            class="mt-4"
            :total-pages="pagination.totalPages"
            :total="pagination.totalResults"
            :current-page="pagination.currentPage"
            @page-changed="onPageChange"
            @limit-changed="onLimitChange"
          />
        </template> -->
      </Table>
    </div>


    <ConfirmModal :is-open="modal.isOpen" :key="modal.count">
      <Title>{{modal.title}}</Title>
      <Row class="w-100" v-if="modal.icon.active">
        <Col class="modal-body__text">
          <i :class="`modalIcon fas ${modal.icon.symbol}`" :style="`color: ${modal.icon.color}`"></i>
        </Col>
      </Row>

      <Row class="w-100">
        <Col :class="{'modal-body__text': !modal.icon.active}">
          <p class="text-center">{{modal.text}}</p>
        </Col>
      </Row>

      <Row class="w-100" v-if="modal.input.active">
          <Col class="pb-2">
            <Input :id="'modal' + modal.model" :label="modal.input.label" v-model="domain"
                    :placeholder="modal.input.placeholder"/>
          </Col>
      </Row>
      <div class="modal-body__footer">
        <Button v-if="modal.buttons.cancel.active" :text="modal.buttons.cancel.label" outline @click="modal.buttons.cancel.callback"/>
        <Button v-if="modal.buttons.confirm.active" :text="modal.buttons.confirm.label" status="success" @click="modal.buttons.confirm.callback(modal.item.id)"/>
      </div>
    </ConfirmModal>

    <Loading :isOpen="loading" />

    <!-- <AuthModal /> -->
  </div>
</template>

<script>
import Table from "../../js/components/Datatables/Table";
import Input from "../../js/components/XgrowDesignSystem/Form/Input";
import Button from "../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import TokenModal from "./modals/Token";
import AuthModal from "./modals/Auth";
import SuccessModal from "./modals/Success";
import FailedModal from "./modals/Failed";
import DeleteConfirmModal from "./modals/Delete";
import Pagination from "../../js/components/Datatables/Pagination";
import formatDateSingleLine from "../../js/components/XgrowDesignSystem/Mixins/formatDateSingleLine";
// import StatusModalComponent from "../../js/components/StatusModalComponent";
import Loading from "../../js/components/XgrowDesignSystem/Utils/Loading";
import ConfirmModal from "../../js/components/XgrowDesignSystem/Modals/ConfirmModal";
import Col from "../../js/components/XgrowDesignSystem/Utils/Col";
import Row from "../../js/components/XgrowDesignSystem/Utils/Row";
import Title from "../../js/components/XgrowDesignSystem/Typography/Title"
import Subtitle from "../../js/components/XgrowDesignSystem/Typography/Subtitle"
import axios from "axios";

export default {
  name: "Index",
  components: {
    Table,
    Input,
    Button,
    Pagination,
    TokenModal,
    AuthModal,
    SuccessModal,
    // StatusModalComponent,
    DeleteConfirmModal,
    FailedModal,
    Loading,
    ConfirmModal,
    Col,
    Row,
    Title,
    Subtitle
  },
  mixins: [formatDateSingleLine],
  data() {
    return {
      results: [],
      loading: false,
      pagination: {
        totalPages: 1,
        totalResults: 0,
        currentPage: 1,
        limit: 25,
      },
      filter: {
        search: "",
      },
      domain: "",
      copyMessageIndex: null,
      /** Axios config */
      axiosHeader: null,
      axiosUrl: null,
      modal: {
        title: '',
        text: '',
        isOpen: false,
        input: { active: false, model: '', label: '', placeholder: '' },
        icon: { active: false, color: "white", symbol: "fa-check" },
        buttons: {
          confirm: { label: 'Confirmar', callback: () => {}, active: true },
          cancel: { label: 'Cancelar', callback: () => {}, active: false }
        },
        item: {},
      }
    };
  },
  watch: {
    "filter.search": async function () {
      await this.search();
    },
  },
  async mounted() {
    await this.setAxiosHeader();
    await this.getData();
  },
  methods: {
    async setAxiosHeader() {
        let res = await axios.get('/learning-area/producer-connect');
        this.axiosHeader = {headers: {Authorization: 'Bearer ' + res.data.response.atx}, };
        this.axiosUrl = res.data.response.url;
    },
    async getData() {
      this.loading = true;

      await axios.get(`${oauthURL}/producer-token`, this.axiosHeader)
        .then(({data}) => {
            this.results = data.map(item => ({...item, show: false}));
        })
        .catch(({response}) => {
            const { message } = response.data;
            errorToast("Algum erro aconteceu!", message ?? "Não foi possível receber os dados, entre em contato com o suporte.");
        });

        this.loading = false;
    },
    async deleteToken(id) {
      this.loading = true;

      await axios.delete(`${oauthURL}/producer-token/${id}`, this.axiosHeader)
        .then(({data}) => {
          successToast("Sucesso!", data.message ?? "Token excluido com sucesso.");
        })
        .catch(({response}) => {
          const { message } = response.data;
          errorToast("Algum erro aconteceu!", message ?? "Não foi possível remover o token, entre em contato com o suporte.");
        });

      this.clearModal();
      this.loading = false;
      await this.getData();
    },
    async createToken() {
      this.loading = true;

      if (this.domain == "") {
        errorToast("Erro!", "O Campo Domínio está vazio!");
        this.loading = false;
        return;
      }

      const params = { domain: this.domain };

      await axios.post(`${oauthURL}/producer-token`, params, this.axiosHeader)
        .then(({data}) => {
          this.clearModal();
          this.openSuccessModal();
        })
        .catch(({response}) => {
          this.clearModal();
          this.openFailedModal();
        });

      this.domain = "";
      this.loading = false;
      await this.getData();
    },
    async onPageChange(page) {
      this.pagination.currentPage = page;
      // await this.getData();
    },
    async onLimitChange(value) {
      this.pagination.limit = parseInt(value);
      this.pagination.currentPage = 1;
      // await this.getData();
    },
    async search() {
      let term = this.filter.search;
      setTimeout(async () => {
        if (term === this.filter.search) {
          this.pagination.currentPage = 1;
          // await this.getData();
        }
      }, 1000);
    },
    showToken(item) {
      item.show = !item.show;
    },
    copyLink(link, index) {
      navigator.clipboard.writeText(link);
      this.copyMessageIndex = index;
      setTimeout(() => (this.copyMessageIndex = null), 3000);
    },
    openDeleteConfirmationModal(item) {
      this.modal = {
        title: 'Tem certeza que deseja excluir o Token?',
        text: 'Essa ação é irreversível!',
        isOpen: true,
        input: { active: false, model: '', label: '', placeholder: '' },
        icon: { active: true, color: "white", symbol: "fa-question-circle" },
        buttons: {
          confirm: { label: 'Confirmar', callback: this.deleteToken, active: true },
          cancel: { label: 'Cancelar', callback: () => { this.modal.isOpen = false }, active: true }
        },
        item,
        count: 0
      }
    },
    openTokenModal() {
      this.modal = {
        title: 'Gerar token',
        text: 'Insira abaixo o domínio desejado para gerar um token:',
        isOpen: true,
        input: { active: true, model: this.domain, label: 'Domínio', placeholder: 'https://' },
        icon: { active: false, color: "white", symbol: "fa-question-circle" },
        buttons: {
          confirm: { label: 'Salvar', callback: this.createToken, active: true },
          cancel: { label: 'Cancelar', callback: () => { this.modal.isOpen = false }, active: true }
        },
        item: {},
        count: 1
      }
    },
    openSuccessModal() {
      this.modal = {
        title: 'Token gerado com sucesso!',
        text: `O novo token atrelado ao domínio “${this.domain}” foi gerado com sucesso. Clique em “OK” para continuar.`,
        isOpen: true,
        input: { active: false, model: this.domain, label: 'Domínio', placeholder: 'https://' },
        icon: { active: true, color: "#93BC1E", symbol: "fa-check-circle" },
        buttons: {
          confirm: { label: 'OK', callback: () => { this.modal.isOpen = false }, active: true },
          cancel: { label: 'Cancelar', callback: () => { this.modal.isOpen = false }, active: false }
        },
        item: {},
        count: 2
      }
    },
    openFailedModal() {
      this.modal = {
        title: 'Erro ao gerar o token!',
        text: `Ocorreu um erro inesperado ao gerar o token para o domínio “${this.domain}”. Tente novamente mais tarde.`,
        isOpen: true,
        input: { active: false, model: this.domain, label: 'Domínio', placeholder: 'https://' },
        icon: { active: true, color: "#EB5757", symbol: "fa-times-circle" },
        buttons: {
          confirm: { label: 'OK', callback: () => { this.modal.isOpen = false }, active: true },
          cancel: { label: 'Cancelar', callback: () => { this.modal.isOpen = false }, active: false }
        },
        item: {},
        count: 3
      }
    },
    clearModal() {
      this.modal.title = '',
      this.modal.text = '',
      this.modal.isOpen = false,
      this.modal.input = { active: false, model: '', label: '', placeholder: '' },
      this.modal.icon = { active: false, color: "white", symbol: "fa-check" },
      this.modal.buttons = {
        confirm: { label: 'Confirmar', callback: () => {}, active: true },
        cancel: { label: 'Cancelar', callback: () => {}, active: false }
      },
      this.modal.item = {},
      this.modal.count = -1
    },
    updateDomainValue(domain) {
      this.domain = domain;
    }
  },
};
</script>
<style>
.dropdown-item:hover {
  color: #fff;
  background-color: #222429;
}

.modal__content {
  padding: 30px 40px;
  color: #fff;
}
</style>
<style lang="scss" scoped>
.token {
  &__key {
    margin-right: 12px;
  }
  &__actions {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background-color: var(--green1);
    border: 0px;
    margin-right: 12px;
    color: #fff;
  }
  &__search {
    width: 400px;
    padding: 0;
    margin: 0;
  }

  &__copy-message {
    background: #1a1a1a;
    color: white;
    position: absolute;
    top: -35px;
    right: -236px;
    border-radius: 8px;
    padding: 5px 10px;
    font-weight: 400;
    width: 290px;
  }
  &__link {
    position: relative;
    display:inline-block;
  }
}
.icon {
  color: #f96c6c;
}
.fa-check {
  color: var(--green1);
  margin-right: 5px;
}

.modalIcon {
  font-size: 6rem;
}

.modal-body {
  padding: 10px 50px!important;

  &__text {
    border-top: 1px solid rgba(196, 196, 196, .15);
    padding-top: 20px;
  }

  &__footer {
    border-top: 1px solid rgba(196, 196, 196, .15);
    margin-top: 0!important;
    padding-top: 40px;
  }
}
</style>
