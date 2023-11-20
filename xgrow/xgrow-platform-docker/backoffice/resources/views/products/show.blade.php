@extends('templates.horizontal.main')

@php
    function match_status($status) {
        switch ($status) {
            case 'approved':
                return 'Aprovado';
            case 'refused':
                return 'Recusado';
            case 'under_analysis':
                return 'Em análise';
            case 'blocked':
                return 'Bloqueado';
            default:
                return 'Status desconhecido';
        }
    }
@endphp

@section('jquery')
@endsection

@push('before-styles')
    <link rel="stylesheet" type="text/css"
        href="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/datatables/media/css/dataTables.bootstrap4.css">
@endpush

@push('after-styles')
    <link rel="stylesheet" href="{{ asset("css/pages/products.css") }}">
    <style>
        .product-image img {
            width: 300px;
            height: auto;
            position: relative;
        }
    </style>
@endpush

@push('before-scripts')
    <script src="{{ mix('/js/home-one.js') }}"></script>
@endpush

@push('after-scripts')
    <script>
        $(document).ready(function () {
            $('.btn-change-status').click(function () {
                const status = $(this).data('status');
                const id = $(this).data('idproduct');
                const url = @json(route('products.change.status', ':id')).replace(/:id/g, id);

                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        'analysis_status': status,
                        '_token': "{{ csrf_token() }}",
                    },
                    success: function (response) {
                        alert(response.message);
                        window.location.reload();
                    },
                    error: function (error) {
                        alert(error.responseJSON.message);
                    }
                });
            });
        });
    </script>
@endpush

