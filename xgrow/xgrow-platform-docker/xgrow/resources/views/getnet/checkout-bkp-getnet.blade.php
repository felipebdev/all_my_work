@extends('getnet.header')

@section('checkout')
    <script async src="{{ $urlCheckout }}/loader.js"
            data-getnet-sellerid="{{$seller_id}}"
            data-getnet-token="{{$response->token_type.' '.$response->access_token}}"
            data-getnet-amount="{{ $plan->price }}"
            data-getnet-customerid="{{$subscriber->id}}"
            data-getnet-orderid="{{$subscriber->id}}"
            data-getnet-button-class="pay-button-getnet"
            data-getnet-customer-first-name="{{ $subscriber->first_name }}"
            data-getnet-customer-last-name="{{ $subscriber->last_name }}"
            data-getnet-customer-document-type="{{ $subscriber->document_type }}"
            data-getnet-customer-document-number="{{ $subscriber->document_number }}"
            data-getnet-customer-email="{{ $subscriber->email }}"
            data-getnet-customer-phone-number="{{ $subscriber->main_phone }}"
            data-getnet-customer-address-street="{{ $subscriber->address_street }}"
            data-getnet-customer-address-street-number="{{ $subscriber->address_number }}"
            data-getnet-customer-address-complementary="{{ $subscriber->address_comp }}"
            data-getnet-customer-address-neighborhood="{{ $subscriber->address_district }}"
            data-getnet-customer-address-city="{{ $subscriber->address_city }}"
            data-getnet-customer-address-state="{{ $subscriber->address_state }}"
            data-getnet-customer-address-zipcode="{{ $subscriber->address_zipcode }}"
            data-getnet-customer-country="{{ $subscriber->address_country }}"
            data-getnet-items="[{'name':'{{ $plan->name }}','description':'{{ $plan->name }}','value':{{ $plan->price }},'quantity':1,'sku':'{{ $plan->id }}'}]"
            data-getnet-url-callback="{{ url('/') }}/getnet/thanks/{{ $platform_id }}/{{ base64_encode($plan->id) }}/{{ base64_encode($subscriber->id) }}"
            data-getnet-pre-authorization-credit="">
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
                        <a id="stepPersonalData" title="Dados pessoais" href="#tab-personal-data" class="step-item-link tab-personal-data complete" data-toggle="tab">
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
                    <div class="tab-pane @if($subscriber->id > 0) active @endif  p-3" id="tab-payment" role="tabpanel">
                        @include('getnet.tab-payment')
                    </div>
                    <div class="tab-pane  p-3" id="obrigado" role="tabpanel">
                        Obrigada pelo pagamento!
                    </div>
                </div>
            </div>
        </div>
        <div class="pay-beta-sidebar">
            @include('getnet.side-bar')
        </div>
    </div>
@endsection
