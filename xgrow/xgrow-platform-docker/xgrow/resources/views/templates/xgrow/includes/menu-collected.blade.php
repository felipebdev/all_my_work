@if (!in_array($routeName, $externalRoutes))
    @can('dashboard')
        <a class="collapse-group-item list-group-item list-group-item-action {{ in_array($routeName, ['dashboard.index', 'home']) ? 'active' : '' }}"
            href="/dashboard" title="Resumo">
            <i class="fa fa-pie-chart"></i>
        </a>
    @endcan
    @canany(['product', 'coupons', 'producer'])
        <button type="button" data-bs-toggle="collapse" data-bs-target="#produtos" aria-expanded="false"
            aria-controls="produtos"
            class="collapse-group-item list-group-item xgrow-drop list-group-item-action {{ in_array($routeName, $productRoutes) ? 'active' : '' }}"
            title="Produtos" onclick="openSidenavWithBtn()">
            <i class="fas fa-box mx-2"></i>
        </button>
    @endcanany
    <a class="collapse-group-item list-group-item list-group-item-action {{ in_array($routeName, ['affiliates']) ? 'active' : '' }}"
        href="/affiliates" title="Afiliados">
        <i class="fa fa-address-card"></i>
    </a>
    @canany(['section', 'course', 'content', 'comment', 'forum', 'author', 'transfer-content', 'design'])
        <a class="collapse-group-item list-group-item list-group-item-action {{ in_array($routeName, $subscribersAllRoutes) ? 'active' : '' }}"
            href="/learning-area" title="Aprendizagem">
            <i class="fa fa-graduation-cap mx-2"></i>
        </a>
        @if (false)
            <button type="button" data-bs-toggle="collapse" data-bs-target="#learning_area" aria-expanded="true"
                aria-controls="learning_area"
                class="collapse-group-item list-group-item xgrow-drop list-group-item-action {{ in_array($routeName, $subscribersAllRoutes) ? 'active' : '' }}"
                title="Aprendizagem" onclick="openSidenavWithBtn()">
                <i class="fa fa-graduation-cap mx-2"></i>
            </button>
        @endif
    @endcanany
    @if (FeatureFlag::check('gamification'))
        @canany(['section', 'course', 'content'])
            <button type="button" data-bs-toggle="collapse" data-bs-target="#gamificationMenu" aria-expanded="false"
                aria-controls="gamificationMenu"
                class="collapse-group-item list-group-item xgrow-drop list-group-item-action  {{ in_array($routeName, $gamificationAllRoutes) ? 'active' : '' }}"
                title="Gamificação" onclick="openSidenavWithBtn()">
                <i class="fas fa-gamepad mx-2"></i>
            </button>
        @endcanany
    @endif
    @canany(['subscriber', 'import-suscriber'])
        <button type="button" data-bs-toggle="collapse" data-bs-target="#subscribers" aria-expanded="true"
            aria-controls="subscribers"
            class="collapse-group-item list-group-item xgrow-drop list-group-item-action {{ in_array($routeName, $subscribersAllRoutes) ? 'active' : '' }}"
            title="Alunos" onclick="openSidenavWithBtn()">
            <i class="fa fa-users mx-2"></i>
        </button>
    @endcanany
    @canany(['financial', 'sale', 'subscription', 'lead', 'producer'])
        <button type="button" data-bs-toggle="collapse" data-bs-target="#sales" aria-expanded="false" aria-controls="sales"
            class="collapse-group-item list-group-item xgrow-drop list-group-item-action {{ in_array($routeName, $salesAllRoutes) ? 'active' : '' }}"
            title="Vendas" onclick="openSidenavWithBtn()">
            <i class="fas fa-wallet mx-2"></i>
        </button>
    @endcanany
    @canany(['report', 'content-report', 'search-report', 'course-report', 'lists'])
        <button type="button" data-bs-toggle="collapse" data-bs-target="#relatorios" aria-expanded="false"
            aria-controls="relatorios"
            class="collapse-group-item list-subgroup-item list-group-item xgrow-drop list-group-item-action {{ in_array($routeName, $reportsAllRoutes) ? 'active' : '' }}"
            title="Relatórios" onclick="openSidenavWithBtn()">
            <i class="fas fa-chart-line"></i>
        </button>
    @endcan

    @canany(['integration', 'callcenter', 'engagement'])
        <button type="button" data-bs-toggle="collapse" data-bs-target="#resource" aria-expanded="false"
            aria-controls="resource"
            class="collapse-group-item list-subgroup-item list-group-item xgrow-drop list-group-item-action {{ in_array($routeName, ['integracao.index']) ? 'active' : '' }}"
            title="Recursos" onclick="openSidenavWithBtn()">
            <i class="fas fa-cogs mx-2"></i>
        </button>
    @endcan
    @canany(['config', 'user', 'permission', 'email', 'category'])
        <button type="button" data-bs-toggle="collapse" data-bs-target="#configuracoes" aria-expanded="false"
            aria-controls="configuracoes"
            class="collapse-group-item list-group-item xgrow-drop list-group-item-action {{ in_array($routeName, $settingsAllRoutes) ? 'active' : '' }}"
            title="Configurações" onclick="openSidenavWithBtn()">
            <i class="fa fa-cog"></i>
        </button>
    @endcan
@else
    <a class="collapse-group-item list-group-item list-group-item-action {{ in_array($routeName, ['choose.platform', 'new.platform']) ? 'active' : '' }}"
        href="{{ route('choose.platform') }}" title="Plataformas">
        <i class="far fa-window-restore"></i>
    </a>
    @php $validate = (!$clientApproved) @endphp
    <a class="collapse-group-item list-group-item list-group-item-action {{ in_array($routeName, ['coproducer']) ? 'active' : '' }}"
        href="{{ $validate ? '#' : route('coproducer') }}"
        @if ($validate) title="Verifique sua identidade para acessar."
        style="opacity: .5;"
        @else
        title="Área do coprodutor" @endif>
        <i class="fa fa-users"></i>
    </a>
    <a class="collapse-group-item list-group-item list-group-item-action {{ in_array($routeName, ['affiliations', 'affiliations.products', 'affiliations.products.resume']) ? 'active' : '' }}"
        href="{{ $validate ? '#' : route('affiliations') }}"
        @if ($validate) title="Verifique sua identidade para acessar."
        style="opacity: .5;"
        @else
        title="Área do Afiliado" @endif>
        <i class="fa fa-building"></i>
    </a>
    <a class="collapse-group-item list-group-item list-group-item-action {{ in_array($routeName, $documentsRoutes) ? 'active' : '' }}"
        href="{{ route('documents') }}" title="Meus dados">
        <i class="fa fa-address-card"></i>
    </a>
@endif
