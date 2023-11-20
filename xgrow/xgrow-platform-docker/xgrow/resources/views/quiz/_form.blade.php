@if($quiz->id > 0)
  {!! Form::model($quiz,['method'=>'put', 'enctype' => 'multipart/form-data', 'route'=> ['quiz.update', $quiz->id]]) !!}
@else
  {!! Form::open(['route' => 'quiz.store', 'enctype' => 'multipart/form-data']) !!}
@endif

@include('elements.alert')

<div class="xgrow-card card-dark mt-3">
    <div class="xgrow-card-body py-4">
        <h5 class="mb-3">Sobre o teste</h5>
        <div class="row">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                {!! Form::text('name', null, ['spellcheck' => 'false', 'autocomplete' => 'off', 'id' => 'name']) !!}
                <label>*Nome</label>
                <span onclick="document.getElementById('name').value = ''"></span>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        {!! Form::textarea('description', null, ['class' =>'w-100 mui--is-empty mui--is-pristine mui--is-touched', 'style' => "height: 165px"]) !!}
                        {!! Form::label('description','Descrição:') !!}
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column">
                <p class="xgrow-medium-bold mb-1">Imagem</p>
                <div class="d-flex flex-row align-items-center">
                    <div class="xgrow-card card-dark mt-2 d-flex flex-row align-items-center justify-content-center p-1" style="width: 100px; height: 100px; background: var(--black1); margin-right: 20px">
                        {!! UpImage::getImageTag($quiz, 'thumb') !!}
                    </div>
                    {!! UpImage::getUploadButton('thumb', 'xgrow-upload-btn-lg btn mt-2', 'Upload') !!}
                </div>
            </div>
        </div>
    </div>
</div>


<div class="xgrow-card-footer border-top mt-2">
    {!! Form::submit('Salvar Alterações',['class'=>'xgrow-button']) !!}
</div>

@include('up_image.modal-xgrow')

</form>
<form action="{{ route('quiz.destroy',$quiz->id) }}" id="form_delete" method="POST">
    {{ method_field('DELETE') }}
    {{ csrf_field() }}
</form>