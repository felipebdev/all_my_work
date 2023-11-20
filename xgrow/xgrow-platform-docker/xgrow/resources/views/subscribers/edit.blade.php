@extends('templates.xgrow.main')

@push('jquery')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.12/jquery.mask.min.js"></script>
    <script>
        function validate() {
            let password = $('input[name=password]').val();
            let re_password = $('input[name=re_password]').val();

            if (password.length > 0 || re_password.length > 0) {
                if (password !== re_password) {
                    errorToast('Senhas diferentes!', 'As senhas devem ser iguais.');
                    return false;
                }
            }

            const countrySelected = $('#address_country').val();
            if (countrySelected == '') {
                errorToast('Algum erro aconteceu!', `Informe o país!`);
                return false;
            }

            return true;
        }

        $('#ipt-refund-amount').mask('#.##0,00', {
            reverse: true
        });
    </script>
@endpush

@push('after-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"
        integrity="sha512-pHVGpX7F/27yZ0ISY+VVjyULApbDlD0/X0rgGbTqCE7WFW5MezNTWG/dnhtbBuICzsd0WQPgpE4REBLv+UqChw=="
        crossorigin="anonymous"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html-to-pdfmake/browser.js"></script>
    <script>
        $(function() {
            $('[data-bs-toggle="popover"]').popover();
        });

        $('.btn-danger-custom').click(
            function() {
                if (!confirm(`Confirma a exclusão desse assinante?`)) {
                    return false;
                }
                errorToast('Excluindo assinante...', 'A exclusão está sendo feita, por favor, aguarde.');
                $.ajax({
                    type: 'POST',
                    url: "{{ URL::route('subscribers.destroy') }}",
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        'id': "{{ $subscriber->id }}",
                        '_token': "{{ csrf_token() }}",
                    },
                    success: function(data) {
                        self.location = "{{ route('subscribers.index') }}";
                    },
                    error: function(data) {
                        errorToast('Algum erro aconteceu!',
                            `Houve um erro na exclusão do registro: ${data.responseJSON.message}`);
                    },
                });

            },
        );

        function cancelSubscription(id) {
            if (!confirm(`Confirma o cancelamento da assinatura?`)) {
                return false;
            }

            $.ajax({
                type: 'POST',
                url: "{{ URL::route('subscriptions.cancel') }}",
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    'id': id,
                    'status_details': 'Assinatura cancelada pelo operador',
                    'returnJson': true,
                },
                success: function(data) {
                    if (data == true) {
                        $('#btnCancel' + id).remove();
                        $('#cancelDate' + id).html(data.canceled_at);
                        successToast('Sucesso', 'Produto cancelado com sucesso');
                    }
                },
                error: function(data) {
                    errorToast('Algum erro aconteceu!', `Veja mais em: ${data.message}`);
                },
            });
        }

        function downloadRefundProof(id) {
            $.ajax({
                type: 'GET',
                url: `/api/payments/${id}`,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    let content = `
                        <div>
                            <div>
                                <h4>Comprovante de estorno</h4><br>
                            </div>
                            <div>
                                <div><br></div>
                                <div style="font-size:18px">O valor de ${formatCoin(data.purchase.total)} do número de pedido ${data.refund.code} foi estornado e estará disponível em sua conta de acordo com o processamento do seu banco.</div>
                            </div>
                            <div><br></div>
                            <div style="font-size:14px">
                                <div>Dados do comprador:</div>
                                <div>Nome: ${data.subscriber.name}</div>
                                <div>${data.subscriber.document_type}: ${data.subscriber.document_number}</div>
                                <div>E-mail: ${data.subscriber.email}</div>
                                <div>Celular: ${data.subscriber.cellphone}</div>
                                <div><br></div>
                                <div>Dados da compra:</div>
                                <div>Produto: ${data.purchase.product}</div>
                                <div>Total: ${formatCoin(data.purchase.total)}</div>
                            </div>
                            <div><br></div>
                            <div><br></div>
                            <div>
                                <p style="font-size:12px">Gerado em ${formatDateTimePTBR(new Date())}</p><br>
                            </div>
                        </div>
                    `;

                    content = htmlToPdfmake(content);
                    const docDefinition = {
                        content
                    };
                    pdfMake.createPdf(docDefinition).download();
                },
                error: function(data) {
                    errorToast('Algum erro aconteceu!', `Veja mais em: ${data.message}`);
                },
            });
        }

        $(function() {
            const hash = location.hash.replace(/^#/, '');
            if (hash) {
                $('.nav-tabs a[href="#' + hash + '"]').tab('show');
            }

            let paymentId;
            $('#modal-send-bank-slip').on('show.bs.modal', function(e) {
                paymentId = $(e.relatedTarget).data('id');
            });

            $('#modal-send-purchase-proof').on('show.bs.modal', function(e) {
                paymentId = $(e.relatedTarget).data('id');
            });

            $('#modal-send-refund').on('show.bs.modal', function(e) {
                paymentId = $(e.relatedTarget).data('id');
            });

            $('#btn-send-bank-slip').on('click', function() {
                successToast('Aguarde, por favor.', 'Estamos reenviando o boleto!');
                $('#modal-send-bank-slip').modal('hide');

                $.ajax({
                    type: 'GET',
                    url: `/api/payments/${paymentId}/send-bank-slip`,
                    dataType: 'json',
                    data: {
                        '_token': "{{ csrf_token() }}",
                    },
                    success: function(data) {
                        successToast('Boleto reenviado!',
                            'O boleto foi reenviado com sucesso.');
                    },
                    error: function(data) {
                        let message = data.responseJSON.message;
                        if (data.status === 404) {
                            message = 'venda não encontrada.';
                        }
                        errorToast('Algum erro aconteceu!',
                            `Não foi possível reenviar o boleto: ${message}`);
                    },
                });
            });

            $('#btn-send-purchase-proof').on('click', function() {
                successToast('Aguarde, por favor!',
                    'Estamos reenviando o comprovante de confirmação de compra!');
                $('#modal-send-purchase-proof').modal('hide');

                $.ajax({
                    type: 'GET',
                    url: `/api/payments/${paymentId}/send-purchase-proof`,
                    dataType: 'json',
                    data: {
                        '_token': "{{ csrf_token() }}",
                    },
                    success: function(data) {
                        successToast('Comprovante reenviado!',
                            'O comprovante de confirmação de compra foi reenviado com sucesso.'
                        );
                    },
                    error: function(data) {
                        let message = data.responseJSON.message;
                        if (data.status === 404) {
                            message = 'venda não encontrada.';
                        }
                        errorToast('Algum erro aconteceu!',
                            `Não foi possível reenviar o comprovante de confirmação de compra: ${message}`
                        );
                    },
                });
            });

            $('#btn-send-refund').on('click', function() {
                successToast('Aguarde, por favor.',
                    'Estamos reenviando o comprovante de estorno de compra!');
                $('#modal-send-refund').modal('hide');

                $.ajax({
                    type: 'GET',
                    url: `/api/payments/${paymentId}/send-refund`,
                    dataType: 'json',
                    data: {
                        '_token': "{{ csrf_token() }}",
                    },
                    success: function(data) {
                        successToast('Comprovante reenviado!',
                            'O comprovante de estorno de compra foi reenviado com sucesso.'
                        );
                    },
                    error: function(data) {
                        let message = data.responseJSON.message;
                        if (data.status === 404) {
                            message = 'venda não encontrada.';
                        }
                        errorToast('Algum erro aconteceu!',
                            `Não foi possível reenviar o comprovante de estorno de compra: ${message}`
                        );
                    },
                });
            });
        });
    </script>
@endpush

@push('before-scripts')
@endpush

@push('after-styles')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.jsdelivr.net/npm/spectrum-colorpicker2@2.0.0/dist/spectrum.min.css">
    <style>
        [data-toggle="tooltip"] {
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item"><a href="/subscribers">Alunos</a></li>
            <li class="breadcrumb-item active"><span>Editar</span></li>
        </ol>
    </nav>

    <div class="xgrow-card card-dark p-0">
        <div class="xgrow-card-body p-3">

            <div class="row">
                <div class="col-md-12">
                    <nav class="xgrow-tabs-wrapper">
                        <div class="xgrow-tabs nav nav-tabs" id="nav-tab" role="tablist">
                            <a class="xgrow-tab-item nav-item nav-link {{ $tab === 'data' ? 'active' : '' }}"
                                id="nav-data-tab" data-bs-toggle="tab" href="#nav-data" role="tab" aria-controls="nav-data"
                                aria-selected="true">Dados</a>
                            <a class="xgrow-tab-item nav-item nav-link {{ $tab === 'history' ? 'active' : '' }}" id="nav-history-tab" data-bs-toggle="tab"
                                href="#nav-history" role="tab" aria-controls="nav-history"
                                aria-selected="false">Produtos</a>
                            <a class="xgrow-tab-item nav-item nav-link {{ $tab === 'payments' ? 'active' : '' }}"
                                id="nav-payments-tab" data-bs-toggle="tab" href="#nav-payments" role="tab"
                                aria-controls="nav-payments" aria-selected="false">Pagamentos</a>
                            <a class="xgrow-tab-item nav-item nav-link" id="nav-payment-data-tab" data-bs-toggle="tab"
                                href="#nav-payment-data" role="tab" aria-controls="nav-payment-data"
                                aria-selected="false">Dados de pagamento</a>
                            <a class="xgrow-tab-item nav-item nav-link" id="nav-email-data-tab" data-bs-toggle="tab"
                                href="#nav-email-data" role="tab" aria-controls="nav-email-data" aria-selected="false">
                                Envio de e-mails
                            </a>
                        </div>
                    </nav>
                </div>

                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade {{ $tab === 'data' ? 'show active' : '' }}" id="nav-data"
                        role="tabpanel" aria-labelledby="nav-data-tab">
                        {!! Form::model($subscriber, ['method' => 'post', 'enctype' => 'multipart/form-data', 'route' => ['subscribers.update', $subscriber->id], 'onsubmit' => 'return validate()', 'autocomplete' => 'off']) !!}
                        @if ($plans->count() == 0)
                            <div class="alert alert-warning">
                                É necessário ter ao menos um produto cadastrado para prosseguir!
                            </div>
                        @else
                            @include('subscribers.form')
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}
                            <div class="row">
                                <div class='xgrow-card-footer p-3 border-top d-flex w-100 justify-content-between'>
                                    {!! Form::button('Excluir', ['class' => 'btn btn-danger-custom xgrow-button-secondary']) !!}
                                    {!! Form::button('Salvar Alterações', ['class' => 'btn xgrow-button', 'type' => 'submit']) !!}
                                </div>
                            </div>
                        @endif
                        {!! Form::close() !!}
                    </div>

                    <div class="tab-pane fade {{ $tab === 'history' ? 'show active' : '' }}" id="nav-history" role="tabpanel" aria-labelledby="nav-history-tab">
                        @include('subscribers._tab-plans')
                    </div>

                    <div class="tab-pane fade {{ $tab === 'payments' ? 'show active' : '' }}" id="nav-payments"
                        role="tabpanel" aria-labelledby="nav-payments-tab">
                        @include('subscribers.payments.payments')
                    </div>

                    <div class="tab-pane fade" id="nav-payment-data" role="tabpanel"
                        aria-labelledby="nav-payment-data-tab">
                        @include('subscribers.payment-data')
                    </div>
                    <div class="tab-pane fade" id="nav-email-data" role="tabpanel" aria-labelledby="nav-email-data-tab">
                        @include('subscribers.email-data')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('subscribers.payments.modal-refund')
    @include('subscribers.payments.modal-resend-billet')
    @include('subscribers.payments.modal-resend-purchase-confirmation')
    @include('subscribers.payments.modal-resend-refund-confirmation')
    @include('subscribers.payments.modal-cancel-product')
    @include('subscribers.payments.modal-cancel-product-and-refund')
    @include('subscribers.payments.modal-cancel-subscription')

    @include('elements.toast')
    @include('elements.status-modal')
@endsection
