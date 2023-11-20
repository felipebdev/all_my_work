<div class="xgrow-card card-dark p-0">

    <div class="xgrow-card-body p-3">

        <div class="row mx-1 my-4 border-bottom"></div>

        <div class="row my-4 py-3 align-items-start">
            <div class="col-xl-6 col-lg-6 col-md-12 mb-2 h-100">
                <div class="d-flex justify-content-between mb-3">
                    <p class="xgrow-card-title">Usuários sem atribuições</p>
                    <button id="move_all_user_0" type="button" onclick="moveAllUser(1)" class="xgrow-button"
                            style="width: 180px; height: 25px;">Permitir selecionados ⇨</button>
                </div>

                <div>
                    <ul class="draggable-list-group list-group" id="users_list_0">
                    </ul>
                </div>
            </div>

            <div class="col-xl-6 col-lg-6 col-md-12 h-100">
                <div class="d-flex justify-content-between mb-3">
                    <p class="xgrow-card-title">Usuários com atribuições</p>
                    <button id="move_all_user_1" type="button" onclick="moveAllUser(0)" class="xgrow-button"
                            style="width: 180px; height: 25px; background: #f13535;">Remover
                        selecionados ⇦</button>
                </div>

                <div>
                    <ul class="draggable-list-group list-group" id="users_list_1">
                    </ul>
                </div>
            </div>
        </div>

        <div class="xgrow-card-footer p-3 border-top">
            {!! Form::submit('Salvar', ['class' => 'xgrow-button']) !!}
        </div>

    </div>
</div>
