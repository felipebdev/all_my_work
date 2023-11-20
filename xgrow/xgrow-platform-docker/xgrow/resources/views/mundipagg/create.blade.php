@extends('mundipagg.header')

@section('content')
    <div id="model_right" class="container-fluid fill">
        <div class="row fill  d-flex justify-content-center align-items-center">
            @if ($errors->any())
                <div class="alert alert-warning">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        <div class="default-skin">
            <div class="pay-beta-main">
                <div class="step-container">
                    <ul class="steps">
                        <li class="step-item">
                            <a id="stepPersonalData" title="Dados pessoais" href="#tab-personal-data"  class="step-item-link tab-personal-data active complete" data-toggle="tab">
                                <span class="rounded step-number">1</span>
                                <span class="step-text">Dados pessoais</span>
                            </a>
                        </li>
                        <li class="step-item">
                            <a id="stepPayment" title="Pagamento" href="#tab-payment"  class="step-item-link tab-payment" data-toggle="tab">
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
                            @include('mundipagg.tab-personal-data')
                        </div>

                        <div class="tab-pane p-3" id="tab-payment" role="tabpanel">
                            {{--                        @include('mundipagg.tab-payment')--}}
                        </div>

                        <div class="tab-pane  p-3" id="obrigado" role="tabpanel">
                            {{--                        Obrigado!--}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="pay-beta-sidebar">
                @include('mundipagg.side-bar')
            </div>
        </div>

@endsection
