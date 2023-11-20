<template>
  <div
    class="my-2"
    :class="[
      `col-xxl-${xxl}`,
      `col-xl-${xl}`,
      `col-lg-${lg}`,
      `col-md-${md}`,
      `col-sm-${sm}`,
      `col-${xs}`,
    ]"
  >
    <div class="card-container p-3">
      <p class="card-data mb-2">{{ cardData }}</p>
      <p class="card-info my-2">{{ info }}</p>

      <div class="card-content">
        <slot></slot>
      </div>

      <p class="card-title mt-2 mb-0">
        <i :class="`fas ${icon} me-2`"></i>
        {{ label }}
      </p>
    </div>
  </div>
</template>

<script>
import bootstrapColumns from "./config/bootstrapColumnsProps";

export default {
  name: "second-card-component",
  props: {
    /* Text properties */
    cardData: {
      required: false,
      type: String,
      default: "",
    },
    info: {
      required: false,
      type: String,
      default: "",
    },
    label: {
      required: false,
      type: String,
      default: "",
    },
    icon: {
      required: false,
      type: String,
      default: "",
    },

    /*
     Bootstrap/responsiviness
     tags properties
     */
    ...bootstrapColumns,
  },
};
</script>

<style lang="scss" scoped>
@import url("/public/xgrow-vendor/assets/css/colors.css");

.card-container {
  background: #3b4150;
  border-radius: 0.5rem;
  height: 100%;

  .card-data {
    font-size: 1.375rem;
    font-weight: bold;
  }

  .card-info {
    opacity: 0.8;
    font-size: 0.875rem;
    font-weight: lighter;
  }

  .card-title {
    font-size: 0.875rem;

    i {
      font-size: 1rem;
      color: var(--green4);
    }
  }

  .card-content {
    width: 100%;
  }
}
</style>
