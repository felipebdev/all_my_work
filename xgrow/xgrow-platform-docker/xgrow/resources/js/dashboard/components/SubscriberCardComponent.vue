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
      <!--      <p class="card-data mb-2">{{ cardData }}</p>-->
      <!--      <p class="card-info my-2">{{ info }}</p>-->

      <!--      <div class="card-content">-->
      <!--        <slot></slot>-->
      <!--      </div>-->

      <!--      <p class="card-title mt-2 mb-0">-->
      <!--        <i :class="`fas ${icon} me-2`"></i>-->
      <!--        {{ label }}-->
      <!--      </p>-->

      <div class="d-flex justify-content-between">
        <div :class="`p-2 card-icon ${iconColor}`">
          <i :class="`fas fa-2x ${icon}`"></i>
        </div>
        <div class="flex-grow-1">
          <p class="card-title">{{ label }}</p>
          <div class="d-flex justify-content-start">
            <div class="card-data">{{ cardData }}</div>
            <div :class="['card-info', infoColor]" v-if="info !== 0">
              <i :class="`fas fa-caret-${carretType}`"></i> {{ infoPercentage }}%
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import bootstrapColumns from "../../components/config/bootstrapColumnsProps";

export default {
  name: "subscriber-card-component",
  props: {
    /* Text properties */
    cardData: {
      required: false,
      type: String,
      default: "",
    },
    info: {
      required: false,
      type: Number,
      default: 0,
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
    iconColor: {
      required: false,
      type: String,
      default: "green",
    },

    /*
     Bootstrap/responsiviness
     tags properties
     */
    ...bootstrapColumns,
  },
  computed: {
    infoPercentage: function () {
      return this.info.toFixed(1).replace(".", ",");
    },
    infoColor: function () {
      return this.info > 0 ? "green" : "red";
    },
    carretType: function () {
      return this.info > 0 ? "up" : "down";
    },
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
    font-weight: bold;
    line-height: 19px;
    padding-right: 8px;
  }

  .card-info {
    font-size: 14px;
    font-style: normal;
    font-weight: 600;
    line-height: 19px;
    color: #addf45;
  }

  .card-title {
    font-size: 14px;
    font-style: normal;
    font-weight: 600;
    line-height: 19px;
    letter-spacing: 0em;
    text-align: left;
  }

  .green {
    color: var(--green4);
  }

  .red {
    color: var(--red);
  }

  .card-content {
    width: 100%;
  }
}
</style>