@section('content')
    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">Produtos</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route("products.index") }}">Produtos</a></li>
                <li class="breadcrumb-item active">{{ $product->name }}</li>
            </ol>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h3>{{ $product->name }} | {{ match_status($product->analysis_status) }}</h3>

            {{-- Produto principal --}}
            <div class="row mt-3 justify-content-between">
                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                    <label>
                        <b>Descrição</b>
                    </label>
                    <p>{{ $product->description }}</p>
    
                    <div class="d-flex flex-wrap">
                        <div class="mr-5">
                            <label>
                                <b>Preço</b>
                            </label>
                            <p>{{ $product->currency." ".$product->price }}</p>
                        </div>
    
                        <div class="mr-5">
                            <label>
                                <b>Tipo de pagamento</b>
                            </label>
                            <p>{{ adjustTypePayment($product->type_plan) }}</p>
                        </div>
                        
                        <div>
                            <label>
                                <b>Nº máx de parcelas</b>
                            </label>
                            <p>{{ $product->installment }}</p>
                        </div>
                    </div>

                    <label>
                        <b>Formas de pagamento</b>
                    </label>
                    <ul>
                        @if ($product->payment_method_boleto == 1)
                            <li>Boleto</li>
                        @endif
                        @if ($product->payment_method_credit_card == 1)
                            <li>Cartão de Crédito</li>
                        @endif
                        @if ($product->payment_method_pix == 1)
                            <li>Pix</li>
                        @endif
                        @if ($product->payment_method_multiple_cards == 1)
                            <li>Múltiplos cartões</li>
                        @endif
                    </ul>
                </div>

                @if ($product->image != null)
                    <div class="col-lg-6 col-md-6 col-sm-12 col-12 d-flex flex-column align-items-center product-image">
                        <label>
                            <b>Imagem do produto</b>
                        </label>
                        <img src="{{ $product->image->filename }}">
                    </div>
                @endif
            </div>

            {{-- Order Bump --}}
            @if ($product->order_bump_plan_id != null)
                <div class="row mt-5 justify-content-between">
                    <div class="col-12">
                        <h5>Order Bump</h5>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                        <label>
                            <b>Descrição</b>
                        </label>
                        <p>{{ $product->order_bump_message }}</p>
        
                        <label>
                            <b>Desconto</b>
                        </label>
                        <p>{{ $product->order_bump_discount."%" }}</p>
                    </div>

                    @if ($product->order_bump_image != null)
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 d-flex flex-column align-items-center product-image">
                            <label>
                                <b>Imagem do order bump</b>
                            </label>
                            <img src="{{ $product->order_bump_image->filename }}">
                        </div>
                    @endif
                </div>
            @endif

            {{-- Upsell --}}
            @if ($product->upsell_plan_id != null)
                <div class="row mt-5 justify-content-between">
                    <div class="col-12">
                        <h5>Upsell</h5>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                        <label>
                            <b>Descrição</b>
                        </label>
                        <p>{{ $product->upsell_message }}</p>
        
                        <label>
                            <b>Desconto</b>
                        </label>
                        <p>{{ $product->upsell_discount."%" }}</p>

                        <label>
                            <b>Vídeo do upsell</b>
                        </label>
                        <p>{{ $product->upsell_video_url }}</p>
                    </div>

                    @if ($product->upsell_image != null)
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 d-flex flex-column align-items-center product-image">
                            <label>
                                <b>Imagem do upsell</b>
                            </label>
                            <img src="{{ $product->upsell_image->filename }}">
                        </div>
                    @endif
                </div>
            @endif

            {{-- Checkout --}}
            <div class="row mt-5 justify-content-between">
                <div class="col-12">
                    <h5>Configuração do checkout</h5>
                </div>
                <div class="col-12 row">
                    @if ($product->checkout_support_platform != null)
                        <div class="col-lg-3 col-md-3 col-sm-6 col-12">
                            <label>
                                <b>Integração com widget de suporte</b>
                            </label>
                            <p>{{ $product->checkout_support_platform }}</p>
                        </div>
                    @endif

                    @if ($product->checkout_email != null)
                        <div class="col-lg-3 col-md-3 col-sm-6 col-12">
                            <label>
                                <b>Email de suporte</b>
                            </label>
                            <p>{{ $product->checkout_email }}</p>
                        </div>
                    @endif
                    
                    <div class="col-lg-3 col-md-3 col-sm-6 col-12">
                        <label>
                            <b>Layout do checkout</b>
                        </label>
                        <p>{{ $product->checkout_layout == 'page' ? 'Página única' : '3 passos' }}</p>
                    </div>

                    <div class="col-lg-3 col-md-3 col-sm-6 col-12">
                        <label>
                            <b>Endereço no checkout</b>
                        </label>
                        <p>{{ $product->checkout_address == 1 ? 'Sim' : 'Não' }}</p>
                    </div>
                </div>
            </div>

            {{-- Mensagem de agradecimento --}}
            <div class="row mt-5 justify-content-between">
                <div class="col-12">
                    <h5>Agradecimento</h5>
                </div>
                <div class="col-12 row">
                    @if ($product->url_checkout_confirm != null)
                        <div class="col-lg-3 col-md-3 col-sm-6 col-12">
                            <label>
                                <b>URL de confirmação</b>
                            </label>
                            <p>{{ $product->url_checkout_confirm }}</p>
                        </div>
                    @endif

                    @if ($product->message_success_checkout != null)
                        <div class="col-lg-3 col-md-3 col-sm-6 col-12">
                            <label>
                                <b>Mensagem</b>
                            </label>
                            <p>{{ $product->message_success_checkout }}</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="product-actions d-flex flex-wrap justify-content-end">
                @if ($product->analysis_status != 'approved')
                    <a
                        href="javascript:void(0)"
                        class="approved mt-3 btn-change-status"
                        data-status="approved"
                        data-idproduct="{{ $product->id }}"
                    >
                        <i class="fas fa-check"></i>
                        Aprovar
                    </a>
                @endif

                @if ($product->analysis_status != 'under_analysis')
                    <a
                        href="javascript:void(0)"
                        class="under-analysis mt-3 btn-change-status"
                        data-status="under_analysis"
                        data-idproduct="{{ $product->id }}"
                    >
                        <i class="far fa-clock"></i>
                        Analisar
                    </a>
                @endif
                
                @if ($product->analysis_status != 'refused')
                    <a
                        href="javascript:void(0)"
                        class="refused mt-3 btn-change-status"
                        data-status="refused"
                        data-idproduct="{{ $product->id }}"
                    >
                        <i class="far fa-thumbs-down"></i>
                        Recusar
                    </a>
                @endif
                
                @if ($product->analysis_status != 'blocked')
                    <a
                        href="javascript:void(0)"
                        class="blocked mt-3 btn-change-status"
                        data-status="blocked"
                        data-idproduct="{{ $product->id }}"
                    >
                        <i class="fas fa-ban"></i>
                        Bloquear
                    </a>
                @endif
            </div>
        </div>
    </div>
@endsection

@php
    function adjustTypePayment($type)
    {
        switch ($type) {
            case 'P':
                $type = 'Venda Única';
                break;
            case 'R':
                $type = 'Assinatura';
                break;
            default:
                $type = $type;
                break;
        }
        return $type;
    }
@endphp