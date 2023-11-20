@push('after-scripts')
    <script>
        let forwardClicked = false;
        let planOptions = ``;
        $(document).ready(function() {
            $('#cademi-modal .btn-avancar').on('click', function() {
                if (!forwardClicked) getPlans();
                forwardClicked = true;
            });

            $('#cademi-modal #btn-add-product').on('click', function () {
                const id = new Date().getTime();
                addProduct(id);
            });

            $(document).on('change', '.cademi-subscriber_product_list', function (event) {
                const position = event.target.name.replace(/\D/g, "");
                $(`input[name="events[on_approve_payment][do_access_subscriber][${position}][subscriber_product_webhook]"]`)
                    .val($(this).val())
                    .addClass('mui--is-not-empty');
            });
        });

        async function getPlans() {
            try {
                const { data: { plans = [] } } = await axios.get(
                    '/api/plans/get-all', 
                );
                
                const productsList = $('#cademi-ipt_subscriber_product_list').val();
                const products = (productsList) ? JSON.parse(productsList) : [];
                if (products.length !== 0) {
                    products.forEach((value, index) => {
                        planOptions = ``;
                        plans.forEach(plan => {
                            const planChecked = (String(plan.id) === String(value.subscriber_product_id)) ? 'selected' : '';
                            planOptions += `<option value="${plan.id}" ${planChecked}>${plan.name}</option>`;
                        });

                        const id = new Date().getTime();
                        if (index === 0) {
                            $('#cademi-subscriber_product_list_0').empty().append(planOptions);
                            $(`#cademi-subscriber_product_webhook_0`).val(value.subscriber_product_id).addClass('mui--is-not-empty');
                        }
                        else {
                            $('#cademi-ipt_total_products').val(index);
                            addProduct(id);
                            $(`#cademi-subscriber_product_webhook_${id}`).val(value.subscriber_product_id).addClass('mui--is-not-empty');

                        }
                    });
                }
                else {
                    plans.forEach(plan => {
                        planOptions += `<option value="${plan.id}">${plan.name}</option>`;
                    });

                    $('#cademi-subscriber_product_list_0').empty().append(planOptions);
                    $(`input[name="events[on_approve_payment][do_access_subscriber][0][subscriber_product_webhook]"]`)
                        .val(plans[0].id)
                        .addClass('mui--is-not-empty');
                }

            }
            catch(error) {}
        }

        function addProduct(id) {
            let totalProducts = $('#cademi-ipt_total_products').val();
            const html = `
                <div id="div-product_${id}" class="row mt-2">
                    <div class="d-flex justify-content-end" style="z-index:1;height:5px">
                        <button class="xgrow-button" type="button" id="btn-remove-product" style="width:16px;height:16px;background-color: #dc3545" onclick="removeProduct(${id})">
                            <i class="fa fa-minus"></i>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <div class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                            <select id="cademi-subscriber_product_list_${id}" class="xgrow-select w-100 cademi-subscriber_product_list" name="events[on_approve_payment][do_access_subscriber][${totalProducts}][subscriber_product_id]"></select>
                            <label for="cademi-subscriber_product_list_${id}">Produto</label>    
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                            <input id="cademi-subscriber_product_webhook_${id}" name="events[on_approve_payment][do_access_subscriber][${totalProducts}][subscriber_product_webhook]" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine" disabled>
                            <label for="cademi-subscriber_product_webhook_${id}">Id do produto</label>
                        </div>
                    </div>
                </div>
            `;

            $('#div-products').append(html);
            $(`#cademi-subscriber_product_list_${id}`).empty().append(planOptions);
            $('#cademi-ipt_total_products').val(++totalProducts);
        }

        function removeProduct(id) {
            let totalProducts = $('#cademi-ipt_total_products').val();
            $('#cademi-ipt_total_products').val(--totalProducts);
            $(`#div-product_${id}`).remove();
        }
    </script>
@endpush

<div id="cademi-modal" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="top-modal">
            <img src="{{ asset('xgrow-vendor/assets/img/cademi.png') }}" alt="">
            <p>Cademí é uma plataforma de ensino e edutenimento com uma experiência de aprendizagem moderna e intuitiva. Aumente a retenção e aproveitamento dos seus alunos em seus cursos e treinamentos online.</p>
            <a href="https://www.cademi.com" target="_blank">Saber mais sobre</a>
        </div>

        <form action="{{ url('/integracao/store') }}" method="POST" novalidate>
            @csrf
            @method('PUT')
            <input type="hidden" name="id_integration" value="15">
            <input type="hidden" name="id_webhook" value="15">
            <input id="cademi-ipt_subscriber_product_list" type="hidden">
            <input id="cademi-ipt_total_products" type="hidden" value="1">
            
            <div class="column-first ">
                <div class="input-two-first">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                        <input required="" id="cademi-name_integration" name="name_integration" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                        <label for="cademi-name_integration">Nome da integração</label>
                    </div>
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                        <input required="" id="cademi-url_webhook" name="url_webhook" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                        <label for="cademi-url_webhook">Url da api cademí</label>
                    </div>
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                        <input required="" id="cademi-api_key" name="api_key" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                        <label for="cademi-api_key">Token da api cademí</label>
                    </div>
                    <div class="d-flex form-check form-switch mb-3">
                        <input class="form-check-input me-2" type="checkbox" id="cademi-flag_enable" name="flag_enable" value="1" checked="">
                        <label class="text-white" for="cademi-flag_enable">Ativo</label> 
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
                            <input id="cademi-on_approve_payment" type="checkbox" name="events[on_approve_payment]" value="true" checked>
                            <label for="cademi-on_approve_payment" class="check-input-label"></label>
                            <div class="label-right-check">
                                <label for="cademi-on_approve_payment"><strong>Pagamento aprovado</strong></label>
                                <label for="cademi-on_approve_payment"></label>
                            </div>
                        </div>
                        <div class="ms-3">
                            <div class="checkbox-modal">
                                <input id="cademi-do_access_subscriber" type="checkbox" name="events[on_approve_payment][do_access_subscriber]" value="true" checked>
                                <label for="cademi-do_access_subscriber" class="check-input-label"></label>
                                <div class="label-right-check">
                                    <label for="cademi-do_access_subscriber">Conceder acesso</label>
                                    <label for="cademi-do_access_subscriber">Para cada aluno com pagamento confirmado na Xgrow será concedido acesso ao conteúdo na Cademí</label>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <p class="modal-paragraph">Adicione os produtos e configura na entrega da Cademí os códigos dos produtos</p>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <div class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                                        <select id="cademi-subscriber_product_list_0" class="xgrow-select w-100 cademi-subscriber_product_list" name="events[on_approve_payment][do_access_subscriber][0][subscriber_product_id]"></select>
                                        <label for="cademi-subscriber_product_list_0">Produto</label>    
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-2">
                                        <input id="cademi-subscriber_product_webhook_0" name="events[on_approve_payment][do_access_subscriber][0][subscriber_product_webhook]" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine" disabled>
                                        <label for="cademi-subscriber_product_webhook_0">Id do produto</label>
                                    </div>
                                </div>
                            </div>
                            <div id="div-products"></div>
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