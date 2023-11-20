<script>
import axios from "axios";

const template = `
<li class="experience-panel-item" :class="{'active': active}" @click="getContents">
    <p class="bold">[[title]]</p>
    <p class="experience-panel-item-step">
      [[steps]] passo[[steps > 1 ? 's' : '']]
      <span v-if="updateDiagram===null">
        <span v-if="diagram === null"> - </span>
        <span class="p-1 bg-danger" v-if="diagram === null" @click="sync(moduleId)">Sincronizar</span>
      </span>
    </p>
</li>
`;

export default {
  delimiters: ["[[", "]]"],
  props: ["title", "steps", "active", "moduleId", "index", "diagram"],
  template: template,
  name: "PanelItem",
  data() {
    return {
      updateDiagram: null,
    };
  },
  methods: {
    getContents: async function () {
      this.$store.dispatch("toggle_loading");
      const contents = await axios.get(getContents, {
        params: { module: this.moduleId },
      });
      this.$store.dispatch("add_contents", contents.data.response.contents);
      this.$store.dispatch("add_title_flow", {
        title: this.title,
        steps: this.steps,
        id: this.moduleId,
        diagram: this.diagram,
      });
      this.$emit("set-active", this.index);
      this.$store.dispatch("toggle_loading");
    },
    sync: async function (module) {
      await axios.post(syncModule, { moduleId: module });
      this.updateDiagram = "Sincronizado";
    },
  },
  created() {
    this.updateDiagram = this.diagram;
  },
};
</script>

<style>
.experience-panel-item {
  background: #343945;
  padding: 10px 20px;
  border-radius: 6px;
  margin: 10px 15px 15px 0;
  cursor: pointer;
}

.experience-panel-item.active {
  border: 1px solid #93bc1e;
  border-left: 5px solid #93bc1e;
}

.experience-panel-item-step {
  font-weight: 300;
  font-size: 14px;
}

.experience-panel-item-title {
  color: #989a9e;
  font-size: 14px;
  line-height: 16px;
  text-transform: uppercase;
}
</style>
