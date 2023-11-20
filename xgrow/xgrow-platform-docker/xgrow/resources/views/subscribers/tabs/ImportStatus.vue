<template>
  <div class="xgrow-card card-dark py-4 h-100 import">
    <Table id="salesTransactionsTable">
      <template v-slot:title>
        <div class="xgrow-table-header w-100">
          <h5 class="title">Status da importação</h5>
          <p>
            Verifique o status dos arquivos carregados e realize o upload novamente, caso
            precise.
          </p>
          <hr />
        </div>
      </template>
      <template v-slot:filter>
        <div class="d-flex my-3 flex-wrap gap-3 align-items-end justify-content-between">
          <div class="d-flex gap-3 align-items-end flex-wrap">
            <Input
              style="margin: 0px"
              id="searchIpt"
              icon="<i class='fa fa-search'></i>"
              placeholder="Pesquise pelo nome do arquivo..."
              v-model="filter.search"
              class="search-input"
            />
          </div>
        </div>
      </template>
      <template v-slot:header>
        <th>ID</th>
        <th>Data de importação</th>
        <th>Status</th>
        <th width="230px">Arquivo original</th>
        <th width="230px">Resultado</th>
      </template>
      <template v-if="files.length > 0" v-slot:body>
        <tr :key="file.id" v-for="file in files">
          <td>{{ file.id }}</td>
          <td>{{ file.date }}</td>
          <td>
            <StatusBadge :status="file.status" />
          </td>
          <td>
            <Button
              outline
              text="nome_do_arquivo"
              icon="fas fa-upload"
            />
          </td>
          <td>
            <Button
              outline
              text="nome_do_arquivo"
              icon="fas fa-upload"
            />
          </td>
        </tr>
      </template>
      <template v-else v-slot:body>
        <tr>
          <td colspan="7" class="text-center" style="padding: 40px">
            <img
              src="/xgrow-vendor/assets/img/new-no-result.svg"
              alt="Nenhum resultado encontrado."
            />
            <div class="d-flex justify-content-center align-items-center flex-column">
              <h6>Você não possui arquivos em importação</h6>
              <p style="max-width: 540px">
                Mas não se procupe, você pode adicionar alunos manualmente ou importar uma
                lista na aba “Importar alunos” acima.
              </p>
            </div>
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
  </div>
</template>

<script>
import Table from "../../../js/components/Datatables/Table";
import Pagination from "../../../js/components/Datatables/Pagination";
import FilterButton from "../../../js/components/XgrowDesignSystem/Buttons/FilterButton";
import Input from "../../../js/components/XgrowDesignSystem/Form/Input";
import StatusBadge from "../../../js/components/XgrowDesignSystem/Badge/StatusBadge";
import Button from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";

export default {
  components: {
    Table,
    Pagination,
    FilterButton,
    Input,
    StatusBadge,
    Button
  },
  data() {
    return {
      files: [
        {
          id: 1,
          date: "01/12/2022",
          status: "paid",
        },
      ],
      pagination: {
        totalPages: 1,
        totalResults: 0,
        currentPage: 1,
        limit: 25,
      },
      filter: {
        search: "",
      },
    };
  },
  methods: {},
};
</script>

<style scoped lang="scss">
.import {
  li {
    list-style: inherit;
  }

  h5 {
    margin-bottom: 30px;
  }

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

  &__send-file {
    width: 100%;
    display: flex;
    justify-content: flex-end;

    button {
      width: 200px;
    }
  }

  img {
    margin-bottom: 20px;
  }
}
</style>
<style>
/* .outline:hover {
  background: none !important;
  border-color: #fff !important;
} */
</style>
