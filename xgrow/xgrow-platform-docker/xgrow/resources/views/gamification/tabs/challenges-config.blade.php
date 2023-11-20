<div class="tab-pane fade show" id="challengesConfig"
    :class="{'active': activeScreen.toString() === 'challenges.config'}">
    <div class="row">

        <div class="col-lg-12 col-md-12 col-sm-12 border-bottom pb-2">
            <div class="d-flex align-items-center my-2">
                <div class="form-check form-switch">
                    {!! Form::checkbox('enableChallenges', null, false, ['id' => 'enableChallenges', 'class' => 'form-check-input', 'v-model' => 'configuration.enableChallenges']) !!}
                    {!! Form::label('enableChallenges', 'Habilitar desafios', ['class' => 'form-check-label']) !!}
                </div>
            </div>
        </div>

        <div class="row" v-if="configuration.enableChallenges === true">
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                <p class="pt-3 pb-2"><b>Forma de entrega</b></p>

                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mt-2">
                    <select id="formDelivery" class="xgrow-select" v-model="configuration.formDelivery">
                        <option v-for="formDelivery in typesOfFormDelivery" :value="formDelivery.id" :key="formDelivery.id">
                            [[ formDelivery.type ]]
                        </option>
                    </select>
                    <label for="formDelivery">Selecione a forma de entrega</label>
                    <span class="caret"></span>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                <p class="pt-3 pb-3"><b>Liberar entrega de desafio em</b></p>

                <xgrow-daterange-component
                    class="w-100"
                    v-model:value="configuration.startFrom"
                    format="DD/MM/YYYY"
                    :clearable="false" type="date"
                    placeholder="Data da liberação" />
            </div>

            <div class="col-lg-12 col-md-12 col-sm-12 pb-2" v-if="configuration.formDelivery === 'programmed'">
                <div class="row">

                    <div class="col-lg-12 col-md-12 col-sm-12 pt-2 pb-3">
                        <p><b>Repete a cada</b></p>
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            <input id="deliveryFrequency" type="number" spellcheck="false"
                                   v-model="configuration.deliveryFrequency"
                                   class="xgrow-input mui--is-empty mui--is-untouched mui--is-pristine mui--is-not-empty">
                            <label for="deliveryFrequency">Defina um intervalo</label>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-12 pb-2">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            <select id="frequencyFormat" class="xgrow-select"
                                    v-model="configuration.frequencyFormat">
                                <option v-for="frequencyFormat in typesOfFrequencyFormat"
                                        :value="frequencyFormat.id" :key="frequencyFormat.id">
                                    [[ frequencyFormat.type ]]
                                </option>
                            </select>
                            <label for="frequencyFormat">Selecione uma opção</label>
                            <span class="caret"></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="border-top border-secondary mt-5">
            <div class="d-flex py-4 px-0 justify-content-end flex-wrap gap-3">
                <button class="xgrow-button xgrow-button-custom " @click.prevent="saveChallengeSettings()">
                    Salvar alterações
                </button>
            </div>
        </div>

    </div>
</div>
