@push('after-styles')
    <link href="{{asset('xgrow-vendor/plugins/summernote/summernote-lite.min.css')}}" rel="stylesheet">
    <link href="{{asset('xgrow-vendor/plugins/summernote/summernote-xgrow.css')}}" rel="stylesheet">
    <style>
        #image {
            max-width: 80% !important;
            max-height: 256px;
        }

        #thumb {
            max-width: 82px;
            max-height: 64px;
        }
    </style>

@endpush

@push('after-scripts')
    <script src="{{asset('xgrow-vendor/plugins/summernote/summernote-lite.min.js')}}"></script>
    <script src="{{asset('xgrow-vendor/plugins/summernote/lang/summernote-pt-BR.min.js')}}"></script>
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

@if ($topic->id == 0)
    <?php $topic->active = 1; ?>
    <?php $topic->tags = ''; ?>
    {!! Form::model($topic, ['method' => 'post', 'enctype' => 'multipart/form-data', 'route' => ['topic.store']]) !!}
@else
    {!! Form::model($topic, ['method' => 'post', 'enctype' => 'multipart/form-data', 'route' => ['topic.update', $topic->id]]) !!}
@endif
{{ csrf_field() }}

<div class="xgrow-card card-dark p-0 mt-4">

    @include('elements.alert')

    <div class="xgrow-card-header border-bottom">
        <div class="form-check form-switch mx-3">
            <span id="topic_exhibition_text">Tópico visível</span>
            {!! Form::checkbox('active', $topic->active, $topic->active, ['id' => 'chk-active-topic', 'class' => 'form-check-input']) !!}
            {!! Form::label('chk-active-topic', ' ') !!}
        </div>
    </div>

    <div class="xgrow-card-body px-3 mt-3">
        <div class="row">
            <div class="col-md-12">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    {!! Form::text('title', null, ['required']) !!}
                    {!! Form::label('title', '* Título do tópico') !!}
                </div>
            </div>

            <div class="col-lg-12 col-md-12 mb-3">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    {!! Form::textarea('description', null, ['id' => 'description', 'rows' => 7, 'cols' => 54, 'style' => 'resize:none']) !!}
                    <label for="description">Descreva detalhadamente o conteúdo do tópico...</label>
                </div>
            </div>

            <div class="col-md-6 py-3">
                <h5>* Imagem do Tópico</h5>
                <p class="xgrow-medium-italic">É a imagem localizada na parte lateral do tópico.</p>
                {!! UpImage::getImageTag($topic, 'image', 'image', 'img-fluid my-3') !!}<br>
                {!! UpImage::getUploadButton('image', 'btn btn-themecolor') !!}
            </div>

            <div class="col-sm-12 col-md-6 my-2">
                <div class="xgrow-form-control">
                    {!! Form::label('tags', 'Tags:', ['class' => 'my-2 ps-0']) !!}
                    <?php $tags = explode(';', $topic->tags); ?>
                    <select name="tags[]" id="tags" class="xgrow-select" multiple="multiple" style="width: 100%">
                        @if ($tags)
                            @foreach ($tags as $tag)
                                @if ($tag != '')
                                    <option selected="selected">{{ $tag }}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                    <small>Adicione a tag e pressione enter para confirmar.</small>
                </div>

                <p class="xgrow-card-title pt-3">Mais opções:</p>
                <div class="form-check form-switch my-1">
                    <span id="mod_exhibition_text">Moderação desligada</span>
                    {!! Form::checkbox('moderation', $topic->moderation, $topic->moderation, ['id' => 'chk-active-moderation', 'class' => 'form-check-input']) !!}
                    {!! Form::label('chk-active-moderation', ' ') !!}
                </div>
            </div>
        </div>
    </div>


    <div class="xgrow-card-footer p-3 border-top">
        {!! Form::submit('Salvar', ['class' => 'xgrow-button']) !!}
    </div>
</div>

@include('up_image.modal-xgrow')
{!! Form::close() !!}
