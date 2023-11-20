<div class="tab-pane fade show" id="challengesNew" :class="{'active': activeScreen.toString() === 'challenges.new'}">

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="tab-pane-title pb-0">
                <h5 class="mb-3">
                    <template v-if="currentId === 0">Novo desafio</template>
                    <template v-else>Editar desafio</template>
                </h5>
            </div>
            <p class="xgrow-medium-regular" v-if="currentId === 0">
                Preencha os campos abaixo para adicionar um novo desafio.
            </p>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 pt-4 mb-3">
            <p><b>Sobre o desafio</b></p>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                {!! Form::text('title', null, ['id' => 'title', 'class' => 'xgrow-input mui--is-empty mui--is-untouched mui--is-pristine', 'v-model' => 'challenge.title']) !!}
                {!! Form::label('title', 'Título do desafio') !!}
            </div>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                <textarea id="description" rows="5" cols="54" v-model="challenge.message"
                    class="mui--is-not-empty mui--is-untouched mui--is-pristine xgrow-input-textarea">
                </textarea>
                <label for="description">Conteúdo do desafio</label>
            </div>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 pt-4 mb-3">
            <p><b>Incluir arquivo multimídia</b></p>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 pb-2">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                <select id="typeMultimedia" class="xgrow-select" v-model="challenge.multimediaType">
                    <option v-for="typeMultimedia in typesOfMultimedia" :value="typeMultimedia.id" :key="typeMultimedia.id">
                        [[ typeMultimedia.type ]]
                    </option>
                </select>
                <label for="typeMultimedia">Selecione uma opção</label>
                <span class="caret"></span>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label" v-show="challenge.multimediaType !== ''">
                {!! Form::text('multimediaUrl', null, ['id' => 'multimediaUrl', 'class' => 'xgrow-input mui--is-empty mui--is-untouched mui--is-pristine', 'v-model' => 'challenge.multimediaUrl']) !!}
                {!! Form::label('multimediaUrl', 'Link') !!}
            </div>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 pt-4 mb-3">
            <p><b>Outras opções</b></p>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 pb-2">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                <select id="answerType" class="xgrow-select" v-model="challenge.answerType">
                    <option v-for="typeAnswer in typesOfAnswer" :value="typeAnswer.id" :key="typeAnswer.id">
                        [[ typeAnswer.type ]]
                    </option>
                </select>
                <label for="answerType">Selecione uma opção</label>
                <span class="caret"></span>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="d-flex align-items-center my-2">
                <div class="form-check form-switch">
                    {!! Form::checkbox('showOnLogin', null, false, ['id' => 'showOnLogin', 'class' => 'form-check-input', 'v-model' => 'challenge.showOnLogin']) !!}
                    {!! Form::label('showOnLogin', 'Mostrar desafio ao logar', ['class' => 'form-check-label']) !!}
                </div>
            </div>
        </div>

        <div class="col-12 mb-3 row" v-if="challenge.answerType === 'singleOption'">
            <div class="col-lg-6 col-md-6 col-sm-12 pe-0" v-for="(option, idx) in challenge.optionsList">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    {!! Form::text(null, null, [':id' => '`option-${idx}`', 'class' => 'xgrow-input mui--is-untouched mui--is-pristine', 'v-model' => 'option.message']) !!}
                    {!! Form::label(null, 'Opção') !!}
                    <button class="ch-delete-button has-margin" @click.prevent="removeOptionFromList(idx)">
                        <i class="fas fa-trash-alt" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <button class="ch-add-button my-3" @click.prevent="addOptionInList">
                    <i class="fas fa-plus" aria-hidden="true"></i>
                    [[ challenge.optionsList.length === 0 ? 'Adicionar opção' : '' ]]
                </button>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 pt-2">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                {!! Form::number('order', null, ['id' => 'order', 'class' => 'xgrow-input mui--is-untouched mui--is-pristine', ':class' => '[ challenge.order ? "mui--is-not-empty" : "mui--is-empty" ]', 'v-model' => 'challenge.order']) !!}
                {!! Form::label('order', 'Ordem') !!}
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 pt-2">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                {!! Form::number('reward', null, ['id' => 'reward', 'class' => 'xgrow-input mui--is-untouched mui--is-pristine', ':class' => '[ challenge.reward ? "mui--is-not-empty" : "mui--is-empty" ]', 'v-model' => 'challenge.reward']) !!}
                {!! Form::label('reward', 'Desafio') !!}
            </div>
        </div>

        <div class="col-lg-4 col-md-12 col-sm-12"></div>

        <div class="col-lg-12 col-md-12 col-sm-12 mt-5 mb-3" v-if="currentId > 0">
            <button class="xgrow-button xgrow-button-custom" @click="void(0)">
                <i class="fas fa-plus-circle mr-2"></i> Adicionar outro desafio
            </button>
        </div>
    </div>

    <div class="row">
        <div class="d-flex py-4 px-0 justify-content-between flex-wrap gap-3 border-top border-secondary mt-4">
            <button class="btn xgrow-button-secondary button-cancel" @click="changePage('challenges.challenges')">
                Cancelar
            </button>
            <button class="xgrow-button xgrow-button-custom " @click="saveChallenge">
                Salvar desafio
            </button>
        </div>
    </div>
</div>
