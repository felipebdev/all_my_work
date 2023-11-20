@extends('templates.horizontal.main')

@push('before-scripts')
<script src="{{ mix('/js/home-one.js') }}"></script>
@endpush


@section('content')

<div class="row page-titles">
    <div class="col-md-6 col-8 align-self-center">
        <h3 class="text-themecolor mb-0 mt-0">Plataformas</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/platforms') }}">Plataformas</a></li>
            <li class="breadcrumb-item active">Editar</li>
        </ol>
    </div>
</div>

<div class="card">
    <div class="card-body">

        <h4 class="card-title">Editar plataforma</h4>

        <form class="mt-4" method="POST" action="{{ url("/platforms/{$platform->id}") }}" enctype="multipart/form-data">
            @if ($customers->count() == 0)
            <div class="alert alert-warning">É necessário ter ao menos um cliente cadastrado para prosseguir!</div>
            @else
            @include('platforms.form')
            {{ csrf_field() }}
            {{ method_field('PUT') }}

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-success">Alterar</button>
            </div>
            @endif
        </form>


            <form method="POST"
                onsubmit="return confirm('Deseja deletar a plataforma: {{addslashes($platform->name)}} ?')"
                action="{{url("/platforms/{$platform->id}")}}">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
                 <button type="submit" class="btn btn-danger">Excluir</button>
            </form>




    </div>
</div>

@endsection
