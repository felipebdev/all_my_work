<!-- {{-- MODAL Estorno --}} -->
@push('after-styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet">
    <style>
        .select2-container--default .select2-selection--multiple {
            border-bottom: none !important;
            background-color: #1E2025 !important;
        }

    </style>
@endpush

@push('jquery')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.12/jquery.mask.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script>

        $('#slc-bank-code').select2({
            language: {
                // You can find all of the options in the language files provided in the
                // build. They all must be functions that return the string that should be
                // displayed.
                noResults: function () {
                    return "Nenhum banco encontrado";
                }
            },
            placeholder: 'Banco',
            tags: false
        });

        const refundURL = @json(route('api.checkout.refund'));

        function callModalRefund(paymentType, paymentPlanId, amount, singleTransaction = null) {

            let valid = [
                'credit_card', 'pix', 'boleto'
            ];

            if (!valid.includes(paymentType)) {
                return errorToast('Estorno indisponível!', `Estorno não está disponível para esse tipo de pagamento.<br>
                    Entre em contato com o suporte caso considere um erro`);
            }

            $('#payment_type').val(paymentType);
            $('#subscription_payment_plan_id').val(paymentPlanId);
            $('#single_transaction').val(singleTransaction);

            let messages = {
                "null": "",
                "true": "Essa operação irá estornar o pagamento apenas desta parcela. Cobranças anteriores não serão reembolsadas e as cobranças futuras serão mantidas. O acesso do cliente ao produto será mantido também. Confirma o estorno da parcela selecionada?",
                "false": "Essa operação irá estornar o pagamento de todas parcelas. Cobranças futuras serão canceladas. O acesso do cliente ao produto será perdido também. Confirma o estorno do cliente selecionado?"
            }

            $(".refund-message").text(messages[singleTransaction]);

            let formatter = new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL',
            });

            let formattedAmount = `${formatter.format(amount)}`;

            $('#modal-refund-confirm .refund-value').text(formattedAmount);
            $('#modal-refund-confirm').modal('show');

            if (paymentType === 'boleto') {
                $('#modal-refund-bank-data').show();
            } else {
                $('#modal-refund-bank-data').hide();
            }
        }

        async function confirmRefund() {
            const type = $('#payment_type').val();
            const cancellationReason = $('#cancellation_reason').val();
            const singleTransaction = $('#single_transaction').val() === 'true';

            if (cancellationReason.trim() === '') {
                errorToast('Erro ao realizar ação!', `É obrigatório descrever o motivo do estorno.`);
                return true;
            }

            if (cancellationReason.trim().length < 10 || cancellationReason.trim().length > 50) {
                errorToast('Erro ao realizar ação!', `O motivo deve ter entre 10 e 100 caracteres.`);
                return true;
            }

            try {

                const payment_plan_id = $('#subscription_payment_plan_id').val();

                const data = {
                    type: type,
                    payment_plan_id,
                    reason: cancellationReason,
                    single: singleTransaction
                };

                let bankData = {};
                if (type === 'boleto') {
                    const bankCode = $('#slc-bank-code').val();
                    const agency = $('#ipt-bank-agency').val();
                    const agencyDigit = $('#ipt-bank-agency-digit').val();
                    const account = $('#ipt-bank-account').val();
                    const accountDigit = $('#ipt-bank-account-digit').val();
                    const typeAccount = $('input[name=typeAccount]:checked').val();
                    const document = $('#ipt-bank-clientdocument').val();
                    const legalName = $('#ipt-bank-clientname').val();

                    if (!bankCode || !agency || !account || !accountDigit || !document || !legalName) {
                        errorToast('Campos faltando!', `Preencha todos os campos para efetuar o estorno.`);
                        return;
                    }

                    if(typeAccount === 'CPF' && !validator.isValidCpf(document)){
                        errorToast('Erro ao realizar ação!', `CPF inválido.`);
                        return;
                    }

                    if(typeAccount === 'CNPJ' && !validator.isValidCnpj(document)){
                        errorToast('Erro ao realizar ação!', `CNPJ inválido.`);
                        return;
                    }

                    if (legalName.trim().length < 5 || legalName.trim().length > 30) {
                        errorToast(
                            'Erro ao realizar ação!',
                            `${typeAccount === 'CNPJ' ? 'A Razão Social' : 'O Nome'} deve ter entre 5 e 30 caracteres.`
                        );
                        return true;
                    }

                    bankData = {
                        bank_code: bankCode,
                        agency: agency,
                        agency_digit: agencyDigit,
                        account: account,
                        account_digit: accountDigit,
                        document_number: document,
                        legal_name: legalName
                    };
                }

                const allData = Object.assign({}, data, bankData);
                const res = await axios.post(refundURL, allData);
                $('#modal-refund-confirm').modal('hide');
                successToast('Pagamento estornado!', res.data.message);

                setTimeout(() => {
                    window.location.reload();
                }, 3000);
            } catch (error) {
                errorToast('Algum erro aconteceu!', error.response.data.message);
            }
        }

        function changeTypeAccount(type){
            $('label[for=ipt-bank-clientdocument]').text(type)
            $('label[for=ipt-bank-clientname]').text(type == 'CNPJ' ? 'Razão Social' : 'Nome')
        }

        $(document).ready(
            function (){
                jQuery('.onlyNumbers').mask('Z',{translation:  {'Z': {pattern: /[0-9]/, recursive: true}}});
            }
        )

    </script>

