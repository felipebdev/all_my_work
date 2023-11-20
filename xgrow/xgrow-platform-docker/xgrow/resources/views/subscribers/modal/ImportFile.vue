<template>
  <Modal class="import-file" :is-open="isOpen" modal-size="xl" @close="() => toggle('modalImport', false)">
    <h5>Adicione uma lista de alunos</h5>
    <hr />
    <Steps :active-step="activeStep" :steps="steps" style="margin-bottom: 25px" />
    <p>
      Selecione um arquivo <b>CSV</b> conforme este
      <a :href="modelLink" class="model" target="_blank">modelo</a> para realizar a importação. O tamanho máximo
      suportado do arquivo é de <b>10MB</b>.
    </p>
    <FileUpload
      id="certificateUpload"
      ref="front"
      refer="front"
      accept=".csv"
      title="Importe o arquivo CSV clicando no botão abaixo."
      btn-title="Buscar arquivo"
      @send-image="sendFile"
      @clear="file = null"
    />
    <div class="import-file__cta">
      <Button text="Voltar" outline :on-click="() => toggle('modalImport', false)" />
      <Button text="Avançar" status="success" :disabled="!Boolean(file)" :on-click="() => next()"/>
    </div>
  </Modal>
</template>

<script>
import Modal from "../../../js/components/XgrowDesignSystem/Modals/Modal";
import Steps from "../../../js/components/XgrowDesignSystem/Steps/Steps";
import Button from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import FileUpload from "../../../js/components/XgrowDesignSystem/Form/FileUpload.vue";

export default {
  components: {
    Modal,
    Steps,
    Button,
    FileUpload,
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
      modelLink,
      activeStep: "import",
      steps: [
        { id: "import", name: "Importar lista de alunos", completed: false },
        { id: "map", name: "Mapear identificadores", completed: false },
      ],
      file: null,
    };
  },
  methods: {
    setStep(oldStep, newStep) {
      this.activeStep = newStep;
      this.step = this.steps.map((step) => {
        if (step.id === oldStep) {
          step.completed = true;
        }

        return step;
      });
    },
    sendFile(obj) {
      this.file = obj.file.files[0];
      console.log(this.file)
    },
    next() {
      this.toggle('modalImport', false)
      this.toggle('modalMap', true)
    }
  },
};
</script>

<style lang="scss">
.import-file {
  .modal__content {
    padding: 40px 53px;
  }
}
</style>

<style lang="scss" scoped>
.import-file {
  h5,
  hr {
    margin-bottom: 20px;
  }

  a {
    color: var(--green4);
  }

  button {
    width: 200px;
  }

  &__cta {
    display: flex;
    justify-content: flex-end;

    .outline {
      margin-right: 20px;
    }
  }
}
</style>
