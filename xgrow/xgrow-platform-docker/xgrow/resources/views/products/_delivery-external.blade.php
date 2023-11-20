@push('after-styles')
    <link href="{{ asset('xgrow-vendor/assets/css/pages/products.css') }}" rel="stylesheet">
@endpush

<div class="col-lg-12 col-md-12 col-sm-12">
    <div class="d-flex align-items-center mb-3">
        <div class="form-check form-switch">
            {!! Form::checkbox('chk-external-area', null, null, ['id' => 'chk-external-area', 'class' => 'form-check-input', 'v-model' => 'externalArea', '@change' => 'syncExternalArea']) !!}
            {!! Form::label('chk-external-area', 'Utilizar área de membros externa', ['class' => 'form-check-label']) !!}
        </div>
    </div>
</div>

<div class="col-lg-12 col-md-12 col-sm-12 mb-3 d-flex gap-3" v-if="externalArea">
    <div class="card-integrate">
        <img class="img-integration-icon" src="{{ asset('xgrow-vendor/assets/img/kajabi-icon.png') }}"
             alt="">
        <h2>Kajabi</h2>
    </div>

    <div class="card-integrate">
        <img class="img-integration-icon" src="{{ asset('xgrow-vendor/assets/img/cademi-icon.png') }}"
             alt="">
        <h2>Cademí</h2>
    </div>
</div>
