<template>
  <div class="links xgrow-card card-dark py-4">
    <div class="links__header">
      <h3 style="color: #fff">Links de divulgação</h3>
      <button class="links__support" @click="handleModal(true)">
        <i class="fa fa-question"></i>
        Suporte
      </button>
    </div>
    <p>
      São links que a XGrow e o Produtor disponibilizam para os afiliados
      divulgarem e venderem este produto.
    </p>
    <hr />

    <Table
      id="platformsTable">
      <template v-slot:title>
        <h5>Links principais</h5>
      </template>
      <template v-slot:header>
        <th>Nome do plano</th>
        <th>URL</th>
        <th style="width:10%">Ação</th>
      </template>
      <template
        v-if="links.main.length > 0"
        v-slot:body>
        <tr
          :key="`link-${id}`"
          v-for="{ id, name, link } in links.main"
        >
          <td>{{ name }}</td>
          <td>
            <a :href="link" target="_blank">{{ link }}</a>
          </td>
          <td>
            <button class="links__copy xgrow-button" type="button"
              @click="copyLink(link)">
              <i class="far fa-copy" aria-hidden="true"></i>
              <span>Copiar link</span>
            </button>
          </td>
        </tr>
      </template>
      <template
        v-else
        v-slot:body>
        <tr>
          <td colspan="3">Não há links principais.</td>
        </tr>
      </template>
    </Table>

    <hr>

    <Table
      id="platformsTable">
      <template v-slot:title>
        <h5>Links adicionais</h5>
      </template>
      <template v-slot:header>
        <th>Nome do Link</th>
        <th>URL</th>
        <th>Ação</th>
      </template>
      <template
        v-if="links.additional.length > 0"
        v-slot:body>
        <tr
          :key="`link-${id}`"
          v-for="{ id, link_name, url } in links.additional"
        >
          <td>{{ link_name }}</td>
          <td>
            <a :href="url" target="_blank">{{ url }}</a>
          </td>
          <td>
            <button class="links__copy xgrow-button" type="button"
              @click="copyLink(url)">
              <i class="far fa-copy" aria-hidden="true"></i>
              <span>Copiar link</span>
            </button>
          </td>
        </tr>
      </template>
      <template
        v-else
        v-slot:body>
        <tr>
          <td colspan="3">Não há links adicionais.</td>
        </tr>
      </template>
    </Table>
    <Modal
      :is-open="isModalOpen" @close="handleModal(false)"
      modal-size="xl">
      <h4>Informações de suporte</h4>
      <hr>
      <div class="support__container">
        <h6>Suporte do produto: {{ product.name }}</h6>
        <p>
          Se você precisa de ajuda, pode falar direto com o vendedor do produto pelo e-mail:
          <a :href="`mailto:${product.supportEmail}`">{{ product.supportEmail }}</a>
        </p>
      </div>
      <hr>
      <button class="xgrow-button" @click="handleModal(false)">Voltar</button>
    </Modal>
  </div>
</template>

<script>
import Table from "../../../../js/components/Datatables/Table";
import Modal from "../../../../js/components/XgrowDesignSystem/Modals/Modal";

export default {
  name: "AffiliateLinks",
  components: {
    Table,
    Modal
  },
  props: {
    links: {
      type: Object,
      default: () => {}
    },
    product: {
      type: Object,
      default: () => {
        return {
          name: '',
          supportEmail: ''
        }
      }
    }
  },
  data() {
    return {
      isModalOpen: false
    };
  },
  methods: {
    copyLink(link) {
      navigator.clipboard.writeText(link);
      successToast('Sucesso', 'Link copiado com sucesso!');
    },
    handleModal(status) {
      this.isModalOpen = status
    }
  },
};
</script>

<style lang="scss" scoped src="./styles.scss"></style>
<style lang="scss">
.modal__content {
  padding: 40px;

  button {
    float: right;
  }
}
.support__container {
  background: #333844;
  padding: 14px;
  border-radius: 8px;
  margin-bottom: 40px;

  a {
    color: #ADDF45;
  }
}
</style>
