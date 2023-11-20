@extends('templates.xgrow.clean')
@push('after-scripts')
    <script>
        const affiliationConfirmUrl = @json(route('affiliation.confirm'));
        const openModalNotApproved = {{($client_approved)? 0 : 1}};
    </script>
    <script src="{{ asset('js/bundle/affiliates-invite.js') }}"></script>
@endpush
@push('after-styles')
    <link rel="stylesheet" href="{{ asset('xgrow-vendor/assets/css/pages/affiliates_invite.css') }}">
@endpush

@section('content')
    <div id="invite">
        @php
            $author = $product->first_name. ' ' . $product->last_name;
        @endphp
        <section class="content xgrow-background-image w-100 font-default">
            <div class="container">
                <div class="xgrow-img-logo">
                    <img src="{{ asset('xgrow-vendor/assets/img/logo/dark.svg') }}" alt="XGrow">
                </div>
                <div class="text-center">
                    @if($is_affiliate)
                        @if($is_affiliate->status == 'pending')
                            <p class="text-1">A sua solicitação esta pendente de aprovação, você pode acompanhar a aprovação pela área de afiliados.</p>
                        @else
                            <p class="text-1">Você já é afiliado deste produto!</p>
                        @endif
                        <p class="text-2">Você já aceitou o convite de {{$author}} para se tornar afiliado do produto {{$product->name}}.</p>
                    @else
                        <p class="text-1">Torne-se afiliado e aumente seus ganhos!</p>
                        <p class="text-2">Você foi convidado por {{$author}} para se tornar afiliado do produto {{$product->name}}.</p>
                    @endif
                </div>
                <div class="box-invite">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="img-product">
                                <img src="{{ $product->filename }}">
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="info-product">
                                <h1>{{$product->name}}</h1>
                                <p class="font-color-2">Por {{$author}}</p>
                                <div class="price border-gray">Receba até <span>R${{ number_format($price, 2, ',', '.') }}</span></div>
                                <p>Por cada venda</p>
                                @if(!$is_affiliate)
                                    <button @click.once="confirmAffiliation('{{$invite->invite_link}}')" invite="'{{$invite->invite_link}}'" class="btn-action mt-4">Solicitar afiliação</button>
                                @else
                                    <button @click="close()" class="btn-action mt-4">Fechar</button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 ">
                            <hr>
                            <h4 class="">DETALHES DO PRODUTO</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="details-product mt-3">
                                        <div class="mb-2"><strong>Categoria:</strong> {{$product->category_name}}</div>
                                        <div><strong>Tipo:</strong> {{($product->type == 'P')? 'Venda Única' : 'Assinatura'}}</div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="details-product mt-3">
                                        <div class="mb-2"><strong>Comissão:</strong> até {{ number_format($invite->commission, 2, ',', '.') }}% do valor do produto</div>
                                        <div><strong>E-mail de suporte:</strong> <a href="mailto:{{$invite->support_email}}">{{$invite->support_email}}</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <hr>
                            <h4>DESCRIÇÃO DO PRODUTO</h4>
                            <p class="mt-3">
                                {!! $product->description !!}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <loading :is-open="isLoading"></loading>

        <modal :is-open="openModalConfirm">
            <div class="modal-content-invite">
                <div v-if="error">
                    <i class="fas fa-exclamation-circle"></i>
                    <p class="mt-4 font-20">Ops!</p>
                    <p class="font-16">[[message]]</p>
                    <button @click="close()" class="btn-action mt-4">OK</button>
                </div>
                <div v-else>
                    <img src="{{ asset('xgrow-vendor/assets/img/confirm-ico.png') }}">
                    <p class="mt-4 font-20">Solicitação de afiliação enviada!</p>
                    <p class="font-16">[[message]]</p>
                    <button @click="close()" class="btn-action mt-4">OK</button>
                </div>

            </div>
        </modal>

        <modal :is-open="openModalNotApproved">
            <div class="modal-content-invite">
                <img src="{{ asset('xgrow-vendor/assets/img/documents/warning.svg') }}">
                <p class="mt-4 font-20">Antes de se tornar afiliado, nós precisamos verificar a sua identidade. <a href="{{ route('documents') }}">Clique aqui para verificar.</a></p>
            </div>
        </modal>

    </div>

@endsection
