<div class="card-integrate">
    <div class="left-card">
        <div class="title-card">
            <img class="img-integration-icon" src="{{ asset('xgrow-vendor/assets/img/panda-video-icon.png') }}" alt="Marca Panda Video">
            <h2>Panda Video</h2>
        </div>
        <p>Segurança em hospedagem de vídeos para marketing digital e cursos online</p>
    </div>
    <div class="right-card">
        @if (!empty($id))
            <div class="top-card">
                <i class="fas fa-check-square"></i>
                <span>Conectado</span>
            </div>
            <div class="bottom-card">
                <a href="javascript:void(0)" class="btn-modal btn-integration-edit"
                   data-url="{{ route('integracao.edit', ['id' => $id]) }}"
                   data-provider="pandavideo"
                   data-href="pandavideo-modal">
                    <i class="fas fa-edit"></i>
                </a>
                <a href="javascript:void(0)"
                   data-bs-toggle="modal" data-bs-target="#modal-integration-delete"
                   data-id="{{ $id }}">
                    <i class="fas fa-trash-alt"></i>
                </a>
            </div>
        @else
            <div class="top-card">
                <a class="btn-conectar btn-modal" data-href="pandavideo-modal">Conectar</a>
            </div>
        @endif
    </div>
</div>
