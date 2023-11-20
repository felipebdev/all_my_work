@push('after-styles')
    <link rel="stylesheet" href="{{ asset('xgrow-vendor/assets/css/pages/payment_data.css') }}">
    <style>
        .dark-mode .payment-card {
            background-color: #313848;
        }
        .xgrow-custom-buttom {
            width: 100%;
            padding: 10px 0;
            background: #313848;
            color: var(--white);
            border: none;
            margin: 10px 0;
            border-radius: 5px;
        }
    </style>
@endpush

@push('after-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>
    <script>
        $("#form-card").hide("fast");

        $("#add-card").on('click', function() {
            $("#form-card").show("fast");
        });

        $("#cancel-form-card").on('click', function() {
            $("#form-card").hide("fast");
        });

        $(".payment-card-check").on('click', function(evt) {
            let id = evt.target.id.split('-')[3];

            axios.post(`/subscribers/{{ $subscriber->id }}/creditcard/${id}`)
                .then(response => {
                    successToast('Cartão alterado!', "Cartão principal alterado com sucesso.");
                })
                .catch(error => {
                    errorToast('Algum erro aconteceu!', `Veja mais em: ${error.response.data}`);
                });
        });

        $(".payment-card-delete").on('click', function(evt) {
            let id = "";

            if (evt.target.nodeName == 'BUTTON') {
                id = evt.target.id.split('-')[3];
            } else {
                id = evt.target.parentElement.id.split('-')[3];
            }

            if ($(`#payment-card-check-${id}`).is(':checked')) {
                errorToast('Algum erro aconteceu!', "Não é possível excluir o seu cartão principal.");
                return;
            }

            axios.post(`/subscribers/{{ $subscriber->id }}/creditcard/${id}/delete`)
                .then(response => {
                    successToast('Cartão excluído!', "Este cartão não está mais disponível.");
                    window.location.reload();
                })
                .catch(error => {
                    errorToast('Algum erro aconteceu!', `Veja mais em: ${error.response.data}`);
                });
        });

        function addCard() {
            let form = document.querySelector('#form-card');
            let formData = new FormData(form);

            axios.post('/subscribers/{{ $subscriber->id }}/creditcard', formData)
                .then(response => {
                    successToast('Cartão adicionado!', "Adição feita com sucesso.");
                    window.location.reload();
                })
                .catch(error => {
                    let message = '';

                    let erros = error.response.data.errors;

                    Object.entries(erros).forEach(([key, value]) => {
                        for (i = 0; i < value.length; i++) {
                            message += value[i] + '\n';
                        }
                    });

                    errorToast('Algum erro aconteceu!', `Veja mais em: ${message}`);
                });
        }

        function sendChangeCardLink() {
            if (!confirm(`Confirma o envio do link de troca?`)) {
                return false;
            }

            successToast('Enviando o link', 'Estamos enviando o link de troca para o assinante.');

            $.ajax({
                type: 'GET',
                url: '/subscribers/{{ $subscriber->id ?? "" }}/change_card/',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    '_token': "{{ csrf_token() }}",
                },
                success: function(data) {
                    successToast('Dados reenviados!', `${data.message}`);
                },
                error: function(data) {
                    errorToast('Algum erro aconteceu!', `${data.responseJSON.message}`);
                },
            });
        }

    </script>
@endpush

