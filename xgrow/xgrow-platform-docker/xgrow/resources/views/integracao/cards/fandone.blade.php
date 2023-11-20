<div class="card-integrate">
    <div class="left-card">
        <div class="title-card">
            <img src="{{ asset('xgrow-vendor/assets/img/integration-img.png') }}" alt="">
            <h2>Fandone</h2>
        </div>
        <p>Texto relacionado as funcionalidades do Fandone</p>
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
                    data-provider="fandone"
                    data-href="fandone-modal">
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
                <a class="btn-conectar btn-modal" data-href="fandone-modal">Conectar</a>
            </div>
        @endif
    </div>
</div>