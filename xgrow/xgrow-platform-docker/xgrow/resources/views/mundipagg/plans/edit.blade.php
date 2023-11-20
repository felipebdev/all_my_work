@extends('templates.monster.main')

@section('jquery')

@endsection

@push('before-scripts')
    <script src="{{ mix('/js/home-one.js') }}"></script>
@endpush

@section('content')

    @if(session()->has('message'))
        <div class="alert alert-success text-center">
            {{ session()->get('message') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-warning">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">Planos</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/getnet/plans">Planos</a></li>
                <li class="breadcrumb-item active">Getnet</li>
            </ol>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Detalhes plano</h4>
            @include('getnet.plans.form')
        </div>
    </div>

@endsection
