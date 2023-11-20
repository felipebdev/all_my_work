<div class="col-lg-12 col-md-12 col-sm-12">
    <div class="d-flex align-items-center mb-3">
        <div class="form-check form-switch">
            {!! Form::checkbox('chk-internal-area', null, null, [
                'id' => 'chk-internal-area',
                'class' => 'form-check-input',
                'v-model' => 'internalArea',
                '@change' => 'syncInternalArea',
            ]) !!}
            {!! Form::label('chk-internal-area', 'Utilizar área de aprendizado unificada Xgrow', [
                'class' => 'form-check-label',
            ]) !!}
        </div>
    </div>
</div>

<div class="col-lg-12 col-md-12 col-sm-12" v-if="internalArea">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 my-3">
            <p class="xgrow-medium-italic">
                Selecione quais os conteúdos abaixo serão entregues na área de aprendizado (são exibidos apenas
                conteúdos e seções publicados).
            </p>
        </div>
    </div>

    <div class="row">
        <!-- Lista de Cursos -->
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <h6>Cursos</h6>
                    <hr>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12" v-if="graphql.data.length > 0">
                    <ul class="p-0">
                        <li v-for="course in graphql.data" :key="course.id">
                            <div class="xgrow-check mb-2 d-flex align-items-center">
                                <input type="checkbox" name="course_ids[]" :id="'course_' + course.id"
                                    class="selected-course" :value="course.id" :checked="hasChecked(course.id, 'c')"
                                    @change="syncDelivery()" :data-id="course.id" :data-content="'c'">
                                <label :for="'course_' + course.id" class="mx-1">[[course.name]]</label>
                            </div>
                        </li>
                    </ul>
                </div>

                <div v-else>
                    <p>Não há cursos cadastrados.</p>
                </div>
            </div>
        </div>

        <!-- Lista de Seções -->
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <h6>Seções</h6>
                    <hr />
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12" v-if="graphql.sections.length > 0">
                    <ul class="p-0">
                        <li v-for="section in graphql.sections" :key="section.id">
                            <div class="xgrow-check mb-2 d-flex align-items-center">
                                <input type="checkbox" name="section_ids[]" :id="'section_' + section.id"
                                    class="selected-section" :value="section.id"
                                    :checked="hasChecked(section.id, 's')" @change="syncDelivery()"
                                    :data-id="section.id" :data-content="'s'" />
                                <label :for="'section_' + section.id" class="mx-1">
                                    [[section.title]]
                                </label>
                            </div>
                        </li>
                    </ul>
                </div>

                <div v-else>
                    <p>Não há seções cadastradas.</p>
                </div>
            </div>
        </div>

        <div class="row justify-content-end">
            <button class="xgrow-button" @click.prevent="saveDeliveries()">Salvar Entregas</button>
        </div>
    </div>

    <hr class="my-3">

    <div class="row">
        <div class="col-sm-12 col-md-6 col-lg-6">
            <div class="xgrow-form-control">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    {!! Form::text('subject_email', null, [
                        'id' => 'subject_email',
                        'autocomplete' => 'off',
                        'spellcheck' => 'false',
                        'class' => 'mui--is-empty mui--is-untouched mui--is-pristine',
                        'min' => 0,
                        'max' => 100,
                        'v-model' => 'email.subjectEmail',
                    ]) !!}
                    {!! Form::label('subject_email', 'Assunto') !!}
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-md-12 col-lg-12">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                {!! Form::textarea('message_email', null, [
                    'class' => '"w-100 mui--is-empty mui--is-pristine mui--is-touched',
                    'id' => 'message_email',
                    'rows' => 7,
                    'cols' => 54,
                    'maxlength' => 250,
                    'style' => 'resize:none; height: auto; min-height:200px',
                    'v-model' => 'email.messageEmail',
                    'disabled' => 'disabled',
                ]) !!}
                {{--                {!! Form::label('message_email', 'Descreva detalhadamente aqui a sua mensagem.') !!} --}}
            </div>
            <ul class="px-0 xgrow-medium-italic">
                <li class="my-2">
                    <span style="color: var(--contrast-green3)">Essa mensagem será enviada ao aluno com os respectivos
                        dados de acesso.</span>
                </li>
            </ul>
        </div>
    </div>
</div>
