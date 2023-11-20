@extends('templates.monster.main')

@section('jquery')

@endsection

@push('before-scripts')
    <script src="{{ mix('/js/home-one.js') }}"></script>
@endpush

@section('content')

    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">Clientes</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/getnet/clients">Clientes</a></li>
                <li class="breadcrumb-item active">Getnet</li>
            </ol>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <h4 class="card-title">Detalhes cliente</h4>

            <form class="mt-4" method="POST" action="">
                @include('getnet.clients.form')
                {{ csrf_field() }}
                {{ method_field('PUT') }}
            </form>

        </div>
    </div>

@endsection
