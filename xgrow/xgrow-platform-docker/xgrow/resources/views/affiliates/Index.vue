<template>
  <div>
    <div class="tab-content" id="nav-tabContent">
      <div
        class="tab-pane fade show"
        :class="{ active: $store.state.tab === 'active' }"
      >
        <List
          ref="list"
          :show-user="showUser"
          :edit-commission="editCommission"
          :confirmation-modal="confirmationModal"
          />

        <ShowUser
          :is-open="modalShow.open"
          :user="modalShow.user"/>

        <EditCommission
          :is-open="modalCommission.open"
          :close-modal="closeModal"
          :user="modalCommission.user"/>

        <ActionConfirmation
          :is-open="modalConfirmation.open"
          :modal="modalConfirmation"
          :handle-action="blockOrCancelAffiliate"/>
      </div>
    </div>
  </div>
</template>

<script>
import List from "./components/Active";
import ShowUser from "./components/ActiveShowUser/index";
import EditCommission from "./components/ActiveEditCommission/index";
import ActionConfirmation from "./components/ConfirmAction/index";
import axios from "axios"

export default {
  name: "Index",
  components: {
    List,
    ShowUser,
    EditCommission,
    ActionConfirmation
  },
  data() {
    return {
      tab: this.$store.state.tab,
      modalShow: {
        open: false,
        user: {
        },
      },
      modalCommission: {
        isOpen: false,
        user: {
          id: 0,
          name: '',
          commission: 0,
          product_id: 0
        }
      },
      modalConfirmation: {
        open: false,
        actionType: "block",
        user: "",
        route: "",
        actionTexts: {
          block: {
            text: "bloqueie",
            button: "Bloquear",
            title: "bloqueada",
          },
          cancel: {
            text: "cancele",
            button: "Cancelar",
            title: "cancelada",
          },
        },
      },
    };
  },
  methods: {
    async showUser(producer_product_id) {
      this.$store.commit("setLoading", true);
      const url = affiliatesShowUrl.replace(
        "producer_product_id",
        producer_product_id
      );

      const { data } = await axios.get(url)

      this.modalShow.user = data[0]

      this.$store.commit("setLoading", false);
      this.modalShow.open = true
    },
    editCommission(id, name, commission, product_id) {
      this.modalCommission.open = true

      this.modalCommission.user = { id, name, commission, product_id };
    },
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
    async blockOrCancelAffiliate() {
      this.$store.commit("setLoading", true);
      this.modalConfirmation.open = false;

      try {
        await axios.delete(this.modalConfirmation.route);
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
    },
  },
  async mounted() {},
};
</script>

<style lang="scss" scoped></style>
