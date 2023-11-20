<template>
  <div class="xgrow-card card-dark py-4 material h-100">
    <Title>Taxas e prazos</Title>
    <Subtitle>
      Estas são as taxas e prazos aplicados nas operações que você vai realizar
      dentro da Xgrow.
    </Subtitle>

    <Row>
      <Col sm="12" md="12" lg="12" xl="12">
        <div class="xgrow-inner-card">
          <Subtitle :isSmall="true" icon="fas fa-percent" icon-color="#3D4353">
            Taxas
          </Subtitle>
          <p class="text-white">
            {{ fees }}
          </p>
        </div>
      </Col>
    </Row>

    <Row>
      <Col sm="12" md="12" lg="12" xl="12">
        <div class="xgrow-inner-card">
          <Subtitle
            :isSmall="true"
            icon="fas fa-calendar-alt"
            icon-color="#3D4353"
            >Prazos para recebimento</Subtitle
          >
          <div class="d-flex">
            <p class="text-white">
              <span style="font-weight: 600">Cartão - </span>
              {{ deadlines.cards }}
            </p>
            <span class="pipe">|</span>
            <p class="text-white">
              <span style="font-weight: 600">Boleto bancário - </span>
              {{ deadlines.boletos }}
            </p>
            <span class="pipe">|</span>
            <p class="text-white">
              <span style="font-weight: 600">Pix - </span> {{ deadlines.pix }}
            </p>
          </div>
        </div>
      </Col>
    </Row>
    <Row class="button-section" v-if="$store.state.edit === false">
      <div class="button-section__buttons">
        <Button
          style="width: 200px"
          text="Finalizar"
          status="success"
          @click="finish"
        />
      </div>
    </Row>
    <StatusModalComponent :is-open="loading" status="loading" />
  </div>
</template>

<script>
import Title from "../../../js/components/XgrowDesignSystem/Typography/Title";
import Subtitle from "../../../js/components/XgrowDesignSystem/Typography/Subtitle";

import Row from "../../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../../js/components/XgrowDesignSystem/Utils/Col";

import Button from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import Input from "../../../js/components/XgrowDesignSystem/Form/Input.vue";

import StatusModalComponent from "../../../js/components/StatusModalComponent";
import axios from "axios";

export default {
  name: "Fees",
  components: {
    StatusModalComponent,
    Title,
    Subtitle,
    Row,
    Col,
    Button,
    Input,
  },
  data() {
    return {
      loading: false,
      fees: "",
      deadlines: {
        cards: "0 dias",
        boletos: "0 dias",
        pix: "0 dias",
      },
    };
  },
  methods: {
    async getInfo() {
      this.loading = true;

      await axios
        .get(timeAndFees)
        .then(({ data }) => {
          const { fees, deadlines_for_receipt } = data;
          [this.fees, this.deadlines] = [fees, deadlines_for_receipt];
        })
        .catch((e) => console.error(e));

      this.loading = false;
    },
    async finish() {
      this.loading = true;
      await setTimeout(() => {
        window.location.href = '/platforms'
      }, 1000);
    }
  },
  async mounted() {
    await this.getInfo();
  },
};
</script>


<style lang="scss" scoped>
.xgrow-inner-card {
  background-color: #252932;
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 20px;
}

.pipe {
  display: block;
  font-weight: 700;
  color: #3d4353;
  margin: 0 10px;
}

.button-section {
  justify-content: flex-end;
  border-top: 1px solid rgba(white, 0.25);
  padding-top: 24px;

  &__buttons {
    display: flex;
    justify-content: flex-end;
    column-gap: 20px;

    &--spaced {
      justify-content: space-between;
    }
  }
}
</style>
