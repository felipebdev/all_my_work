@if (boolval($app->is_active))
    <div class="info-label">
        <p>Ativo</p>
    </div>
@else
    <div class="error-label">
        <p>Inativo</p>
    </div>
@endif