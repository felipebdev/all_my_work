@extends('templates.horizontal.main')

@section('jquery')
@endsection

@push('before-scripts')
    <script src="{{ mix('/js/home-one.js') }}"></script>
@endpush

@section('content')

    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">E-mails</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item">Configurações</li>
                <li class="breadcrumb-item active">E-mails</li>
            </ol>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-warning">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
			<h4 class="card-title">Editar</h4>
			<form class="mt-4" action="{{ route('email-provider.update', ['provider' => $provider->id]) }}" method="post">
                @csrf
       			@include('email-provider._form')
                <button type="submit" class="btn btn-primary">Editar provedor de e-mail</button>
       		</form>
        </div>
    </div>

@endsection
