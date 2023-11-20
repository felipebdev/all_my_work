@php
    use App\Platform;
    use App\Client;
    
    $isOwner = false;
    $client = Client::where('email', Auth::user()->email)->first();
    
    if ($client) {
        $platformOwner = Platform::where('customer_id', $client->id)
            ->where('id', Auth::user()->platform_id)
            ->get();
        $isOwner = count($platformOwner) > 0;
    }
@endphp
@if (!in_array($routeName, $externalRoutes))
    <a href="/dashboard"
        class="list-principal-item list-group-item list-group-item-action {{ in_array($routeName, $dashboardAllRoutes) ? 'active' : '' }}">
        <i class="fa fa-pie-chart mx-2"></i> Resumo
    </a>
    @canany(['product', 'coupons', 'producer'])
        <button type="button" data-bs-toggle="collapse" data-bs-target="#produtos" aria-expanded="false"
            aria-controls="produtos"
            class="list-group-item xgrow-drop list-group-item-action {{ in_array($routeName, $productRoutes) ? 'active' : '' }}"
            {{-- onclick="changeChevronIcon()" --}}>
            <span><i class="fas fa-box mx-2"></i> Produtos</span><i class="fa fa-chevron-down"></i>
        </button>
        <div class="xgrow-sidenav-collapse-body collapse {{ in_array($routeName, $productRoutes) ? 'show' : '' }}"
            id="produtos">
            @canany(['product', 'producer'])
                <a href="/products"
                    class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $plansAllRoutes) ? 'active' : '' }}">
                    Produtos
                    <i class="fa fa-chevron-right"></i>
                </a>
            @endcanany

            @canany(['coupons'])
                <a href="/coupons"
                    class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $couponsAllRoutes) ? 'active' : '' }}">
                    Cupons
                    <i class="fa fa-chevron-right"></i>
                </a>
            @endcanany

        </div>
    @endcanany

    <a href="/affiliates"
        class="list-principal-item list-group-item list-group-item-action {{ in_array($routeName, ['affiliates']) ? 'active' : '' }}">
        <i class="fa fa-address-card mx-2"></i>
        Afiliados
    </a>

    @canany(['section', 'course', 'content', 'comment', 'forum', 'author', 'transfer-content', 'design', 'live'])
        <a href="/learning-area"
            class="list-principal-item list-group-item list-group-item-action {{ in_array($routeName, $learnigAreaRoutes) ? 'active' : '' }}">
            <i class="fa fa-graduation-cap mx-2"></i>
            Aprendizagem
        </a>

        @if (false)
            <button type="button" data-bs-toggle="collapse" data-bs-target="#learning_area" aria-expanded="false"
                aria-controls="learning_area"
                class="list-group-item xgrow-drop list-group-item-action {{ in_array($routeName, $learnigAreaRoutes) ? 'active' : '' }}">
                <span><i class="fa fa-graduation-cap mx-2"></i> Aprendizagem</span><i class="fa fa-chevron-down"></i>
            </button>
            <div class="xgrow-sidenav-collapse-body collapse {{ in_array($routeName, $learnigAreaRoutes) ? 'show' : '' }}"
                id="learning_area">
                @can('section')
                    <a href="{{ url('section') }}"
                        class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $sectionsAllroutes) ? 'active' : '' }}">
                        Seções <i class="fa fa-chevron-right"></i>
                    </a>
                @endcan
                @can('course')
                    <a href="{{ url('course') }}"
                        class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $coursesAllRoutes) ? 'active' : '' }}">
                        Cursos <i class="fa fa-chevron-right"></i>
                    </a>
                @endcan
                @can('content')
                    <a href="{{ url('content') }}"
                        class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $contentAllRoutes) ? 'active' : '' }}">
                        Conteúdo <i class="fa fa-chevron-right"></i>
                    </a>
                @endcan
                @can('comment')
                    <a href="{{ url('comments') }}"
                        class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $commentAllRoutes) ? 'active' : '' }}">
                        Comentários <i class="fa fa-chevron-right"></i>
                    </a>
                @endcan
                @if (env('APP_ENV') != 'production')
                    @can('forum')
                        <a href="{{ url('forum') }}"
                            class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $forumAllRoutes) ? 'active' : '' }}">
                            Fórum <i class="fa fa-chevron-right"></i>
                        </a>
                    @endcan
                @endif
                @can('design')
                    <a href="/la-custom"
                        class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $designAllRoutes) ? 'active' : '' }}">
                        Design<i class="fa fa-chevron-right"></i>
                    </a>
                @endcan
            </div>
        @endif
    @endcanany

    @if (FeatureFlag::check('gamification'))
        @canany(['section', 'course', 'content'])
            <button type="button" data-bs-toggle="collapse" data-bs-target="#gamificationMenu" aria-expanded="false"
                aria-controls="gamificationMenu"
                class="list-group-item xgrow-drop list-group-item-action {{ in_array($routeName, $gamificationAllRoutes) ? 'active' : '' }}">
                <span><i class="fas fa-gamepad mx-2"></i> Gamificação</span><i class="fa fa-chevron-down"></i>
            </button>
            <div class="xgrow-sidenav-collapse-body collapse {{ in_array($routeName, $gamificationAllRoutes) ? 'show' : '' }}"
                id="gamificationMenu">
                <a href="/gamification/config"
                    class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $gamificationConfigurationsAllRoutes) ? 'active' : '' }}">
                    Configurações
                    <i class="fa fa-chevron-right"></i>
                </a>
                <a href="/gamification" style="display: none"
                    class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $gamificationDashAllRoutes) ? 'active' : '' }}">
                    Dashboard
                    <i class="fa fa-chevron-right"></i>
                </a>
                <a href="/gamification/challenges"
                    class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $gamificationChallengesAllRoutes) ? 'active' : '' }}">
                    Desafios
                    <i class="fa fa-chevron-right"></i>
                </a>
                <a href="/gamification/reports"
                    class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $gamificationReportsAllRoutes) ? 'active' : '' }}">
                    Relatórios
                    <i class="fa fa-chevron-right"></i>
                </a>
            </div>
        @endcanany
    @endif
    @canany(['subscriber', 'import-suscriber'])
        <button type="button" data-bs-toggle="collapse" data-bs-target="#subscribers" aria-expanded="false"
            aria-controls="subscribers"
            class="list-group-item xgrow-drop list-group-item-action {{ in_array($routeName, $subscribersAllRoutes) ? 'active' : '' }}">
            <span>
                <i class="fa fa-users mx-2"></i> Alunos
            </span>
            <i class="fa fa-chevron-down"></i>
        </button>
        <div class="xgrow-sidenav-collapse-body collapse {{ in_array($routeName, $subscribersAllRoutes) ? 'show' : '' }}"
            id="subscribers">
            @can('subscriber')
                <a href="/subscribers"
                    class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $studentAllRoutes) ? 'active' : '' }}">
                    Alunos
                    <i class="fa fa-chevron-right"></i>
                </a>
            @endcan
            @can('import-suscriber')
                <a href="/subscribers/import"
                    class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $studentImportAllRoutes) ? 'active' : '' }}">
                    Importar alunos
                    <i class="fa fa-chevron-right"></i>
                </a>
            @endcan
        </div>
    @endcanany
    @canany(['sale', 'subscription', 'lead', 'financial'])
        <button type="button" data-bs-toggle="collapse" data-bs-target="#sales" aria-expanded="false" aria-controls="sales"
            class="list-group-item xgrow-drop list-group-item-action {{ in_array($routeName, $salesAllRoutes) ? 'active' : '' }}">
            <span>
                <i class="fas fa-wallet mx-2"></i> Vendas
            </span>
            <i class="fa fa-chevron-down"></i>
        </button>
        <div class="xgrow-sidenav-collapse-body collapse {{ in_array($routeName, $salesAllRoutes) ? 'show' : '' }}"
            id="sales">
            @can('financial')
                <a href="{{ url('reports/financial') }}"
                    class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $reportsFinancialAllRoutes) ? 'active' : '' }}">
                    Financeiro<i class="fa fa-chevron-right"></i>
                </a>
            @endcan
            @canany(['sale', 'producer'])
                <a href="{{ url('reports/sales') }}"
                    class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $reportsSaleAllRoutes) ? 'active' : '' }}">
                    Vendas<i class="fa fa-chevron-right"></i>
                </a>
            @endcanany
            @canany(['subscription', 'producer'])
                <a href="{{ url('reports/subscription') }}"
                    class="list-group-item list-group-item-action {{ in_array($routeName, $reportsSubscriptionAllRoutes) ? 'active' : '' }}">
                    Assinatura<i class="fa fa-chevron-right"></i>
                </a>
            @endcanany
            @can('lead')
                <a href="/leads"
                    class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $leadsAllRoutes) ? 'active' : '' }}">
                    Leads
                    <i class="fa fa-chevron-right"></i>
                </a>
            @endcan
        </div>
    @endcanany

    @canany(['report', 'content-report', 'search-report', 'course-report', 'lists'])
        <button type="button" data-bs-toggle="collapse" data-bs-target="#relatorios" aria-expanded="false"
            aria-controls="relatorios"
            class="list-group-item xgrow-drop list-group-item-action {{ in_array($routeName, $reportsAllRoutes) ? 'active' : '' }}">
            <span>
                <i class="fas fa-chart-line mx-2"></i> Relatórios
            </span>
            <i class="fa fa-chevron-down"></i>
        </button>
        <div class="xgrow-sidenav-collapse-body collapse {{ in_array($routeName, $reportsAllRoutes) ? 'show' : '' }}"
            id="relatorios">
            @if (env('APP_ENV') != 'production')
                @can('report')
                    <a href="{{ url('reports/access') }}"
                        class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $reportsAccessAllRoutes) ? 'active' : '' }}">
                        Acessos
                        <i class="fa fa-chevron-right"></i>
                    </a>
                @endcan
                @can('content-report')
                    <a href="{{ url('reports/content') }}"
                        class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $reportsContentAllRoutes) ? 'active' : '' }}">
                        Conteúdos
                        <i class=" fa fa-chevron-right"></i>
                    </a>
                @endcan
                @can('search-report')
                    <a href="{{ url('reports/content-search') }}"
                        class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $reportsContentSearchAllRoutes) ? 'active' : '' }}">
                        Pesquisa
                        <i class="fa fa-chevron-right"></i>
                    </a>
                @endcan
                @can('course-report')
                    <a href="{{ url('reports/course-search') }}"
                        class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $reportsCourseSearchAllRoutes) ? 'active' : '' }}">
                        Cursos
                        <i class="fa fa-chevron-right"></i>
                    </a>
                @endcan
                @can('course-report')
                    <a href="{{ url('reports/simplified-progress') }}"
                        class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $reportsProgressAllRoutes) ? 'active' : '' }}">
                        Progresso <i class="fa fa-chevron-right"></i>
                    </a>
                @endcan
            @endif
            @can('lists')
                <a href="{{ url('reports/downloads') }}"
                    class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $reportsDownloadsAllRoutes) ? 'active' : '' }}">
                    Listas exportadas
                    <i class="fa fa-chevron-right"></i>
                </a>
            @endcan
        </div>
    @endcanany

    @canany(['integration', 'callcenter', 'engagement'])
        <button type="button" data-bs-toggle="collapse" data-bs-target="#resource" aria-expanded="false"
            aria-controls="resource"
            class="list-group-item xgrow-drop list-group-item-action {{ in_array($routeName, $resourcesAllRoutes) ? 'active' : '' }}">
            <span>
                <i class="fa fa-cogs mx-2"></i> Recursos
            </span>
            <i class="fa fa-chevron-down"></i>
        </button>
        <div class="xgrow-sidenav-collapse-body nested collapse {{ in_array($routeName, $resourcesAllRoutes) ? 'show' : '' }}"
            id="resource">
            @can('integration')
                <a href="{{ route('apps.integrations.index') }}"
                    class=" list-principal-item list-group-item list-group-item-action {{ in_array($routeName, $integrationRoutes) ? 'active' : '' }}">
                    Integrações <i class="fa fa-chevron-right"></i>
                </a>
            @endcan
            @if (FeatureFlag::check(
                    'mobile-subscriber-notifications',
                    env('APP_ENV') != 'production' || Auth::user()->platform_id == '89d6084b-99ae-481c-8646-05c99c98b469'))
                <button type="button" data-bs-toggle="collapse" data-bs-target="#mobile" aria-expanded="false"
                    aria-controls="mobile" class="list-group-item xgrow-drop list-group-item-action">
                    Mobile <i class="fa fa-chevron-down"></i>
                </button>
                <div class="xgrow-sidenav-collapse-body nested collapse {{ in_array($routeName, $pushNotificationRoutes) ? 'show' : '' }}"
                    id="mobile">
                    <a href="{{ route('push-notification.index') }}"
                        class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $pushNotificationRoutes) ? 'active' : '' }}">
                        Notification <i class="fa fa-chevron-right"></i>
                    </a>
                </div>
            @endif
            @can('engagement')
                <button type="button" data-bs-toggle="collapse" data-bs-target="#engagement" aria-expanded="false"
                    aria-controls="engagement"
                    class="list-group-item xgrow-drop list-group-item-action {{ in_array($routeName, $engagementAllRoutes) ? 'active' : '' }}">
                    Engajamento<i class="fa fa-chevron-down"></i>
                </button>
                <div class="xgrow-sidenav-collapse-body nested collapse {{ in_array($routeName, $engagementAllRoutes) ? 'show' : '' }}"
                    id="engagement">
                    <a href="{{ route('audience.index') }}"
                        class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $audienceAllRoutes) ? 'active' : '' }}">
                        Público<i class="fa fa-chevron-right"></i>
                    </a>
                    <?php
                    /*
                                                                                                                                                    <a href="{{ route('campaign.index') }}"
                                                                                                                                                        class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $campaignAllRoutes) ? 'active' : '' }}">
                                                                                                                                                        Campanhas<i class="fa fa-chevron-right"></i>
                                                                                                                                                    </a>
                                                                                                                                                */
                    ?>
                </div>
            @endcan
            @can('callcenter')
                <button type="button" data-bs-toggle="collapse" data-bs-target="#callcenter" aria-expanded="false"
                    aria-controls="callcenter"
                    class="list-group-item xgrow-drop list-group-item-action {{ in_array($routeName, $callCenterRoutes) ? 'active' : '' }}">
                    Call Center<i class="fa fa-chevron-down"></i>
                </button>
                <div class="xgrow-sidenav-collapse-body nested collapse {{ in_array($routeName, $callCenterRoutes) ? 'show' : '' }}"
                    id="callcenter">

                    <a href="{{ route('callcenter.dashboard') }}"
                        class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, ['callcenter.dashboard']) ? 'active' : '' }}">
                        Dashboard<i class="fa fa-chevron-right"></i>
                    </a>

                    <a href="{{ route('attendant.index') }}"
                        class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, ['attendant.index', 'attendant.create', 'attendant.edit']) ? 'active' : '' }}"
                        title="">
                        Atendentes<i class="fa fa-chevron-right"></i>
                    </a>


                    <button type="button" data-bs-toggle="collapse" data-bs-target="#callcenter-report"
                        aria-expanded="false" aria-controls="callcenter-report"
                        class="list-group-item xgrow-drop list-group-item-action {{ in_array($routeName, array_merge($callcenterReportsRoutes, $callcenterReportsPublicRoutes)) ? 'active' : '' }}">
                        Relatórios<i class="fa fa-chevron-down"></i>
                    </button>
                    <div class="xgrow-sidenav-collapse-body nested collapse {{ in_array($routeName, array_merge($callcenterReportsRoutes, $callcenterReportsPublicRoutes)) ? 'show' : '' }}"
                        id="callcenter-report">
                        <a href="{{ route('callcenter.reports') }}"
                            class="list-bottomgroup-item list-subgroup-item list-group-item list-group-item-action  {{ in_array($routeName, $callcenterReportsRoutes) ? 'active' : '' }}">
                            Atendentes<i class="fa fa-chevron-right"></i>
                        </a>
                        <a href="{{ route('callcenter.reports.public') }}"
                            class="list-bottomgroup-item list-subgroup-item list-group-item list-group-item-action  {{ in_array($routeName, $callcenterReportsPublicRoutes) ? 'active' : '' }}">
                            Públicos<i class="fa fa-chevron-right"></i>
                        </a>
                    </div>

                    <a href="{{ route('callcenter.config') }}"
                        class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, ['callcenter.config']) ? 'active' : '' }}"
                        title="">
                        Configurações<i class="fa fa-chevron-right"></i>
                    </a>
                </div>
            @endcan

        </div>
    @endcanany

    @canany(['config', 'user', 'permission', 'email', 'category'])
        <button type="button" data-bs-toggle="collapse" data-bs-target="#configuracoes" aria-expanded="false"
            aria-controls="configuracoes"
            class="list-group-item xgrow-drop list-group-item-action {{ in_array($routeName, $settingsAllRoutes) ? 'active' : '' }}">
            <span>
                <i class="fa fa-cog mx-2"></i> Configurações
            </span>
            <i class="fa fa-chevron-down"></i>
        </button>
        <div class="xgrow-sidenav-collapse-body collapse {{ in_array($routeName, $settingsAllRoutes) ? 'show' : '' }}"
            id="configuracoes">
            @can('config')
                <a href="{{ route('platform-profile.edit') }}"
                    class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $configAllRoutes) ? 'active' : '' }}"
                    title="">Perfil Plataforma<i class="fa fa-chevron-right"></i>
                </a>
            @endcan
            @can('user')
                <a href="{{ route('platforms-users.index') }}"
                    class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $userAllRoutes) ? 'active' : '' }}">
                    Usuários<i class="fa fa-chevron-right"></i>
                </a>
            @endcan
            @can('permission')
                <a href="{{ route('permission.index') }}"
                    class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $permissionAllRoutes) ? 'active' : '' }}">
                    Permissões<i class="fa fa-chevron-right"></i>
                </a>
            @endcan
            @if ($isOwner)
                <a href="{{ route('developer') }}"
                    class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $developerAllRoutes) ? 'active' : '' }}">
                    Desenvolvedor<i class="fa fa-chevron-right"></i>
                </a>
            @endif
            @can('email')
                <button type="button" data-bs-toggle="collapse" data-bs-target="#emails" aria-expanded="false"
                    aria-controls="emails"
                    class="list-group-item xgrow-drop list-group-item-action {{ in_array($routeName, $emailsAllRoutes) ? 'active' : '' }}">
                    E-mails<i class="fa fa-chevron-down"></i>
                </button>

                <div class="xgrow-sidenav-collapse-body nested collapse {{ in_array($routeName, $emailsAllRoutes) ? 'show' : '' }}"
                    id="emails">
                    <a href="/emails"
                        class="list-bottomgroup-item list-subgroup-item list-group-item list-group-item-action  {{ in_array($routeName, $emailMessageAllRoutes) ? 'active' : '' }}">
                        Mensagens<i class="fa fa-chevron-right"></i>
                    </a>
                    @if (false)
                        <a href="/emails/conf"
                            class="list-bottomgroup-item list-subgroup-item list-group-item list-group-item-action  {{ in_array($routeName, $emailConfAllRoutes) ? 'active' : '' }}">
                            Configurações<i class="fa fa-chevron-right"></i>
                        </a>
                        <a href="{{ route('ruler.index') }}"
                            class="list-bottomgroup-item list-subgroup-item list-group-item list-group-item-action  {{ in_array($routeName, $emailRulerAllRoutes) ? 'active' : '' }}">
                            Régua de cobrança<i class="fa fa-chevron-right"></i>
                        </a>
                    @endif
                </div>
            @endcan

            {{-- @can('category') --}}
            {{-- <a href="{{ route('category.index') }}" --}}
            {{-- class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, $categoryAllRoutes) ? 'active' : '' }} mb-4"> --}}
            {{-- Agrupamentos<i class="fa fa-chevron-right"></i> --}}
            {{-- </a> --}}
            {{-- @endcan --}}

        </div>
    @endcanany
