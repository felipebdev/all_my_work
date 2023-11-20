<template>
  <Modal
    class="import-file"
    :is-open="isOpen"
    modal-size="xl"
    @close="() => toggle('modalMap', false)"
  >
    <h5>Adicione uma lista de alunos</h5>
    <hr />
    <Steps :active-step="activeStep" :steps="steps" style="margin-bottom: 25px" />
    <p>Mapeie as colunas do arquivo com os indicadores para enviar a sua lista.</p>
    <hr />
    <p class="mb-3">
      <b>2 colunas</b> foram mapeadas e serão carregadas. Corrija os erros (caso haja)
      antes de salvar e finalizar.
    </p>
    <Tab id="tabCourse">
      <template v-slot:header>
        <TabNav
          :items="tabs.items"
          id="tabNav"
          :startTab="tabs.active"
          @change-page="
            (val) => {
              tabs.active = val;
            }
          "
        />
      </template>
      <template v-slot:body>
        <TabContent id="mapped" :selected="tabs.active === 'mapped'">
          <table class="mb-3">
            <thead>
              <tr>
                <th>Mapear coluna ao indentificador</th>
                <th>Diretrizes de formatação</th>
                <th>Exemplo</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="d-flex gap-3">
                  <div>
                    <p>jhondoe@gmail.com</p>
                    <p>marcos.vinicius@gmail.com</p>
                    <p>mariaeduarda@gmail.com</p>
                  </div>
                  <Select
                    id="status-map"
                    label="Status"
                    placeholder="Selecione uma opção"
                    :options="columnTypes"
                  />
                </td>
                <td>
                  <p>jLorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                  <p>Nulla sit cras nisi, integer cras facilisi.</p>
                  <p>Aliquam sollicitudin leo.</p>
                </td>
                <td>
                  <p>jhondoe@gmail.com</p>
                  <p>marcos.vinicius@gmail.com</p>
                  <p>mariaeduarda@gmail.com</p>
                </td>
              </tr>
              <tr>
                <td class="d-flex gap-3">
                  <div>
                    <p>jhondoe@gmail.com</p>
                    <p>marcos.vinicius@gmail.com</p>
                    <p>mariaeduarda@gmail.com</p>
                  </div>
                  <Select
                    id="status-map-2"
                    label="Status"
                    placeholder="Selecione uma opção"
                    :options="columnTypes"
                  />
                </td>
                <td>
                  <p>jLorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                  <p>Nulla sit cras nisi, integer cras facilisi.</p>
                  <p>Aliquam sollicitudin leo.</p>
                </td>
                <td>
                  <p>jhondoe@gmail.com</p>
                  <p>marcos.vinicius@gmail.com</p>
                  <p>mariaeduarda@gmail.com</p>
                </td>
              </tr>
            </tbody>
          </table>
        </TabContent>
        <TabContent id="not-mapped" :selected="tabs.active === 'not-mapped'">
          Aba 2
        </TabContent>
      </template>
    </Tab>
    <div class="import-file__cta">
      <Button text="Voltar" outline :on-click="back" />
      <Button text="Salvar e finalizar" status="success" :disabled="!Boolean(file)" />
    </div>
  </Modal>
</template>

<script>
import Modal from "../../../js/components/XgrowDesignSystem/Modals/Modal";
import Steps from "../../../js/components/XgrowDesignSystem/Steps/Steps";
import Button from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import Tab from "../../../js/components/XgrowDesignSystem/Tab/XgrowTab";
import TabContent from "../../../js/components/XgrowDesignSystem/Tab/XgrowTabContent";
import TabNav from "../../../js/components/XgrowDesignSystem/Tab/XgrowTabNav";
import Select from "../../../js/components/XgrowDesignSystem/Form/Select";

export default {
  components: {
    Modal,
    Steps,
    Button,
    Tab,
    TabContent,
    TabNav,
    Select,
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
      activeStep: "map",
      steps: [
        { id: "import", name: "Importar lista de alunos", completed: true },
        { id: "map", name: "Mapear identificadores", completed: false },
      ],
      tabs: {
        active: "mapped",
        items: [
          { title: "Colunas mapeadas", screen: "mapped" },
          { title: "Colunas indefinidas", screen: "not-mapped" },
        ],
      },
      columnTypes: [
        { value: "active", name: "Ativo" },
        { value: "canceled", name: "Cancelado" },
      ],
    };
  },
  methods: {
    back() {
      this.toggle("modalMap", false);
      this.toggle("modalImport", true);
    },
    sendToast() {
      successToast(
        "Lista carregada!",
        "A lista de alunos que você configurou foi carregada com sucesso! Clique aqui para visualizar e finalizar a importação."
      );
    },
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

table {
  border-collapse: separate;
  border-spacing: 0 1em;
}

tbody {
  tr {
    background: #333844;
    border-radius: 6px;
  }
}
</style>
