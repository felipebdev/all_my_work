<script>
const template = `
    <div class="col-lg-6 col-md-6 col-sm-12 mb-4">
        <div class="experience-panel h-100" :style="{background: this.theme.panelColor}">
            <header class="experience-panel-header" :class="inverse ? 'active' : ''" :style="{background: this.theme.headerColor}">
                <i class="experience-panel-icon fa" :class="icon" :style="{color: this.theme.iconColor}"></i>
                <span class="experience-panel-title">[[title]]</span>
            </header>
            <div class="experience-panel-body">
                <slot></slot>
            </div>
        </div>
  </div>
`;

export default {
  delimiters: ['[[', ']]'],
  props: ['icon', 'title', 'inverse'],
  template: template,
  name: 'Panel',
  data() {
    return {
      theme: {
        iconColor: this.inverse ? '#BDE942 !important' : '#474C58 !important',
        headerColor: this.inverse ? '#93BC1E !important' : '#242832 !important',
        panelColor: this.inverse ? '#222429 !important' : '#343945 !important'
      }
    };
  },
  created() {
  }
};
</script>

<style>
.experience-panel {
  border-radius: 5px;
}

.experience-panel-header {
  border-radius: 5px 5px 0 0;
  padding: 10px 20px;
}

.experience-panel-title {
  color: #FFFFFF;
  font-size: 16px;
  line-height: 45px;
  font-weight: 500;
}

.experience-panel-body {
  padding: 15px;
}

.experience-panel-icon {
  font-size: 1rem;
}
</style>
