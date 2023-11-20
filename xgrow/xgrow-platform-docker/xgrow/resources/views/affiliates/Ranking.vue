<template>
  <div>
    <div class="tab-content" id="nav-tabContent">
      <div
        class="tab-pane fade show"
        :class="{ active: $store.state.tab === 'ranking' }"
      >
        <List
          ref="list"
          :show-user="showUser"
          />

        <ShowUser
          :show-affiliation-details="false"
          :is-open="modalShow.open"
          :user="modalShow.user"/>
      </div>
    </div>
  </div>
</template>

<script>
import List from "./components/Ranking";
import ShowUser from "./components/ActiveShowUser/index";
import axios from "axios"

export default {
  name: "Index",
  components: {
    List,
    ShowUser,
  },
  data() {
    return {
      tab: this.$store.state.tab,
      modalShow: {
        open: false,
        user: {
        },
      },
    };
  },
  methods: {
    async showUser(producer_product_id) {
      this.$store.commit("setLoading", true);
      const url = affiliatesUserShowUrl.replace(
        "producer_product_id",
        producer_product_id
      );

      const { data } = await axios.get(url)

      this.modalShow.user = data[0]

      this.$store.commit("setLoading", false);
      this.modalShow.open = true
    },
    async closeModal() {
      await this.$refs.list.getData()
      this.modalCommission.open = false
    },
  },
};
</script>

<style lang="scss" scoped></style>
