<div class="tab-pane fade show" id="nav-term" role="tabpanel" aria-labelledby="nav-term-tab">
    <div class="xgrow-card card-dark p-0 mt-4">
        <div class="xgrow-card-body p-3">
            <h5 class="xgrow-card-title my-3" style="font-size: 1.5rem; line-height: inherit">
                Termos de uso
            </h5>
            <div class="row">
                <div class="col-md-12">
                    {!! Form::label('terms', ' ', ['class' => 'my-2']) !!}
                    {!! Form::textarea('terms', null, ['class' => 'summernote-textarea', 'id' => 'terms','rows' => 7, 'cols' => 54, 'style' => 'resize:none', 'placeholder' => "Descreva detalhadamente aqui a sua mensagem."]) !!}
                </div>
            </div>
        </div>
        <div class="xgrow-card-footer p-3 border-top mt-4">
            <input class="xgrow-button" type="submit" value="Salvar">
        </div>
    </div>
</div>

