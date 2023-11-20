<template>
  <div>
    <div class="tab-content" id="nav-tabContent">
      <div
        class="tab-pane fade show"
        :class="{ active: $store.state.tab === 'pending' }"
      >
        <List
          ref="list"
          :confirmation-modal="confirmationModal"
          />

        <ActionConfirmation
          :is-open="modalConfirmation.open"
          :modal="modalConfirmation"
          :handle-action="confirmOrRefuseAffiliate"/>
      </div>
    </div>
  </div>
</template>

<script>
import List from "./components/Pending";
import ActionConfirmation from "./components/ConfirmAction/index";
import axios from "axios"

export default {
  name: "Index",
  components: {
    List,
    ActionConfirmation
  },
  data() {
    return {
      tab: this.$store.state.tab,
      modalConfirmation: {
        open: false,
        actionType: "active",
        user: "",
        route: urlAcceptOrResufe,
        actionTexts: {
          active: {
            text: "aceite",
            button: "Aceitar",
            title: "aceita"
          },
          refused: {
            text: "recuse",
            button: "Recusar",
            title: "recusada"
          },
        },
      },
    };
  },
  methods: {
    async closeModal() {
      await this.$refs.list.getData()
      this.modalCommission.open = false
    },
    confirmationModal(route, producer_product_id, user, actionType) {
      this.modalConfirmation.open = true;
      this.modalConfirmation.route = route.replace(
        "producer_product_id",
        producer_product_id
      );
      this.modalConfirmation.actionType = actionType;
      this.modalConfirmation.user = user;
    },
    async confirmOrRefuseAffiliate() {
      this.$store.commit("setLoading", true);
      this.modalConfirmation.open = false;

      try {
        await axios.post(this.modalConfirmation.route, {
          status: this.modalConfirmation.actionType
        });
        await this.$refs.list.getData()

        successToast(
          `Afiliação ${this.modalConfirmation.actionTexts[this.modalConfirmation.actionType].title}!`,
          `A afiliação de ${this.modalConfirmation.user} foi ${
            this.modalConfirmation.actionTexts[this.modalConfirmation.actionType].title
          } com sucesso.`
        );
      } catch (e) {
        errorToast(
          `Falha ao ${
            this.modalConfirmation.actionTexts[this.modalConfirmation.actionType].button
          } afiliação!`,
          `Ocorreu um erro inesperado ao ${
            this.modalConfirmation.actionTexts[this.modalConfirmation.actionType].button
          } a afiliação de ${this.modalConfirmation.user}, tente novamente mais tarde.`
        );
      }

      this.$store.commit("setLoading", false);
    },
  },
  async mounted() {},
};
</script>

<style lang="scss" scoped></style>
