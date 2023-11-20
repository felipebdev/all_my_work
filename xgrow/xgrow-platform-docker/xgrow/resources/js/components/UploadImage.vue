<template id="uploadImage">
  <div class="col-sm-12 col-md-12 col-lg-12 mb-3">
    <h5>{{ title }}</h5>
    <div class="xgrow-medium-italic">
      <p>{{ subtitle }}</p>
      <p v-if="imageSize">Tamanho: {{ imageSize }}</p>
    </div>
    <img
      :src="getThumbByProportion"
      class="my-3"
      :class="imgAspectRatio"
      :style="'object-fit:cover;' + getSizeByFormat + style"
      alt="upload image"
      :ref="'img-' + refer"
      :id="'img-' + refer"
    />
    <br />
    <div>
        <button
            type="button"
            @click="$refs['file-' + refer].click()"
            class="btn btn-themecolor up_image_button btn xgrow-upload-btn-lg"
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
</template>

<script>
export default {
  name: "UploadImage",
  props: {
    title: { required: true, type: String },
    subtitle: { required: true, type: String },
    imageSize: { required: false, type: String },
    imgAspectRatio: { required: true, type: String },
    refer: { required: true, type: String },
    btnIcon: { default: "fa-upload", type: String },
    btnTitle: { default: "Upload", type: String },
    accept: { default: "image/*", type: String },
    style: { required: false, type: String },
  },
  computed: {
    /**
     * Return Size/5 for better frontend image view
     * Look XP-1073 for reference
     */
    getSizeByFormat() {
      if (this.imgAspectRatio === "3x2") return "width:256px;height:169.6px;"; // Proportion/5
      if (this.imgAspectRatio === "2x3") return "width:169.6px;height:256px;"; // Proportion/5
      if (this.imgAspectRatio === "3x1") return "width:320px;height:180px;"; // Not Resized
      if (this.imgAspectRatio === "1x1") return "width:180px;height:180px;"; // Not Resized
      if (this.imgAspectRatio === "16x9") return "width:288px;height:162px;"; // Proportion/5
      if (this.imgAspectRatio === "25x45") return "width:276px;height:138px;"; // Proportion/5
      return "";
    },
    /**
     * Return correct thumb image by proportion
     * Look XP-1073 for reference
     */
    getThumbByProportion() {
      const bigImage = "/xgrow-vendor/assets/img/big-file.png";
      const smallImage = "/xgrow-vendor/assets/img/icon-file.png";
      if (this.imgAspectRatio === "3x2") return bigImage;
      if (this.imgAspectRatio === "2x3") return smallImage;
      if (this.imgAspectRatio === "3x1") return bigImage;
      if (this.imgAspectRatio === "1x1") return smallImage;
      if (this.imgAspectRatio === "16x9") return bigImage;
      if (this.imgAspectRatio === "25x45") return smallImage;
      return bigImage;
    },
  },
  methods: {
    preview() {
      let file = this.$refs["file-" + this.refer];
      let img = this.$refs["img-" + this.refer];
      let fr = new FileReader();
      fr.onload = function () {
        img.src = fr.result;
      };
      fr.readAsDataURL(file.files[0]);
      this.sendImage();
    },
    sendImage() {
      this.$emit("sendImage", {
        file: this.$refs["file-" + this.refer],
        name: "file-" + this.refer,
      });
    },
    reset() {
      this.$refs["img-" + this.refer].src = this.getThumbByProportion;
    },
    src(src) {
      this.$refs["img-" + this.refer].src = src;
    },
  },
};
</script>

<style scoped>
.file {
  visibility: hidden;
}
</style>
