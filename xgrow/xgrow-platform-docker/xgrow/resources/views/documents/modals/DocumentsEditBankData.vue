<template>
  <Modal :is-open="isOpen" @close="closeModal">
    <div class="modal-content">
      <Title>Autenticação necessária</Title>
      <p>
        Para confirmar a sua identidade, digite o código que enviamos para o seu
        e-mail {{ email }}
      </p>

      <h5 class="subtitle">Código de Acesso</h5>

      <div class="form">
        <input
          v-for="field in [0, 1, 2, 3, 4, 5]"
          :key="'input-' + field"
          class="token-input"
          type="text"
          maxlength="1"
          :id="field"
          v-model="fields[field]"
          autocomplete="chrome-off"
          v-maska="'#'"
          @input="validateFields"
          @keyup="nextField"
        />
      </div>

      <a class="resend" @click.prevent="resendToken">
        <i class="fas fa-paper-plane me-2"></i>Reenviar Código
        <span v-if="timer.count < 180"> ({{ timer.count }}) </span>
      </a>

      <div class="button-section__buttons">
        <Button
          style="width: 200px"
          icon="fas fa-check"
          text="Confirmar"
          status="success"
          @click="confirm"
          :disabled="emptyFields"
        />
      </div>
    </div>
  </Modal>
</template>

<script>
import { maska } from "maska";
import Title from "../../../js/components/XgrowDesignSystem/Typography/Title";
import Subtitle from "../../../js/components/XgrowDesignSystem/Typography/Subtitle";

import Row from "../../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../../js/components/XgrowDesignSystem/Utils/Col";

import Button from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import Modal from "../../../js/components/XgrowDesignSystem/Modals/Modal.vue";

import StatusModalComponent from "../../../js/components/StatusModalComponent";
import axios from "axios";

export default {
  name: "ModalEditBankData",
  components: {
    StatusModalComponent,
    Title,
    Subtitle,
    Row,
    Col,
    Button,
    Modal,
  },
  props: {
    email: { type: String, required: true },
    isOpen: { type: Boolean, required: true },
  },
  data() {
    return {
      timer: {
        active: false,
        count: 180, //timer in seconds 3 * 60 == 3 minutes
      },
      fields: Array(6),
      emptyFields: true,
    };
  },
  watch: {
    isOpen: function () {
      if (!this.timer.active) {
        this.startTimer();
      }
    },
  },
  directives: { maska },
  methods: {
    resendToken() {
      if (this.timer.active) return;

      this.startTimer();
      this.$emit("resendToken");
    },
    confirm() {
      this.$emit("confirm", this.fields.join(""));
    },
    closeModal() {
      this.$emit("close");
    },
    startTimer: function () {
      this.timer.active = true;

      const intervalId = setInterval(() => {
        this.timer.count--;
        this.$emit("timer", this.timer.count);

        if (this.timer.count === 0) {
          clearInterval(intervalId);
          this.$emit("stop-timer");
          this.timer.active = false;
          this.timer.count = 180;
        }
      }, 1000);
    },
    validateFields(e) {
      const value = e.target.value;

      if (value.length > 0) return;

      if (
        !this.fields[0] ||
        !this.fields[1] ||
        !this.fields[2] ||
        !this.fields[3] ||
        !this.fields[4] ||
        !this.fields[5]
      ) {
        this.emptyFields = true;
      } else {
        this.emptyFields = false;
      }
    },
    async nextField(e) {
      if (e.key == "Backspace" && parseInt(e.target.id) > 0) {
        e.target.previousElementSibling.focus();
        return;
      }
      console.log(e)

      if (this.fillFields[e.target.id] != e.key) {
        this.fillFields[e.target.id] = e.key;
      }

      if (navigator.clipboard && e.ctrlKey && e.key == "v") {
        this.fillFields(await navigator.clipboard.readText());
        this.emptyFields = false;
        e.target.blur();
        return;
      }

      if (
        parseInt(e.target.id) < 5 &&
        !isNaN(parseInt(e.key)) &&
        e.key != "Backspace"
      ) {
        e.target.nextElementSibling.focus();
      } else {
        this.emptyFields = false;
      }
    },
    fillFields(text) {
      const newText = text.trim();
      for (let i = 0; i < newText.length; i++) {
        if (i > 5) return;
        if (parseInt(newText[i])) {
          this.fields[i] = newText[i];
        }
      }
    },
  },
  async mounted() {},
};
</script>


<style lang="scss" scoped>
.modal-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 26px 0 26px 72px;
}

.subtitle {
  font-size: 16px;
  line-height: 22px;
  text-align: center;
  margin: 20px 0;
}

.form {
  display: flex;
  justify-content: center;
  align-items: center;
  column-gap: 6px;
}

.token-input {
  width: 38px;
  height: 48px;
  border: 1px solid white;
  background-color: transparent;
  border-radius: 2px;
  color: white;
  text-align: center;
  font-size: 16px;
  line-height: 26px;

  &:focus {
    border: 1px solid #93bc1e;
  }
}

.resend {
  display: block;
  margin: 20px 0;
  color: #93bc1e;
  font-size: 14px;
  line-height: 22px;
  cursor: pointer;
}

.button-section {
  justify-content: center;
  border-top: 1px solid rgba(white, 0.25);
  padding-top: 24px;
}
</style>
