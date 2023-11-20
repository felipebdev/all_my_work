@include('audit/_form-filter-header')

@php
    $cols3 = 'col-xs-12 col-sm-12 col-md-4 col-lg-4';
    $cols4 = 'col-xs-12 col-sm-12 col-md-3 col-lg-3';

@endphp

@push('before-styles')

    <style>

	    .dtrg-group, .dtrg-group td
	    {
            text-align: left!important;
        }

	    .table-container
	    {
		    position: relative;
	    }

	    .table-loader
	    {
		    position: absolute;
		    top:0;
		    left: 0;
		    width: 100%;
		    height: 100%;
		    background: rgba(255, 255, 255, 0.8);
		    display: table;
		    z-index: 10;
	    }

	    .table-loader .spinner-container
	    {
		    display: table-cell;
		    vertical-align: middle;
		    margin: auto;
		    text-align: center;
	    }

        .table-content
        {
            padding:20px!important;
        }

        table, table.dataTable, .dataTable
        {
            width:100%!important;
        }

        .table td, .table th
        {
	        white-space: nowrap;
	        text-align: center;
        }

        :root
        {
            --student-color: #8fcafe;
            --client-color: #8fdf82;
            --payment-color: #f7e1b5;

            --student-color-opacity: rgba(143, 202, 254, 0.4);
            --client-color-opacity: rgba(143, 223, 130, 0.4);
            --payment-color-opacity: rgba(247, 225, 181, 0.4);

            --student-color-opacity-01: rgba(143, 202, 254, 0.02);
            --client-color-opacity-01: rgba(143, 223, 130, 0.02);
            --payment-color-opacity-01: rgba(247, 225, 181, 0.02);
        }

        .badge-sort
        {
            background-color: #ffbc34;
            color: black!important;
            position: absolute;
            right: 25px;
            top: 15px;
            border: 1px solid goldenrod;
        }

        fieldset
        {
            margin-bottom: 30px!important;
        }

        legend
        {
            width: auto!important;
            padding: 0 10px!important;
        }

        fieldset.student
        {
            border:3px solid var(--student-color)!important;
            padding: 20px;
        }

        fieldset.client
        {
            border:3px solid var(--client-color)!important;
            padding: 20px;
        }

        fieldset.payment
        {
            border:3px solid var(--payment-color)!important;
            padding: 20px;
        }

        thead .label, tfoot .label
        {
            line-height: 1.3;
            font-size: 100%!important;
            color:black;
        }

        th
        {
            position:relative;
        }

        thead .header-info th.student, tfoot .header-info th.student
        {
            background: var(--student-color)!important;
        }

        thead .header-info th.client, tfoot .header-info th.client
        {
            background: var(--client-color)!important;
        }

        thead .header-info th.payment, tfoot .header-info th.payment
        {
            background: var(--payment-color)!important;
        }

        thead .header-names th.student, tfoot .header-names th.student
        {
            background: var(--student-color-opacity)!important;
        }

        thead .header-names th.client, tfoot .header-names th.client
        {
            background: var(--client-color-opacity)!important;
        }

        thead .header-names th.payment, tfoot .header-names th.payment
        {
            background: var(--payment-color-opacity)!important;
        }

        tbody td.student
        {
            background: var(--student-color-opacity-01)!important;
        }

        tbody td.client
        {
            background: var(--client-color-opacity-01)!important;
        }

        tbody td.payment
        {
            background: var(--payment-color-opacity-01)!important;
        }

        span.name
        {
            color: teal;
            font-weight: 500;
        }

        span.status
        {
            border-radius: 5px;
            border:1px solid #dbdada;
            padding:5px;
            white-space: nowrap;
        }

        span.document
        {
            color: brown;
        }

        span.date
        {
            color: #0b67cd;
        }

        span.time
        {
            color: #0b97c4;
        }

        span.currency
        {
            white-space: nowrap;
        }

        span.currency-symbol
        {
            color: goldenrod;
        }

        span.currency-value
        {
            color: goldenrod;
            font-weight: 400;
            white-space: nowrap;
        }

        .input-daterange .input-group-addon
        {
            margin-top: -4px;
            border-left: none;
            border-right: none;
        }

        .group-absolute
        {
            cursor: pointer;
            pointer-events: none;
            position: absolute;
            top: 12px;
            right: 11px;
        }

        .group-absolute.datetime
        {
            top: 10px;
        }

        tbody td, tbody th
        {
            text-align: center!important;
            vertical-align: middle!important;
        }
        /*
        th.sorting_desc, th.sorting_asc
        {
            pointer-events: none!important;
            cursor: pointer!important;
        }
        */
        th.sorting_desc .label, th.sorting_asc .label
        {
            border: 2px solid crimson;
            padding: 2px 5px;
            border-radius: 5px;
            background: white;
            font-weight: 500;
            font-size: 120% !important;
        }

        .topbar
        {
            width: 100%;
            display: table;
            position: fixed!important;
            top: 0;
            left: 0;
        }

        .navbar-inner
        {
            width: 8vw;
            background-color:white;
        }
        .left-sidebar
        {
            padding-top: 44px!important;
        }

        .page-wrapper
        {
            padding-top: 120px!important;
        }

        #report-filters-submit
        {
            background: deeppink;
            border: none;
            transition: 0.5s;
        }

        #report-form, .report-filters-title
        {
            width: 100%!important;
            padding: 10px;
        }
        #report-table
        {
            text-align: center!important;
            width: 100% !important;
        }

        #report-table .dataTables_empty
        {
            font-size: 300%;
        }

        #report-table_wrapper .dt-buttons
        {
            margin-bottom: 20px;
        }

        #report-table_wrapper ul.pagination
        {
            margin: 2px 0 30px 0!important;
        }

        .container-fluid.full
        {
            max-width: 100%!important;
        }

        #report-filters-submit
        {
            border-radius: 10px!important;
            padding: 15px 0 !important;
            font-size: 25px!important;
        }

        .no-interaction
        {
            pointer-events: none!important;
            cursor:default!important;
            overflow: hidden!important;
        }

        .hidden
        {
            display: none!important;
        }

        .select2-fix
        {

        }

        .title-fix
        {
            padding-left: 20px;
        }

        .select2-container--default .select2-selection--multiple
        {
            border: 1px solid #ced4da!important;
            padding-top: 3px;
        }
        .select2-selection
        {
            padding: .375rem .75rem !important;
            padding: 0 .75rem .750rem .75rem !important;
        }
        .select2-container .select2-search--inline .select2-search__field
        {
            vertical-align: middle!important;
        }
        .select2-container
        {
            padding-top: 3px!important;
            width: 100%!important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow
        {
	        display: none!important;
        }
        /*
         * Loader
         */

        #report-loader
        {
            position: fixed;
            z-index: 99999 !important;
            background: rgba(0, 0, 0, 0.5);
            width: 100%;
            height: 100%;
            display: table;
            top: 0;
            left: 0;
        }

        #report-loader .loader-container
        {
            display: table-cell!important;
            vertical-align: middle!important;
        }

        .loader,
        .loader:after
        {
            border-radius: 50%;
            width: 10em;
            height: 10em;
        }
        .loader
        {
            margin: 60px auto;
            font-size: 10px;
            position: relative;
            text-indent: -9999em;
            border-top: 1.1em solid rgba(255, 255, 255, 0.2);
            border-right: 1.1em solid rgba(255, 255, 255, 0.2);
            border-bottom: 1.1em solid rgba(255, 255, 255, 0.2);
            border-left: 1.1em solid #ffffff;
            -webkit-transform: translateZ(0);
            -ms-transform: translateZ(0);
            transform: translateZ(0);
            -webkit-animation: load8 1.1s infinite linear;
            animation: load8 1.1s infinite linear;
        }
        @-webkit-keyframes load8 {
            0% {
                -webkit-transform: rotate(0deg);
                transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }
        @keyframes load8 {
            0% {
                -webkit-transform: rotate(0deg);
                transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }


    </style>

@endpush

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

@macro('select2', $label, $name, $data, $valueIdentifier = 'id', $labelIdentifier = 'name', $multiple = true, $placeholder = null)

<div class="form-group">

    @if($label)
        <label for="{{$name}}">{{$label}}</label>
    @endif

    <div class="input-group">

        <select class="form-control select-2 custom-select" id="{{$name}}" name="{{$name}}[]" {{$multiple ? 'multiple' : ''}}>

            @if($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif

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

<div class="row">

    <form id="report-form" class="form" novalidate>

        <fieldset class="student">

            <!-- row -->
            <div class="row">

                <div class="{{$cols4}}}">

                    {!! Html::select2('Cliente', 'input-clients', $clients, 'id', 'client_full_name') !!}

                </div>

                <div class="{{$cols4}}}">

                    {!! Html::input('Data inicial', 'input-date-start', 'fas fa-calendar-week', '', 'datetime') !!}

                </div>

                <div class="{{$cols4}}}">

                    {!! Html::input('Data final', 'input-date-end', 'fas fa-calendar-week', '', 'datetime') !!}

                </div>

                <div class="{{$cols4}}}">

                    {!! Html::select2('Tabela', 'input-tables', $dataBaseColumns, 'id', 'column', false, 'Selecione uma tabela') !!}

                </div>

            </div>
            <!-- /row -->

        </fieldset>

        <!-- submit -->
        <button id="report-filters-submit" type="submit" class="btn btn-rounded btn-primary btn-block">
            <i class="fa fa-search"></i> Filtrar
        </button>
        <!-- /submit -->

    </form>

</div>
