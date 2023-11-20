@extends('templates.xgrow.main')

@push('before-styles')
@endpush

@push('before-scripts')
@endpush

@push('after-scripts')
@endpush

@section('content')
    @include('elements.alert')
    <div id="app">
        <div class="d-flex my-3 mb-0">
            <nav class="xgrow-breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Início</a></li>
                    <li class="breadcrumb-item"><a href="/subscribers">Alunos</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><span>Exportar</span></li>
                </ol>
            </nav>
        </div>
        <div class="xgrow-card card-dark p-0">
            <div class="xgrow-card-body p-3">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-6 mt-3 mb-3">
                        <h4 style="color: var(--contrast-green);">Informações para exportação dos dados</h4>
                        <p>Para exportar a lista de alunos, deverá selecionar quais os campos que você deseja exportar.
                        </p>
                        <p style="font-weight:bold; var(--contrast-green);">*Os campos nome, e-mail e plano são
                            obrigatórios
                            e por esse motivo não estão listados abaixo.</p>
                        <p class="mt-4">Selecione os campos que deseja exportar.</p>
                    </div>

                    <div class="col-sm-12 col-md-12 col-lg-6 mt-3 mb-3">
                        <div class="row">
                            {!! Form::model(null, ['method' => 'post', 'id' => 'exportForm', 'enctype' => 'multipart/form-data', 'route' => ['subscribers.export']]) !!}
                            <div class="d-flex flex-column mb-3">
                                <p class="xgrow-large-bold mb-3">Dados pessoais</p>
                                <div class="d-flex flex-row">
                                    <div class="xgrow-check me-3">
                                        {!! Form::checkbox('document_number', 'a.document_number', true, ['id' => 'document_number']) !!}
                                        {!! Form::label('document_number', 'CPF') !!}
                                    </div>
                                    <div class="xgrow-check me-3">
                                        {!! Form::checkbox('gender', 'a.gender', true, ['id' => 'gender']) !!}
                                        {!! Form::label('gender', 'Gênero') !!}
                                    </div>
                                    <div class="xgrow-check me-3">
                                        {!! Form::checkbox('cel_phone', 'a.cel_phone', true, ['class' => 'custom-control-input', 'id' => 'cel_phone']) !!}
                                        {!! Form::label('cel_phone', 'Telefone celular', ['class' => 'custom-control-label']) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-column mb-3">
                                <p class="xgrow-large-bold mb-3">Dados de endereço</p>
                                <div class="d-flex flex-row flex-wrap">
                                    <div class="xgrow-check me-3">
                                        {!! Form::checkbox('address_zipcode', 'a.address_zipcode', true, ['id' => 'address_zipcode']) !!}
                                        {!! Form::label('address_zipcode', 'CEP') !!}
                                    </div>
                                    <div class="xgrow-check me-3">
                                        {!! Form::checkbox('address_state', 'a.address_state', true, ['id' => 'address_state']) !!}
                                        {!! Form::label('address_state', 'Estado') !!}
                                    </div>
                                    <div class="xgrow-check me-3">
                                        {!! Form::checkbox('address_street', 'a.address_street', true, ['id' => 'address_street']) !!}
                                        {!! Form::label('address_street', 'Rua') !!}
                                    </div>
                                    <div class="xgrow-check me-3">
                                        {!! Form::checkbox('address_number', 'a.address_number', true, ['id' => 'address_number']) !!}
                                        {!! Form::label('address_number', 'Nº') !!}
                                    </div>
                                    <div class="xgrow-check me-3">
                                        {!! Form::checkbox('address_comp', 'a.address_comp', true, ['id' => 'address_comp']) !!}
                                        {!! Form::label('address_comp', 'Complemento') !!}
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-column mb-3">
                                <p class="xgrow-large-bold mb-3">Dados extras</p>
                                <div class="d-flex flex-row">
                                    <div class="xgrow-check me-3">
                                        {!! Form::checkbox('status', 'a.status', true, ['id' => 'status']) !!}
                                        {!! Form::label('status', 'Status') !!}
                                    </div>
                                    <div class="xgrow-check me-3">
                                        {!! Form::checkbox('login', 'a.login', true, ['id' => 'login']) !!}
                                        {!! Form::label('login', 'Último acesso') !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12 pt-3">
                                <div class="xgrow-form-control mui-textfield mui-textfield--float-label mb-3">
                                    {!! Form::select('filter_by_login', $filter_by_login, null, ['id' => 'filter_by_login', 'class' => 'xgrow-select', 'required' => 'required']) !!}
                                    <label for="filter_by_login" class="">Filtrar</label>
                                </div>
                            </div>

                            @include('elements.code-action-modal')

                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="xgrow-card-footer p-3 border-top">
                <button type="button" data-bs-toggle="modal" data-bs-target="#exportModal" class="xgrow-button"
                    v-on:click="sendCode">Exportar
                    .CSV
                </button>
            </div>
        </div>
    </div>
@endsection
