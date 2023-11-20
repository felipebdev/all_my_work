@push('jquery')
    <script type="text/javascript">
        $(function () {
            checkSelected();
            //$("input[name='audiences[]']").click(() => checkSelected())

            $("#allaudience").click( () => checkSelected());

        });


        function checkSelected(){
             if($('#allaudience').prop('checked'))
                $('#audiences').addClass('d-none')
            else
                $('#audiences').removeClass('d-none')
        }


    </script>
@endpush

@include('elements.alert')

{!! Form::model($attendant, $params_route) !!}


<div class="xgrow-card-header pb-3 mb-3">
    <div class="d-flex align-items-center px-3">
        <div class="form-check form-switch">
            {!! Form::checkbox('active', null, null, ['id' => 'active', 'class' => 'form-check-input']) !!}
            {!! Form::label('active', 'Ativar atendente', ['class' => 'form-check-label']) !!}
        </div>
    </div>
</div>

<hr class="mt-0" style="border-color: var(--border-color)"/>
<div class="xgrow-card-body p-3">
    <h5 class="xgrow-card-title my-3" style="font-size: 1.5rem; line-height: inherit">
        Dados de acesso
    </h5>
    <div class="row">
        <div class="col-lg-6 col-md-12">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                {!! Form::text('name', null, ['id' => 'name', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine', 'required']) !!}
                {!! Form::label('name', 'Nome') !!}
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                {!! Form::email('email', null, ['id' => 'email', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine', 'required']) !!}
                {!! Form::label('email', 'E-mail') !!}
            </div>
        </div>
        <small class="mb-3"><i class="fas fa-info-circle" style="color: var(--green1)"></i> A senha será enviada por e-mail ao atendente a cada acesso.</small>
    </div>
</div>
<hr style="border-color: var(--border-color)"/>
<div class="xgrow-card-body p-3">
    <h5 class="xgrow-card-title my-3" style="font-size: 1.5rem; line-height: inherit">
        Públicos
    </h5>
    <div class="row">
        <div class="col-12 mb-3">
            <div class="d-flex align-items-center">
                <div class="form-check form-switch">
                    {!! Form::checkbox('allaudience', null, null, ['id' => 'allaudience', 'class' => 'form-check-input']) !!}
                    {!! Form::label('allaudience', 'Atribuir todos os públicos', ['class' => 'form-check-label']) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col col-md-12">
                <div class="row d-none" id="audiences">
                    @foreach ($audiences as $audience)
                        @if ($audience->callcenter_active !== false)    
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 my-2">
                                <div class="xgrow-check">
                                    {!! Form::checkbox('audiences[]', $audience->id, null, ['id' => 'audience' . $audience->id, 'class' => 'form-check-input']) !!}
                                    {!! Form::label('audience' . $audience->id, $audience->name, ['class' => 'form-check-label']) !!}
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
<hr style="border-color: var(--border-color)"/>
<div class="xgrow-card-footer p-3 mt-4">
    {!! Form::submit('Salvar atendente',['class'=>'xgrow-button']) !!}
</div>
{!! Form::close() !!}