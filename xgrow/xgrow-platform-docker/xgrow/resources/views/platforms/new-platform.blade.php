@extends('templates.xgrow.main')

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-5" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Xgrow</a></li>
            <li class="breadcrumb-item active mx-2"><span>Nova Plataforma</span></li>
        </ol>
    </nav>

    @include('platforms.first-flow')
@endsection
