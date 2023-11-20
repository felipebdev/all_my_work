<template>
  <div class="container-fluid p-0">
    <div class="row">
      <div class="col-sm-12">
        <div :class="`alert alert-${status} d-flex align-items-center`" role="alert">
          <div class="alert-icon" v-if="!showIcon" v-html="activeIcon"></div>
          <div class="alert-content">
            <h6 class="alert-title">{{ title }}</h6>
            <slot></slot>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Alert",
  props: {
    title: { type: String, required: true },
    status: { type: String, required: true },
    icon: { type: String, required: false, default: null },
    hideIcon: { type: Boolean, required: false, default: false}
  },
  data() {
    return {
      activeIcon: "",
      icons: {
        info: '<i class="fas fa-info-circle"></i>',
        warning: '<i class="fa fa-exclamation-triangle"></i>',
      },
      showIcon: this.hideIcon,
    };
  },
  mounted() {
    if (this.icon) {
      this.activeIcon = this.icon;
    } else if (this.icons[this.status]) {
      this.activeIcon = this.icons[this.status];
    } else {
      this.showIcon = true
    }
  },
};
</script>

<style lang="scss" scoped>
.alert {
  align-items: flex-start !important;
  gap: 12px;

  &-title {
    font-size: 16px;
  }

  &-icon {
    font-size: 22px;
    border-radius: 8px;
    width: 40px;
    height: 40px;
    display: flex;
    justify-content: center;
    align-items: center;
  }
}

.alert-info {
  border: none;
  border-left: 6px solid var(--status-info);
  background: #303848;
  color: #c5dffd;

  .alert-icon {
    background: rgba(var(--status-info), 15%);
    color: var(--status-info) !important;
  }
}

.alert-warning {
  border: none;
  color: var(--status-warning);
  border-left: 4px solid var(--status-warning);
  background-color: #3d3736;

  .alert-icon {
    background: rgba(var(--status-warning), 15%);
    color: var(--status-warning) !important;
  }
}
/*
    .alert-secondary {}
    .alert-success {}
    .alert-danger {}
    .alert-warning {}
    .alert-info {}
    .alert-light {}
    .alert-dark {}
  */
</style>
