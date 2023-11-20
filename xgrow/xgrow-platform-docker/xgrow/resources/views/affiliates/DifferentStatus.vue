<template>
  <div>
    <div
      class="tab-content"
      id="nav-tabContent">
        <div class="tab-pane fade show" :class="{'active': $store.state.tab === 'different-status'}">
          <DifferentStatus />
      </div>
  </div>
</div>
</template>

<script>
import DifferentStatus from './components/DifferentStatus'

export default {
  name: "Index",
  components: {
      DifferentStatus,
    },
  data() {
    return {
      tab: this.$store.state.tab
    }
  },
  methods: {},
  mounted() {
    this.$store.commit('setLoading', true)

    this.$store.commit('setLoading', false)
  }
};
</script>

<style lang="scss" scoped></style>
