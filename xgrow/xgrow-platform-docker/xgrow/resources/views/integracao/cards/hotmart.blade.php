<div class="card-integrate">
    <div class="left-card">
        <div class="title-card">
            <img class="img-integration-icon" src="{{ asset('xgrow-vendor/assets/img/hotmart-icon.png') }}" alt="">
            <h2>Hotmart</h2>
        </div>
        <p>Texto relacionado as funcionalidades do Hotmart</p>
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
                    data-provider="hotmart"
                    data-href="hotmart-modal">
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
                <a class="btn-conectar btn-modal" data-href="hotmart-modal">Conectar</a>
            </div>
        @endif
    </div>
</div>