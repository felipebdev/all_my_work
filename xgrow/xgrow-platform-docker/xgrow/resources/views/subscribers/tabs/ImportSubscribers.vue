<template>
  <div class="xgrow-card card-dark py-4 h-100 import">
    <h5 style="color: white">Importar alunos</h5>
    <CsvImportInstructions />
    <Checkbox
      class="import__instructions"
      id="readInstructions"
      label="Declaro que li as instruções acima."
      @checked="setReadInstructions()"
    />
    <div class="import__container">
      <div class="import__options w-100">
        <h6>Selecione as opções de importação:</h6>
        <Select
          id="status"
          label="Status"
          placeholder="Selecione uma opção"
          :options="options.status"
          v-model="form.status"
          :disabled="!readInstructions"
        />
        <Select
          id="state"
          label="Produto"
          placeholder="Selecione uma opção"
          :options="options.plans"
          v-model="form.plan_id"
          :disabled="!readInstructions"
        />
        <Select
          id="delimiter"
          label="Delimitador"
          placeholder="Selecione uma opção"
          :options="options.delimiters"
          v-model="form.state"
          :disabled="!readInstructions"
        />
      </div>
      <div class="import__file w-100">
        <h6>Importar lista de alunos</h6>
        <p>
          Selecione um arquivo CSV conforme o modelo informado acima para realizar a
          importação. O tamanho máximo suportado do arquivo é de 10MB.
        </p>
        <Button
          outline
          text="Adicionar arquivo CSV"
          icon="fas fa-upload"
          :disabled="!readInstructions"
          :on-click="() => toggleModal('modalImport', true)"
        />
      </div>
    </div>
    <hr />
    <div class="import__send-file">
      <Button
        text="Importar CSV"
        status="success"
        outline
        :disabled="!readInstructions"
      />
    </div>

    <ImportFile :is-open="modalImport" :toggle="toggleModal" />
    <Map :is-open="modalMap" :toggle="toggleModal" />
    <Modal :is-open="false" id="success-modal">
      <i class="check fa fa-check-circle"></i>
      <h6>Lista salva com sucesso!</h6>
      <p>
        A lista selecionada foi salva e configurada com sucesso. Clique em “OK” para
        carregá-la dentro da plataforma e continuar.
      </p>
      <Button status="success" text="OK" />
    </Modal>
  </div>
</template>

<script>
import Modal from "../../../js/components/XgrowDesignSystem/Modals/Modal";
import Checkbox from "../../../js/components/XgrowDesignSystem/Form/Checkbox";
import Select from "../../../js/components/XgrowDesignSystem/Form/Select";
import Button from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import CsvImportInstructions from "../../../js/components/XgrowDesignSystem/Alert/CsvImportInstructions";
import ImportFile from "../modal/ImportFile";
import Map from "../modal/Map";

export default {
  components: {
    Modal,
    Map,
    Checkbox,
    Select,
    Button,
    CsvImportInstructions,
    ImportFile,
  },
  data() {
    return {
      modalImport: false,
      modalMap: false,
      readInstructions: false,
      options: {
        status: [
          { value: "active", name: "Ativo" },
          { value: "canceled", name: "Cancelado" },
        ],
        plans,
        delimiters: [
          { value: ";", name: "Ponto e vírgula (;)" },
          { value: ",", name: "Somente vírgula (,)" },
        ],
      },
      form: {
        status: "",
        plan_id: "",
        delimiter: "",
      },
    };
  },
  methods: {
    setReadInstructions() {
      this.readInstructions = !this.readInstructions;
    },
    toggleModal(modal, status) {
      this[modal] = status;
    },
  },
};
</script>

<style scoped lang="scss">
.import {
  li {
    list-style: inherit;
  }

  h5 {
    margin-bottom: 30px;
  }

  &__instructions {
    margin-bottom: 30px;
  }

  &__container {
    display: flex;
    gap: 60px;
  }

  &__file {
    p {
      font-size: 12px;
      font-style: italic;
      font-weight: 400;
      line-height: 19px;
      letter-spacing: 0em;
      text-align: left;
      margin-bottom: 20px;
      max-width: 480px;
    }
  }

  &__send-file {
    width: 100%;
    display: flex;
    justify-content: flex-end;

    button {
      width: 200px;
    }
  }
}
</style>
<style lang="scss">
#success-modal {
  .modal__content {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    padding: 40px;
    text-align: center;

    .check {
      width: 98px;
      height: 98px;
      color: var(--green4);
      margin-bottom: 33px;
    }

    p {
      margin-bottom: 31px;
    }
  }
}
</style>
