<template>
  <Modal
    :is-open="isOpen"
    v-if="isOpen"
    modalSize="md"
    @close="isOpen = false"
  >
    <h5>
      Dados do afiliado: <span>{{ user.name }}</span>
    </h5>
    <hr>
    <Row>
      <Col sm="12" md="12" lg="12" xl="12" class="mb-4">
        <h6>Comissão</h6>
        <Input
          id="status"
          label="Valor da comissão"
          v-model="commissionUnformated"
          :mask="['#%','#,#%','#,##%', '##,##%', '###,##%']"
          v-on:update:model-value="checkPercent"
        />
      </Col>
    </Row>
    <div class="cta">
      <button class="xgrow-button cancel" @click="isOpen = false">Voltar</button>
      <button class="xgrow-button" @click="save">Salvar</button>
    </div>
  </Modal>
</template>

<script>
import Subtitle from "../../../../js/components/XgrowDesignSystem/Typography/Subtitle";
import Modal from "../../../../js/components/XgrowDesignSystem/Modals/Modal.vue";
import Row from "../../../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../../../js/components/XgrowDesignSystem/Utils/Col";
import Input from "../../../../js/components/XgrowDesignSystem/Form/Input";
import maska from "maska";
import axios from "axios"

export default {
  name: "AffiliatesActiveShowUserModal",
  components: {
    Modal,
    Row,
    Col,
    Input,
    Subtitle,
  },
  directives: {maska},
  props: {
    isOpen: {
      type: Boolean,
      default: false,
    },
    user: {
      type: Object,
      default: () => {
        return {
          id: 0,
          name: '',
          commission: 0,
          product_id: 0,
        }
      },
    },
    closeModal: {
      type: Function,
      default: () => {},
    },
  },
  data() {
    return {
      isOpenData: this.isOpen,
      commissionUnformated: '',
      commission: '',
    }
  },
  watch: {
    isOpen(val) {
      if (val === true) {
        this.commissionUnformated = parseFloat(this.user.commission).toFixed(2)
      }
    }
  },
  methods: {
    async save() {
      if (this.checkPercent(this.commissionUnformated)) {

        const url = affiliatesEditUrl
          .replace('producer_product_id', this.user.id)


        this.$store.commit("setLoading", true);

        try {
          await axios.post(url, {
            percent: parseFloat(this.commission)
          })

          await this.closeModal()

          successToast(
            'Comissão atualizada com sucesso!',
            `O valor da comissão de ${this.user.name} foi atualizado com sucesso.`
          );
        } catch(e) {
          this.$store.commit("setLoading", false);
          errorToast(
            'Falha ao atualizar comissão!',
            `Ocorreu um erro inesperado ao atualizar a comissão de ${this.user.name}, tente novamente mais tarde.`
          );
        }


      }
    },
    checkPercent(val) {
      if (val === '') {
          errorToast("Atenção", "O Valor da comissão deve ser no mínimo 1% e no máximo 80%");

          return false
      }

      const pieces = val.split('')
      const finalValue = parseFloat(val.replace(',', '.'))

      if (pieces[val.length - 1] === '%') {
        if (finalValue < 1 || finalValue > 80) {
            errorToast("Atenção", "O Valor da comissão deve ser no mínimo 1% e no máximo 80%");

            this.commissionUnformated = val
            this.commission = val.replace(',','.').replace('%','')

            return false
        }

        this.commissionUnformated = val
        this.commission = val.replace(',','.').replace('%','')

        return true
      }
    },
  }
};
</script>

<style lang="scss" src="./styles.scss" scoped></style>
<style lang="scss">
.modal__content {
  padding: 30px;
}

.form-group {
  margin: 0px !important;
}
.cancel, .cancel:hover {
  background: none;
  border: 2px solid;
}
</style>
