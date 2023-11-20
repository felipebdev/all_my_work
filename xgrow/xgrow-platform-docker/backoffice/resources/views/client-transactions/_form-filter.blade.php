@include('client-transactions/_form-filter-header')
@push('before-styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('/css/style.css') }}">
@endpush
@php
    $cols3 = 'col-xs-12 col-sm-12 col-md-4 col-lg-4';
    $cols4 = 'col-xs-12 col-sm-12 col-md-3 col-lg-3';

@endphp

@macro('input', $label, $name, $icon = '', $value = '', $type = 'text', $classes = '')

@if($type === 'datetime')
    @php
    $classes .= 'datepicker';
    @endphp
@endif

<div class="form-group">
    @if($label)
    <label for="{{$name}}">{{$label}}</label>
    @endif
    <div class="input-group w-100">
        @if($icon)
        <div class="input-group-prepend">
            <span class="input-group-text"><i class="{{$icon}}"></i></span>
        </div>
        @endif
        <input type="{{$type}}" class="form-control shadow-sm {{$classes}}" id="{{$name}}" name="{{$name}}" value="{{$value}}" />
    </div>

</div>

@endmacro

@macro('select2', $label, $name, $data, $valueIdentifier = 'id', $labelIdentifier = 'name')

<div class="form-group">

    @if($label)
        <label for="{{$name}}">{{$label}}</label>
    @endif

    <div class="input-group">

        <select class="form-control select-2 custom-select" id="{{$name}}" name="{{$name}}[]" multiple>

            @if(is_array($data))
            @foreach ($data as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
            @endforeach
            @else
                @foreach ($data as $element)
                    <option value="{{ $element->{$valueIdentifier} }}">{{ $element->{$labelIdentifier} }}</option>
                @endforeach
            @endif

        </select>

        <div class="input-group-append group-absolute">
            <i class="fas fa-arrow-down"></i>
        </div>

    </div>

</div>

@endmacro

<div class="row">

    <div class="col-xs-12">

        <h1>Filtros:</h1>

    </div>

</div>
<!--
<div class="row">

    <div class="col">

        <button id="export-button" class="btn btn-default btn-block btn-primary">exportar</button>

    </div>

</div>
-->
<div class="row">

    <form id="report-form" class="form">

        <fieldset class="student">

            <legend>Infomações sobre o aluno</legend>

            <!-- row -->
            <div class="row">

                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">

                    {!! Html::input('Nome', 'student-name', 'fas fa-user') !!}

                </div>

                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">

                    {!! Html::input('Email', 'student-email', 'fas fa-at') !!}

                </div>

                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">

                    {!! Html::input('CPF ou CNPJ', 'student-document-number', 'fas fa-id-card-alt') !!}

                </div>

                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">

                    {!! Html::input('04 últimos dígitos do cartão', 'student-card-number', 'fas fa-credit-card') !!}

                </div>

                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">

                    {!! Html::input('Último login', 'student-last-login', 'fas fa-calendar-week', '', 'datetime') !!}

                </div>

            </div>
            <!-- /row -->

        </fieldset>

        <fieldset class="client">

            <legend>Informações sobre o cliente</legend>

            <!-- row -->
            <div class="row">

                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">

                    {!! Html::input('CPF', 'client-cpf', 'fas fa-id-card-alt') !!}

                </div>

                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">

                    {!! Html::input('CNPJ', 'client-cnpj', 'fas fa-id-card-alt') !!}

                </div>

                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">

                    {!! Html::select2('Clientes', 'clients-names', $clients, 'id', 'client_full_name') !!}

                </div>

                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">

                    {!! Html::select2('Plataforma', 'client-platform', $platforms, 'id', 'name') !!}

                </div>

                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-2">

                    {!! Html::select2('Produtos', 'client-product', $plans, 'id', 'name') !!}

                </div>

            </div>
            <!-- /row -->

        </fieldset>

        <fieldset class="payment">

            <legend>Informações sobre pagamento</legend>

            <!-- row -->
            <div class="row">

                <div class="{{$cols3}}}">

                    {!! Html::select2('Status', 'payment-status', $status, 'id', 'name') !!}

                </div>

                <div class="{{$cols3}}}">

                    {!! Html::input('Data', 'payment-last-date', 'fas fa-calendar-week', '', 'datetime') !!}

                </div>

                <div class="{{$cols3}}}">

                    {!! Html::input('Valor', 'payment-last-value', 'far fa-money-bill-alt') !!}

                </div>

            </div>

        </fieldset>

        <!-- submit -->
        <button id="report-filters-submit" type="submit" class="btn btn-rounded btn-primary btn-block">
            <i class="fa fa-search"></i> Filtrar
        </button>
        <!-- /submit -->

    </form>

</div>
