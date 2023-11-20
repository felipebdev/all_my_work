@extends('templates.xgrow.main')

@push('before-styles')
    <style>
        .custom-link {
            color: #92bc1d;
        }

        .custom-link:hover {
            color: #ffffff;
        }
    </style>
@endpush

@push('before-scripts')
@endpush

@push('after-scripts')
@endpush

@section('content')
    <div class="d-flex my-3 mb-0">
        <nav class="xgrow-breadcrumb" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Início</a></li>
                <li class="breadcrumb-item"><a href="/subscribers">Alunos</a></li>
                <li class="breadcrumb-item active" aria-current="page"><span>Importar</span></li>
            </ol>
        </nav>
    </div>
    <div class="xgrow-card card-dark p-0">
        {!! Form::model($subscriber, ['method' => 'post', 'enctype' => 'multipart/form-data', 'route' => ['subscribers.import']]) !!}
        <div class="xgrow-card-body p-3">
            @include('elements.alert')
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-6 mt-3 mb-3">
                    <div class="row">
                        <div class="col-sm-12">
                            <h4 style="color: var(--contrast-green);">Informações para importação dos dados</h4>
                            <p class="mt-1">Só é permitido o envio de arquivos csv.</p>
                            <p>* Os campos obrigatórios são Nome e E-mail (nessa ordem).</p>
                            <p>- Verifique qual é o delimitador de dados de seu csv antes de enviar.</p>
                            <p>- Baixe o arquivo de exemplo: <a href="{{url('docs/exemplo_importacao_aluno.csv')}}"
                                                                class="custom-link">baixar arquivo</a></p>
                            <p style="color:#92bc1d"><b>- A primeira linha é utilizada para o nome das colunas e não
                                    será importada para o sistema</b></p>
                        </div>
                        <div class="col-sm-12 mt-3">
                            <div class="xgrow-form-control">
                                <label for="formFile" class="px-0 mb-1">Selecione um arquivo .csv</label>
                                <input class="xgrow-form-control" type="file" id="formFile" name="file"
                                       accept="text/csv" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-md-12 col-lg-6 mb-3">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12 mb-4">
                            <p class="mt-3 mb-2">Selecione as opções de importação:</p>
                        </div>

                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div
                                class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label">
                                <select class="xgrow-select" name="status" id="status" required>
                                    <option value="" selected hidden></option>
                                    <option value="active">Ativo</option>
                                    <option value="canceled">Cancelado</option>
                                </select>
                                {!! Form::label('status', 'Status') !!}
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 my-3">
                            <div
                                class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label">
                                <select class="xgrow-select" name="plan_id" id="plan_id" required>
                                    <option value="" selected hidden></option>
                                    @foreach ($plans as $key => $value)--}}
                                    <option value="{{ $value->id }}"
                                        {{ isset($subscriber->plan_id) && $subscriber->plan_id == $value->id ? 'selected' : '' }}>
                                        {{ $value->name }}
                                    </option>
                                    @endforeach
                                </select>
                                {!! Form::label('plan_id', 'Produto') !!}
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div
                                class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label">
                                <select class="xgrow-select" name="delimiter" id="delimitador" required>
                                    <option value="" selected hidden></option>
                                    <option value=";">Ponto e vírgula (;)</option>
                                    <option value=",">Somente vírgula (,)</option>
                                </select>
                                {!! Form::label('delimiter', 'Delimitador') !!}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="xgrow-card-footer p-3 border-top">
            <button class="xgrow-button">Importar .CSV</button>
        </div>
        {!! Form::close() !!}
    </div>
@endsection
