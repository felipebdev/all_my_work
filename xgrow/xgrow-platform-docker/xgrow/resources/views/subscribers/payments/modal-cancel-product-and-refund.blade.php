{{--  @todo merge with 'modal-refund.blade.php' --}}
@push('jquery')
    <script>
        const refundURL2 = @json(route('api.checkout.refund'));

        function callModalCancelAndRefund(paymentType, paymentPlanId, planName = '', subscriberName = '') {

            let valid = [
                'credit_card', 'pix', 'boleto'
            ];

            $('#modal-subscription-cancel-refund .spn-cancel-product').html(planName || '')
            $('#modal-subscription-cancel-refund .spn-cancel-subscriber').html(subscriberName || '')

            if (!valid.includes(paymentType)) {
                return errorToast('Estorno indisponível!', `Estorno não está disponível para esse tipo de pagamento.<br>
                    Entre em contato com o suporte caso considere um erro`);
            }

            $('#subscription_payment_type').val(paymentType);
            $('#subscription_payment_plan_id').val(paymentPlanId);
            $('#subscription_single_transaction').val(true);

            $('#modal-subscription-cancel-refund').modal('show');

            if (paymentType === 'boleto') {
                $('#modal-cancel-and-refund-bank-data').show();
            } else {
                $('#modal-cancel-and-refund-bank-data').hide();
            }
        }

        async function confirmCancelAndRefund() {
            const type = $('#subscription_payment_type').val();
            const cancellationReason = $('#subscription_cancellation_reason').val();

            if (cancellationReason.trim() === '') {
                errorToast('Erro ao realizar ação!', `É obrigatório descrever o motivo.`);
                return true;
            }

            if (cancellationReason.trim().length < 10 || cancellationReason.trim().length > 100) {
                errorToast('Erro ao realizar ação!', `O motivo deve ter entre 10 e 100 caracteres.`);
                return true;
            }

            try {
                const data = {
                    type: type,
                    payment_plan_id: $('#subscription_payment_plan_id').val(),
                    reason: cancellationReason,
                    single: false
                };

                let bankData = {};
                if (type === 'boleto') {
                    const bankCode = $('#slc-subscription-bank-code').val();
                    const agency = $('#ipt-subscription-bank-agency').val();
                    const agencyDigit = $('#ipt-subscription-bank-agency-digit').val();
                    const account = $('#ipt-subscription-bank-account').val();
                    const accountDigit = $('#ipt-subscription-bank-account-digit').val();
                    const document = $('#ipt-subscription-bank-clientdocument').val();
                    const legalName = $('#ipt-subscription-bank-clientname').val();

                    if (!bankCode || !agency || !account || !accountDigit || !document || !legalName) {
                        errorToast('Campos faltando!', `Preencha todos os campos para efetuar o estorno.`);
                        return;
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
                const res = await axios.post(refundURL2, allData);

                successToast('Cancelamento e estorno efetuados!', res.data.message);

                setTimeout(() => {
                    window.location.reload();
                }, 3000);
            } catch (error) {
                errorToast('Algum erro aconteceu!', error.response.data.message);
            }
        }
    </script>
@endpush

{{-- <-- MODAL CANCELAR PRODUTO E ESTORNAR --> --}}
<div class="modal-sections modal fade" tabindex="-1" id="modal-subscription-cancel-refund" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="column-first" method="POST">
            @csrf
            @method('DELETE')

            <div class="modal-content">
                <div class="d-flex w-100 justify-content-end p-3 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-header">
                    <p class="modal-title">Confirmar cancelamento de produto e estorno de pagamento</p>
                </div>
                <div class="modal-body flex-column align-items-start"  style="text-align: left;">
                    <p class="mb-3 px-2 xgrow-medium-italic">
                        Você tem certeza que deseja cancelar o produto <strong class="spn-cancel-product"></strong>
                        do aluno <strong class="spn-cancel-subscriber"></strong>
                        e estornar os pagamentos?
                    </p>

                    <div class="my-3 w-100">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            {!! Form::hidden('payment_method', null, ['id' => 'subscription_payment_type']) !!}
                            {!! Form::hidden('payment_id', null, ['id' => 'subscription_payment_id']) !!}
                            {!! Form::hidden('payment_plan_id', null, ['id' => 'subscription_payment_plan_id']) !!}
                            {!! Form::hidden('single', null, ['id' => 'subscription_single_transaction']) !!}
                            {!! Form::text('cancellation_reason', null, ['id' => 'subscription_cancellation_reason', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine', 'required']) !!}
                            {!! Form::label('cancellation_reason', 'Motivo') !!}
                        </div>
                    </div>

                    {{-- Bank data required only for "boleto" --}}
                    <div id="modal-cancel-and-refund-bank-data">
                        <div class="row">
                            <p class="xgrow-medium-light mb-2">Preencha os dados do favorecido para prosseguir</p>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="xgrow-form-control">
                                    <select class="xgrow-select mb-3" id="slc-subscription-bank-code" name="bankCode" required>
                                        <option value="" selected disabled>Banco</option>
                                        @foreach ($bankList as $item)
                                            <option value="{{ $item->code }}">{{ $item->code }} -
                                                {{ $item->bank }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                    <input id="ipt-subscription-bank-agency" name="agency" type="text" inputmode="numeric" required
                                           class="mui--is-empty mui--is-untouched mui--is-pristine">
                                    <label for="ipt-bank-agency">Agência</label>
                                    <span onclick="document.getElementById('ipt-bank-agency').value = ''"></span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                    <input id="ipt-subscription-bank-agency-digit" name="agencyDigit" type="text" inputmode="numeric"
                                           class="mui--is-empty mui--is-untouched mui--is-pristine">
                                    <label for="ipt-bank-agency-digit">DG</label>
                                    <span onclick="document.getElementById('ipt-bank-agency-digit').value = ''"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                    <input id="ipt-subscription-bank-account" name="account" type="text" inputmode="numeric" required
                                           class="mui--is-empty mui--is-untouched mui--is-pristine">
                                    <label for="ipt-bank-account">Conta</label>
                                    <span onclick="document.getElementById('ipt-bank-account').value = ''"></span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                    <input id="ipt-subscription-bank-account-digit" name="accountDigit" type="text"
                                           inputmode="numeric" class="mui--is-empty mui--is-untouched mui--is-pristine">
                                    <label for="ipt-bank-account-digit">DG</label>
                                    <span onclick="document.getElementById('ipt-bank-account-digit').value = ''"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                    <input id="ipt-subscription-bank-clientdocument" name="documentNumber" type="text"
                                           inputmode="numeric" required
                                           class="mui--is-empty mui--is-untouched mui--is-pristine">
                                    <label for="ipt-bank-clientdocument">CPF/CNPJ</label>
                                    <span
                                        onclick="document.getElementById('ipt-bank-clientdocument').value = ''"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                    <input id="ipt-subscription-bank-clientname" name="legalName" type="text" required
                                           class="mui--is-empty mui--is-untouched mui--is-pristine">
                                    <label for="ipt-bank-clientname">Nome/razão social</label>
                                    <span onclick="document.getElementById('ipt-bank-clientname').value = ''"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="mb-3 px-2 xgrow-medium-italic refund-message"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal" aria-label="Close" onclick="confirmCancelAndRefund()">
                        Sim, cancelar
                    </button>
                    <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal">Não cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>
