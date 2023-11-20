@push('after-scripts')
    <script>
        let topicID;

        $(function() {
            $('#deleteContentModal').on('show.bs.modal', function(e) {
                topicID = $(e.relatedTarget).data('id');
            });
        });

    </script>
@endpush

<div class="tab-pane fade" id="nav-topics" role="tabpanel" aria-labelledby="nav-topics-tab">
    <div class="xgrow-card card-dark p-0 mt-4">
        <div class="xgrow-card-header border-bottom d-flex justify-content-end px-3">
            <button onclick="location.href='{{ route('topic.create') }}'" class="xgrow-button">
                <i class="fa fa-plus me-2" aria-hidden="true"></i> Novo tópico
            </button>
        </div>
        <div class="xgrow-card-body p-3">
            <div class="xgrow-card-body d-flex flex-wrap justify-content-start">
                @foreach ($topics as $topic)
                    <div class="card-course d-flex p-3 m-2 card-dark shadow-none">
                        <div>
                            <a class="section-photo" href="{{ route('topic.edit', $topic->id) }}">
                                @if (isset($topic->image->filename))
                                    <img src="{{ $topic->image->filename }}" alt="{{ $topic->title }}"
                                        class="img-responsive" style="max-height: 128%;" />
                                @else
                                    <img src="{{ asset('xgrow-vendor/assets/img/profile_default.jpg') }}"
                                        alt="{{ $topic->title }}" class="img-responsive" style="max-height: 128%;" />
                                @endif
                            </a>
                            <div class="d-flex align-items-center justify-content-between">
                                <p class="section-label">{{ $topic->title }}</p>
                                @if ($topic->active != 1)
                                    <p class="section-label-sub">Inativo</p>
                                @else
                                    <p class="section-label-sub active">Ativo</p>
                                @endif
                            </div>
                        </div>
                        <div class="section-btn-group mx-4">
                            <div class="row">
                                <a href="{{ route('topic.edit', $topic->id) }}"
                                    class="xgrow-button btn-sm-section d-flex justify-content-center">
                                    <i class="fa fa-cog align-self-center"></i>
                                </a>
                            </div>
                            <div class="row">
                                <a href="{!! Auth::user()->platform->url !!}/{{ $topic->name_slug }}"
                                    class="xgrow-button btn-sm-section d-flex justify-content-center">
                                    <i class="fa fa-eye align-self-center"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Modal --}}
<div class="modal-sections modal fade" id="deleteTopicModal" tabindex="-1" aria-labelledby="deleteTopicModal"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <div class="modal-header">
                <p class="modal-title" id="deleteTopicModal">Excluir tópico</p>
            </div>
            <div class="modal-body">
                Você tem certeza que deseja excluir este tópico?<br>Lembre-se, ao excluir este tópico, todos os posts
                relacionados a ele serão excluídos.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal" onclick="deleteTopic()"
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
