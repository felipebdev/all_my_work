<template>
  <div
    :class="[
      `col-xxl-${xxl}`,
      `col-xl-${xl}`,
      `col-lg-${lg}`,
      `col-md-${md}`,
      `col-sm-${sm}`,
      `col-${xs}`,
    ]"
  >
    <div class="pill-container my-2">
      <div class="icon">
        <i :class="icon" v-if="!iconIsCustom" :style="{ color: iconColor }"></i>
        <img :src="icon" v-if="iconIsCustom" />
      </div>
      <div class="content">
        <p class="value">{{ value }}</p>
        <p class="title">{{ title }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import bootstrapColumns from "./config/bootstrapColumnsProps";

export default {
  name: "PillComponent",
  props: {
    /*
     The icon is not required
     To use you have to pass the fontawesome reference
     or a URL to am image file (set iconIsCustom as true
     to use an image file)

     Example:
      icon="fas fa-meteor"
     or
      icon="/url/to/the/image"
      :icon-is-custom="true"
     */
    icon: {
      required: false,
      type: String,
      default: "",
    },
    iconIsCustom: {
      required: false,
      type: Boolean,
      default: false,
    },
    iconColor: {
      required: false,
      type: String,
      default: "#ffffff",
    },
    value: {
      required: true,
    },
    title: {
      required: true,
      type: String,
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
.pill-container {
  background: #2a2e39;
  border-radius: 0.5rem;
  padding: 18px;
  display: flex;
  align-items: center;

  .icon {
    img {
      height: 30px;
      width: auto;
    }

    i {
      font-size: 30px;
    }
  }

  .content {
    margin-left: 18px;

    p {
      color: #e7e7e7;

      &.value {
        font-size: 20px;
        line-height: 27.24px;
        font-weight: 700;
      }

      &.title {
        font-size: 14px;
        line-height: 19.07px;
        font-weight: 400;
      }
    }
  }
}
</style>