@endpush


<div class="modal-sections modal fade" id="modal-refund-confirm" aria-labelledby="modal-refund-confirmLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <div class="modal-header">
                <p class="modal-title" id="modal-refund-confirmLabel">
                    Confirmar estorno
                </p>
            </div>
            <div class="modal-body flex-column align-items-start" style="text-align: left;">
                <p class="mb-3 px-2 xgrow-large-regular">
                    Valor do estorno: <span class="xgrow-large-bold refund-value">R$ 0,00</span>
                </p>
                <p class="mb-3 px-2 xgrow-medium-italic">
                    O estorno do pagamento poderá realizar o cancelamento do produto.
                </p>

                <div class="my-3 w-100">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        {!! Form::hidden('payment_method', null, ['id' => 'payment_type']) !!}
                        {!! Form::hidden('payment_id', null, ['id' => 'payment_id']) !!}
                        {!! Form::hidden('single', null, ['id' => 'single_transaction']) !!}
                        {!! Form::text('cancellation_reason', null, [
                                'id' => 'cancellation_reason',
                                'autocomplete' => 'off',
                                'spellcheck' => 'false',
                                'class' => 'mui--is-empty mui--is-untouched mui--is-pristine',
                                'maxlength' => 50,
                                'required'
                          ]) !!}
                        {!! Form::label('cancellation_reason', 'Motivo') !!}
                    </div>
                </div>

                {{-- Bank data required only for "boleto" --}}
                <div id="modal-refund-bank-data">
                    <div class="row">
                        <p class="xgrow-medium-light mb-2">Preencha os dados do favorecido para prosseguir</p>
                        <p class="xgrow-medium-light mb-2">O documento do titular da conta deve ser o mesmo do aluno</p>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="xgrow-form-control mui-textfield mui-textfield--float-label mb-3">
                                <div class="xgrow-form-control">
                                    <select class="xgrow-select-tag mb-3" id="slc-bank-code" name="bankCode" required>
                                        <option value="" selected disabled>Banco</option>
                                        @foreach ($bankList as $item)
                                            <option value="{{ $item->code }}">{{ $item->code }} -
                                                {{ $item->bank }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                <input id="ipt-bank-agency" name="agency"
                                       type="text"
                                       maxlength="6"
                                       inputmode="numeric" required
                                       class="mui--is-empty mui--is-untouched mui--is-pristine onlyNumbers">
                                <label for="ipt-bank-agency">Agência</label>
                                <span onclick="document.getElementById('ipt-bank-agency').value = ''"></span>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                <input id="ipt-bank-agency-digit" name="agencyDigit"
                                       maxlength="2"
                                       type="text" inputmode="numeric"
                                       class="mui--is-empty mui--is-untouched mui--is-pristine onlyNumbers" value="0">
                                <label for="ipt-bank-agency-digit">DG</label>
                                <span onclick="document.getElementById('ipt-bank-agency-digit').value = '0'"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                <input id="ipt-bank-account" name="account" type="text"
                                       maxlength="9"
                                       inputmode="numeric" required
                                       class="mui--is-empty mui--is-untouched mui--is-pristine onlyNumbers">
                                <label for="ipt-bank-account">Conta</label>
                                <span onclick="document.getElementById('ipt-bank-account').value = ''"></span>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                <input id="ipt-bank-account-digit" name="accountDigit" type="text"
                                       maxlength="2"
                                       inputmode="numeric" class="mui--is-empty mui--is-untouched mui--is-pristine onlyNumbers"
                                       value="0">
                                <label for="ipt-bank-account-digit">DG</label>
                                <span onclick="document.getElementById('ipt-bank-account-digit').value = '0'"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="subs-input-type-person d-flex flex-column">
                                <p class="xgrow-medium-bold">Tipo de pessoa</p>
                                <div class="xgrow-btn-group btn-group" role="group" aria-label="Basic radio toggle button group">
                                    <input type="radio" class="btn-check" name="typeAccount" id="natural_person_account" value="CPF"
                                           onclick="changeTypeAccount('CPF')"
                                           checked>
                                    <label class="btn btn-outline-primary" for="natural_person_account">Física</label>

                                    <input type="radio" class="btn-check" name="typeAccount" id="legal_person_account" value="CNPJ"
                                           onclick="changeTypeAccount('CNPJ')">
                                    <label class="btn btn-outline-primary" for="legal_person_account">Jurídica</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                <input id="ipt-bank-clientdocument" name="documentNumber" type="text"
                                       maxlength="18"
                                       inputmode="numeric" required
                                       class="mui--is-empty mui--is-untouched mui--is-pristine  onlyNumbers">
                                <label for="ipt-bank-clientdocument">CPF</label>
                                <span
                                    onclick="document.getElementById('ipt-bank-clientdocument').value = ''"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                <input id="ipt-bank-clientname" name="legalName" maxlength="30" type="text" required
                                       class="mui--is-empty mui--is-untouched mui--is-pristine">
                                <label for="ipt-bank-clientname">Nome</label>
                                <span onclick="document.getElementById('ipt-bank-clientname').value = ''"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="mb-3 px-2 xgrow-medium-italic refund-message"></p>
            </div>

            <div class="modal-footer px-2">
                <button id="" type="button" class="btn btn-success"  onclick="confirmRefund()">
                    Confirmar
                </button>
                <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal" aria-label="Close">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>
