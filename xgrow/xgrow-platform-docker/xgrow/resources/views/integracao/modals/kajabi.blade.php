@push('after-scripts')
    <script>
        let forwardClickedKajabi = false;
        let planOptionsKajabi = ``;
        $(document).ready(function() {
            $('#kajabi-modal .btn-avancar').on('click', function() {
                if (!forwardClickedKajabi) getPlansKajabi();
                forwardClickedKajabi = true;
            });

            $('#kajabi-modal #btn-add-product').on('click', function () {
                const id = new Date().getTime();
                addProductKajabi(id);
            });
        });

        async function getPlansKajabi() {
            try {
                const { data: { plans = [] } } = await axios.get(
                    '/api/plans/get-all', 
                );
                
                const productsList = $('#kajabi-ipt_subscriber_product_list').val();
                const products = (productsList) ? JSON.parse(productsList) : [];
                if (products.length !== 0) {
                    products.forEach((value, index) => {
                        planOptionsKajabi = ``;
                        plans.forEach(plan => {
                            const planChecked = (String(plan.id) === String(value.subscriber_product_id)) ? 'selected' : '';
                            planOptionsKajabi += `<option value="${plan.id}" ${planChecked}>${plan.name}</option>`;
                        });

                        const id = new Date().getTime();
                        if (index === 0) {
                            $('#kajabi-subscriber_product_list').empty().append(planOptionsKajabi);
                            $(`#kajabi-subscriber_product_webhook`).val(value.subscriber_product_webhook).addClass('mui--is-not-empty');
                        }
                        else {
                            $('#kajabi-ipt_total_products').val(index);
                            addProductKajabi(id);
                            $(`#kajabi-subscriber_product_webhook_${id}`).val(value.subscriber_product_webhook).addClass('mui--is-not-empty');

                        }
                    });
                }
                else {
                    plans.forEach(plan => {
                        planOptionsKajabi += `<option value="${plan.id}">${plan.name}</option>`;
                    });

                    $('#kajabi-subscriber_product_list').empty().append(planOptionsKajabi);
                }

            }
            catch(error) {}
        }

        function addProductKajabi(id) {
            let totalProducts = $('#kajabi-ipt_total_products').val();
            const html = `
                <div id="kajabi-div-product_${id}" class="row mt-2">
                    <div class="d-flex justify-content-end" style="z-index:1;height:5px">
                        <button class="xgrow-button" type="button" id="btn-remove-product" style="width:16px;height:16px;background-color: #dc3545" onclick="removeProductKajabi(${id})">
                            <i class="fa fa-minus"></i>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <div class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                            <select id="kajabi-subscriber_product_list_${id}" class="xgrow-select w-100 kajabi-subscriber_product_list" name="events[on_create_subscriber][do_access_subscriber][${totalProducts}][subscriber_product_id]"></select>
                            <label for="kajabi-subscriber_product_list">Produto</label>    
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                            <input id="kajabi-subscriber_product_webhook_${id}" name="events[on_create_subscriber][do_access_subscriber][${totalProducts}][subscriber_product_webhook]" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                            <label for="kajabi-subscriber_product_webhook">Activation Webhook URL</label>
                        </div>
                    </div>
                </div>
            `;

            $('#kajabi-div-products').append(html);
            $(`#kajabi-subscriber_product_list_${id}`).empty().append(planOptionsKajabi);
            $('#kajabi-ipt_total_products').val(++totalProducts);
        }

        function removeProductKajabi(id) {
            let totalProducts = $('#kajabi-ipt_total_products').val();
            $('#kajabi-ipt_total_products').val(--totalProducts);
            $(`#kajabi-div-product_${id}`).remove();
        }
    </script>
@endpush

<div id="kajabi-modal" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="top-modal">
            <img src="{{ asset('xgrow-vendor/assets/img/kajabi.png') }}" alt="">
            <p>Kajabi é um sistema de marketing de conteúdo que oferece aos indivíduos e pequenas e médias empresas uma plataforma única e centralizada para vender, comercializar e entregar o conteúdo do produto.</p>
            <a href="https://www.kajabi.com" target="_blank">Saber mais sobre</a>
        </div>

        <form action="{{ url('/integracao/store') }}" method="POST" novalidate>
            @csrf
            @method('PUT')
            <input type="hidden" name="id_integration" value="14">
            <input type="hidden" name="id_webhook" value="14">
            <input id="kajabi-ipt_subscriber_product_list" type="hidden">
            <input id="kajabi-ipt_total_products" type="hidden" value="1">
            
            <div class="column-first ">
                <div class="input-two-first">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                        <input required="" id="kajabi-name_integration" name="name_integration" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                        <label for="kajabi-name_integration">Nome da integração</label>
                    </div>
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                        <input required="" id="kajabi-email_client" name="email_client" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                        <label for="kajabi-email_client">E-mail da conta kajabi</label>
                    </div>
                    <div class="d-flex form-check form-switch mb-3">
                        <input class="form-check-input me-2" type="checkbox" id="kajabi-flag_enable" name="flag_enable" value="1" checked="">
                        <label class="text-white" for="kajabi-flag_enable">Ativo</label> 
                    </div>
                </div>
                <div class="footer-modal">
                    <button class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                    <button type="submit" class="xgrow-button btn-avancar">Avançar</button>
                </div>
            </div>
            <div class="column-two d-none">
                <div class="top-column-two">
                    <label for="">Em quais eventos a integração será acionada?</label>
                    <div class="mt-2 mb-3">
                        <div class="checkbox-modal">
                            <input id="kajabi-on_create_subscriber" type="checkbox" name="events[on_create_subscriber]" value="true" checked>
                            <label for="kajabi-on_create_subscriber" class="check-input-label"></label>
                            <div class="label-right-check">
                                <label for="kajabi-on_create_subscriber"><strong>Aluno criado</strong></label>
                                <label for="kajabi-on_create_subscriber"></label>
                            </div>
                        </div>
                        <div class="ms-3">
                            <div class="checkbox-modal">
                                <input id="kajabi-do_access_subscriber" type="checkbox" name="events[on_create_subscriber][do_access_subscriber]" value="true" checked>
                                <label for="kajabi-do_access_subscriber" class="check-input-label"></label>
                                <div class="label-right-check">
                                    <label for="kajabi-do_access_subscriber">Conceder acesso</label>
                                    <label for="kajabi-do_access_subscriber">Para cada aluno com pagamento confirmado na Xgrow será concedido acesso ao conteúdo no Kajabi</label>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <div class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                                        <select id="kajabi-subscriber_product_list" class="xgrow-select w-100" name="events[on_create_subscriber][do_access_subscriber][0][subscriber_product_id]"></select>
                                        <label for="kajabi-subscriber_product_list">Produto</label>    
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                                        <input id="kajabi-subscriber_product_webhook" name="events[on_create_subscriber][do_access_subscriber][0][subscriber_product_webhook]" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                                        <label for="kajabi-subscriber_product_webhook">Activation Webhook URL</label>
                                    </div>
                                </div>
                            </div>
                            <div id="kajabi-div-products"></div>
                            <div class="row mt-2">
                                <div class="d-flex justify-content-end">
                                    <button class="xgrow-button" type="button" id="btn-add-product" style="width:32px;height:32px">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer-modal mt-3">
                    <button class="xgrow-button-cancel btn-voltar">Voltar</button>
                    <button type="submit" class="xgrow-button">Integrar</button>
                </div>
            </div>
        </form>
    </div>
</div>