<template>
  <div class="xgrow-card card-dark py-4 material">
    <Table id="eventsTable">
      <template v-slot:title>
        <div class="d-flex flex-column">
          <Row>
            <Title>Eventos: {{ pagination.totalResults }}</Title>
          </Row>
          <Row>
            <Subtitle
              >Veja todos os eventos associados aos seus afiliados.</Subtitle
            >
          </Row>
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
              placeholder="Pesquise pelo tipo do evento..."
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
              <Col sm="12" md="4" lg="4" xl="4" class="my-4">
                <Multiselect
                  :options="options.eventType"
                  v-model="filter.eventType"
                  :searchable="true"
                  mode="tags"
                  placeholder="Digite o tipo de evento ou selecione um..."
                  :canClear="true"
                  @change="updateAdvancedFilters"
                >
                  <template v-slot:noresults>
                    <p class="multiselect-option" style="opacity: 0.5">
                      Tipo de evento não encontrado...
                    </p>
                  </template>
                </Multiselect>
              </Col>
              <Col sm="12" md="4" lg="4" xl="4" class="my-4">
                <Input
                  v-model="filter.source"
                  label="Digite a origem do evento"
                  id="sourceIpt"
                />
              </Col>
              <Col sm="12" md="4" lg="4" xl="4" class="my-4">
                <DatePicker
                  :value="filter.eventDate"
                  format="DD/MM/YYYY"
                  :clearable="true"
                  type="date"
                  range
                  placeholder="Data do evento"
                  value-type="format"
                  @change="updateDatePicker"
                />
              </Col>
              <Col sm="12" md="4" lg="4" xl="4" class="mb-4">
                  <Input
                      v-model="filter.affiliates"
                      label="Digite o ID do afiliado"
                      id="affiliateID"
                  />
