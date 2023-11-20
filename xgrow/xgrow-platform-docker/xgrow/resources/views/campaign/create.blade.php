@extends('templates.xgrow.main')
@inject('model', 'App\Campaign')

@push('after-styles')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="{{ asset('xgrow-vendor/assets/css/pages/campaign_add.css') }}">
<link href="{{asset('xgrow-vendor/plugins/summernote/summernote-lite.min.css')}}" rel="stylesheet">
<link href="{{asset('xgrow-vendor/plugins/summernote/summernote-xgrow.css')}}" rel="stylesheet">
@endpush

@push('jquery')
    <script type="text/javascript">
        $(function () {
            $('.xgrow-datepicker').datepicker({
                format: 'dd/mm/yyyy',
                startDate: new Date()
            });

            //$('input[name=has_start]').click(() => updateHasStart());

            //$('input[name=has_finish]').click(() => updateHasFinish());

            $('input[name=type]').click(() => updateType());

            $('input[name=format]').click(() => updateFormat());

            $('select[name=automatic_type]').change(() => {
                updateAutomaticType()
                updateAutomaticIds()
            });

            //updateHasStart()
            //updateHasFinish()
            updateType()
            updateFormat()
            updateAutomaticType()
            updateAutomaticIds({{ (old('automatic_id')) ?  old('automatic_id') :$campaign->automatic_id  }})
        })

        function updateHasStart() {
            const has_start = $('#has_start').prop('checked')
            const div = $('#div_has_start')
            if (has_start) {
                div.removeClass('d-none');
            } else {
                div.addClass('d-none');
            }
        }

        function updateHasFinish() {
            const has_finish = $('#has_finish').prop('checked')
            const div = $('#div_has_finish')
            if (has_finish) {
                div.removeClass('d-none');
            } else {
                div.addClass('d-none');
            }
        }

        function updateType() {
            $('#div_types > div').addClass('d-none');
            const div = "#div_type_" + $('input[name=type]:checked').val()
            $(div).removeClass('d-none');
        }

        function updateFormat() {
            const input_format = $('input[name=format]:checked')
            const audio = input_format.data('audio')
            const msg_type = input_format.data('msg_type')
            const reply_to = input_format.data('reply_to')
            const subject = input_format.data('subject')

            $('#div_audio, #div_msg_type_text, #div_msg_type_html, #div_reply_to, #div_subject').addClass('d-none');

            if (audio == 1) {
                $('#div_audio').removeClass('d-none');
            }

            if (msg_type == 1) {
                $('#div_msg_type_text').removeClass('d-none');
            }

            if (msg_type == 2) {
                $('#div_msg_type_html').removeClass('d-none');
            }

            if (reply_to == 1) {
                $('#div_reply_to').removeClass('d-none');
            }

            if (subject == 1) {
                $('#div_subject').removeClass('d-none');
            }
        }

        function updateAutomaticType() {
            $('select[name=automatic_id]').empty()
            const automatic_type = $('select[name=automatic_type]').val()
            div = $('#div_automatic_id')
            if (automatic_type > 1) {
                $(div).removeClass('d-none');
            } else {
                $(div).addClass('d-none');
            }
        }

        function updateAutomaticIds(automatic_id = 0) {
            const type = $('select[name=automatic_type]').val()
            const select = $('select[name=automatic_id]')
            select.append("<option value='0'>Carregando ...</option>")
            if (type && type > 1) {
                $.ajax({
                    url: "/campaign/get-automatic-ids/" + type,
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        select.empty().append("<option value=''>Selecionar</option>")
                        res.data.forEach(content => {
                            select.append(`<option value='${content.id}' ${(automatic_id == content.id) ? `selected` : ``}>${content.name}</option>`)
                        })
                    }
                });
            }
        }
    </script>
@endpush

