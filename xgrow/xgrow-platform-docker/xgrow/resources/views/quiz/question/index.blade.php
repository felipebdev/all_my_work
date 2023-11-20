<div class="modal-sections modal fade" id="deleteQuestionModal" tabindex="-1" aria-labelledby="deleteQuestionModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <div class="modal-header">
                <p class="modal-title" id="deleteQuestionModal">Excluir pergunta</p>
            </div>
            <div class="modal-body">
                Deseja realmente excluir essa pergunta?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal"
                        onclick="deleteQuestion();"
                        aria-label="Close">
                    Sim, excluir
                </button>
                <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal" aria-label="Close">
                    Não, manter
                </button>
            </div>
        </div>
    </div>
</div>

<form method="POST" id="question_delete">
    {{ csrf_field() }}
    {{ method_field('DELETE') }}
</form>

<!-- Tab de Perguntas -->
<div class="xgrow-question-view xgrow-card card-dark mt-4" style="display: block">
    <div id="xgrow-view" class="xgrow-card-body">
        <div class="d-flex justify-content-between align-items-center pt-2 flex-wrap">

            <!--CAIXA DE PESQUISA IMPLEMENTAR-->
            <div class="xgrow-form-control d-none">
                <div class="xgrow-input" style="display: none">
                    <input id="input5" placeholder="Buscar por" type="text"/>
                    <span class="xgrow-input-cancel">
                        <i class="fa fa-search"></i>
                    </span>
                </div>
            </div>
            <!--CAIXA DE PESQUISA IMPLEMENTAR-->
            <p class="xgrow-card-title py-2" style="font-size: 1.5rem; line-height: inherit;">Gerencie as perguntas</p>
            {!! link_to_route("question.create", "Adicionar nova pergunta", [$quiz->id, 0], ['class' => 'xgrow-button four-col link d-flex justify-content-center align-items-center']) !!}
        </div>

        <div class="mt-3">
            <p class="xgrow-card-body-title mb-1">
                Perguntas disponíveis
            </p>

            <div>
                <ul class="draggable-list-group list-group" id="exhibitionOrder">
                    @if($questions->count() > 0)
                        <li class="draggable-list-group-item list-group-item sortable-disabled my-2">
                            <div class="card-thin-full-head">
                                <div class="card-thin-full-head-heading">
                                    <p class="p-3">Nome do pergunta</p>
                                </div>
                            </div>
                        </li>
                        @foreach ($questions as $item => $question)
                            <li class="draggable-list-group-item list-group-item">
                                <div class="mb-2 p-2 card-thin-full flex-wrap">
                                    <div class="card-thin-full-heading">
                                        <p>Pergunta {{ ($item + 1) }} - {{ $question->description }}</p>
                                    </div>
                                    <div class="d-flex flex-row justify-content-center align-items-center">

                                        {!! icon_link_to_route('fas fa-cog', "question.edit", "", [$quiz->id, $question->id], ['class' => 'fandone-edit mx-1']) !!}

                                        <button data-bs-toggle="modal" data-bs-target="#deleteQuestionModal"
                                                data-id="{{ $question->id }}"
                                                class="delete-button mx-1" type="button">
                                            <i class="fas fa-trash" aria-hidden="true"></i>
                                        </button>

                                        <form method="POST" id="question_delete_{{ $question->id }}"
                                              action="{{ Route('question.destroy', [$quiz->id, $question->id]) }}">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                        </form>

                                    </div>
                                </div>
                            </li>
                        @endforeach
                    @else
                        Nenhuma pergunta cadastrada
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="xgrow-card-footer mt-5"></div>
</div>
