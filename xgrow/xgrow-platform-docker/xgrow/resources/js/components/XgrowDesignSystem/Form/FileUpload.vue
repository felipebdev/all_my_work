<template>
  <div class="row mt-3 p-2">
    <div class="col-sm-12 col-md-12 col-lg-12 udi-border" v-if="!fileName">
      <div class="udi-container">
        <div v-html="uploadIcon" style="z-index: 1"></div>
        <p class="udi-p">
          {{ title }}
        </p>
        <small class="udi-small">{{description}}</small>
        <button
          type="button"
          @click="$refs['file-' + refer].click()"
          class="btn btn-themecolor up_image_button xgrow-upload-btn-lg udi-button"
        >
          <i class="fa" :class="btnIcon" aria-hidden="true"></i> {{ btnTitle }}
        </button>
      </div>
      <input
        type="file"
        class="file"
        id="file"
        :ref="'file-' + refer"
        :accept="accept"
        @change="preview()"
      />
    </div>
    <div v-else style="position: relative">
      <Input
        class="w-100"
        type="text"
        id="email"
        label="Arquivo CSV"
        :model-value="fileName"
        v-on:update:model-value="(res) => (form.support_email = res)"
      />
      <Button id="clear" text="" icon="fas fa-close" status="danger" :on-click="() => fileName = null"/>
    </div>
  </div>
</template>

<script>
import Input from "./Input.vue";
import Button from "../Buttons/DefaultButton.vue";

export default {
  name: "FileUpload",
  components: {
    Input,
    Button,
  },
  props: {
    title: { required: true, type: String },
    description: { required: false, type: String },
    refer: { required: true, type: String },
    btnIcon: { default: "fa-upload", type: String },
    btnTitle: { default: "Upload", type: String },
    accept: { default: "image/*", type: String },
    style: { required: false, type: String },
  },
  data() {
    return {
      fileName: null,
    };
  },
  emits: ["sendImage", "clear"],
  computed: {
    uploadIcon: function () {
      return (
        '<svg width="90" height="63" viewBox="0 0 90 63" fill="none" xmlns="http://www.w3.org/2000/svg">\n' +
        '<path d="M75.6 27.3656C76.1766 25.8609 76.5 24.2156 76.5 22.5C76.5 15.0469 70.4531 9 63 9C60.2297 9 57.6422 9.84375 55.5047 11.2781C51.6094 4.52812 44.3391 0 36 0C23.5688 0 13.5 10.0687 13.5 22.5C13.5 22.8797 13.5141 23.2594 13.5281 23.6391C5.65312 26.4094 0 33.9188 0 42.75C0 53.9297 9.07031 63 20.25 63H72C81.9422 63 90 54.9422 90 45C90 36.2953 83.8125 29.025 75.6 27.3656ZM55.3219 36H46.125V51.75C46.125 52.9875 45.1125 54 43.875 54H37.125C35.8875 54 34.875 52.9875 34.875 51.75V36H25.6781C23.6672 36 22.6688 33.5813 24.0891 32.1609L38.9109 17.3391C39.7828 16.4672 41.2172 16.4672 42.0891 17.3391L56.9109 32.1609C58.3313 33.5813 57.3187 36 55.3219 36Z" fill="#333844"/>\n' +
        "</svg>\n"
      );
    },
  },
  methods: {
    preview() {
      let file = this.$refs["file-" + this.refer];

      this.fileName = file.files[0].name;

      this.$emit("sendImage", {
        file: this.$refs["file-" + this.refer],
        name: "file-" + this.refer,
      });
    },
    sendImage() {

    },
    clear() {
      this.$refs["img-" + this.refer].src = "/xgrow-vendor/assets/img/dot.svg";
      this.$emit("clear");
    },
    src(src) {
      this.$refs["img-" + this.refer].src = src;
    },
  },
};
</script>
<style lang="scss">
#clear {
    width: 28px;
    height: 28px;
    padding: 0;
    border-radius: 6px;
    position: absolute;
    top: 30px;
    right: 20px;
  .icon {
    margin-right: 0px !important;
  }
}
</style>
<style scoped>
.file {
  visibility: hidden;
  position: absolute;
  left: 0;
  top: 0;
}

.udi-border {
  background: #252932;
  border: 2px dashed #646d85;
  box-sizing: border-box;
  border-radius: 14px;
  min-height: 320px;
  margin-top: 10px;
  padding: 20px;
  position: relative;
}

.udi-container {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap: 18px;
  width: 100%;
  height: 100%;
  text-align: center;
}

.udi-button {
  display: flex;
  width: fit-content;
  height: 48px;
  padding: 8px 32px;
  align-items: center;
  gap: 8px;
  z-index: 1;
}

.udi-p {
  font-size: 16px;
  font-style: normal;
  font-weight: 500;
  line-height: 24px;
  max-width: 300px;
  min-width: 200px;
  text-align: center;
  z-index: 1;
}

.udi-small {
  color: #c1c5cf;
  font-size: 14px;
  font-style: normal;
  font-weight: 300;
  margin-bottom: 14px;
  z-index: 1;
}


.udi-button-close {
  background: black;
  border: none;
  font-size: 21px;
  position: absolute;
  right: 16px;
  top: 10px;
  width: 30px;
  height: 30px;
  border-radius: 999999px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: white;
}
</style>
