<div class="col-xxl-3 col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12 mb-3 px-2">
    <div class="integration-card" title="{{ $app->description }}">
        <div class="card-left">
            <div class="card-title">
                <img src="{{ asset('xgrow-vendor/assets/img/integration-img.png') }}">
                <p>Fandone</p>
            </div>
            <div class="card-desc">
                <p>Plataforma de conteúdos online.</p>
            </div>
        </div>
        <div class="card-right">
            @include('apps::integrations.includes.status', ['app' => $app])
            <div class="card-buttons">
                @include(
                    'apps::integrations.includes.more', 
                    ['app' => $app, 'showActions' => false, 'queueable' => false]
                )
            </div>
        </div>
    </div>
</div>