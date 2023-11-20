@push('jquery')
    <script>
        const updateOrderBumpURL = @json(route('products.update.resources'));
        const updateProductImageURL = @json(route('products.image.resources'));
        const saveOrderBumpURL = @json(route('products.save.resources'));
        const getAllOrderBumpsURL = @json(route('products.get.resources', [':id', 'O']));
        const getOneOrderBumpURL = @json(route('products.get.one.resource', ''));
        const deleteOrderBumpURL = @json(route('products.delete.resources'));
        const planORS = @json($plan->id);

        function valueBRL(value) {
            const formatter = new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL'
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
                disableOrderBump: function(isChecked){
                    const chkOrderBumpExists = $("#chk-orderbump-exists")
                    if(chkOrderBumpExists.prop("checked"))
                        chkOrderBumpExists.click();
                    chkOrderBumpExists.prop("disabled", isChecked);
                },
                getOrderBumps: function () {
                    const url = getAllOrderBumpsURL.replace(/:id/g, planORS);
                    fetch(`${url}`)
                        .then(res => res.json())
                        .then(data => {
                            this.orderBumps = data.data;
                            this.hasOrderBump = data.data.length > 0;
                            document.getElementById('chk-orderbump-exists').checked = this.hasOrderBump;
                        });
                },
                addOrderBumpModal: function () {
                    this.clearFields();
                    this.modo = 'Adicionar';
                    this.obModal = true;
                },
                saveOrderBump: function () {
                    const {product, discount, message, image} = this.form;

                    if (discount < 0) {
                        errorToast('Erro ao salvar informações', 'Insira um valor de desconto não pode ser menor que  0.');
                        return false;
                    }

                    if (product === '' || product === 0) {
                        errorToast('Erro ao salvar informações', 'Selecione um produto válido.');
                        return false;
                    }

                    axios.create({headers: {'Content-Type': 'multipart/form-data'}});
                    const formData = new FormData();
                    formData.append('product', product);
                    formData.append('discount', discount);
                    formData.append('message', message);
                    formData.append('image', this.form.image);
                    formData.append('resource', this.orderBumpId);

                    if (this.modo === 'Atualizar') {
                        axios.post(`${updateOrderBumpURL}`, formData
                        ).then(res => {
                            successToast('Dados atualizados.', res.data.message.toString());
                            this.obModal = false;
                            this.clearFields();
                            this.getOrderBumps();
                        }).catch(err => console.log(err));
                    } else {
                        formData.append('plan', planRS);
                        formData.append('type', 'O');
                        axios.post(`${saveOrderBumpURL}`, formData).then(res => {
                            successToast('Dado cadastrado.', res.data.message.toString());
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
                        successToast('Registro removido.', res.data.message.toString());
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
                    formData.append('resource', this.orderBumpId);
                    axios.post(`${updateProductImageURL}`, formData
                    ).then(res => {
                        successToast('Dados atualizados.', res.data.message.toString());
                        this.getOrderBumps();
                        this.form.image = this.form.filename = '';
                        this.$refs.file.value = null;
                    }).catch(err => console.log(err));
                }
            };
        }
    </script>
@endpush

@push('after-styles')
    <link href="{{ asset('xgrow-vendor/assets/css/pages/products.css') }}" rel="stylesheet">
@endpush

<div x-data="orderBump()" x-init="getOrderBumps()">
    <div class="row mt-5">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="d-flex align-items-center mb-3">
                <div class="form-check form-switch">
                    {!! Form::checkbox('chk-orderbump-exists', null, null, ['id' => 'chk-orderbump-exists', 'class' => 'form-check-input', ':checked' => 'hasOrderBump', '@click' => "hasOrderBump = !hasOrderBump"]) !!}
                    {!! Form::label('chk-orderbump-exists', 'Habilitar order bump', ['class' => 'form-check-label']) !!}
                </div>
            </div>
        </div>
    </div>

    <div id="hasOrderBump" x-show="hasOrderBump" class="card-thumb-collection">
        <template x-for="ob in orderBumps" :key="ob.id">
            <div class="card-thumb">
                <div class="button-drop" @click="orderBumpMenu(ob.id)"><i class="fas fa-ellipsis-v"></i></div>
                <div class="card-thumb-menu d-none" :id="'menu-'+ob.id">
                    <ul>
                        <li @click="editOrderBump(ob.id)">Editar</li>
                        <li @click="deleteOrderBumpModal(ob.id)"> Excluir</li>
                    </ul>
                </div>
                <img :src="ob.image?.filename ? ob.image.filename : '{{url('xgrow-vendor/assets/img/icon-file.png')}}'"
                     :alt="ob.plans.name" class="card-thumb-img">
                <h1 class="card-thumb-title" x-text="ob.plans.name"></h1>
                <span class="card-thumb-price" x-text="valueBRL(ob.plans.price)">R$ 0,00</span>
            </div>
        </template>

        <div class="card-thumb card-thumb-button" x-show="orderBumps.length < 5">
            <button class="custom-link" type="button"
                    @click="addOrderBumpModal()">
                <i class="fas fa-plus-circle"></i><br>Adicionar<br>order bump
            </button>
        </div>
    </div>

    {{-- MODAL --}}
    <div class="modal-sections modal fade show m-1" tabindex="-1" aria-hidden="true" style="background: rgba(0,0,0,.7);"
         :class="obModal ? 'd-block' : ''">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="d-flex w-100 justify-content-end p-3 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            @click="obModal=!obModal"></button>
                </div>
                <div class="modal-header">
                    <p class="modal-title"><span x-text="modo">Adicionar</span> order bump</p>
                </div>
                <div class="modal-body d-block">
                    <div class="row p-2" style="text-align: left!important;">
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="xgrow-form-control">
                                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label"
                                     style="margin-top:-3px;">
                                    {!! Form::select('order_bump_plan_id', [''=>'Selecione um produto'] + $orderBumps, null, ['id' => 'order_bump_plan_id', 'class' => 'xgrow-select form-check-input', 'x-model' => 'form.product']) !!}
                                    {!! Form::label('order_bump_plan_id', 'Selecione o produto:') !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="xgrow-form-control">
                                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                    {!! Form::number('order_bump_discount', null, ['id' => 'order_bump_discount', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine', 'min' => 0, 'x-model.number' => 'form.discount']) !!}
                                    {!! Form::label('order_bump_discount', 'Porcentagem de desconto') !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                {!! Form::textarea('order_bump_message', null, [
                                        'class' => '"w-100 mui--is-empty mui--is-pristine mui--is-touched',
                                        'id' => 'orderbump_checkout_textarea',
                                        'rows' => 7,
                                        'cols' => 54,
                                        'maxlength' => 250,
                                        'style' => 'resize:none; height: auto; min-height:200px',
                                        'x-model' => 'form.message'])
                                !!}
                                {!! Form::label('order_bump_message', 'Descreva detalhadamente aqui a sua mensagem.') !!}
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-12 col-lg-12 mb-3">
                            <p class="xgrow-medium-light-italic mb-2">
                                Imagem que identifica o produto. Tamanho 500x500.
                            </p>
                            <input type="file" name="image" x-ref="file" @change="form.image = $refs.file.files[0]"
                                   accept="image/png, image/jpeg, image/gif"/>
                            <div x-show="form.filename !== ''">
                                <p class="mt-2 d-flex align-items-center gap-2">
                                    Atual: <span x-text="form.filename"></span>
                                    <i class="fas fa-close text-danger" style="cursor:pointer"
                                       @click="removeImage()">
                                    </i>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" @click="saveOrderBump()" x-text="modo">Adicionar
                    </button>
                    <button type="button" class="btn btn-outline-success" @click="obModal=false">Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DELETE --}}
    <div class="modal-sections modal fade show m1" tabindex="-1" aria-hidden="true" style="background: rgba(0,0,0,.7);"
         :class="obDeleteModal ? 'd-block' : ''">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="d-flex w-100 justify-content-end p-3 pb-0 m-3">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            @click="obDeleteModal=!obDeleteModal"></button>
                </div>
                <div class="modal-header">
                    <p class="modal-title">Deseja remover este Order Bump?</p>
                </div>
                <div class="modal-body">
                    <div class="row" style="text-align: left!important;">
                        <p>Tem certeza que deseja remover este order bump?</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" @click="deleteOrderBump()">Remover</button>
                    <button type="button" class="btn btn-outline-success" @click="obDeleteModal=false">Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