@else
    <a id="platforms-link" href="{{ route('choose.platform') }}"
        class="list-principal-item list-group-item list-group-item-action platform-item start-button {{ in_array($routeName, ['choose.platform', 'choose.platform.new', 'new.platform']) ? 'active' : '' }}">
        <span class="list-group-item-label">Minhas plataformas</span>
    </a>
    @php $validate = (!$clientApproved) @endphp

    <a href="{{ $validate ? '#' : route('coproducer') }}" id="coProducerButton"
        class="list-principal-item list-group-item list-group-item-action platform-item start-button my-3 {{ in_array($routeName, ['coproducer']) ? 'active' : '' }}"
        @if ($validate) title="Verifique sua identidade para acessar."
        style="opacity: .5;" @endif>
        <span class="list-group-item-label">Área do coprodutor</span>
    </a>
    @if (in_array($routeName, ['coproducer']))
        <button type="button" data-bs-toggle="collapse" data-bs-target="#salesCoproducer" aria-expanded="false"
            aria-controls="salesCoproducer" id="salesCoproducerMenu"
            class="list-group-item xgrow-drop list-group-item-action {{ in_array($routeName, []) ? 'active' : '' }} cop-menu d-none">
            <span>
                <i class="fas fa-wallet mx-2"></i> Vendas
            </span>
            <i class="fa fa-chevron-down"></i>
        </button>
        <div class="xgrow-sidenav-collapse-body collapse {{ in_array($routeName, []) ? 'show' : '' }}  cop-menu d-none"
            id="salesCoproducer">
            <a href="javascript:void(0)" id="linkWithdraw" data-id="linkWithdraw"
                class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, []) ? 'active' : '' }}">
                Saques<i class="fa fa-chevron-right"></i>
            </a>
            <a href="javascript:void(0)" id="linkTransaction" data-id="linkTransaction"
                class="list-subgroup-item list-group-item list-group-item-action {{ in_array($routeName, []) ? 'active' : '' }}">
                Transações<i class="fa fa-chevron-right"></i>
            </a>
        </div>
    @endif

    <a href="{{ $validate ? '#' : route('affiliations') }}" id="affiliations-link"
        class="external-area list-principal-item list-group-item list-group-item-action platform-item start-button {{ in_array($routeName, $affiliationsRoutes) ? 'active' : '' }}"
        @if ($validate) title="Verifique sua identidade para acessar."
        style="opacity: .5;" @endif>
        <span class="list-group-item-label">Área do Afiliado</span>
    </a>

    @if (in_array($routeName, $affiliationsRoutes))
        <a href="/affiliations/products/resume" id="affiliate-link-1" style="display:none"
            class="list-group-item list-group-item-action">
            <i class="fa fa-pie-chart mx-2"></i> Resumo
        </a>
        <button id="affiliate-link-2" style="display:none" type="button" data-bs-toggle="collapse"
            data-bs-target="#salesCoproducer" aria-expanded="false" aria-controls="salesCoproducer"
            class="list-group-item xgrow-drop list-group-item-action cop-menu">
            <span>
                <i class="fas fa-wallet mx-2"></i> Vendas
            </span>
            <i class="fa fa-chevron-down"></i>
        </button>
        <div style="display:none" id="affiliate-link-2-content"
            class="xgrow-sidenav-collapse-body collapse show cop-menu" id="salesCoproducer">
            <a id="affiliate-link-2-withdraw" href="/affiliations/products/withdraws"
                class="list-subgroup-item list-group-item list-group-item-action ">
                Saques<i class="fa fa-chevron-right"></i>
            </a>
            <a id="affiliate-link-2-transactions" href="/affiliations/products/transactions"
                class="list-subgroup-item list-group-item list-group-item-action ">
                Transações<i class="fa fa-chevron-right"></i>
            </a>
        </div>
    @endif

    <a href="{{ route('documents') }}" id="documents-link"
        class="external-area list-principal-item list-group-item list-group-item-action platform-item start-button my-3 {{ in_array($routeName, $documentsRoutes) ? 'active' : '' }}">
        <span class="list-group-item-label">Meus dados</span>
    </a>
@endif

<style>
    .list-group-item.start-button.active {
        background: #93BC1E !important;
        border-radius: 8px !important;
        height: 48px !important;
        border-color: #93BC1E !important;
    }

    .list-group-item.start-button.active>span {
        font-weight: 700 !important;
    }

    .list-group-item.start-button {
        background: transparent !important;
        border-radius: 8px !important;
        border: 1px solid #FFFFFF !important;
        height: 48px !important;
    }

    .start-button {
        display: flex;
        align-items: center !important;
        justify-content: center !important;
    }

    .start-button:hover {
        color: #FFFFFF !important;
    }

    .list-group-item.platform-item:not(:first-child) span {
        color: #FFFFFF !important;
        font-weight: 500 !important;
    }
</style>
@if (in_array($routeName, $externalRoutes))
    <style>
        .start-button {
            width: 180px !important;
            margin-left: 10px !important;
        }

        .hidden {
            display: none !important;
        }
    </style>
@endif
