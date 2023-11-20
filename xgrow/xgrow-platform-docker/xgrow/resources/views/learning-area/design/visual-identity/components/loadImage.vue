<template>
  <div class="load-image">
    <Title :is-form-title="true">{{ title }}</Title>
    <h3 class="load-image__subtitle">{{subtitle}}</h3>

    <div class="load-image__thumb"
      :class="{
        'is-vertical': isVertical,
        'is-horizontal': isHorizontal,
      }"
    >

      <img :src="src" />
    </div>

    <button class="load-image__btn" @click="$emit('remove-image')">
      <i class="fas fa-trash"></i>
    </button>

  </div>
</template>

<script>
import Title from '../../../../../js/components/XgrowDesignSystem/Typography/Title.vue'
export default {
  name: 'laodImage',
  components: {
    Title
  },
  props: {
    src: { type: String, required: true },
    title: {required: true, type: String},
    subtitle: {required: true, type: String},
    isVertical: { type: Boolean, required: false },
    isHorizontal: { type: Boolean, required: false },
  }
}
</script>

<style lang="scss" scoped>
.load-image {
  display: flex;
  flex-direction: column;

  &__thumb {
    width: 120px;
    height: 120px;
    background-color: #252932;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 8px;
    padding: 0.5rem;

    & > img {
      width: 100%;
      height: 100%;
      display: block;
      object-fit: cover;
    }
  }

  &__subtitle {
    font-family: "Open Sans", serif;
    font-style: italic;
    font-weight: 400;
    font-size: 0.75rem;
    line-height: 1rem;
    color: #E7E7E7;
  }

  &__btn {
    display: flex;
    gap: 0.75rem;
    justify-content: center;
    align-items: center;
    width: 128px;
    color: #fff;
    border: 1px solid #fff;
    border-radius: 8px;
    width: 48px;
    height: 48px;
    background: transparent;
    margin-top: 10px;
    transition: background .3s linear;

    &:hover {
      background: #EB5757;
    }
  }
}

.is-vertical {
    width: 122px !important;
    height: 183px !important;
    padding: 5px;
}

.is-horizontal {
    width: 183px !important;
    height: 122px !important;
    padding: 5px;
}
</style>
