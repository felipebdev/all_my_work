<template>
  <Modal :is-open="$store.state.isOpenAuthModal" @close="$store.commit('toggleAuthModal', false)">
    <h5>Autenticação necessária</h5>
    <hr />
    <p class="mb-4">Para gerar um novo token você precisa confirmar a sua identidade. Insira abaixo
      a senha de acesso de seu usuário ('Mostrar e-mail do usuário') na xgrow
    </p>
    <Input id="password" type="password" placeholder="********" label="Senha de acesso" class="" />

    <div class="d-flex justify-content-center gap-3 mt-5">
      <Button
        text="Confirmar"
        status="success"
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
  props: {
  },
};
</script>

<style lang="scss" scoped>
button {
  width: 175px;
}
</style>