@push('after-scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="{{asset('xgrow-vendor/plugins/summernote/summernote-lite.min.js')}}"></script>
    <script src="{{asset('xgrow-vendor/plugins/summernote/lang/summernote-pt-BR.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        $('.summernote').summernote({
            height: 300,
            minHeight: null,
            maxHeight: null,
            focus: false,
            lang: 'pt-BR',
            placeholder: "Descreva detalhadamente aqui o conteúdo...",
        });
    </script>
@endpush

@section('content')
<nav class="xgrow-breadcrumb mt-3" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Engajamento</li>
        <li class="breadcrumb-item"><a href="/campaign">Campanhas</a></li>
        <li class="breadcrumb-item active mx-2"><span>{{ $campaign->id == 0 ? 'Nova' : 'Editar' }} campanha</span></li>
    </ol>
</nav>

<div class="xgrow-card card-dark mt-2">
    <div class="xgrow-card-header">
        <p class="xgrow-card-title">{{ $campaign->id == 0 ? 'Nova' : 'Editar' }} campanha</p>
    </div>

    {!! Form::model($campaign, $params_route) !!}

    @include('elements.alert')
    <div class="xgrow-card-body py-1">
        <div class="row">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                {!! Form::text('name', null, ['spellcheck' => 'false', 'autocomplete' => 'off', 'id' => 'name', 'required' => 'required']) !!}
                {!! Form::label('name', '*Nome da campanha') !!}
            </div>
        </div>
         <div class="row">
            <div class="col col-md-4">
                <h5 class="mb-3 mt-3">Tipo</h5>
                <div class="d-flex justify-content-between">
                    @foreach ($model::listTypes() as $key => $value)
                        <div class="xgrow-radio d-flex align-items-center my-2">
                            {!! Form::radio('type', $key, null, ['id' => 'type_' . $key]) !!}
                            {!! Form::label('type_' . $key, $value, ['class' => 'mx-2']) !!}
                        </div>
                    @endforeach
                </div>
            </div>
          </div>
          <div class="row" id="div_types">

              <div id="div_type_{{ $model::TYPE_SCHEDULED }}" class="mt-1 d-none">
                    <div class="row">

                        <div class="col-12 col-lg-3">
                            <div class="form-check form-switch">
                                {!! Form::label('has_start', 'Lançamento:', ['class' => 'form-check-label']) !!}
                            </div>
                        </div>
                        <div class="col-12 col-lg-9"  id="div_has_start">
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="xgrow-floating-input xgrow-form-control mui-textfield mui-textfield--float-label">
                                        {!! Form::text('start_date', old('start_date', $campaign->start_date ?? null), [
                                            'class' => 'custom-datepicker xgrow-datepicker',
                                            'data-provide' => 'datepicker',
                                            'autocomplete' => 'off',
                                        ]) !!}
                                        {!! Form::label('start_date', 'Data de início') !!}
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="xgrow-floating-input xgrow-form-control mui-textfield ">
                                        {!! Form::time('start_time', null, ['class' => '']) !!}
                                        {!! Form::label('start_time', 'Hora de início') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        /*
                        <div class="col col-md-6 d-none">
                            <div class="row">
                                <div class="col col-md-4">
                                    <div class="form-check form-switch">
                                        {!! Form::checkbox('has_finish', null, null, ['id' => 'has_finish', 'class' => 'form-check-input']) !!}
                                        {!! Form::label('has_finish', 'Término', ['class' => 'form-check-label']) !!}
                                    </div>
                                </div>
                                <div class="col col-md-8 d-none"  id="div_has_finish" >
                                    <div class="row">
                                        <div class="col col-md-6">
                                            <div class="xgrow-floating-input xgrow-form-control mui-textfield mui-textfield--float-label d-flex justify-content-between">
                                                {!! Form::date('finish_date', null, [
                                                'class' => ''
                                                ]) !!}
                                            </div>
                                        </div>
                                        <div class="col col-md-6">
                                            <div class="xgrow-floating-input xgrow-form-control mui-textfield mui-textfield--float-label d-flex justify-content-between">
                                                {!! Form::time('finish_time', null, [
                                                'class' => ''
                                                ]) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        */
                        ?>

                    </div>
              </div>

              <div id="div_type_{{ $model::TYPE_AUTOMATIC }}" class="mt-2 d-none">
                  <div class="row">
                      <div class="col-12 col-lg-6">
                          <div class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label"
                               style="margin-bottom: 16px">
                              {!! Form::select('automatic_type', ['' => ''] + $model::listAutomaticTypes(), null, ['class' => 'xgrow-select']) !!}
                              {!! Form::label('automatic_type', '* Selecione o tipo de interação') !!}
                          </div>
                      </div>
                      <div class="col-12 col-lg-6" id="div_automatic_id">
                          <div class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label"
                               style="margin-bottom: 16px">
                              {!! Form::select('automatic_id', $automatic_ids, null, ['class' => 'xgrow-select']) !!}
                              {!! Form::label('automatic_id', '* Especificar item') !!}
                          </div>
                      </div>
                  </div>
              </div>
          </div>

          <div class="row">
                <div class="col-md-6">
                      <h5 class="mb-3 mt-3">Formato</h5>
                      <div class="d-flex justify-content-between">
                        @foreach ($model::listFormats() as $key => $format)
                            @if($format['active'] == 1)
                                <div class="xgrow-radio d-flex align-items-center my-2">
                                    {!! Form::radio('format', $key, null, ['id' => 'format_' . $key, 'data-reply_to' => $format['reply_to'], 'data-audio' => $format['audio'], 'data-subject' => $format['subject'], 'data-msg_type' => $format['msg_type']]) !!}
                                    {!! Form::label('format_' . $key, $format['name'], ['class' => 'mx-2']) !!}
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
          </div>

          <div id="div_audio" class="row mt-3 d-none">
                        {!! Form::label('upload_audio', 'Upload do áudio (.mp3)') !!}
                        {!! Form::file('upload_audio', ['accept' => '.mp3']) !!}
                        <br>
                        @if ($campaign->audio_id != 0)
                            <span class="mt-2">
                                <small>Atual {{ $campaign->audio->original_name }}</small>
                            <span>
                        @endif
          </div>

          <div id="div_subject" class="row mt-3 d-none">

                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                     {!! Form::text('subject', null, ['spellcheck' => 'false', 'autocomplete' => 'off']) !!}
                     {!! Form::label('subject', '*Assunto:') !!}
                </div>

          </div>

        <div id="div_reply_to" class="row d-none mt-3">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                {!! Form::text('replyto', null, ['spellcheck' => 'false', 'autocomplete' => 'off', 'id' => 'replyto']) !!}
                {!! Form::label('replyto', 'Responder para') !!}
            </div>
        </div>

        <div id="div_msg_type_text" class="row mt-3 d-none">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mt-2">
                        {!! Form::textarea('msg_type_text', null, ['id' => 'msg_type_text', 'style' => 'height: 150px', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine']) !!}
                        {!! Form::label('msg_type_text', 'Mensagem') !!}
                </div>
          </div>

        <div id="div_msg_type_html" class="row mt-3 d-none">
          <div class="col-lg-12 col-md-12 mb-3">
                {!! Form::textarea('msg_type_html', null, ['id' => 'msg_type_html', 'class' => 'summernote', 'rows' => 7, 'cols' => 54, 'style' => 'resize:none; height: 150px']) !!}
          </div>
        </div>

        <div class="row mt-3">
            <div class="col col-md-12">
                <div class="row">
                    <h5 class="mb-3 mt-3">Público(s) da campanha</h5>
                    @foreach ($audiences as $audience)
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 my-2">
                            <div class="xgrow-check">
                                {!! Form::checkbox('audiences[]', $audience->id, null, ['id' => 'audience' . $audience->id, 'class' => 'form-check-input']) !!}
                                {!! Form::label('audience' . $audience->id, $audience->name, ['class' => 'form-check-label']) !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
    <div class="xgrow-card-footer border-top mt-2">
        {!! Form::submit('Salvar Alterações',['class'=>'xgrow-button']) !!}
    </div>

</div>
@endsection
