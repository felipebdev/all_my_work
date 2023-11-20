<div class="tab-pane fade show" id="configurationsConfig"
    :class="{'active': activeScreen.toString() === 'configurations.config'}">

    <div class="tab-pane-grid">
        <div class="pane-header-grid border-bottom pb-2 mb-3">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="settings__title">
                        <h5 class="tab-pane-title mb-4">Configurações</h5>
                        <div class="form-check form-switch">
                            {!! Form::checkbox('isEnabled', null, false, ['id' => 'isEnabled', 'class' => 'form-check-input', 'v-model' => 'configuration.isEnabled']) !!}
                            {!! Form::label('isEnabled', 'Habilitar gamificação', ['class' => 'form-check-label']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pane-body-grid">
            <div class="row mb-3">
                <div class="col-lg-12 col-md-12 col-sm-12 py-3">
                    <p><b>Visualização do ranking</b></p>
                </div>
                <div class="settings__items">
                    <div class="form-check form-switch">
                        {!! Form::checkbox('showBestPlayersRanking', null, false, ['id' => 'showBestPlayersRanking', 'class' => 'form-check-input', 'v-model' => 'configuration.showBestPlayersRanking']) !!}
                        {!! Form::label('showBestPlayersRanking', 'Mostrar o ranking de melhores alunos', ['class' => 'form-check-label']) !!}
                    </div>
                    <div class="form-check form-switch">
                        {!! Form::checkbox('showWorsePlayersRanking', null, false, ['id' => 'showWorsePlayersRanking', 'class' => 'form-check-input', 'v-model' => 'configuration.showWorsePlayersRanking']) !!}
                        {!! Form::label('showWorsePlayersRanking', 'Mostrar o ranking de alunos sem engajamento', ['class' => 'form-check-label']) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 py-3">
                    <p><b>Outras opções</b></p>
                </div>
                <div class="settings__items">
                    <div class="form-check form-switch">
                        {!! Form::checkbox('showPoints', null, false, ['id' => 'showPoints', 'class' => 'form-check-input', 'v-model' => 'configuration.showPoints']) !!}
                        {!! Form::label('showPoints', 'Mostrar pontos', ['class' => 'form-check-label']) !!}
                    </div>
                    <div class="form-check form-switch">
                        {!! Form::checkbox('showPhases', null, false, ['id' => 'showPhases', 'class' => 'form-check-input', 'v-model' => 'configuration.showPhases']) !!}
                        {!! Form::label('showPhases', 'Mostrar fases', ['class' => 'form-check-label']) !!}
                    </div>
                    <div class="form-check form-switch">
                        {!! Form::checkbox('showChallengesReward', null, false, ['id' => 'showChallengesReward', 'class' => 'form-check-input', 'v-model' => 'configuration.showChallengesReward']) !!}
                        {!! Form::label('showChallengesReward', 'Mostrar pontos por atividades', ['class' => 'form-check-label']) !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="pane-footer-grid border-top border-secondary mt-5">
            <div class="d-flex py-4 px-0 justify-content-end flex-wrap gap-3">
                <button class="xgrow-button xgrow-button-custom " @click="saveSettings">
                    Salvar alterações
                </button>
            </div>
        </div>
    </div>
</div>
