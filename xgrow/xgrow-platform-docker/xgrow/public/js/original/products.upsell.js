//https://obfuscator.io/

// const getOneResourceURL = @json(route('products.get.one.resource', ''));
// const saveResourceURL = @json(route('products.save.resources'));
// const deleteResourceURL = @json(route('products.delete.resources'));
// const getAllResourcesURL = @json(route('products.get.resources', [':id', 'U']));
// const planRS = @json($plan->id);

function upsell() {
  return {
    upSell: [],
    hasUpSell: false,
    upModal: false,
    upDeleteModal: false,
    modo: 'Adicionar',
    grettingChk: false,
    grettingSwitch: false,
    upSellId: 0,
    upSellForm: {
      product: 0,
      discount: 0,
      message: '',
      link_video: '',
      accept: 1,
      decline: 1,
      accept_url: '',
      decline_url: '',
      image: null,
      image_filename: '',
    },
    init() {
      this.getUpsell();
    },
    addUpSell: async function () {
      this.clearFields();
      this.modo = 'Adicionar';
      this.upModal = true;
    },
    editUpSell: function (id) {
      this.upSellMenu(id);
      this.upSellId = id;
      axios.get(`${getOneResourceURL}/${id}`)
        .then(res => {
          this.cleanFieldsCss();
          const data = res.data.data;
          this.upSellForm.product = data.plan_id;
          this.upSellForm.discount = data.discount;
          this.upSellForm.message = data.message;
          this.upSellForm.accept = data.accept_event;
          this.upSellForm.decline = data.decline_event;
          this.upSellForm.accept_url = data.accept_url;
          this.upSellForm.decline_url = data.decline_url;
          this.upSellForm.link_video = data.video_url;
          this.upSellForm.image_filename = data.image !== null ? data.image.original_name : '';
          this.modo = 'Atualizar';
          this.upModal = true;
          this.upSellId = id;
        });
    },
    deleteUpSell: function (id) {
      this.upSellMenu(id);
      this.upSellId = id;
      this.upDeleteModal = true;
    },
    upSellMenu: function (id) {
      const menu = document.getElementById('menu-' + id);
      if (menu.classList.contains('d-none')) {
        menu.classList.remove('d-none');
      } else {
        menu.classList.add('d-none');
      }
    },
    saveUpSell: async function () {
      const {
        product,
        discount,
        message,
        link_video,
        accept,
        decline,
        accept_url,
        decline_url
      } = this.upSellForm;

      if (discount < 0 || discount > 100) {
        errorToast('Erro ao salvar informações', 'Insira um valor de desconto entre 0 e 100.')
        return false;
      }

      if (product === '' || product === 0) {
        errorToast('Erro ao salvar informações', 'Selecione um produto válido.')
        return false;
      }

      axios.create({headers: {'Content-Type': 'multipart/form-data'}});
      const formData = new FormData();
      formData.append("product", product);
      formData.append("discount", discount);
      formData.append("message", message);
      formData.append("video_url", link_video ?? '');
      formData.append("accept_event", accept);
      formData.append("decline_event", decline);
      formData.append("accept_url", (parseInt(accept) === 2) ? accept_url : '');
      formData.append("decline_url", (parseInt(decline) === 2) ? decline_url : '');
      formData.append("image", this.upSellForm.image);
      formData.append("resource", this.upSellId);

      if (this.modo === 'Atualizar') {
        axios.post(`${updateResourceURL}`, formData
        ).then(res => {
          successToast('Dados atualizados.', res.data.message.toString())
          this.upModal = false;
          this.clearFields();
          this.getUpsell();
        }).catch(err => console.log(err));
      } else {
        formData.append("plan", planRS);
        formData.append("type", "U");
        axios.post(`${saveResourceURL}`, formData).then(res => {
          successToast('Dado cadastrado.', res.data.message.toString())
          this.upModal = false;
          this.clearFields();
          this.getUpsell();
        }).catch(err => console.log(err));
      }
    },
    clearFields: function () {
      this.upSellForm.product = this.upSellForm.discount = 0;
      this.upSellForm.accept = this.upSellForm.decline = 1;
      this.upSellForm.message = this.upSellForm.link_video = this.upSellForm.filename
        = this.upSellForm.accept_url = this.upSellForm.decline_url = '';
      this.$refs.upsellFile.value = this.upSellForm.image = null;
    },
    deleteResource: function () {
      axios.delete(`${deleteResourceURL}`, {data: {resource: this.upSellId}}
      ).then(res => {
        this.getUpsell();
        successToast('Registro removido.', res.data.message.toString())
        this.upDeleteModal = false;
      }).catch(err => console.log(err));
    },
    getUpsell: function () {
      const url = getAllResourcesURL.replace(/:id/g, planRS);
      fetch(`${url}`)
        .then(res => res.json())
        .then(data => {
          this.upSell = data.data;
          this.hasUpSell = data.data.length > 0;
          this.checkUpsell();
        });
    },
    cleanFieldsCss: function () {
      $('#upsell_checkout_textarea').removeClass('mui--is-empty mui--is-untouched mui--is-pristine');
      $('#upsell_checkout_textarea').addClass('mui--is-not-empty mui--is-touched mui--is-dirty');
      $('#upsell_video_url').addClass('mui--is-not-empty mui--is-touched mui--is-dirty');
      $('#upsell_video_url').addClass('mui--is-not-empty mui--is-touched mui--is-dirty');
      $('#accept_url').addClass('mui--is-not-empty mui--is-touched mui--is-dirty');
      $('#accept_url').addClass('mui--is-not-empty mui--is-touched mui--is-dirty');
      $('#decline_url').addClass('mui--is-not-empty mui--is-touched mui--is-dirty');
      $('#decline_url').addClass('mui--is-not-empty mui--is-touched mui--is-dirty');
    },
    removeImage: function () {
      const formData = new FormData();
      formData.append("resource", this.upSellId);
      axios.post(`${updateProductImageURL}`, formData
      ).then(res => {
        successToast('Dados atualizados.', res.data.message.toString())
        this.getUpsell();
        this.upSellForm.image_filename = '';
        this.$refs.upsellFile.value = this.upSellForm.image = null;
      }).catch(err => console.log(err));
    },
    checkUpsell: function () {
      if (this.hasUpSell) {
        document.getElementById("chk-greeting-exists").disabled = true;
        document.getElementById("chk-greeting-exists").checked = false;
        document.getElementById("divGretting").classList.add('d-none');
      } else {
        if (this.upSell.length === 1){
          axios.delete(`${deleteResourceURL}`, {data: {resource: this.upSell[0].id}}
          ).then(res => {
            this.getUpsell();
          }).catch(err => console.log(err));
        }
        document.getElementById("chk-greeting-exists").disabled = false;
        document.getElementById("divGretting").classList.add('d-none');

        const hasCheckout = document.getElementById("url_checkout_confirm").value;
        if (hasCheckout !== '' || hasCheckout === null){
          document.getElementById("chk-greeting-exists").checked = true;
          document.getElementById("divGretting").classList.remove('d-none');
        }
      }
    },
    checkGretting: function () {
      const checked = document.getElementById("chk-greeting-exists").checked;
      if (checked) {
        document.getElementById("divGretting").classList.remove('d-none');
      } else {
        document.getElementById("url_checkout_confirm").value = '';
        document.getElementById("divGretting").classList.add('d-none');
      }
    },
  }
}
