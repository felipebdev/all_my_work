<template>
  <div>
    <Breadcrumbs :items="breadcrumbs"></Breadcrumbs>

    <Container>
      <template v-slot:header-left>
        <Title>Usuários: {{ pagination.totalResults }}</Title>
        <Subtitle
          >Veja todos os usuários cadastrados em sua plataforma ou cadastre
          novos.</Subtitle
        >
      </template>
      <template v-slot:header-right>
        <DefaultButton
          status="success"
          icon="fas fa-plus"
          text="Novo usuário"
          :on-click="() => redirectToCreate()"
        ></DefaultButton>
      </template>
      <template v-slot:content>
        <Table id="users-table">
          <template v-slot:header>
            <th
              v-for="header in ['ID', 'Nome', 'E-mail', 'Tipo de acesso', 'Permissão']"
              :key="header"
            >
              {{ header }}
            </th>
            <th style="width: 80px"></th>
          </template>
          <template v-slot:body v-if="results.length">
            <tr :key="`link-${item.id + i}`" v-for="(item, i) in results">
              <td>{{ item.id }}</td>
              <td>{{ item.name }}</td>
              <td>{{ item.email }}</td>
              <td>{{ item.type_access == "full" ? "Total" : "Restrito" }}</td>
              <td>{{ setTypeAccess(item) }}</td>
              <td>
                <DropdownButton
                  v-if="item.id != owner.id"
                  :id="item.id"
                  :items="getActions(item)"
                />
              </td>
            </tr>
          </template>
          <template v-slot:body v-else>
            <tr>
              <td colspan="11">teste1</td>
            </tr>
          </template>
          <template v-slot:footer>
            <Pagination
              :offset="this.pagination.limit"
              :total-pages="this.pagination.totalPages"
              :total="this.pagination.totalResults"
              :current-page="this.pagination.currentPage"
              @limit-changed="onLimitChange"
              @page-changed="onPageChange"
            />
          </template>
        </Table>
      </template>
    </Container>

    <Loading :is-open="loading"></Loading>

    <ConfirmModal :is-open="confirmModal.isOpen">
      <div class="modal-body__content">
        <h1>Deseja excluir o usuário ({{ confirmModal.item.name }})?</h1>
        <p>Ao remover este usuário, o mesmo não poderá ser recuperado!</p>
      </div>
      <div class="modal-body__footer">
        <DefaultButton
          text="Cancelar"
          outline
          @click="() => closeModalConfirm()"
        ></DefaultButton>
        <DefaultButton
          text="Excluir mesmo assim"
          status="success"
          @click="deleteUser(confirmModal.item.id)"
        >
        </DefaultButton>
      </div>
    </ConfirmModal>
  </div>
</template>

<script>
import axios from "axios";
import Container from "../../js/components/XgrowDesignSystem/Cards/Container";
import Loading from "../../js/components/XgrowDesignSystem/Utils/Loading";
import DefaultButton from "../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import Breadcrumbs from "../../js/components/XgrowDesignSystem/Breadcrumb/XgrowBreadcrumb";
import Title from "../../js/components/XgrowDesignSystem/Typography/Title";
import Subtitle from "../../js/components/XgrowDesignSystem/Typography/Subtitle";
import Table from "../../js/components/Datatables/Table";
import ButtonDetail from "../../js/components/Datatables/ButtonDetail";
import Pagination from "../../js/components/Datatables/Pagination";
import ConfirmModal from "../../js/components/XgrowDesignSystem/Modals/ConfirmModal";
import DropdownButton from "../../js/components/XgrowDesignSystem/Buttons/DropdownButtonV2";

export default {
  components: {
    Container,
    Loading,
    DefaultButton,
    Breadcrumbs,
    Title,
    Subtitle,
    Table,
    ButtonDetail,
    Pagination,
    ConfirmModal,
    DropdownButton,
  },
  data() {
    return {
      breadcrumbs: [
        { title: "Início", link: "/" },
        { title: "Configurações", link: "/platform-config" },
        { title: "Usuários", link: false },
      ],
      results: [],
      pagination: {
        totalPages: 1,
        totalResults: 0,
        currentPage: 1,
        limit: 25,
      },
      owner: { id: "" },
      editUser,
      loading: false,
      confirmModal: {
        item: {},
        isOpen: false,
      },
    };
  },
  methods: {
    getActions(item) {
      const result = [
        {
          name: "Editar",
          ico: "fa fa-pencil",
          url: "#",
          callback: () => this.redirectToEdit(),
        },
        {
          name: "Excluir",
          ico: "fa fa-trash red",
          url: "#",
          callback: () => this.openConfirmModal(item),
        },
      ];

      return result;
    },
    async onLimitChange(limit) {
      this.pagination.currentPage = 1;
      this.pagination.limit = limit;
      await this.getData();
    },
    async onPageChange(page) {
      this.pagination.currentPage = page;
      await this.getData();
    },
    async getData() {
      this.loading = true;

      const params = {
        offset: this.pagination.limit,
        page: this.pagination.currentPage,
        search: "",
      };

      try {
        const { data } = await axios.get(getUsers, { params });
        const { users, owner } = data.response;

        this.results = users.data;
        this.owner.id = owner.id;

        this.pagination.totalPages = users.last_page;
        this.pagination.totalResults = users.total;
        this.pagination.currentPage = users.current_page;
        this.pagination.limit = users.per_page;
      } catch (e) {
        console.log(e.response.message);
      }

      this.loading = false;
    },
    async deleteUser(id) {
      const url = deleteUser.replace("id_user", id);

      this.closeModalConfirm();
      this.loading = true;

      await axios
        .delete(url)
        .then(async ({ data }) => {
          console.log(data[0]);
          if (data[0] == 400) {
            errorToast("Um erro aconteceu!", data.message);
          } else {
            successToast("Usuário removido!", `Usuário removido com sucesso!`);
            await this.getData();
          }
        })
        .catch((e) => {
          console.log(e.response.message);
          errorToast("Um erro aconteceu!", e.response.message);
        });

      this.loading = false;
    },
    redirectToCreate() {
      this.loading = true;
      window.location.href = "/platform-config/users/create";
    },
    redirectToEdit() {
      this.loading = true;
      window.location.href = this.editUser;
    },
    openConfirmModal(item) {
      this.confirmModal.isOpen = true;
      this.confirmModal.item = item;
    },
    closeModalConfirm() {
      this.confirmModal.isOpen = false;
      this.confirmModal.item = {};
    },
    setTypeAccess(user) {
      const accessType = {
        full: "Total",
        owner: "Proprietário",
      };
      return user.email == owner ? accessType["owner"] : accessType["full"];
    },
  },
  async mounted() {
    await this.getData();
  },
};
</script>
