@push('after-scripts')
    <script>
        $('#removeImageOrderBump').on('click', function() {
            $('#order_bump_image').attr('src', '/xgrow-vendor/assets/img/big-file.png');
            $('#order_bump_image_upimage_file_id').val(0);
            $('#order_bump_image_upimage_url').val('');
        });

        $('#removeImageUpsell').on('click', function() {
            $('#upsell_image').attr('src', '/xgrow-vendor/assets/img/big-file.png');
            $('#upsell_image_upimage_file_id').val(0);
            $('#upsell_image_upimage_url').val('');
        });

    </script>
@endpush

<div class="tab-pane fade show" id="nav-checkout" role="tabpanel" aria-labelledby="nav-checkout-tab">

    @include('elements.alert')

    <div class="xgrow-card card-dark p-0 mt-4">
        <div class="xgrow-card-body p-3">
            <h5 class="xgrow-card-title my-3" style="font-size: 1.5rem; line-height: inherit">
                Checkout
            </h5>
            <!-- <div class="row">
                <div class="col-lg-6 col-md-12">
                    <div class="xgrow-form-control mb-3">
                        {!! Form::select('gateways', $gateways, null, ['class' => 'xgrow-select', 'placeholder' => 'Gateway', 'id' => 'integration_id']) !!}
            </div>
        </div>
    </div> -->
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="d-flex align-items-center mb-3">
                        <div class="form-check form-switch">
                            {!! Form::checkbox(null, null, isset($plan->order_bump_plan_id) || old('order_bump_plan_id') !== null, ['id' => 'chk-orderbump-exists', 'class' => 'form-check-input']) !!}
                            {!! Form::label(null, 'Habilitar order bump', ['class' => 'form-check-label']) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div id="div-orderbump">
                <div class="row">
                    <div class="col-lg-6 col-md-12">
                        <div
                            class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                            <select class="xgrow-select" id="order_bump_plan_id" name="order_bump_plan_id">
                                <option value="" selected disabled></option>
                                @foreach (\App\Plan::where('type_plan', 'P')
        ->where('platform_id', Auth::user()->platform_id)
        ->get()
    as $key => $value)
                                    <option value="{{ $value->id }}"
                                        {{ isset($plan->order_bump_plan_id) && $plan->order_bump_plan_id == $value->id ? 'selected' : '' }}>
                                        {{ $value->name }}</option>
                                @endforeach
                            </select>
                            {!! Form::label('order_bump_plan_id', 'Order bump') !!}
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="xgrow-form-control">
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                {!! Form::number('order_bump_discount', null, ['id' => 'order_bump_discount', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine', 'min' => 1, 'max' => 100]) !!}
                                {!! Form::label('order_bump_discount', 'Porcentagem de desconto') !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-12">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            {!! Form::textarea('order_bump_message', null, [
    'class' => '"w-100 mui--is-empty mui--is-pristine mui--is-touched',
    'id' => 'orderbump_checkout_textarea',
    'rows' => 7,
    'cols' => 54,
    'maxlength' => 250,
    'style' => 'resize:none; height: auto; min-height:200px',
]) !!}
                            {!! Form::label('order_bump_message', 'Descreva detalhadamente aqui a sua mensagem.') !!}
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-12">
                        <h6>Imagem do Order Bump</h6>
                        <p class="xgrow-medium-italic">Tamanho 500x500</p>
                        <div id="order-bump-image-upload">
                            {!! UpImage::getImageTag($plan, 'order_bump_image', 'order_bump_image', 'img-fluid my-3') !!}
                            <br>
                            {!! UpImage::getUploadButton('order_bump_image', 'btn btn-themecolor') !!}
                            <button type="button" class="btn xgrow-upload-btn-lg my-2" id="removeImageOrderBump">
                                <i class="fa fa-trash" aria-hidden="true"></i> Remover imagem
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="d-flex align-items-center mb-3">
                        <div class="form-check form-switch">
                            {!! Form::checkbox(null, null, isset($plan->upsell_plan_id) || old('upsell_plan_id') !== null, ['id' => 'chk-upsell-exists', 'class' => 'form-check-input']) !!}
                            {!! Form::label(null, 'Habilitar upsell', ['class' => 'form-check-label']) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div id="div-upsell">
                <div class="row">
                    <div class="col-lg-6 col-md-12">
                        <div
                            class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                            <select class="xgrow-select" id="upsell_plan_id" name="upsell_plan_id">
                                <option value="" selected disabled></option>
                                @foreach (\App\Plan::where('platform_id', Auth::user()->platform_id)->get() as $key => $value)
                                    <option value="{{ $value->id }}"
                                        {{ isset($plan->upsell_plan_id) && $plan->upsell_plan_id == $value->id ? 'selected' : '' }}>
                                        {{ $value->name }}</option>
                                @endforeach
                            </select>
                            {!! Form::label('upsell_plan_id', 'Upsell') !!}
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            {!! Form::number('upsell_discount', null, ['id' => 'upsell_discount', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine', 'min' => 1, 'max' => 100]) !!}
                            {!! Form::label('upsell_discount', 'Porcentagem de desconto') !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-12">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            {!! Form::textarea('upsell_message', null, [
    'class' => '"w-100 mui--is-empty mui--is-pristine mui--is-touched',
    'id' => 'upsell_checkout_textarea',
    'rows' => 7,
    'cols' => 54,
    'maxlength' => 250,
    'style' => 'resize:none; height: auto; min-height:200px',
]) !!}

                            {!! Form::label('upsell_message', 'Descreva detalhadamente aqui a sua mensagem.') !!}
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="row">
                            <div class="col-lg-12 col-md-12">
                                <h6>Vídeo do Upsell</h6>
                                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mt-3 pt-2">
                                    {!! Form::url('upsell_video_url', null, ['id' => 'upsell_video_url']) !!}
                                    {!! Form::label('upsell_video_url', 'Link para vídeo') !!}
                                </div>
                            </div>

                            <div class="col-lg-12 col-md-12">
                                <h6>Imagem do Upsell</h6>
                                <p class="xgrow-medium-italic">Tamanho 500 x 500</p>
                                <div id="upsell-image-upload" class="pb-2">
                                    {!! UpImage::getImageTag($plan, 'upsell_image', 'upsell_image', 'img-fluid my-3') !!}
                                    <br>
                                    {!! UpImage::getUploadButton('upsell_image', 'btn btn-themecolor') !!}
                                    <button type="button" class="btn xgrow-upload-btn-lg my-2" id="removeImageUpsell">
                                        <i class="fa fa-trash" aria-hidden="true"></i> Remover imagem
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="xgrow-card-footer p-3 border-top mt-4">
            <input class="xgrow-button" type="submit" value="Salvar">
        </div>
    </div>
</div>
