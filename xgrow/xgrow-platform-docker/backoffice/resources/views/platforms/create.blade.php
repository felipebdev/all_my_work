@extends('templates.horizontal.main')
@section('jquery')

@endsection

@push('before-scripts')

<script src="{{ mix('/js/home-one.js') }}"></script>
@endpush
@push('after-scripts')
    <!--
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    -->
    <script type="module" src="{{ asset('/js/classes.js') }}"></script>
    <script type="module" src="{{ asset('/js/platforms.js') }}"></script>
@endpush

@section('content')

<div class="row page-titles">
    <div class="col-md-6 col-8 align-self-center">
        <h3 class="text-themecolor mb-0 mt-0">Plataformas</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/platforms') }}">Plataformas</a></li>
            <li class="breadcrumb-item active">Novo</li>
        </ol>
    </div>
</div>

<div class="card">
    <div class="card-body">

        <h4 class="card-title">Nova plataforma</h4>

        @if(false)
        @include('platforms._form')
        @endif
        <form class="mt-4" method="POST" id="post_form" action="{{ url("/platforms") }}">
            @if ($customers->count() == 0)
            <div class="alert alert-warning">É necessário ter ao menos um cliente cadastrado para prosseguir!</div>
            @else
            @include('platforms.form')
            {{ csrf_field() }}
            {{ method_field('POST') }}
            <button type="button" class="btn btn-success" onclick="document.getElementById('post_form').submit()">Cadastrar</button>
            @endif
        </form>

    </div>
</div>

@endsection
