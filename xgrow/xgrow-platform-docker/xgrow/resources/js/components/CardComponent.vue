<template>
  <card-container-component>
    <p class="card-title mb-2" v-if="title != ''">{{ title }}</p>
    <p class="card-subtitle mb-3" v-if="subtitle != ''">{{ subtitle }}</p>
    <div class="card-content row flex-wrap">
      <slot></slot>
    </div>
    <p class="card-subtitle" v-if="footerText != ''">{{ footerText }}</p>
  </card-container-component>
</template>

<script>
import CardContainerComponent from "../components/CardContainerComponent";

export default {
  name: "card-component",
  components: { CardContainerComponent },
  data() {
    return {};
  },
  props: {
    /* Text properties */
    title: {
      required: false,
      type: String,
      default: "",
    },
    subtitle: {
      required: false,
      type: String,
      default: "",
    },
    footerText: {
      required: false,
      type: String,
      default: "",
    },
  },
};
</script>
<style lang="scss" scoped>
.card-title {
  font-weight: bold;
  font-size: 1rem;
}

.card-subtitle {
  opacity: 0.8;
  font-weight: lighter;
  font-size: 0.8rem;
}
</style>
