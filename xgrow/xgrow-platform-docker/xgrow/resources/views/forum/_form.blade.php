@push('after-styles')
    <style>
        #image {
            max-width: 100% !important;
            max-height: 256px;
        }

        #thumb {
            max-width: 82px;
            max-height: 64px;
        }

        .card-course {
            max-width: 330px !important;
        }
    </style>
@endpush

<div class="tab-pane fade show active" id="nav-forum" role="tabpanel" aria-labelledby="nav-forum-tab">

    @include('elements.alert')

    {!! Form::model($forum, ['method' => 'post', 'enctype' => 'multipart/form-data', 'route' => ['forum.store', 'id=' . $forum->id]]) !!}
    {{ csrf_field() }}

    <div class="xgrow-card card-dark p-0 mt-4">
        <div class="xgrow-card-header pb-3">
            <div class="form-check form-switch mx-3">
                <span id="chk-active-forum-label">Fórum ativado</span>
                {!! Form::checkbox('active', $forum->active, $forum->active, ['id' => 'chk-active-forum', 'class' => 'form-check-input']) !!}
                {!! Form::label('chk-active-forum', ' ') !!}
            </div>
        </div>
        <hr class="mt-0" style="border-color: var(--border-color)" />
        <div class="xgrow-card-body p-3">
            <div class="row">
                <div class="col-sm-12 col-md-8 mb-4">
                    <div class="row">
                        <div class="col-sm-12 col-md-12">
                            <h5>Imagem de cabeçalho do Fórum</h5>
                            <div>
                                <p class="xgrow-medium-italic">É a imagem localizada na parte superior do fórum.</p>
                                <p class="xgrow-medium-italic">Tamanho: 1600 x 250</p>
                            </div>
                            {!! UpImage::getImageTag($forum, 'image', 'image', 'img-fluid my-3') !!}<br>
                            {!! UpImage::getUploadButton('image', 'btn btn-themecolor') !!}
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-md-4 mb-4">
                    <div class="row">

                        <div class="col-sm-12 col-md-12">
                            <h5>Icone do fórum</h5>
                            <div class="xgrow-small-italic">
                                <p>O ícone aparece nos menus de navegação e as vezes, no título da seção.</p>
                                <p>Tamanho: 230 x 180</p>
                            </div>
                            {!! UpImage::getImageTag($forum, 'thumb', 'thumb', 'img-fluid my-3') !!}<br>
                            {!! UpImage::getUploadButton('thumb', 'col-lg-6 col-md-12', 'Upload', 'unsplash') !!}
                        </div>

                        <div class="col-sm-12 col-md-12 mt-4">
                            <p class="xgrow-card-title mb-3">Mais opções:</p>
                            <div class="form-check form-switch">
                                <span id="type_theme">Tema claro</span>
                                {!! Form::checkbox('theme', $forum->theme, $forum->theme, ['id' => 'chk-theme-forum', 'class' => 'form-check-input']) !!}
                                {!! Form::label('chk-theme-forum', ' ') !!}
                            </div>
                        </div>

                    </div>
                </div>
                @include('up_image.modal-xgrow')
            </div>
            <div class="xgrow-card-footer p-3 border-top">
                {!! Form::submit('Salvar', ['class' => 'xgrow-button']) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>
