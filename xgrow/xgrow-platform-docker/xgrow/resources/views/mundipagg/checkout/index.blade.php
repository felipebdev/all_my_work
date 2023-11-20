@extends('mundipagg.checkout.header')

@section('content')
    <div id="error" class="alert alert-danger" style="display: none"></div>
    @if ($errors->any())
        <div class="alert alert-danger text-center">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="d-block d-lg-none d-xl-none">
        @include('mundipagg.checkout.side-bar')
    </div>
    <div class="default-skin">
        <div class="pay-beta-main">
            <div class="step-container">
                <ul class="steps">
                    <li class="step-item">
                        <a id="stepPersonalData" title="Dados pessoais" href="#"
                           class="step-item-link tab-personal-data active" data-toggle="tab">
                            <span class="rounded step-number">1</span>
                            <span class="step-text">Dados pessoais</span>
                        </a>
                    </li>
                    <li class="step-item">
                        <a id="stepPayment" title="Pagamento" href="#" class="step-item-link tab-payment">
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
                        <div class="tab-pane active" id="tab-personal-data" role="tabpanel">
                            @include('mundipagg.checkout.tab-personal-data')
                        </div>
                        <div class="tab-pane p-3" id="tab-payment" role="tabpanel">
                            @include('mundipagg.checkout.tab-payment')
                        </div>
                        <div class="tab-pane  p-3" id="obrigado" role="tabpanel">
                            Obrigada pelo pagamento!
                        </div>
                    </div>
            </div>
        </div>
        <div class="pay-beta-sidebar d-none d-lg-block d-xl-block">
            @include('mundipagg.checkout.side-bar')
        </div>
    </div>
@endsection
