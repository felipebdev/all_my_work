@extends('templates.monster.main')

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <h5 class="card-header">Clientes</h5>
                <div class="card-body">
                    <a class="btn btn-primary" href="{{url('/')}}/getnet/clients" role="button">Clientes</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <h5 class="card-header">Planos</h5>
                <div class="card-body">
                    <a class="btn btn-primary" href="{{url('/')}}/getnet/plans" role="button">Planos</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <h5 class="card-header">Assinaturas</h5>
                <div class="card-body">
                    <a class="btn btn-primary" href="{{ url('/') }}/getnet/subscriptions" role="button">Assinaturas</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <h5 class="card-header">#</h5>
                <div class="card-body">
                    <a class="btn btn-primary" href="#" role="button">#</a>
                </div>
            </div>
        </div>
    </div>

@endsection
