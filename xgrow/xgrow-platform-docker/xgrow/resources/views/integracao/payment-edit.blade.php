@extends('templates.monster.main')

@section('jquery')

@endsection

@push('before-scripts')
<script src="{{ mix('/js/home-one.js') }}"></script>
@endpush

@section('content')

<div class="row page-titles">
    <div class="col-md-6 col-8 align-self-center">
        <h3 class="text-themecolor mb-0 mt-0">Planos</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item"><a href="/plans">Planos</a></li>
            <li class="breadcrumb-item active">Editar</li>
        </ol>
    </div>
</div>

<div class="card">
    <div class="card-body">

        <h4 class="card-title">Editar assinatura</h4>

        <form class="mt-4" >

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="price">Valor</label>
                        <input type="text" min="0" step="any" class="form-control" id="price" name="price" value="{{ (isset($payment->price)) ?  number_format($payment->price, 2, ',', '.') : '' }}" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="payment_data">Data de pagamento</label>
                        <input type="text" min="0" step="any" class="form-control" id="payment_data" name="payment_data" value="{{ (isset($payment->payment_data)) ?  $payment->payment_data : '' }}" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <input type="text" min="0" step="any" class="form-control" id="status" name="status" value="{{ (isset($payment->status)) ?  $payment->status : '' }}" readonly>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="price">Valor</label>
                        <input type="text" min="0" step="any" class="form-control" id="price" name="price" value="{{ (isset($payment->price)) ?  number_format($payment->price, 2, ',', '.') : '' }}" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="payment_data">Data de pagamento</label>
                        <input type="text" min="0" step="any" class="form-control" id="payment_data" name="payment_data" value="{{ (isset($payment->payment_data)) ?  $payment->payment_data : '' }}" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <input type="text" min="0" step="any" class="form-control" id="status" name="status" value="{{ (isset($payment->status)) ?  $payment->status : '' }}" readonly>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>

@endsection
