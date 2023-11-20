<div id="alert_question" class="alert alert-danger alert-bordered pd-y-15" role="alert" style="display: none">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true"><i class="ico icon-close"></i></span>
    </button>
    <div class="d-sm-flex align-items-center justify-content-start">
        <div class="mg-t-20 mg-sm-t-0">
            <ul>
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </ul>
        </div>
    </div>
</div>

@if($question->id > 0)
  {!! Form::model($question,['method'=>'put', 'onsubmit' => 'return submitQuestionForm()', 'id' => 'questionForm', 'enctype' => 'multipart/form-data', 'route'=> ['question.update', $quiz->id, $question->id]]) !!}
@else
  {!! Form::open(['route' => ['question.store',  $quiz->id], 'enctype' => 'multipart/form-data', 'id' => 'questionForm', 'onsubmit' => 'return submitQuestionForm()']) !!}
@endif

<div class="xgrow-card card-dark mt-3">
    <div class="xgrow-card-body py-4">
      <div class="row">
          <div class="col-md-12">
               <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    {!! Form::textarea('description', null, ['class' =>'w-100 mui--is-empty mui--is-pristine mui--is-touched', 'style' => "height: 115px"]) !!}
                    {!! Form::label('description','Descrição:') !!}
                </div>
              </div>
          </div>
      </div>

      <div class="row">

        <div class="col col-12 col-md-4">


            <div class="row">

                  <div class="col-md-12">
                      <div class="xgrow-form-control mui-textfield mui-textfield--float-label mt-3 mb-3">
                          {!! Form::select('type', $types, null, [
                          'class' => 'xgrow-select',
                          'id' => 'type_option',
                          'required' => 'required'
                          ]) !!}
                           {!! Form::label('type', '*Tipos:') !!}
                      </div>
                  </div>

                  <div class="col-md-12">
                       <div class="xgrow-form-control mui-textfield mui-textfield--float-label mt-3 mb-3">
                            {!! Form::select('order', $orders, $question->order, ['class' => 'xgrow-select',
                            'required' => 'required']) !!}
                             {!! Form::label('order', '*Ordem da pergunta:') !!}
                        </div>
                  </div>

                  <div class="col-md-12">

                    <div class="row" style="color: #fff">

                      {!! Form::label('thumb','Imagem (opcional):') !!}
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

        <div class="col col-12 col-md-8">

              <div class="mb-2 p-1" id="awswers" style="min-height: 300px; border: dashed 1px #bbb">
                  {!! Form::label('','Alternativas') !!}
                  <table class="table">
                        <tbody>
                        </tbody>
                        <tfoot>
                          <tr>
                            <td colspan="3">
                              <div class="d-flex justify-content-end">

                                <button class="xgrow-button btn_add_item" id="newOption" type="button">
                                    <i class="fa fa-plus"></i> Adicionar Item
                                </button>

                              </div>
                            </td>
                          </tr>
                        </tfoot>
                  </table>

              </div>  

        </div>  

      <div class="xgrow-card-footer border-top mt-2">
          {!! Form::submit('Salvar Alterações',['class'=>'xgrow-button']) !!}
      </div>

      @include('up_image.modal-xgrow')

    </div>
</div>

{!! Form::close() !!}

</form>