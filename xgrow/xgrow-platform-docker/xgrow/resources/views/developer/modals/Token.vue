<template>
  <Modal :is-open="$store.state.isOpenTokenModal" @close="$store.commit('toggleTokenModal', false)">
    <h5>Gerar token</h5>
    <hr />
    <p class="mb-4">Insira abaixo o domínio desejado para gerar um token:</p>
    <Input id="domain" placeholder="https://" label="Domínio" class="" v-model="domain" />

    <div class="d-flex justify-content-center gap-3 mt-5">
      <Button
        :outline="true"
        text="Cancelar"
        @click="$store.commit('toggleTokenModal', false)"
      />
      <Button
        text="Salvar"
        status="success"
        @click="$emit('confirmed')"
      />
    </div>
  </Modal>
</template>

<script>
import Modal from "../../../js/components/XgrowDesignSystem/Modals/Modal";
import Input from "../../../js/components/XgrowDesignSystem/Form/Input";
import Button from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";

export default {
  components: {
    Modal,
    Input,
    Button,
  },
  data() {
    return {
      domain: ""
    }
  },
  watch: {
    domain: function() {
      this.$emit('domain', this.domain);
    }
  }
};
</script>

<style lang="scss" scoped>
button {
  width: 175px;
}
</style>
