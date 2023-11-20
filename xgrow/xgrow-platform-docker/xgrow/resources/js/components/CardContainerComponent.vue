<template>
  <div
    class="container-fluid px-3"
    :class="[
      checkCardContainer,
      `col-xxl-${xxl}`,
      `col-xl-${xl}`,
      `col-lg-${lg}`,
      `col-md-${md}`,
      `col-sm-${sm}`,
      `col-${xs}`,
      `mt-${marginTop}`,
      `mb-${marginBottom}`,
      `pt-${paddingTop}`,
      `pb-${paddingBottom}`,
    ]"
  >
    <slot></slot>
  </div>
</template>

<script>
import bootstrapColumns from "./config/bootstrapColumnsProps";

export default {
  name: "card-component",
  data() {
    return {};
  },
  props: {
    /* Styling properties */
    marginTop: {
      required: false,
      type: Number,
      default: 3,
    },
    marginBottom: {
      required: false,
      type: Number,
      default: 3,
    },
    paddingTop: {
      required: false,
      type: Number,
      default: 3,
    },
    paddingBottom: {
      required: false,
      type: Number,
      default: 4,
    },
    withoutBackground: {
      required: false,
      type: Boolean,
      default: false,
    },

    /*
     Bootstrap/responsiviness
     tags properties
     */
    ...bootstrapColumns,
  },
  computed: {
    checkCardContainer: function () {
      return this.withoutBackground ? null : "card-container";
    },
  },
};
</script>

<style lang="scss" scoped>
@import url("/public/xgrow-vendor/assets/css/colors.css");

* {
  color: var(--card-font-color);
}

.card-container {
  background: var(--card-color);
  border-radius: 0.5rem;
  border: 1px solid var(--card-border-color);
  box-shadow: 0 1px 3px rgb(0 0 0 / 12%), 0 1px 2px rgb(0 0 0 / 24%);
}
</style>
