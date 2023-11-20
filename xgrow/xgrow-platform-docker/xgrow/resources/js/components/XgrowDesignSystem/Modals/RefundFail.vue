<template>
  <Modal id="refundFailModal" :is-open="isOpen" @close="toggle(false)">
    <Title>Falha no estorno</Title>
    <hr />
    <Subtitle class="d-block content">
      Não foi possível finalizar o estorno do boleto bancário. Nesses casos o principal
      motivo para a falha no estorno é a
      <b>divergência dos dados bancários</b> informados:
      <ul>
        <li>
          A conta bancária informada está vinculada a um documento diferente do documento
          do pagador do boleto;
        </li>
        <li>
          A conta bancária foi selecionada como corrente ou poupança incorretamente;
        </li>
        <li>
          Foi informado algum dígito incorreto na agência ou conta ou qualquer outro número
          incorreto ou incompleto;
        </li>
        <li>O código do banco informado está incorreto;</li>
      </ul>
      Verifique os dados bancários informados na última tentativa de estorno e tente
      novamente. Caso o problema persistir após 48 horas, entre em contato com o suporte.
    </Subtitle>
  </Modal>
</template>

<script>
import Modal from "./Modal.vue";
import Title from "../Typography/Title.vue";
import Subtitle from "../Typography/Subtitle.vue";
import Input from "../Form/Input.vue";

export default {
  components: {
    Modal,
    Title,
    Subtitle,
    Input,
  },
  props: {
    isOpen: {
      type: Boolean,
      default: false,
    },
    toggle: {
      type: Function,
      default: () => {},
    },
  },
  data() {
    return {
    };
  },
};
</script>

<style lang="scss">
#refundFailModal {
  .modal__content {
    padding: 40px;
    position: relative;
  }

  li {
    list-style-type: disc;
  }

  hr {
    color: white;
  }
}

</style>