<!--                <Multiselect-->
<!--                  :options="options.affiliates"-->
<!--                  v-model="filter.affiliates"-->
<!--                  :searchable="true"-->
<!--                  mode="tags"-->
<!--                  placeholder="Digite o nome do afiliado ou selecione..."-->
<!--                  :canClear="true"-->
<!--                  @change="updateAdvancedFilters"-->
<!--                >-->
<!--                  <template v-slot:noresults>-->
<!--                    <p class="multiselect-option" style="opacity: 0.5">-->
<!--                      Affiliado não encontrado...-->
<!--                    </p>-->
<!--                  </template>-->
<!--                </Multiselect>-->
              </Col>
              <Col sm="12" md="4" lg="4" xl="4" class="mb-4">
                <Multiselect
                  :options="options.plans"
                  v-model="filter.plans"
                  :searchable="true"
                  mode="tags"
                  placeholder="Digite o nome de um plano ou selecione um..."
                  :canClear="true"
                  @change="updateAdvancedFilters"
                >
                  <template v-slot:noresults>
                    <p class="multiselect-option" style="opacity: 0.5">
                      Plano não encontrado...
                    </p>
                  </template>
                </Multiselect>
              </Col>
            </Row>
          </div>
        </div>
      </template>
      <template v-slot:header>
        <th
          v-for="header in [
            'Tipo',
            'Origem',
            'Data',
            'Afiliado',
            'Plataforma e plano',
          ]"
          :key="header"
        >
          {{ header }}
        </th>
      </template>
      <template v-slot:body v-if="results.length">
        <tr :key="`link-${item.id + i}`" v-for="(item, i) in results">
          <td>{{ splitCamelCase(item.type) }}</td>
          <td>{{ item.source }}</td>
          <td v-html="formatDateTimeDualLine(item.created_at)"></td>
          <td>{{ item.affiliate_name }}</td>
          <td>
            <span style="font-weight: 600">
              {{ item.platform_name }}
            </span>
            <br />
            {{ item.plan_name }}
          </td>
          <td style="width: 20px">
            <Tooltip
              v-if="item.order_number && item.buyer"
              :id="`item${pagination.currentPage}${i}`"
              icon="<i class='fas fa-info-circle'></i>"
              :allow-HTML="true"
              :tooltip="`
              <p
                class='text-center'
                style='font-weight: bold;
              '>Comprador</p>
              <p><b>Nome: </b>${item.buyer.name}</p>
              <p><b>E-mail: </b>${item.buyer.email}</p>
              `"
            />
          </td>
        </tr>
      </template>
      <template v-else v-slot:body>
        <tr>
          <td colspan="11">Não há eventos associados aos seus afiliados</td>
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

    <StatusModalComponent :is-open="loading" status="loading" />
  </div>
</template>

<script>
import Title from "../../../../js/components/XgrowDesignSystem/Typography/Title";
import Subtitle from "../../../../js/components/XgrowDesignSystem/Typography/Subtitle";
import Row from "../../../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../../../js/components/XgrowDesignSystem/Utils/Col";
import FilterButton from "../../../../js/components/XgrowDesignSystem/Buttons/FilterButton";
import Input from "../../../../js/components/XgrowDesignSystem/Form/Input";
import Tooltip from "../../../../js/components/XgrowDesignSystem/Tooltips/XgrowTooltip";
import Table from "../../../../js/components/Datatables/Table";
import Pagination from "../../../../js/components/Datatables/Pagination";
import Modal from "../../../../js/components/XgrowDesignSystem/Modals/Modal";
import StatusModalComponent from "../../../../js/components/StatusModalComponent";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import DatePicker from "vue-datepicker-next";
import "vue-datepicker-next/index.css";
import "vue-datepicker-next/locale/pt-br";
import formatDateTimeDualLine from "../../../../js/components/XgrowDesignSystem/Mixins/formatDateTimeDualLine";

import axios from "axios";

export default {
  name: "AffiliateEvents",
  components: {
    Table,
    Modal,
    Title,
    Subtitle,
    Row,
    Col,
    Pagination,
    FilterButton,
    Input,
    Multiselect,
    DatePicker,
    StatusModalComponent,
    Tooltip,
  },
  mixins: [formatDateTimeDualLine],
  data() {
    return {
      pagination: {
        totalPages: 1,
        totalResults: 0,
        currentPage: 1,
        offset: 25,
      },
      results: [],
      loading: false,
      filter: {
        search: "",
        eventType: [],
        eventDate: [],
        affiliates: "",
        plans: [],
        source: "",
      },
      options: {
        eventType: [],
        plans: [],
        affiliates: [],
      },
    };
  },
  props: {
    content: {
      type: String,
      default: "",
    },
  },
  methods: {
    search: async function () {
      let term = this.filter.search;

      setTimeout(async () => {
        if (term === this.filter.search) {
          this.pagination.currentPage = 1;
          this.getData();
        }
      }, 1000);
    },
    source: async function () {
      let term = this.filter.source;

      setTimeout(async () => {
        if (term === this.filter.source) {
          this.getData();
        }
      }, 1000);
    },
    affiliates: async function () {
      let term = this.filter.affiliates;

      setTimeout(async () => {
          if (term === this.filter.affiliates) {
              this.getData();
          }
      }, 1000);
    },
    onLimitChange(limit) {
      this.pagination.offset = limit;
      this.pagination.currentPage = 1;
      this.getData();
    },
    onPageChange(page) {
      this.pagination.currentPage = page;
      this.getData();
    },
    updateDatePicker(date) {
      this.filter.eventDate = date;
      this.pagination.currentPage = 1;
      this.getData();
    },
    updateAdvancedFilters() {
      setTimeout(async () => {
        this.getData();
      }, 500);
    },
    async getData() {
      const params = {
        limit: this.pagination.offset,
        page: this.pagination.currentPage,
        type: [this.joinCamelCase(this.filter.search)],
        start_created_at: this.filter.eventDate[0],
        end_created_at: this.filter.eventDate[1],
        source: this.filter.source ? this.filter.source : null,
        affiliateId: this.filter.affiliates ? this.filter.affiliates : null,
        planId: this.filter.plans.length ? this.filter.plans : null,
      };

      if (this.filter.eventType.length)
        params.type.push(...this.filter.eventType);

      this.loading = true;

      await axios
        .get(affiliateEventsList, { params })
        .then(({ data }) => {
          const { response } = data;
          const {
            events,
            current_page,
            per_page,
            total,
            total_pages
          } = response;

          this.results = events;

          this.updatePurchaseEvents();

          this.pagination.offset = per_page;
          this.pagination.currentPage = current_page;
          this.pagination.totalResults = total;
          this.pagination.totalPages = total_pages;
        })
        .catch((e) => {
          const defaultErrorMessage = "Não foi possível carregar os dados de eventos, entre em contato com o suporte.";
          errorToast(
            "Algum erro aconteceu!",
            e?.response?.data?.message || defaultErrorMessage
          );
        });

      this.loading = false;
    },
    async getFilters() {
      this.loading = true;

      await axios
          .get(affiliateEventsFilters)
          .then(({ data }) => {
              const { response } = data;
              const {
                  plans,
                  types,
              } = response;

              this.options.plans = Object.entries(plans).map((plan) => {
                  return { value: plan[0], label: plan[1] };
              });

              this.options.eventType = types.map((type) => {
                  return { value: type, label: this.splitCamelCase(type) };
              });

          })
          .catch((e) => {
              const defaultErrorMessage = "Não foi possível carregar os filtros, entre em contato com o suporte.";
              errorToast(
                  "Algum erro aconteceu!",
                  e?.response?.data?.message || defaultErrorMessage
              );
          });

      this.loading = false;
    },

    async getBuyerInfo(order_number) {
      const url = buyerInfo.replace("order_number", order_number);

      let buyer = {
        name: "Comprador não identificado",
        email: "Não encontrado",
      };

      await axios
        .get(url)
        .then(({ data }) => {
          if (data.response.buyer[0]) {
            const { name, email } = data.response.buyer[0];
            buyer = { name, email };
          }
        })
        .catch((e) => {
          errorToast(
            "Algum erro aconteceu!",
            "Não foi possível carregar as informações do comprador"
          );
        });

      return buyer;
    },
    async updatePurchaseEvents() {
      if (!this.results) return;

      let purchaseEvents = this.results
        .map((event, index) => {
          return event.order_number ? index : false;
        })
        .filter((index) => index || index === 0);

      for (let i = 0; i < purchaseEvents.length; i++) {
        const event = this.results[purchaseEvents[i]];
        event.buyer = await this.getBuyerInfo(event.order_number);
      }
    },
    splitCamelCase(str) {
      let i = 0;
      let src = "";
      while (i < str.length) {
        src += this.checkIsUpperCase(str[i])
          ? ` ${str[i].toLowerCase()}`
          : str[i];
        i++;
      }
      return src;
    },
    joinCamelCase(str) {
      let src = "";
      let isSpace = false;

      for (let i = 0; i < str.length; i++) {
        if (str[i] == " ") {
          isSpace = true;
        } else {
          src += isSpace ? str[i].toUpperCase() : str[i];
          isSpace = false;
        }
      }

      return src;
    },
    checkIsUpperCase(char) {
      return char.toUpperCase() === char && char !== char.toLowerCase();
    },
  },
  watch: {
    "filter.search": function () {
      this.search();
    },
    "filter.source": function () {
      this.source();
    },
    "filter.affiliates": function () {
      this.affiliates();
    },
  },
  async mounted() {
    await this.getData();
    await this.getFilters();
  },
};
</script>

<style lang="scss" scoped>
.search-input {
  width: 400px;
  padding: 0;
  margin: 0;
}

.collapse-card {
  background: #222429;
  border: 1px solid #333844;
  box-shadow: 0px 2px 20px rgba(0, 0, 0, 0.15);
  border-radius: 4px;
}

.advancedFilters__icon {
  color: #addf45;
}
</style>
