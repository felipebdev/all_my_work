@extends('templates.horizontal.main')

@section('jquery')
    <script src="{{asset('vendor/password-validator/jquery-password-validator.js')}}"></script>
    <script>
        $('#user_password').passwordValidator('#user_password-repeat', true, true, true, 5);

        $('#btnSubmit').click(function () {
            $('#user_form-create').preventDefault();
            if ($('#user_password').val() === $('#user_password-repeat').val()) {
                $('#user_form-create').submit();
                return true;
            }
            return false;
        });

        $(document).ready(function () {
            $('.multiple-select').select2();
        });
    </script>
@endsection

@push('before-scripts')
    <script src="{{ mix('/js/home-one.js') }}"></script>
@endpush

@push('after-styles')
    <link rel="stylesheet" href="{{ asset('/css/select2.min.css') }}"/>
    <link rel="stylesheet" href="{{asset('vendor/password-validator/password-validator.css')}}">
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
                            {{ $error }} <br/>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">Plataformas</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/platforms">Plataformas</a></li>
                <li class="breadcrumb-item"><a href="/platforms/users">Usuários</a></li>
                <li class="breadcrumb-item active">Novo</li>
            </ol>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <h4 class="card-title">Novo usuário</h4>

            <form class="mt-4" method="POST" action="{{ url("/platforms/users") }}" id="user_form-create">
                @if ($platforms->count() == 0)
                    <div class="alert alert-warning">É necessário ter ao menos uma plataforma cadastrada para
                        prosseguir!
                    </div>
                @else
                    @include('platforms-users.form')
                    {{ csrf_field() }}
                    {{ method_field('POST') }}
                    <button type="submit" class="btn btn-success" id="btnSubmit">Cadastrar</button>
                @endif
            </form>

        </div>
    </div>

@endsection
