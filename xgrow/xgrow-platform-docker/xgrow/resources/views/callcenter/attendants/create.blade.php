@extends('templates.xgrow.main')

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item"><a href="{{ route('callcenter.dashboard') }}">Call center</a></li>
            <li class="breadcrumb-item"><a href="{{ route('attendant.index') }}">Atendentes</a></li>
            <li class="breadcrumb-item active mx-2"><span>{{ $attendant->id == 0 ? 'Criar' : 'Editar' }} atendente</span></li>
        </ol>
    </nav>

    <div class="xgrow-card card-dark p-3 py-4">
        @include('callcenter.attendants.form')
    </div>
@endsection