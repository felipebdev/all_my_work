@extends('getnet.header')

@section('jquery')
    <script src="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/jquery/jquery.min.js"></script>
    <script>
        $(function() {
            $('#cardholder_identification').mask('0000 0000 0000 0000', {reverse: true});
            $('#expiration').mask('00/00', {reverse: true});
            $('#security_code').mask('000', {reverse: true});
        });
    </script>
@endsection

@section('content')
    @if ($errors->any())
        <div class="alert alert-warning">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="default-skin">
        <div class="pay-beta-main">
            <div class="step-container">
                <ul class="steps">
                    <li class="step-item">
{{--                        <a id="stepPersonalData" title="Dados pessoais" href="#tab-personal-data" class="step-item-link tab-personal-data complete" data-toggle="tab">--}}
                        <a id="stepPersonalData" title="Dados pessoais" class="step-item-link tab-personal-data complete" data-toggle="tab">
                            <span class="rounded step-number">1</span>
                            <span class="step-text">Dados pessoais</span>
                        </a>
                    </li>
                    <li class="step-item">
                        <a id="stepPayment" title="Pagamento" href="#tab-payment" class="step-item-link tab-payment @if($subscriber->id > 0) active complete @endif" data-toggle="tab">
                            <span class="rounded step-number">2</span>
                            <span class="step-text">Pagamento</span>
                        </a></li>
                    <li class="step-item">
                        <a id="stepThanks" title="Obrigado!" class="step-item-link">
                            <span class="rounded step-number">3</span>
                            <span class="step-text">Obrigado!</span>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane" id="tab-personal-data" role="tabpanel">
                        @include('getnet.tab-personal-data')
                    </div>
                    <form class="" id="formSubscriber" method="POST" action="{{ route('getnet.card.store') }}">
                    <div class="tab-pane @if($subscriber->id > 0) active @endif  p-3" id="tab-payment" role="tabpanel">
                        @include('getnet.tab-payment')
                    </div>
                    <div class="tab-pane  p-3" id="obrigado" role="tabpanel">
{{--                        Obrigada pelo pagamento!--}}
                    </div>
                </div>
            </div>
        </div>
        <div class="pay-beta-sidebar">
            @include('getnet.side-bar')
        </div>
        </form>
    </div>
@endsection
