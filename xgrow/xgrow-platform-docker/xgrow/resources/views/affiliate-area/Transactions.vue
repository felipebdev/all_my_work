<template>
  <div>
    <Breadcrumb :items="breadcrumbs" class="mb-3" />

    <Row class="pb-4">
      <Col sm="12" md="3" lg="3" xl="3">
        <Metric :title="formatBRLCurrency(metrics.commission)" subtitle="Minha comissão" borderColor="#4DA2D1" />
      </Col>
      <Col sm="12" md="3" lg="3" xl="3">
        <Metric :title="formatBRLCurrency(metrics.pending)" subtitle="Pendente" borderColor="#F4E558" />
      </Col>
      <Col sm="12" md="3" lg="3" xl="3">
        <Metric :title="formatBRLCurrency(metrics.refunded)" subtitle="Estornado" borderColor="#F45858" />
      </Col>
      <Col sm="12" md="3" lg="3" xl="3">
        <Metric :title="formatBRLCurrency(metrics.chargeback)" subtitle="Chargeback" borderColor="#F5F5F5" />
      </Col>
    </Row>

    <Container>
      <template v-slot:content>
      <Table
        id="transactionsTable">
        <template v-slot:title>
          <div class="d-flex flex-column">
            <Row>
              <Title>Transações: {{pagination.totalResults}}</Title>
            </Row>
            <Row>
              <Subtitle>Veja todas as suas transações realizadas.</Subtitle>
            </Row>
          </div>
        </template>
        <!-- <template v-slot:filter>
          <div class='d-flex my-3 flex-wrap gap-3 align-items-end justify-content-between pt-3' style="border-top: 1px solid #414655">
              <Row class="w-100">
                <Col sm="12" md="3" lg="3" xl="3">
                  <Input id='searchIpt' icon="<i class='fa fa-search'></i>" placeholder='Pesquise pelo nome ou e-mail...' v-model='filter.search' class="search-input" />
                </Col>
              </Row>
          </div>
        </template> -->
        <template v-slot:header>
          <th
            v-for="header in ['Produto', 'Cliente', 'Parcelas', 'Data', 'Status', 'Métodos', 'Minha comissão']"
            :key="header"
          >
            {{header}}
          </th>
          <!-- <th style="width: 80px"></th> -->
        </template>
        <template
          v-slot:body
          v-if="results.length"
        >
          <tr
            :key="`link-${item.id}`"
            v-for="(item) in results"
          >
            <td>{{item.product_name}}</td>
            <td v-if="item.obfuscated" class="client-info">
              <span class="client-info__name">
                {{item.client_name}}
              </span>
              <br>
              <span class="client-info__email">
                {{item.client_email}}
              </span>
            </td>
            <td v-else class="blur">John Doe <br> jhondoe@email.com</td>
            <td>{{item.installments > 1 ? `${item.installments}x` : 'à vista'}}</td>
            <td v-html="formatDateTimeDualLine(item.payment_date)"></td>
            <td><Status :status="item.payment_status" /></td>
            <td>
              {{item.payment_method == 'credit_card' ?
              ('Cartão de crédito').toUpperCase():
              (item.payment_method).toUpperCase()}}
            </td>
            <td>{{formatBRLCurrency(item.commission)}}</td>
            <!-- <td>
              <ButtonDetail :id="'details-' + i">
                <li class="dropdown-item table-menu-item">
                  <i class='fa fa-eye'></i>
                  Ver detalhes
                </li>
                <li class="dropdown-item table-menu-item">
                  <i class='fas fa-paper-plane'></i>
                  Enviar mensagem no Whatsapp
                </li>
              </ButtonDetail>
            </td> -->
          </tr>
        </template>
        <template
          v-else
          v-slot:body>
          <tr>
            <td colspan="11">Não há transações referentes a esse produto!</td>
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

    <StatusModalComponent :is-open="loading" status="loading" />
  </div>
</template>

<script>
import axios from "axios";

import formatBRLCurrency from '../../js/components/XgrowDesignSystem/Mixins/formatBRLCurrency'
import formatDateTimeDualLine from '../../js/components/XgrowDesignSystem/Mixins/formatDateTimeDualLine'

import Container from '../../js/components/XgrowDesignSystem/Cards/Container'
import Metric from '../../js/components/XgrowDesignSystem/Cards/FinancialCard'

