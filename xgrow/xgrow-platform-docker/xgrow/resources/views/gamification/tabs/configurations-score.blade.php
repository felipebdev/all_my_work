@push('after-styles')
    <style>
        .multiselect.is-disabled {
            color: var(--contrast-gray);
            font: var(--text-medium-regular);
            border-color: var(--input-bg-disabled) !important;
            padding-left: 12px;
            height: 60px;
            background-color: var(--input-bg-disabled) !important;
        }
    </style>
@endpush

<div class="tab-pane fade show" id="configurationsScore"
    :class="{'active': activeScreen.toString() === 'configurations.score'}">
    <div class="tab-pane-grid">

        <div class="pane-header-grid border-bottom pb-2 mb-2">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="tab-pane-title pb-0">
                        <h5 class="mb-3">Pontuação</h5>
                    </div>
                    <p class="xgrow-medium-regular mb-3">
                        Defina a pontuação de cada ação de seus alunos.
                    </p>
                </div>
            </div>
        </div>

        <div class="pane-body-grid" style="overflow-x: auto">
            <xgrow-table-component :id="'content-table'">
                <template v-slot:header>
                    <th style="width:10%">Ativar</th>
                    <th style="width:30%">Nome da ação</th>
                    <th style="width:10%">Limitar</th>
                    <th style="width:16.66%">Período</th>
                    <th style="width:16.66%">Qtd. Repetições</th>
                    <th style="width:16.66%">Pontuação</th>
                </template>
                <template v-slot:body>
                    <tr v-for="(action, idx) in score.actions" :key="idx">
                        <td>
                            <div class="form-check form-switch">
                                {!! Form::checkbox(null, null, false, [':id' => '`isEnabled-${idx}`', 'class' => 'form-check-input', 'v-model' => 'action.isEnabled']) !!}
                            </div>
                        </td>
                        <td>
                            <p class="m-0">[[ action.actionName ]]</p>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                {!! Form::checkbox('', null, false, [':id' => '`isLimited-${idx}`', 'class' => 'form-check-input', 'v-model' => 'action.isLimited', ':disabled' => '!action.isEnabled']) !!}
                            </div>
                        </td>
                        <td>
                            <multiselect
                                v-model="action.limitType"
                                :options="[
                                    {
                                        value: 'hour',
                                        label: 'Hora'
                                    },
                                    {
                                        value: 'day',
                                        label: 'Dia'
                                    },
                                    {
                                        value: 'week',
                                        label: 'Semana'
                                    },
                                ]"
                                :can-clear="false"
                                :disabled="!action.isLimited"
                                style="min-width: 109px"
                            />
                        </td>
                        <td>
                            <div class="xgrow-floating-input mui-textfield mui-textfield-no-label">
                                {!! Form::number('', null, [':id' => '`limitQuantity-${idx}`', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine input-no-label', 'v-model' => 'action.limitQuantity', ':disabled' => '!action.isLimited']) !!}
                            </div>
                        </td>
                        <td>
                            <div class="xgrow-floating-input mui-textfield mui-textfield-no-label">
                                {!! Form::number('', null, [':id' => '`actionValue-${idx}`', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine input-no-label', 'v-model' => 'action.actionValue', ':disabled' => '!action.isEnabled']) !!}
                            </div>
                        </td>
                    </tr>
                </template>
            </xgrow-table-component>
        </div>

        <div class="pane-footer-grid border-top border-secondary mt-5">
            <div class="d-flex py-4 px-0 justify-content-end flex-wrap gap-3">
                <button class="xgrow-button xgrow-button-custom " @click.prevent="saveScore">
                    Salvar alterações
                </button>
            </div>
        </div>
    </div>
</div>
