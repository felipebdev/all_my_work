// const updateOrderBumpURL = @json(route('products.update.resources'));
// const updateProductImageURL = @json(route('products.image.resources'));
// const saveOrderBumpURL = @json(route('products.save.resources'));
// const getAllOrderBumpsURL = @json(route('products.get.resources', [':id', 'O']));
// const getOneOrderBumpURL = @json(route('products.get.one.resource', ''));
// const deleteOrderBumpURL = @json(route('products.delete.resources'));
// const planORS = @json($plan->id);

function valueBRL(value) {
  const formatter = new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  });
  return formatter.format(value);
}

function orderBump() {
  return {
    orderBumps: [],
    hasOrderBump: false,
    obModal: false,
    obDeleteModal: false,
    modo: 'Adicionar',
    resource: 0,
    orderBumpId: 0,
    form: {
      product: 0,
      discount: 0,
      message: '',
      image: '',
      filename: ''
    },
    getOrderBumps: function () {
      const url = getAllOrderBumpsURL.replace(/:id/g, planORS);
      fetch(`${url}`)
        .then(res => res.json())
        .then(data => {
          this.orderBumps = data.data;
          this.hasOrderBump = data.data.length > 0;
          document.getElementById("chk-orderbump-exists").checked = this.hasOrderBump;
        })
    },
    addOrderBumpModal: function () {
      this.clearFields();
      this.modo = 'Adicionar';
      this.obModal = true;
    },
    saveOrderBump: function () {
      const {product, discount, message, image} = this.form;

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
      formData.append("image", this.form.image);
      formData.append("resource", this.orderBumpId);

      if (this.modo === 'Atualizar') {
        axios.post(`${updateOrderBumpURL}`, formData
        ).then(res => {
          successToast('Dados atualizados.', res.data.message.toString())
          this.obModal = false;
          this.clearFields();
          this.getOrderBumps();
        }).catch(err => console.log(err));
      } else {
        formData.append("plan", planRS);
        formData.append("type", "O");
        axios.post(`${saveOrderBumpURL}`, formData).then(res => {
          successToast('Dado cadastrado.', res.data.message.toString())
          this.obModal = false;
          this.clearFields();
          this.getOrderBumps();
        }).catch(err => console.log(err));
      }
    },
    editOrderBump: function (id) {
      this.orderBumpMenu(id);
      this.orderBumpId = id;
      axios.get(`${getOneOrderBumpURL}/${id}`)
        .then(res => {
          this.cleanFieldsCss();
          const data = res.data.data;
          this.form.product = data.plan_id;
          this.form.discount = data.discount;
          this.form.message = data.message;
          this.form.filename = data.image !== null ? data.image.original_name : '';
          this.modo = 'Atualizar';
          this.obModal = true;
          this.orderBumpId = id;
        });
    },
    orderBumpMenu: function (id) {
      const menu = document.getElementById('menu-' + id);
      if (menu.classList.contains('d-none')) {
        menu.classList.remove('d-none');
      } else {
        menu.classList.add('d-none');
      }
    },
    deleteOrderBumpModal: function (id) {
      this.orderBumpMenu(id);
      this.orderBumpId = id;
      this.obDeleteModal = true;
    },
    deleteOrderBump: function () {
      axios.delete(`${deleteOrderBumpURL}`, {data: {resource: this.orderBumpId}}
      ).then(res => {
        this.getOrderBumps();
        successToast('Registro removido.', res.data.message.toString())
        this.obDeleteModal = false;
      }).catch(err => console.log(err));
    },
    clearFields: function () {
      this.form.product = this.form.discount = 0;
      this.form.message = this.form.image = this.form.filename = '';
      this.$refs.file.value = null;
    },
    cleanFieldsCss: function () {
      $('#orderbump_checkout_textarea').removeClass('mui--is-empty mui--is-untouched mui--is-pristine');
      $('#orderbump_checkout_textarea').addClass('mui--is-not-empty mui--is-touched mui--is-dirty');
      $('#order_bump_discount').removeClass('mui--is-empty mui--is-untouched mui--is-pristine');
      $('#order_bump_discount').addClass('mui--is-not-empty mui--is-touched mui--is-dirty');
    },
    removeImage: function () {
      const formData = new FormData();
      formData.append("resource", this.orderBumpId);
      axios.post(`${updateProductImageURL}`, formData
      ).then(res => {
        successToast('Dados atualizados.', res.data.message.toString())
        this.getOrderBumps();
        this.form.image = this.form.filename = '';
        this.$refs.file.value = null;
      }).catch(err => console.log(err));
    },
  }
}
