@extends('templates.xgrow.main')

@push('jquery')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script type="text/javascript">
        function searchQuiz() {
            let searchField = document.getElementById('searchQuiz');
            let searchFilter = searchField.value.toLowerCase();
            $('.card-quiz').filter(function() {
                if ($(this).find('p.section-label').text().toLowerCase().indexOf(searchFilter) > -1) {
                    $(this).removeClass('d-none');
                } else {
                    $(this).addClass('d-none');
                }
            });
        }

        let quizID;
        $(function() {
            $('#deleteQuizModal').on('show.bs.modal', function(e) {
                quizID = $(e.relatedTarget).data('id');
            });
        });

        function deleteQuiz() {
            successToast('Teste excluído!', 'Este teste não está mais disponível.');
            let myToastEl = document.getElementById('dialogToast');
            myToastEl.addEventListener('hidden.bs.toast', function() {
                $('#quiz_delete').attr('action', `/quiz/${quizID}`).submit();
            });
        }

    </script>
@endpush

@push('after-styles')

    <link href="{{ asset('xgrow-vendor/assets/css/pages/section_index.css') }}" rel="stylesheet">

    <style>
        .invisivel-quiz {
            display: none;
        }

        .session_title {
            font-family: 'Rubik', sans-serif !important;
        }

    </style>

@endpush

@push('before-scripts')
@endpush

@section('content')

    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item active mx-2"><span>Testes</span></li>
        </ol>
    </nav>

    <div class="xgrow-card card-dark p-0">
        <div class="xgrow-card-header px-4 align-items-center flex-wrap border-bottom">
            <div class="xgrow-form-control mt-md-3">
                <div class="xgrow-input" style="background-color: var(--input-bg);">
                    <input id="searchQuiz" onkeyup="searchQuiz()" placeholder="Buscar teste" type="text" />
                    <span class="xgrow-input-cancel"><i class="fa fa-search"></i></span>
                </div>
            </div>

            <button class="xgrow-button border-light" onclick="location.href='{{ route('quiz.create') }}'">
                <i class="fa fa-plus"></i> Novo teste
            </button>
        </div>

        <div class="xgrow-card-body px-3 pb-5">
            @include('elements.alert')
            <div class="row flex-wrap justify-content-lg-center justify-content-md-around">
                @foreach ($quizzes as $quiz)
                    <div class="card-quiz d-flex p-3 m-2 card-dark shadow-none col-sm-12 col-md-6 col-lg-3"
                        style="min-width: 330px; max-width: 331px" id="section-{{ $quiz->id }}">
                        <div>
                            <div class="section-photo">
                                @if (isset($quiz->thumb->filename))
                                    <img src="{{ $quiz->thumb->filename }}" alt="Imagem da seção"
                                        class="img-responsive" />
                                @else
                                    <i class="fa fa-image"></i>
                                @endif
                            </div>
                            <p class="section-label">{{ \Illuminate\Support\Str::limit($quiz->name, 20) }}
                            </p>
                        </div>
                        <div class="section-btn-group mx-4">
                            <div class="row">
                                <a href="{{ route('quiz.edit', $quiz->id) }}"
                                    class="xgrow-button btn-sm-section d-flex justify-content-center">
                                    <i class="fa fa-cog align-self-center"></i>
                                </a>
                            </div>
                            <div class="row">
                                <button class="xgrow-button btn-sm-section d-flex justify-content-center"
                                    data-bs-toggle="modal" data-bs-target="#deleteQuizModal" data-id="{{ $quiz->id }}">
                                    <i class="fa fa-trash align-self-center"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Modal --}}
    <div class="modal-sections modal fade" id="deleteQuizModal" tabindex="-1" aria-labelledby="deleteQuizModal"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-header">
                    <p class="modal-title" id="deleteQuizModal">Excluir teste</p>
                </div>
                <div class="modal-body">
                    Você tem certeza que deseja excluir este teste?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal" onclick="deleteQuiz()"
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
    @include('elements.toast')

    <form method="POST" id="quiz_delete">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
    </form>
@endsection
