<div class="tab-pane fade show" id="typePerson" :class="{'active': activeScreen === 'typePerson'}">

    <div class="row">
        <div class="col-sm-12 col-md-12 text-center">
            <p class="pb-2"><b>Para onde devemos enviar os seus saques?</b></p>
            <p>Selecione apenas 1 opção.</p>
        </div>

        <div class="col-sm-12 col-md-12">
            <div class="row xgrow-inner-card">
                <div class="col-sm-12 col-md-6 text-center">
                    <div class="xgrow-radio d-flex align-items-center my-2 gap-2 justify-content-center">
                        {!! Form::radio('user-type', 'J', null, ['id' => 'cnpj', 'v-model' => 'user.type']) !!}
                        {!! Form::label('cnpj', 'Quero receber na minha empresa (CNPJ).') !!}
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 text-center">
                    <div class="xgrow-radio d-flex align-items-center my-2 gap-2 justify-content-center">
                        {!! Form::radio('user-type', 'F', null, ['id' => 'cpf', 'v-model' => 'user.type']) !!}
                        {!! Form::label('cpf', 'Quero receber na minha conta de pessoa física (CPF).') !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="footer col-sm-12 border-top mt-3 py-3 d-flex justify-content-center">
            <button class="xgrow-button xgrow-button-custom" @click="nextStep('dataPerson')">
                Confirmar e prosseguir
            </button>
        </div>
    </div>

</div>
