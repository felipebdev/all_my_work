<script>
const template = `
<li>
    <i class="fa" :class="getIcon"></i>[[title]]
</li>
`;

export default {
  delimiters: ['[[', ']]'],
  props: ['icon', 'title'],
  template: template,
  name: 'PanelItemTimeline',
  data() {
    return {};
  },
  computed:{
    getIcon: function () {
      if (this.icon === 'content') return 'fa-file-alt';
      if (this.icon === 'video') return 'fa-photo-video';
      if (this.icon === 'link') return 'fa-link';
      if (this.icon === 'text') return 'fa-align-justify';
      if (this.icon === 'archive') return 'fa-paperclip';
      if (this.icon === 'start' || this.icon === 'end') return 'fa-circle';
      return 'fa-file-alt';
    }
  },
  methods: {},
  created() {
  }
};
</script>

<style>
.experience-panel-timeline li:first-child i {
  color: #ADFF2F !important;
}

.experience-panel-timeline li:last-child i {
  color: #EB5757 !important;
}

.experience-panel-timeline li {
  padding-bottom: 1.75rem;
  border-left: 1px solid #616571;
  position: relative;
  padding-left: 25px;
  margin-left: 10px;
  display: flex;
  align-items: center;
}

.experience-panel-timeline li:last-child {
  border: 0;
  padding-bottom: 0;
}

.experience-panel-timeline li i {
  position: absolute;
  left: -10px;
  font-size: 1.25rem;
  color: #C0C0C1;
}
</style>
