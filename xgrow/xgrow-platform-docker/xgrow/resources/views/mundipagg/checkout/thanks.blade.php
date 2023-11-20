@extends('mundipagg.checkout.header')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger text-center">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session()->has('message'))
        <div class="alert alert-success text-center">
            {{ session()->get('message') }}
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
                        <a id="stepPersonalData" title="Dados pessoais" class="step-item-link tab-personal-data complete" data-toggle="tab">
                            <span class="rounded step-number">1</span>
                            <span class="step-text">Dados pessoais</span>
                        </a>
                    </li>
                    <li class="step-item">
                        <a id="stepPayment" title="Pagamento" class="step-item-link tab-payment complete" data-toggle="tab">
                            <span class="rounded step-number">2</span>
                            <span class="step-text">Pagamento</span>
                        </a></li>
                    <li class="step-item">
                        <a id="stepThanks" title="Obrigado!" href="#tab-thanks" class="step-item-link active complete" data-toggle="tab">
                            <span class="rounded step-number">3</span>
                            <span class="step-text">Obrigado!</span>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane" id="tab-personal-data" role="tabpanel">
                        @include('mundipagg.checkout.tab-personal-data')
                    </div>
                    <div class="tab-pane p-3" id="tab-payment" role="tabpanel">
                        @include('mundipagg.checkout.tab-payment')
                    </div>
                    <div class="tab-pane  p-3 active complete" id="tab-thanks" role="tabpanel">
                        @if( strlen(strip_tags($plan->message_success_checkout)) > 1 )
                            {!! $plan->message_success_checkout !!}
                        @else
                            Obrigado pelo pagamento!
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="pay-beta-sidebar d-none d-lg-block d-xl-block">
            @include('mundipagg.checkout.side-bar')
        </div>
    </div>
@endsection
