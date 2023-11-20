@push('after-styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet"/>
@endpush

@push('after-scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/i18n/pt-BR.min.js"></script>
    <script>
        $('#removeImage').on('click', function () {
            $('#image').attr('src', '/xgrow-vendor/assets/img/big-file.png');
            $('#image_upimage_file_id').val(0);
            $('#image_upimage_url').val('');
        });

        $('#keywords').select2({
            tags: true,
            tokenSeparators: [';'],
            placeholder: 'Palavras-chave (Opcional)',
            language: 'pt-BR',
            minimumInputLength: 3
        });
    </script>
@endpush

<div class="tab-pane fade {{(Request::get('type')) ? 'show active' : '' }}" id="nav-informations" role="tabpanel"
     aria-labelledby="nav-informations">
    {!! Form::model($product, ['route' => 'products.store', 'enctype' => 'multipart/form-data']) !!}
    <div class="xgrow-card card-dark p-0 mt-4">
        <div class="xgrow-card-body p-3">
            <h5 class="xgrow-card-title my-3" style="font-size: 1.5rem; line-height: inherit">
                Informações do produto
            </h5>
            <div class="row">

                <div class="col-sm-12 col-md-12 col-lg-8">
                    <div class="row">

                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                {!! Form::text('name', null, ['id' => 'name', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine', 'required']) !!}
                                {!! Form::label('name', 'Nome') !!}
                            </div>
                        </div>

                        {!! Form::hidden('type', Request::get('type'), ['id' => 'type']) !!}

                        <div id="div_category" class="col-sm-12 col-md-6 col-lg-6">
                            <div
                                class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                                {!! Form::select('category_id', $categories, null, ['name'=>'category_id', 'class' => "xgrow-select"]) !!}
                                {!! Form::label('category_id', 'Categoria:') !!}
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                {!! Form::textarea('description', null, ['id' => 'description', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine', 'rows' => 7, 'cols' => 54, 'style' => 'resize:none']) !!}
                                {!! Form::label('description', 'Descreva aqui a descrição do produto principal...') !!}
                            </div>
                        </div>


                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="xgrow-form-control">
                                <select name="keywords[]" id="keywords" class="xgrow-select" multiple="multiple"
                                        style="width: 100%"></select>
                            </div>
                            <small>Adicione a tag e pressione enter para confirmar.</small>
                        </div>

                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                {!! Form::email('support_email', null, ['id' => 'support_email', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine', 'required']) !!}
                                {!! Form::label('support_email', 'E-mail para suporte (obrigatório)') !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-md-12 col-lg-4">
                    <h6>Imagem do Produto</h6>
                    <p class="xgrow-medium-italic">
                        Imagem localizada na parte superior que identifica o produto. Tamanho 500x500.
                    </p>
                    <div class="row">
                        {!! UpImage::getImageTag($product, 'image', 'image', 'w-100 img-fluid my-3') !!}
                    </div>
                    <br>
                    {!! UpImage::getUploadButton('image', 'btn btn-themecolor') !!}
                    <button type="button" class="btn xgrow-upload-btn-lg my-2" id="removeImage">
                        <i class="fa fa-trash" aria-hidden="true"></i> Remover imagem
                    </button>
                </div>
            </div>
        </div>
        <div class="xgrow-card-footer p-3 border-top mt-4">
            <button class="xgrow-button">Próximo</button>
        </div>
    </div>
    @include('up_image.modal-xgrow', ['restrictAcceptedFormats' => 'image/*'])
    {!! Form::close() !!}
</div>
