<div class="tab-pane fade show pb-3" id="configurationsLevel"
     :class="{'active': activeScreen.toString() === 'configurations.level'}">

    <div class="tab-pane-grid">
        <div class="pane-header-grid border-bottom pb-2 mb-5">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="tab-pane-title flex-wrap">
                        <h5 class="m-0 p-0">Fases</h5>
                        <button class="xgrow-button" style="height:40px; width:128px" @click="openLevelModal">
                            <i class="fa fa-plus"></i> Nova Fase
                        </button>
                    </div>
                    <p class="xgrow-medium-regular mb-3">
                        Defina quais serão as fases da sua gamificação.
                    </p>
                </div>
            </div>
        </div>

        <div class="pane-body-grid mt-3">
            <div class="d-flex flex-wrap gap-4 justify-content- justify-content-center justify-content-md-start">
                <template v-for="lvl in levels" :key="lvl._id">
                    <xgrow-level-card-component
                        :id="lvl._id" :level="lvl.order" :name="lvl.name"
                        :score="lvl.requiredPoints" :color="lvl.color" :cover="lvl.iconUrl"
                        @edit="editLevel"
                        @remove="openLevelDeleteModal">
                    </xgrow-level-card-component>
                </template>
            </div>
        </div>
    </div>
</div>

<xgrow-modal-component :is-open="levelModal" @close="clearLevelFields">
    <template v-slot:title>
        [[ method == 'edit' ? 'Editar' : 'Nova' ]] fase: [[ resumeDetail(level.name, 16) ]]
    </template>
    <template v-slot:content>
        <div class="row">
            <div class="col-sm-12 col-md-8 col-lg-8">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    {!! Form::text('levelTitle', null, ['id' => 'levelTitle', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine', 'v-model' => 'level.name']) !!}
                    {!! Form::label('levelTitle', 'Nome da Fase') !!}
                </div>
            </div>
            <div class="col-sm-12 col-md-4 col-lg-4">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    {!! Form::number('order', null, ['id' => 'order', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine', 'v-model' => 'level.order']) !!}
                    {!! Form::label('order', 'Ordem') !!}
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-6">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    {!! Form::number('LevelScore', null, ['id' => 'LevelScore', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine', 'v-model' => 'level.requiredPoints']) !!}
                    {!! Form::label('LevelScore', 'Pontuação necessária') !!}
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-6">
                <div class="d-flex align-items-center gap-3 h-100">
                    <label for="LevelColor">Cor</label>
                    <input type="color" v-model="level.color">
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-12 mt-3">
                <upload-image ref="iconUrl" title="Ícone da fase"
                              subtitle="Esta é a imagem que irá representar a fase em questão." image-size="(1:1)"
                              img-aspect-ratio="1x1" refer="iconUrl" v-on:send-image="receiveImage"
                              :style="'width:128px;height:128px;border-radius:10px;'">
                </upload-image>
            </div>
        </div>
    </template>
    <template v-slot:footer="slotProps">
        <button type="button" class="btn btn-outline-light mr-2  xgrow-button-cancel" @click="slotProps.closeModal">
            Cancelar
        </button>
        <button type="button" class="btn btn-success" @click.prevent="saveLevel">
            <i class="fas fa-check mr-2"></i> Aplicar configurações
        </button>
    </template>
</xgrow-modal-component>

<xgrow-modal-component :is-open="deleteModal" @close="deleteModal=false">
    <template v-slot:content>
        <div class="row gap-3 text-center w-100" style="color:var(--gray1)">
            <i aria-hidden="true" class="fas custom-alert-symbol fa-question-circle fa-5x"></i>
            <h5 class="m-0 p-0" style="color:#FFFFFF"><b>Deseja realmente excluir este item?</b></h5>
            <span>Não há maneira de recuperar esse item após sua exclusão.</span>
        </div>
    </template>
    <template v-slot:footer="slotProps">
        <button type="button" class="btn btn-outline-light mr-2 xgrow-button-cancel" @click="slotProps.closeModal">
            Cancelar
        </button>
        <button type="button" class="btn btn-success" @click.prevent="removeLevel">
            <i class="fas fa-check mr-2"></i> Sim, remover
        </button>
    </template>
</xgrow-modal-component>
