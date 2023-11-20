// const product = @json($product->id);
// const getAllDeliveries = @json(route('products.get.all.deliveries'));
// const attachURL = @json(route('products.attach.content'));
// const detachURL = @json(route('products.detach.content'));
// const setDeliveryURL = @json(route('products.set.delivery'));
// const productInfoURL = @json(route('products.info', ':id'));
// const accessLink = @json("https://la.xgrow.com/".$product->platform_id);

Vue.createApp({
  delimiters: ['[[', ']]'],
  data() {
    return {
      deliveries: [],
      courses: [],
      sections: [],
      hasDelivery: false,
      externalArea: false,
      internalArea: false,
      onlySell: false,
      pxCourses: [],
      pxSections: [],
      email: {
        subjectEmail: '',
        messageEmail: '',
      }
    }
  },
  methods: {
    async getAllDeliveries() {
      const res = await axios.post(getAllDeliveries, {product});

      const {external_area, internal_area, only_sell, email, message} = res.data.product;
      this.externalArea = !!external_area;
      this.internalArea = !!internal_area;
      this.onlySell = !!only_sell;

      this.hasDelivery = res.data.has.course || res.data.has.section;

      this.courses = res.data.courses;
      this.pxCourses = res.data.px_courses;

      this.sections = res.data.sections;
      this.pxSections = res.data.px_sections;

      this.email.subjectEmail = email;
      this.email.messageEmail = message;

      if (message === '' || message === null) {
        const msg = "Olá ##NOME_ASSINANTE##,\n" +
          " \n" +
          "Seus dados de acesso são os abaixo:\n" +
          " \n" +
          "Login: ##EMAIL_ASSINANTE##\n" +
          "Senha: ##AUTO##\n" +
          " \n" +
          "Link de acesso: " + accessLink

        this.email.messageEmail = msg;
        this.email.subjectEmail = "Bem-vindo";
      }
    },
    hasChecked(idSearch, type) {
      if (type === 'c') {
        return this.pxCourses.some(x => x.course === idSearch);
      } else {
        return this.pxSections.some(x => x.section === idSearch);
      }
    },
    syncDelivery() {
      const idContent = event.target.getAttribute('data-id');
      const typeContent = event.target.getAttribute('data-content');
      const idProduct = product;
      if (event.target.checked) {
        axios.post(`${attachURL}`, {typeContent, idContent, idProduct}
        ).then((res) => {
          successToast('Item adicionado.', res.data.message.toString());
        }).catch(function (error) {
          errorToast('Erro ao adicionar item', 'Ocorreu um erro ao adicionar esse item, por favor tente mais tarde.');
        });
      } else {
        axios.post(`${detachURL}`, {typeContent, idContent, idProduct}
        ).then((res) => {
          successToast('Item removido.', res.data.message.toString());
        }).catch(function (error) {
          errorToast('Erro ao remover item', 'Ocorreu um erro ao remover esse item, por favor tente mais tarde.');
        });
      }
    },
    saveForm() {
      let type_delivery;
      if (this.onlySell) type_delivery = 'onlySell';
      if (this.externalArea) type_delivery = 'external';
      if (this.internalArea) type_delivery = 'internal';

      if (!this.internalArea && !this.onlySell && !this.externalArea) {
        errorToast('Verifique', 'Você deve marcar 1 tipo de entrega obrigatóriamente.');
        return true;
      }

      if (this.internalArea) {
        const idProduct = product;
        const subject_email = this.email.subjectEmail;
        const message_email = this.email.messageEmail;
        axios.post(`${setDeliveryURL}`, {idProduct, subject_email, message_email, type_delivery}
        ).then(res => {
          successToast('Dados salvos.', 'Dados salvos com sucesso.');
        }).catch(function (error) {
          errorToast('Erro ao salvar os dados', 'Ocorreu um erro ao os dados, por favor tente mais tarde.');
        });
      }
      const url = productInfoURL.replace(/:id/g, product);
      successToast('Dados salvos.', 'Dados salvos com sucesso.');
      setTimeout(function () {
        window.location.href = url;
      }, 1500);
    },
    syncOnlySell() {
      this.onlySell = true;
      this.externalArea = false;
      this.internalArea = false;
      this.sync();
    },
    syncExternalArea() {
      this.externalArea = true;
      this.onlySell = false;
      this.internalArea = false;
      this.sync();
    },
    syncInternalArea() {
      this.internalArea = true;
      this.onlySell = false;
      this.externalArea = false;
      this.sync();
    },
    sync() {
      const idProduct = product;
      let type_delivery;

      if (this.internalArea) type_delivery = 'internal';
      if (this.onlySell) type_delivery = 'onlySell';
      if (this.externalArea) type_delivery = 'external';

      this.ifNotSelected();

      axios.post(`${setDeliveryURL}`, {idProduct, type_delivery}
      ).catch(function (error) {
        errorToast('Erro ao adicionar item', error.response.data.message.toString());
      });
    },
    ifNotSelected() {
      if (!this.internalArea && !this.onlySell && !this.externalArea) {
        errorToast('Verifique', 'Você deve marcar 1 tipo de entrega obrigatóriamente.');
        return true;
      }
      return false;
    }
  },
  mounted() {
    this.getAllDeliveries();
  }
}).mount('#deliveryApp')
