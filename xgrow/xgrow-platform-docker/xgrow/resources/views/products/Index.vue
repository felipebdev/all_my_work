<template>
  <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="/">Início</a></li>
      <li class="breadcrumb-item active mx-2"><span>Produtos</span></li>
    </ol>
  </nav>
  <VerifyDocument v-if="verifyDocument" :description="recipientStatusMessage"/>
  <div class="xgrow-card card-dark py-4">
    <Table id="salesTransactionsTable">
      <template v-slot:title>
        <div class="xgrow-table-header w-100">
          <div class="d-flex justify-content-between">
            <h5 class="title">Produtos: {{ pagination.totalResults }}</h5>
            <a
              href="/products/create"
              role="button"
              class="xgrow-button create-product"
              style="display: flex; align-items: center; justify-content: space-evenly"
            >
              <i class="fa fa-plus"></i> Novo produto
            </a>
          </div>
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
              placeholder="Pesquise pelo nome do produto..."
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
              <Col sm="12" md="4" lg="4" xl="4" class="my-4">
                <Multiselect
                  :options="filter.productTypes.options"
                  v-model="filter.productTypes.selected"
                  :searchable="true"
                  mode="tags"
                  placeholder="Digite ou selecione o tipo do produto"
                  :canClear="true"
                  @select="changeFilter"
                  @deselect="changeFilter"
                  @clear="changeFilter('productTypes')"
                >
                  <template v-slot:noresults>
                    <p class="multiselect-option" style="opacity: 0.5">
                      Tipo de produto não encontrado...
                    </p>
                  </template>
                </Multiselect>
              </Col>
              <Col sm="12" md="4" lg="4" xl="4" class="my-4">
                <Multiselect
                  :options="filter.deliveryTypes.options"
                  v-model="filter.deliveryTypes.selected"
                  :searchable="true"
                  mode="tags"
                  placeholder="Digite ou selecione o tipo da entrega..."
                  :canClear="true"
                  @select="changeFilter"
                  @deselect="changeFilter"
                  @clear="changeFilter('deliveryTypes')"
                >
                  <template v-slot:noresults>
                    <p class="multiselect-option" style="opacity: 0.5">
                      Tipo de entrega não encontrado...
                    </p>
                  </template>
                </Multiselect>
              </Col>
              <Col sm="12" md="4" lg="4" xl="4" class="my-4">
                <Multiselect
                  :options="filter.status.options"
                  v-model="filter.status.selected"
                  :searchable="true"
                  mode="tags"
                  placeholder="Digite ou selecione o status de um produto..."
                  :canClear="true"
                  @select="changeFilter"
                  @deselect="changeFilter"
                  @clear="changeFilter('status')"
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
        <th>Tipo do produto</th>
        <th>Entrega</th>
        <th>Status</th>
        <th style="width: 60px"></th>
      </template>
      <template v-if="products.length > 0" v-slot:body>
        <tr :key="product.id" v-for="product in products">
          <td>
            {{ product.name }}
          </td>
          <td>
            {{ formatPaymentType(product.type) }}
          </td>
          <td>
            {{ formatDeliveryType(product) }}
          </td>
          <td>
            <div class="form-check form-switch">
              <input
                class="form-check-input"
                :id="`switch-${product.id}`"
                type="checkbox"
                :checked="Boolean(product.status)"
                @click="changeStatus(product.id)"
              />
              <label class="form-check-label" :for="`switch-${product.id}`"></label>
            </div>
          </td>
          <td>
            <DropdownButton
              :id="product.id"
              :items="
                getActions(product.id, product.name, product.subscribers_count !== 0)
              "
            />
          </td>
        </tr>
      </template>
      <template v-else v-slot:body>
        <tr>
          <td colspan="7" class="text-center">Não há produtos.</td>
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
      :callback="questionModal.callback"
    />
  </div>
</template>

<script>
import Table from "../../js/components/Datatables/Table";
import Pagination from "../../js/components/Datatables/Pagination";

import Subtitle from "../../js/components/XgrowDesignSystem/Typography/Subtitle";
import Row from "../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../js/components/XgrowDesignSystem/Utils/Col";
import FilterButton from "../../js/components/XgrowDesignSystem/Buttons/FilterButton";
import Input from "../../js/components/XgrowDesignSystem/Form/Input";
import Modal from "../../js/components/XgrowDesignSystem/Modals/Modal";
import QuestionModal from "../../js/components/XgrowDesignSystem/Modals/QuestionModal";
import VerifyDocument from "../../js/components/XgrowDesignSystem/Alert/VerifyDocument";
import DropdownButton from "../../js/components/XgrowDesignSystem/Buttons/DropdownButtonV2";

import StatusModal from "../../js/components/StatusModalComponent";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import axios from "axios";