import Title from "../../js/components/XgrowDesignSystem/Typography/Title";
import Subtitle from "../../js/components/XgrowDesignSystem/Typography/Subtitle";

import Row from "../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../js/components/XgrowDesignSystem/Utils/Col";

import FilterButton from "../../js/components/XgrowDesignSystem/Buttons/FilterButton";

import Input from "../../js/components/XgrowDesignSystem/Form/Input";

import Status from '../../js/components/XgrowDesignSystem/Badge/StatusBadge'

import Table from "../../js/components/Datatables/Table";
import Pagination from "../../js/components/Datatables/Pagination";
import ButtonDetail from "../../js/components/Datatables/ButtonDetail";

import StatusModalComponent from "../../js/components/StatusModalComponent";
import Breadcrumb from "../../js/components/XgrowDesignSystem/Breadcrumb/Breadcrumb";

export default {
  name: "Index",
  components: {
    Container, Title, Subtitle, Row, Col, FilterButton, Input, Table, Pagination,
    Status, ButtonDetail, Metric, StatusModalComponent,
    Breadcrumb
  },
  mixins: [formatBRLCurrency, formatDateTimeDualLine],
  data() {
    return {
      loading: false,
      platformId: localStorage.getItem("affiliates-platform_id"),
      productId: localStorage.getItem("affiliates-product_id"),
      productName: localStorage.getItem("affiliates-product_name"),
      metrics: {
        commission: 0,
        pending: 0,
        refunded: 0,
        chargeback: 0
      },
      pagination: {
        totalPages: 1,
        totalResults: 0,
        currentPage: 1,
        offset: 25
      },
      results: [],
      filter: {
        search: '',
      },
      breadcrumbs: [
        { title: "Área do afiliado", link: "/affiliations", isVueRouter: true },
        { title: "Produtos", link: "/affiliations/products" , isVueRouter: true},
        { title: "Transações", link: "#", isVueRouter: true },
      ],
    };
  },
  async mounted() {
    this.setMenu()

    if (!this.productId) {
      if (!this.platformId) {
        return this.$router.push("/affiliates");
      }

      this.$router.push("/affiliates/products");
    }

    await this.getData();
  },
  methods: {
    async getData() {
      this.loading = true;
      const transactionReportsURL = affiliateReportSales.replace('platform_id', this.platformId)
      const params = {
        offset: this.pagination.offset,
        page: this.pagination.currentPage
      }

      try {
        const { data } = await axios.get(transactionReportsURL, { params });
        const { response } = data
        const { transactions, chargeback, commission, pending, refunded } = response

        this.results = transactions.data.map(transaction => transaction)

        this.metrics = { chargeback, commission, pending, refunded }

        this.pagination.totalPages = Math.ceil(transactions.total / transactions.per_page)
        this.pagination.totalResults = transactions.total
        this.pagination.currentPage = transactions.current_page
        this.pagination.offset = transactions.per_page
      } catch(e) {
        console.log(e)
      } finally {
        this.loading = false;
      }
    },
     onLimitChange(limit) {
      this.pagination.offset = limit
      this.getData()
    },
    onPageChange(page) {
      this.pagination.currentPage = page
      this.getData()
    },
    setMenu() {
      document.getElementById('coProducerButton').style.display = 'none'
      document.getElementById('platforms-link').style.display = 'none'
      document.getElementById('affiliations-link').style.display = 'none'
      document.getElementById('documents-link').style.display = 'none'

      document.getElementById('affiliate-link-2-transactions').classList.add('active')
      document.getElementById('affiliate-link-1').classList.remove('active')
      document.getElementById('affiliate-link-2-withdraw').classList.remove('active')

      document.getElementById('affiliate-link-2-withdraw').style.display = 'block'
      document.getElementById('affiliate-link-2-transactions').style.display = 'block'
      document.getElementById('affiliate-link-1').style.display = 'block'
      document.getElementById('affiliate-link-2').style.display = 'block'
      document.getElementById('affiliate-link-2-content').style.display = 'block'
    }
  },
};
</script>

<style lang="scss" scoped>
  .xgrow-button-action,.dropdown-menu,.table-menu-item {
    background: #222429;
    cursor: pointer;
  }

  .dropdown-item:hover{ color: var(--contrast-green3); }

  .client-info {
    &__name { font-weight: 700; }
    &__email { font-weight: 400; }
  }

  .blur {
    user-select: none;
    filter: blur(3px);
  }
</style>
