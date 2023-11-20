@push('jquery')
    <script>
        const getOneResourceURL = @json(route('products.get.one.resource', ''));
        const updateResourceURL = @json(route('products.update.resources'));
        const saveResourceURL = @json(route('products.save.resources'));
        const deleteResourceURL = @json(route('products.delete.resources'));
        const getAllResourcesURL = @json(route('products.get.resources', [':id', 'U']));
        const planRS = @json($plan->id);

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
    </script>
@endpush

<div x-data="upsell()">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="d-flex align-items-center mb-3">
                <div class="form-check form-switch">
                    {!! Form::checkbox('chk-upsell-exists', null, null, ['id' => 'chk-upsell-exists', 'class' => 'form-check-input', 'x-model' => 'hasUpSell', '@change' => "checkUpsell"]) !!}
                    {!! Form::label('chk-upsell-exists', 'Habilitar upsell', ['class' => 'form-check-label']) !!}
                </div>
            </div>
        </div>
    </div>

    <div x-show="hasUpSell" class="card-thumb-collection">
        <template x-for="up in upSell" :key="up.id">
            <div class="card-thumb">
                <div class="button-drop" @click="upSellMenu(up.id)"><i class="fas fa-ellipsis-v"></i></div>
                <div class="card-thumb-menu d-none" :id="'menu-'+up.id">
                    <ul>
                        <li @click="editUpSell(up.id)">Editar</li>
                        <li @click="deleteUpSell(up.id)"> Excluir</li>
                    </ul>
                </div>
                <img :src="up.image?.filename ? up.image.filename : '{{url('xgrow-vendor/assets/img/icon-file.png')}}'"
                     :alt="up.plans.name"
                     class="card-thumb-img">
                <h1 class="card-thumb-title" x-text="up.plans.name"></h1>
                <span class="card-thumb-price" x-text="valueBRL(up.plans.price)">R$ 0,00</span>
            </div>
        </template>

        <div class="card-thumb card-thumb-button" x-show="upSell.length < 1">
            <button class="custom-link" type="button"
                    @click="addUpSell()">
                <i class="fas fa-plus-circle"></i><br>Adicionar<br>upsell
            </button>
        </div>
    </div>

    {{-- MODAL --}}
    <div class="modal-sections modal fade show m-1" tabindex="-1" aria-hidden="true" style="background: rgba(0,0,0,.7);"
         :class="upModal ? 'd-block' : ''">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="d-flex w-100 justify-content-end p-3 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            @click="upModal=!upModal"></button>
                </div>
                <div class="modal-header">
                    <p class="modal-title"><span x-text="modo">Adicionar</span> upsell</p>
                </div>
                <div class="modal-body d-block">
                    <div class="row" style="text-align: left!important;">
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="xgrow-form-control">
                                <div
                                    class="xgrow-floating-input mui-textfield mui-textfield--float-label"
                                    style="margin-top:-3px;">
                                    {!! Form::select('upsell_plan_id', [''=>'Selecione um produto'] + $upSells, null, ['id' => 'upsell_plan_id', 'class' => 'xgrow-select form-check-input', 'x-model' => 'upSellForm.product']) !!}
                                    {!! Form::label('upsell_plan_id', 'Selecione o produto:') !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="xgrow-form-control">
                                <div
                                    class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                    {!! Form::number('upsell_discount', null, ['id' => 'upsell_discount', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine', 'min' => 0, 'max' => 100, 'x-model' => 'upSellForm.discount']) !!}
                                    {!! Form::label('upsell_discount', 'Porcentagem de desconto') !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div
                                class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                {!! Form::textarea('upsell_message', null, [
                                        'class' => '"w-100 mui--is-empty mui--is-pristine mui--is-touched',
                                        'id' => 'upsell_checkout_textarea',
                                        'rows' => 7,
                                        'cols' => 54,
                                        'maxlength' => 250,
                                        'style' => 'resize:none; height: auto; min-height:200px',
                                        'x-model' => 'upSellForm.message'])
                                !!}
                                {!! Form::label('upsell_message', 'Descreva detalhadamente aqui a sua mensagem.') !!}
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div
                                class="xgrow-floating-input mui-textfield mui-textfield--float-label pt-2">
                                {!! Form::url('upsell_video_url', null, ['id' => 'upsell_video_url', 'x-model' => 'upSellForm.link_video']) !!}
                                {!! Form::label('upsell_video_url', 'Link do vídeo') !!}
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-lg-12">
                                    <div class="xgrow-form-control">
                                        <div
                                            class="xgrow-floating-input mui-textfield mui-textfield--float-label"
                                            style="margin-top:-3px;">
                                            {!! Form::select('accept_upsell', $upsellOptions, null, ['id' => 'accept_upsell', 'class' => 'xgrow-select form-check-input', 'x-model' => 'upSellForm.accept']) !!}
                                            {!! Form::label('accept_upsell', 'Ao aceitar upsell:') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-12" x-show="upSellForm.accept === '2'">
                                    <div
                                        class="xgrow-floating-input mui-textfield mui-textfield--float-label pt-2">
                                        {!! Form::url('accept_url', null, ['id' => 'accept_url', 'x-model' => 'upSellForm.accept_url']) !!}
                                        {!! Form::label('accept_url', 'URL ao aceitar upsell') !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-lg-12">
                                    <div class="xgrow-form-control">
                                        <div
                                            class="xgrow-floating-input mui-textfield mui-textfield--float-label"
                                            style="margin-top:-3px;">
                                            {!! Form::select('refuse_upsell', $upsellOptions, null, ['id' => 'refuse_upsell', 'class' => 'xgrow-select form-check-input', 'x-model' => 'upSellForm.decline']) !!}
                                            {!! Form::label('refuse_upsell', 'Ao recusar upsell:') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-12" x-show="upSellForm.decline === '2'">
                                    <div
                                        class="xgrow-floating-input mui-textfield mui-textfield--float-label pt-2">
                                        {!! Form::url('decline_url', null, ['id' => 'decline_url', 'x-model' => 'upSellForm.decline_url']) !!}
                                        {!! Form::label('decline_url', 'URL ao recusar upsell') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 mb-3">
                            <p class="xgrow-medium-light-italic mb-2">
                                Imagem que identifica o produto. Tamanho 500x500.
                            </p>
                            <input type="file" name="image" x-ref="upsellFile"
                                   @change="upSellForm.image = $refs.upsellFile.files[0]"
                                   accept="image/png, image/jpeg, image/gif"/>

                            <div x-show="upSellForm.image_filename !== ''">
                                <p class="mt-2 d-flex align-items-center gap-2">
                                    Atual: <span x-text="upSellForm.image_filename"></span>
                                    <i class="fas fa-close text-danger" style="cursor:pointer"
                                       @click="removeImage()">
                                    </i>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-success" @click="saveUpSell()" x-text="modo">Adicionar
                    </button>
                    <button type="button" class="btn btn-outline-success" @click="upModal=false">Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DELETE --}}
    <div class="modal-sections modal fade show m-1" tabindex="-1" aria-hidden="true" style="background: rgba(0,0,0,.7);"
         :class="upDeleteModal ? 'd-block' : ''">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="d-flex w-100 justify-content-end p-3 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            @click="upDeleteModal=!upDeleteModal"></button>
                </div>
                <div class="modal-header">
                    <p class="modal-title">Deseja remover este upsell?</p>
                </div>
                <div class="modal-body">
                    <div class="row" style="text-align: left!important;">
                        <p>Tem certeza que deseja remover este upsell?</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" @click="deleteResource()">Remover</button>
                    <button type="button" class="btn btn-outline-success" @click="upDeleteModal=false">Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
