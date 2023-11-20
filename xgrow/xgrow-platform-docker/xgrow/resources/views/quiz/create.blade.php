@extends('templates.xgrow.main')

@push('jquery')
<script>

        question_options = 1;
        questionID = 0;

        $( function() {

            $('#deleteQuestionModal').on('show.bs.modal', function (e) {
                questionID = $(e.relatedTarget).data('id');
            });

            @if(Route::current()->getName() == 'question.create')
                addOption({{$question_total_default}});
            @endif

            $('#newOption').click( () => addOption(1))

            $('#type_option').change( () => changeTypeOption())

            @if(in_array(Route::current()->getName(), ['question.create', 'question.edit']))
                @foreach($options as $key => $option)
                    renderOption({{$option->id}}, {{$key}}, {{$question->type}}, "{{$option->description}}", {{$option->correct}});
                @endforeach
            @endif


        });

        function deleteQuestion() {
            successToast('Pergunta excluída!', 'Esta pergunta não está mais disponível.');
            let myToastEl = document.getElementById('dialogToast');
            myToastEl.addEventListener('hidden.bs.toast', function () {
                $('#question_delete').attr('action', `question/${questionID}`).submit();
            });
        }

        function changeTypeOption(){
            const type = $('#type_option').val();
            if(type == 3)
                $('#awswers').hide()
            else{
                $('#awswers').show()
                $('.correct').prop('type', (type == 1) ? 'radio' : 'checkbox')
            }
        }

        function addOption(total){
            const type = $('#type_option').val();
            console.log(type)
            for(i = 1; i <= total; i++){
                current = $('.options').length
                renderOption(0, current, type);
            }
        }

        function renderOption(id, current, type, description = "", correct = 0){
            const list_question = `
                    <tr id="tr_${current}">
                        <td width="30px">
                          <input type="hidden" name="options[]" value="${id}"  />
                          <input type="hidden" name="excluded[]" id="delete_${current}" value="0" />
                          <input type="${ (type == 1) ? 'radio' : 'checkbox'}" name="correct[]" value="${current}" id="correct_${current}" class="correct mt-4" ${(correct > 0) ? `checked="checked"` : ``}>
                        </td>
                        <td>
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                <input
                                type="text" name="options_description[]" class="form-control options"
                               ${(description == "") ? `placeholder="Preencha essa opção"` : `value="${description}"`}>
                           </div>
                        </td>
                        <td width="50px">
                            <button class="text-white xgrow-button table-action-button btn_delete_item mt-3"
                                    style="background-color: #dc3545" data-item="${current}" type="button">
                                <i class="fa fa-minus"></i>
                            </button>
                        </td>
                  </tr>`
           $('tbody').append(list_question)
           $('.btn_delete_item').click(  function (){
                const i = $(this).data('item')
                $(`#delete_${i}`).val(1)
                $(`#tr_${i}`).hide()
                $(`#correct_${i}`).prop('checked', false)
            })
        }

        function submitQuestionForm(){
            let errors = [];

            const description = $('textarea[name=description]').val()

            if(description == ''){
                errors.push('Informe o campo descrição')
            }

            if ($('#awswers').is(':visible')){

                total_visible = $('.correct:visible').length

                if (total_visible < 2) {
                   errors.push('Informe ao menos duas opções como alternativa')
                }
                else if(!$(".correct:checked").val()) {
                   errors.push('Informe ao menos uma opção como alternativa correta')
                }
                else{
                    $("input[name='options_description[]']:visible").each(function() {
                       if ($(this).val() == "") {
                          errors.push('Preencha todas as alternativas')
                          return false;
                       }
                    });
                }
            }


            if(errors.length > 0){
                $('#alert_question').show();
                const alert = $('#alert_question ul');
                alert.empty();

                errors.forEach(
                    error => alert.append(`<li>${error}</li>`)
                )

                $("html, body").animate({ scrollTop: 0 }, "slow");
                return false
            }

            return true

        }

    </script>
@endpush

@push('after-styles')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/css/bootstrap-image-checkbox.css">
    <style type="text/css">
        #thumb{
            width: 100px; height: 100px;
            object-fit: cover;
        }
        input::-webkit-input-placeholder, textarea::-webkit-input-placeholder {
          color: #636363!important;
        }
        input:-moz-placeholder, textarea:-moz-placeholder {
          color: #636363!important;
        }
    </style>
@endpush

@push('before-scripts')
@endpush

@push('after-scripts')
        <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
@endpush

@section('content')

    <div class="d-flex my-3 mb-0">
        <nav class="xgrow-breadcrumb" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Início</a></li>
                <li class="breadcrumb-item"><a href="/quiz">Testes</a></li>
                <li class="breadcrumb-item active" aria-current="page">
                    <span>@if($quiz->id > 0) Editar @else Adicionar @endif teste</span>
                </li>
            </ol>
        </nav>
    </div>

    <nav class="xgrow-tabs-wrapper">
        <div class="xgrow-tabs nav nav-tabs" id="nav-tab" role="tablist">

            @if($quiz->id > 0)
                <a class="xgrow-tab-item nav-item nav-link  {{ in_array(Route::current()->getName(), ['quiz.edit'])   ? ' show active' : ''}} "
                   href="{{ Route('quiz.edit', $quiz->id) }}">Teste</a>

                <a class="xgrow-tab-item nav-item nav-link  {{ in_array(Route::current()->getName(), ['question.index', 'question.create', 'question.edit'])   ? ' show active' : ''}}"
                   href="{{ Route('question.index', $quiz->id) }}">Perguntas</a>
            @else
                <a class="xgrow-tab-item nav-item nav-link active" id="nav-quizs-tab" data-bs-toggle="tab"
                   href="#nav-quizs" role="tab" aria-controls="nav-quizs" aria-selected="false">Teste</a>
            @endif
        </div>
    </nav>

    @if($quiz->id > 0)
        <div class="tab-content" id="nav-tabContent">

            <div
                class="tab-pane p-3 {{ in_array(Route::current()->getName(), ['quiz.edit'])   ? ' show active' : ''}}"
                role="tabpanel">
                @if(in_array(Route::current()->getName(), ['quiz.edit', 'quiz.create']))
                    @include('quiz._form')
                @endif
            </div>

            <div
                class="tab-pane p-3{{ in_array(Route::current()->getName(), ['question.index', 'question.create', 'question.edit'])   ? ' show active' : ''}}"
                role="tabpanel">
                @if(Route::current()->getName() == 'question.index')
                    @include('quiz.question.index')
                @elseif(in_array(Route::current()->getName(), ['question.create', 'question.edit']))
                    @include('quiz.question._form')
                @endif
            </div>

        </div>
    @else
        <div class="tab-pane fade{{ Route::current()->getName() == 'quiz.create' ? ' show active' : ''}}"
             id="nav-quizs" role="tabpanel" aria-labelledby="nav-quizs-tab">
             @include('quiz._form')
        </div>
    @endif

    @include('elements.toast')

@endsection
