@push('after-styles')
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"/>
@endpush

@push('after-scripts')
    {{--    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>--}}
    <script src="//cdn.muicss.com/mui-0.10.3/js/mui.min.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        $('.xgrow-datepicker').datepicker({
            format: 'dd/mm/yyyy',
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#add-condition').click(function () {

                const $template = $('#additional-condition-template').html();
                const index = Math.floor(Math.random() * 999999999999) + 1;
                const html = $template.replace(/{%index%}/g, index)

                $('#dynamic-additional-conditions').append(html);
            });
        });
    </script>
@endpush

@include('elements.alert')
{{--<div class="row @if ($type=='edit' ) mt-3 @endif">--}}
{{--    <h5 class="my-2 mb-4">Público</h5>--}}
{{--</div>--}}

<div class="row">
    <div class="col-sm-12">
        <p class="xgrow-card-title my-2">Público</p>
    </div>

    <div class="col-sm-12 col-md-12">
        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
            {!! Form::text('name', $audience->name ?? null, ['required', 'id' => 'name']) !!}
            {!! Form::label('name', '*Nome') !!}
        </div>
    </div>
    <div class="col-sm-12 col-md-12">
        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
            {!! Form::text('description', $audience->description ?? null, ['required', 'id' => 'description']) !!}
            {!! Form::label('description', '*Descrição') !!}
        </div>
    </div>
</div>

<div class="col-sm-12">
    <p class="xgrow-card-title my-2">Condições</p>
</div>

@foreach ($conditions as $condition)
    @if ($loop->first)
        <div class="row condition-row">
            <div class="col-12 col-md-4">
                <div class="xgrow-form-control mb-2">
                    @include('audience.components.fields.field', ['current_value' => $condition->field])
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="xgrow-form-control mb-2">
                    @include('audience.components.operator', ['operator' => $condition->operator])
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="xgrow-form-control mb-2 value-condition-replacement">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        @include('audience.components.input-values.input-text', ['value' => $condition->value])
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row condition-row">
            <div class="col-md-2 mb-2">
                <div class="subs-input-type-person d-flex flex-column">
                    <p class="xgrow-medium-bold">Condição</p>
                    <div class="xgrow-btn-group btn-group" role="group" aria-label="Basic radio toggle button group">
                        @include('audience.components.logical-condition', [
                            'uniq' => $condition->id,
                            'value' => $condition->condition_type,
                        ])
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="xgrow-form-control mb-3">
                    @include('audience.components.fields.field', ['current_value' => $condition->field])
                </div>
            </div>

            <div class="col-md-3">
                <div class="xgrow-form-control mb-2">
                    @include('audience.components.operator', ['operator' => $condition->operator])
                </div>
            </div>

            <div class="col-md-3">
                <div class="xgrow-form-control mb-2 value-condition-replacement">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        @include('audience.components.input-values.input-text', ['value' => $condition->value])
                    </div>
                </div>
            </div>

            <div class="col-md-1 col-xl-1">
                <div class="xgrow-form-control">
                    <button type="button" onClick="$(this).closest('.row').remove();" class="xgrow-button table-action-button m-1">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif
@endforeach

<div id="dynamic-additional-conditions"></div>

<div class="row">
    <div class="sm-12 pt-3">
        <a id="add-condition"><i class="fa fa-plus"></i> Adicionar outra condição</a>
    </div>
</div>

<template id="additional-condition-template">
    <div class="row condition-row">
        <div class="col-md-2 mb-2">
            <div class="subs-input-type-person d-flex flex-column">
                <p class="xgrow-medium-bold">Condição</p>
                <div class="xgrow-btn-group btn-group" role="group" aria-label="Basic radio toggle button group">
                    @include('audience.components.logical-condition', [
                        'uniq' => '{%index%}',
                        'value' => 1,
                    ])
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="xgrow-form-control mb-3">
                @include('audience.components.fields.field')
            </div>
        </div>

        <div class="col-md-3">
            <div class="xgrow-form-control mb-2">
                @include('audience.components.operator')
            </div>
        </div>

        <div class="col-md-3">
            <div class="xgrow-form-control mb-2 value-condition-replacement">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    @include('audience.components.input-values.input-text')
                </div>
            </div>
        </div>

        <div class="col-md-1 col-xl-1">
            <div class="xgrow-form-control">
                <button type="button" onClick="$(this).closest('.row').remove();" class="xgrow-button table-action-button m-1">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
</template>
