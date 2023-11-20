<div class="xgrow-card card-dark p-0">

    <div class="xgrow-card-body p-3">

        <div class="xgrow-card-header">
            <p class="xgrow-card-title">{{ $permission->id == 0 ? 'Criar' : 'Editar' }} grupo</p>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    {!! Form::text('name', null, ['id' => 'name', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine']) !!}
                    {!! Form::label('name', 'Nome do grupo') !!}
                </div>
            </div>
        </div>

        <div class="row my-2 py-3">
            <div class="col-xl-12">
                <p class="xgrow-card-title mb-2">Atribuições</p>
                    @foreach ($categories as $category)
                        <h6>{{ $category->name }}</h6>
                        <div class="d-flex flex-row flex-wrap">
                                @foreach ($category->roles()->orderBy('order', 'ASC')->get() as $role)
                                <div class="mx-1 col-md-2">
                                    <div class="xgrow-check">
                                        {!! Form::checkbox('roles[]', $role->id, null, ['id' => 'role_' . $role->id]) !!}
                                        {!! Form::label('role_' . $role->id, $role->name) !!}
                                    </div>
                                </div>
                                @endforeach
                        </div>
                        <br>
                    @endforeach
            </div>
        </div>

        <div class="xgrow-card-footer p-3 border-top">
            {!! Form::submit('Salvar', ['class' => 'xgrow-button']) !!}
        </div>

    </div>
</div>