export default {
  components: {
    DropdownButton,
    Table,
    Pagination,
    Subtitle,
    Row,
    Col,
    FilterButton,
    Input,
    Multiselect,
    StatusModal,
    Modal,
    VerifyDocument,
    QuestionModal,
  },
  data() {
    return {
      recipientStatusMessage,
      questionModal: {
        open: false,
        title: "",
        callback: () => {},
      },
      verifyDocument,
      productsEditURL,
      loading: false,
      products: [],
      pagination: {
        totalPages: 1,
        totalResults: 0,
        currentPage: 1,
        limit: 25,
      },
      filter: {
        search: "",
        productTypes: {
          options: {
            R: "Assinatura",
            P: "Venda única",
          },
          selected: [],
        },
        deliveryTypes: {
          options: {
            external: "Área Externa",
            internal: "Área de Aprendizado Unificada XGROW",
            onlySell: "Somente venda",
          },
          selected: [],
        },
        status: {
          options: {
            1: "Ativo",
            0: "Inativo",
          },
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
  computed() {},
  async mounted() {
    await this.getProducts();
  },
  methods: {
    async getProducts() {
      this.loading = true;

      try {
        const res = await axios.get(productsAllURL, {
          params: {
            page: this.pagination.currentPage,
            offset: this.pagination.limit,
            productTypes: this.filter.productTypes.selected,
            deliveryTypes: this.filter.deliveryTypes.selected,
            status: this.filter.status.selected,
            search: this.filter.search,
          },
        });

        const productsResponse = res.data.response.products;

        this.products = productsResponse.data;

        this.pagination.totalPages = productsResponse.last_page;
        this.pagination.totalResults = productsResponse.total;
        this.pagination.currentPage = productsResponse.current_page;
      } catch (error) {
        errorToast(
          "Algum erro aconteceu!",
          `Houve um erro ao alterar o registro: ${error.response.data.message}`
        );
      }

      this.loading = false;
    },
    async changeFilter(clear = false) {
      if (["productTypes", "deliveryTypes", "status"].includes(clear)) {
        this.filter[clear].selected = [];
      }
      this.pagination.currentPage = 1;
      await this.getProducts();
    },
    async paginationChange(type, page) {
      this.pagination[type] = parseInt(page);
      await this.getProducts();
    },
    formatPaymentType(payment) {
      const types = {
        P: "Venda única",
        R: "Assinatura",
      };

      return types[payment];
    },
    formatDeliveryType(product) {
      let delivery = "Entrega não selecionada";
      if (parseInt(product.only_sell)) {
        delivery = "Somente venda";
      } else if (parseInt(product.external_learning_area)) {
        delivery = "Área Externa";
      } else if (parseInt(product.internal_learning_area)) {
        delivery = "Área de Aprendizado Unificada XGROW";
      }
      return delivery;
    },
    async changeStatus(id) {
      const url = productsUpdateStatusURL.replace(/:id/g, id);

      try {
        await axios.put(url);
        return successToast("Registro alterado!", "Ação feita com sucesso!");
      } catch (error) {
        return errorToast(
          "Algum erro aconteceu!",
          `Houve um erro ao alterar o registro: ${error.response.data.message}`
        );
      }
    },
    async searchByTerm() {
      this.pagination.currentPage = 1;
      const term = this.filter.search;
      setTimeout(async () => {
        if (term === this.filter.search) {
          await this.getProducts();
        }
      }, 1000);
    },
    getActions(productId, productName, hideDelete) {
      const result = [
        {
          name: "Editar",
          ico: "fa fa-pencil",
          url: productsEditURL.replace(":id", productId),
        },
        {
          name: "Duplicar produto",
          ico: "far fa-copy",
          url: "#",
          callback: () =>
            this.openModal(
              `Deseja duplicar este produto?`,
              `Caso você duplique o produto ${productName} essa ação não poderá ser desfeita`,
              () => this.duplicateProduct(productId)
            ),
        },
        {
          name: "Links",
          ico: "fas fa-link",
          url: `${productsEditURL.replace(":id", productId)}#links`,
        },
        {
          name: "Excluir",
          ico: "fa-solid fa-xmark red",
          url: "#",
          callback: () =>
            this.openModal(
              `Deseja excluir este produto?`,
              `Caso você exclua o produto ${productName} essa ação não poderá ser desfeita`,
              () => this.deleteProduct(productId)
            ),
          hide: hideDelete,
        },
      ];

      return result;
    },
    async deleteProduct(productId) {
      this.loading = true;

      const deleteUrl = productsDeleteURL.replace(/:id/g, productId);

      try {
        await axios.delete(deleteUrl);
        successToast("Sucesso!", "Produto excluído com sucesso!");
      } catch (e) {
        errorToast("Alguem erro aconteceu!", "Erro ao excluir produto!");
      }

      await this.getProducts();
      this.questionModal.open = false;
      this.loading = false;
    },
    async duplicateProduct(productId) {
      this.loading = true;

      const replicateUrl = productsDuplicateURL.replace(/:id/g, productId);

      try {
        await axios.post(replicateUrl);
        successToast("Sucesso!", "Produto duplicado com sucesso!");
      } catch (e) {
        errorToast("Alguem erro aconteceu!", "Erro ao duplicar produto!");
      }

      await this.getProducts();
      this.questionModal.open = false;
      this.loading = false;
    },
    openModal(title, description, callback) {
      this.questionModal.open = true;
      this.questionModal.title = title;
      this.questionModal.description = description;
      this.questionModal.callback = callback;
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

.create-product:hover {
  color: #fff !important;
}
</style>
