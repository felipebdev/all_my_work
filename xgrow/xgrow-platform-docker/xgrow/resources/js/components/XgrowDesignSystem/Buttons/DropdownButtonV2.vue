<template>
  <div class="dropdown x-dropdown">
    <button
      class="xgrow-button xgrow-button-action table-action-button m-1"
      type="button"
      :id="`dropdownMenuButton${id}`"
      data-bs-toggle="dropdown"
    >
      <i class="fas fa-ellipsis-v"></i>
    </button>
    <ul class="dropdown-menu table-menu" :ariaLabelledby="`dropdownMenuButton${id}`">
      <li :key="item.name" v-for="item in items" v-show="!item.hide">
        <router-link
          :to="item.url"
          v-if="isVueRouter"
          class="dropdown-item table-menu-item"
        >
          <i :class="item.ico" class="icon"></i>
          {{ item.name }}
        </router-link>
        <a
          class="dropdown-item table-menu-item"
          :href="item.url"
          @click.prevent="handleClick(item.url, item.callback)"
          v-else
        >
          <i :class="item.ico" class="icon"></i>
          {{ item.name }}
        </a>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  props: {
    id: {
      type: [String, Number],
      default: "",
      required: true,
    },
    items: {
      type: Array,
      default: () => [
        {
          name: "",
          ico: "",
          url: "",
          hide: false,
          callback: () => {},
        },
      ],
      required: true,
    },
    isVueRouter: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    handleClick(link, callback) {
      if (link !== "#") {
        return (window.location.href = link);
      }
      return callback();
    },
  },
};
</script>
<style lang="scss" scoped>
.icon {
  height: 14px;
  width: 16px;
  color: var(--contrast-green3);
  margin-right: 5px;
}

.red {
  color: #f96c6c;
}

a:hover {
  color: #fff !important;
}
</style>
<style lang="scss">
.table-menu-item:hover {
  background: #222429 !important;
  color: #fff !important;
}
</style>
