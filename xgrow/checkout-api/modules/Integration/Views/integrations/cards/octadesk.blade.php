<div class="col-xxl-3 col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12 mb-3 px-2">
    <div class="integration-card" title="{{ $app->description }}">
        <div class="card-left">
            <div class="card-title">
                <img src="{{ asset('xgrow-vendor/assets/img/octadesk-icon.png') }}">
                <p>Octadesk</p>
            </div>
            <div class="card-desc">
                <p>Sistema de marketing, vendas e atendimento.</p>
            </div>
        </div>
        <div class="card-right">
            @include('apps::integrations.includes.status', ['app' => $app])
            <div class="card-buttons">
                @include(
                    'apps::integrations.includes.more', 
                    ['app' => $app, 'showActions' => true, 'queueable' => true]
                )
            </div>
        </div>
    </div>
</div>