<div class="row mt-3 justify-content-between" style="min-height: 512px">
    @if (isset($subscriber->customer_id) && count($cards) > 0)
        <div class="col-lg-8 px-4 mb-2">
    @else
        <div class="col-lg-12 px-4 mb-2">
    @endif
        <div class="row">
            <div class="col-12">
                <h5><strong>Troca de cartão</strong></h5>
            </div>
            <div class="col-12 payment-card-header">
                <p>Cartões do aluno:</p>
            </div>

            @foreach ($cards as $card)
            <div class="col-12">
                <div class="payment-card p-3 my-2 d-flex align-items-center justify-content-between flex-wrap">
                    <div class="payment-card-left d-flex align-items-center">
                        <div class="payment-card-img">
                            @php
                                $card_brand = 'xgrow-vendor/assets/img/credit-cards/' . strtolower($card->brand) . '.png';
                            @endphp
                            <img src="{{ asset($card_brand) }}" alt="Cartão de crédito">
                        </div>
                        <div class="payment-card-info">
                            <p>Terminado em:</p>
                            <p>...{{ $card->last_four_digits }}</p>
                        </div>
                    </div>
                    <div class="payment-card-right d-flex align-items-center">
                        <div class="payment-method xgrow-radio d-flex align-items-center mx-2">
                            <input type="radio" id="payment-card-check-{{ $card->id }}" class="payment-card-check"
                                name="paymentPrincipalCard"
                                {{ $card->id == $subscriber->credit_card_id ? 'checked' : '' }} />
                            <label for="payment-card-check">Usar este cartão</label>
                        </div>

                        {{-- <button class="xgrow-button table-action-button mx-2" data-card-id="{{ $card->id }}">
                            <i class="fa fa-edit"></i>
                        </button> --}}

                        <button id="payment-card-delete-{{ $card->id }}"
                            class="xgrow-button table-action-button payment-card-delete">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach

            @if (isset($subscriber->customer_id))
                <div class="col-12">
                    <button type="button" class="xgrow-custom-buttom" id="add-card">
                        <i class="fa fa-plus"></i> Adicionar um novo cartão
                    </button>
                </div>
            @else
                <div class="col-12 payment-card py-2 mt-2 d-flex align-items-center justify-content-center flex-wrap">
                    <p>O assinante precisa ter passado pelo checkout</p>
                </div>
            @endif

            <div class="col-12">
                <form id="form-card" class="payment-card py-3 mt-2 p-3" method="POST">
                    <h5 class="mb-3">Dados do novo cartão:</h5>
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                        <input type="text" class="form-control" name="holder_name" id="holder_name" value=""
                            autocomplete="off" type="text" required>
                        <label>Nome do titular</label>
                    </div>
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                        <input type="text" class="form-control" name="holder_document" id="holder_document" value=""
                            autocomplete="off" type="text" required>
                        <label>Documento do titular</label>
                    </div>
                    <div class="row justify-content-between">
                        <div class="col-sm-12 col-md-6 col-lg-8 xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                            <input type="number" class="form-control" name="number" id="number" value="" autocomplete="off"
                                type="text" required>
                            <label>Número do cartão</label>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4 xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                            <input type="number" class="form-control" name="cvv" id="cvv" value="" autocomplete="off"
                                type="text" required>
                            <label>CVV</label>
                        </div>
                    </div>
                    <div class="row justify-content-between">
                        <div class="col-sm-12 col-md-6 col-lg-4 xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                            <input type="number" class="form-control" name="exp_month" id="exp_month" value=""
                                autocomplete="off" type="text" required>
                            <label>Mês</label>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4 xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                            <input type="number" class="form-control" name="exp_year" id="exp_year" value=""
                                autocomplete="off" type="text" required>
                            <label>Ano</label>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-4 xgrow-form-control">
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                <select name="brand" id="bank" class="xgrow-select" required>
                                    <option value="0" disabled="" selected="" hidden=""></option>
                                    <option value="Elo">Elo</option>
                                    <option value="Mastercard">Mastercard</option>
                                    <option value="Visa">Visa</option>
                                    <option value="Amex">Amex</option>
                                    <option value="JCB">JCB</option>
                                    <option value="Aura">Aura</option>
                                    <option value="Hipercard">Hipercard</option>
                                    <option value="Diners">Diners</option>
                                    <option value="Discover">Discover</option>
                                </select>
                                <label for="brand">Bandeira</label>
                            </div>
                        </div>
                    </div>

                    @csrf

                    <div class='xgrow-card-footer pt-3 d-flex w-100 justify-content-between'>
                        <button id="cancel-form-card" type="button" class="btn xgrow-button-secondary">Cancelar</button>
                        <button type="button" class="btn xgrow-button" onclick="addCard()">Adicionar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if (isset($subscriber->customer_id) && count($cards) > 0)
        <div class="col-lg-4 px-4 d-flex justify-content-center">
            <button class="xgrow-button w-auto px-2" onclick="sendChangeCardLink()">
                Enviar link de troca de cartão
            </button>
        </div>
    @endif
    {{-- <div class="col-lg-4">
        <div class="row">
            <div class="col-12">
                <h5><strong>Escolher outras formas</strong></h5>
            </div>
            <div class="col-12 payment-method xgrow-check d-flex align-items-center">
                <input type="checkbox" id="payment-bank-slip" name="paymentMethod" value="Boleto"/><label for="payment-bank-slip">Boleto</label>
            </div>
            <div class="col-12 payment-method xgrow-check d-flex align-items-center">
                <input type="checkbox" id="payment-pix" name="paymentMethod" value="Pix"/><label for="payment-bank-slip">Pix</label>
            </div>
            <div class="col-12 payment-method xgrow-check d-flex align-items-center">
                <input type="checkbox" id="payment-two-cards" name="paymentMethod" value="Two Cards"/><label for="payment-bank-slip">Dois cartões</label>
            </div>
            <div class="col-12 payment-method xgrow-check d-flex align-items-center">
                <input type="checkbox" id="payment-no-limit" name="paymentMethod" value="No limit"/><label for="payment-bank-slip">Sem limite</label>
            </div>
        </div>
    </div> --}}
</div>

{{-- <div class="row">
    <div class='xgrow-card-footer pt-3 d-flex w-100 justify-content-end'>
        <button class="btn xgrow-button">Salvar</button>
    </div>
</div> --}}
