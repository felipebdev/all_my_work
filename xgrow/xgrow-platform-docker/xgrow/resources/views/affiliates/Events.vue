<template>
  <div>
    <div
      class="tab-content"
      id="nav-tabContent">
        <div class="tab-pane fade show" :class="{'active': $store.state.tab === 'events'}">
            <Events />
        </div>
    </div>
  </div>
</template>

<script>
import Events from './components/Events'

export default {
    name: "Index",
    components: {
        Events,
    },
    data() {
        return {
            tab: this.$store.state.tab
        }
    },
    methods: {

    },
    mounted() {
      this.$store.commit('setLoading', true)

      this.$store.commit('setLoading', false)
    }
};
</script>

<style lang="scss" scoped></style>
