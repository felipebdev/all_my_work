@extends('templates.monster.main')

@section('jquery')
<script src="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/jquery/jquery.min.js"></script>
<script type="text/javascript">
    $(function() {

        const payload = $('#payloadView').html()

        const pretty = JSON.stringify(JSON.parse(payload), null, 3)
            .replace(/,/g, ', <br>')
            .replace(/{/g, '{ <br>')
            .replace(/}/g, ' <br>}')
        $('#payloadView').html(pretty)
    });

</script>

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
        <h3 class="mb-0 mt-0"><i class="mdi mdi-account"></i> Logs das Integrações</h3>
        <ol class="breadcrumb fandone-bc ">
            <li class="fandone-bc-item"><a href="/">Home</a></li>
            <li>
                <div class="arrow"></div>
            </li>
            <li class="fandone-bc-item"><a href="{{ url('/integracao-logs') }}">Logs das Integrações</a></li>
            <li>
                <div class="arrow"></div>
            </li>
            <li>Detalhes</li>
        </ol>
    </div>
</div>

<div class="card">
    <div class="card-body">

        <h4 class="card-title">Detalhes</h4>

        <div class="row">
            @if(!empty($details->id_return))
            <div class="col-md-3">
                <label>Id de Retorno</label>
                <h4>{{ $details->id_return }}</h4>
            </div>
            @endif
            <div class="col-md-3">
                <label>Id do Assinante</label>
                <h4>{{ $details->subscriber_id }}</h4>
            </div>
            @if(!empty($details->item_name))
            <div class="col-md-3">
                <label>Nome do Assinante</label>
                <h4>{{ $details->item_name }}</h4>
            </div>
            @endif
            @if(!empty($details->item_email))
            <div class="col-md-3">
                <label>Email do Assinante</label>
                <h4>{{ $details->item_email }}</h4>
            </div>
            @endif
        </div>
        <br>

        <div class="row">
            <div class="col-md-3">
                <label>Gatilho da Integração</label>
                <h4>{{ $details->trigger }}</h4>
            </div>
            <div class="col-md-3">
                <label>Nome da Ação</label>
                <h4>{{ $details->action_name }}</h4>
            </div>
            @if(!empty($details->action))
            <div class="col-md-3">
                <label>Ação</label>
                <h4>{{ $details->action }}</h4>
            </div>
            @endif
            <div class="col-md-3">
                <label>Metodo da Requisição</label>
                <h4>{{ $details->method }}</h4>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12">
                <label>Rota</label>
                <h4>{{ $details->route }}</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label>Payload</label>
                <br>
                <div id="payloadView" style="background-color: #000; color: #fff;">
                    @php
                    $payload = $details->payload;
                    @endphp
                    @json($payload)
                </div>
            </div>
        </div>
    </div>
</div>



@endsection
