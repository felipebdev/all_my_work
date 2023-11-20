@extends('templates.horizontal.main')

@section('jquery')
<script>
    $("#user_form-create").submit(function() {

        var password = $("#user_password").val()
        var password_repeat = $("#user_password-repeat").val()

        if (password !== password_repeat) {
            event.preventDefault();
            toastr["warning"]("As senhas devem ser iguais! \n Redigite por favor!");
        }

        return true;
    });

    $(document).ready(function() {
        $('.multiple-select').select2();
    });
</script>
@endsection

@push('before-scripts')
<script src="{{ mix('/js/home-one.js') }}"></script>
@endpush

@push('after-styles')
    <link rel="stylesheet" href="{{ asset('/css/select2.min.css') }}" />
@endpush
@push('after-scripts')
    <script src="{{ asset('/js/select2.full.min.js') }}"></script>
@endpush

@section('content')

    @if (count($errors) > 0)
        <div class="row">
            <div class="col col-sm-6 offset-sm-3">
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            {{ $error }} <br />
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

<div class="row page-titles">
    <div class="col-md-6 col-8 align-self-center">
        <h3 class="text-themecolor mb-0 mt-0">Relatórios</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item"><a href="/#">Relatórios</a></li>
            <li><a href="/audit">Auditoria</a></li>
        </ol>
    </div>
</div>

<div class="card">
    <div class="card-body">

        <h4 class="card-title">Novo usuário</h4>

        <form class="mt-4" method="POST" action="{{ url("/audit/users") }}" id="user_form-create">
            @if ($platforms->count() == 0)
            <div class="alert alert-warning">É necessário ter ao menos uma plataforma cadastrada para prosseguir!</div>
            @else
            @include('platforms-users.form')
            {{ csrf_field() }}
            {{ method_field('POST') }}
            <button type="submit" class="btn btn-success">Cadastrar</button>
            @endif
        </form>

    </div>
</div>

@endsection
