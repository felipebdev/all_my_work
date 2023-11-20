@php
    
    use Illuminate\Support\Facades\Auth;
    $routeName = Route::current()->getName();
    
    //Dashboards Routes
    $dashboardAllRoutes = ['dashboard.index', 'home'];
    
    //Products Routes
    $plansAllRoutes = ['plans.index', 'plans.create', 'plans.edit'];
    $couponsAllRoutes = ['coupons.index', 'coupons.create', 'coupons.edit'];
    $producerAllRoutes = ['producers.index', 'producers.create', 'producers.edit', 'producers.products.index'];
    $productRoutes = array_merge($plansAllRoutes, $couponsAllRoutes, $producerAllRoutes);
    $affiliationsRoutes = ['affiliations', 'affiliations.products', 'affiliations.products.resume', 'affiliations.products.transactions', 'affiliations.products.withdraws'];
    $documentsRoutes = ['documents'];
    //Learning Areas Routes
    $subscribersAllRoutes = ['subscribers.index', 'subscribers.create', 'subscribers.edit', 'subscribers.import.create', 'subscribers.export.create'];
    $sectionsAllroutes = [];
    $coursesAllRoutes = ['course.experience'];
    $forumAllRoutes = ['forum.index', 'forum.moderation', 'forum.moderation.pending', 'topic.create', 'topic.edit'];
    $contentAllRoutes = [];
    $commentAllRoutes = ['comments.index', 'comments.pedding'];
    $designAllRoutes = [];
    
    $authorsAllRoutes = ['authors.index', 'authors.create', 'authors.edit'];
    $learnigAreaIntegrationRoutes = ['learningarea.index', 'learningarea*', 'learning.area.*']; //TODO Pegar as rodas na nova LA
    $learnigAreaRoutes = array_merge($sectionsAllroutes, $coursesAllRoutes, $forumAllRoutes, $contentAllRoutes, $commentAllRoutes, $designAllRoutes, $authorsAllRoutes);
    
    /** Gamification Routes */
    $gamificationDashAllRoutes = ['gamification.index'];
    $gamificationConfigurationsAllRoutes = ['gamification.configuration'];
    $gamificationChallengesAllRoutes = ['gamification.challenges'];
    $gamificationReportsAllRoutes = ['gamification.reports'];
    $gamificationAllRoutes = array_merge($gamificationDashAllRoutes, $gamificationConfigurationsAllRoutes, $gamificationChallengesAllRoutes, $gamificationReportsAllRoutes);
    
    //Subscribers Routes
    $studentAllRoutes = ['subscribers.index', 'subscribers.create', 'subscribers.edit'];
    $studentImportAllRoutes = ['subscribers.import.create'];
    $subscribersAllRoutes = array_merge($studentAllRoutes, $studentImportAllRoutes);
    
    //Sales Routes
    $reportsFinancialAllRoutes = ['reports.financial'];
    $reportsSaleAllRoutes = ['reports.sales'];
    $reportsSubscriptionAllRoutes = ['reports.subscription'];
    $leadsAllRoutes = ['leads.index'];
    $salesAllRoutes = array_merge($reportsFinancialAllRoutes, $reportsSaleAllRoutes, $reportsSubscriptionAllRoutes, $leadsAllRoutes);
    
    //Reports Routes
    $reportsAccessAllRoutes = ['reports.access'];
    $reportsContentAllRoutes = ['reports.content'];
    $reportsContentSearchAllRoutes = ['reports.content-search'];
    $reportsCourseSearchAllRoutes = ['reports.course-search'];
    $reportsProgressAllRoutes = ['reports.progress', 'reports.simplified.progress'];
    $reportsDownloadsAllRoutes = ['reports.downloads'];
    $reportsAllRoutes = array_merge($reportsAccessAllRoutes, $reportsContentAllRoutes, $reportsContentSearchAllRoutes, $reportsCourseSearchAllRoutes, $reportsDownloadsAllRoutes, $reportsProgressAllRoutes);
    
    //Resources Routes
    $integrationRoutes = ['integracao.index', 'integracao.create', 'integracao.edit', 'getnet.sales.index'];
    $audienceAllRoutes = ['audience.index', 'audience.edit', 'audience.create'];
    $pushNotificationRoutes = ['push-notification.index'];
    $campaignAllRoutes = ['campaign.index', 'campaign.edit', 'campaign.create'];
    $engagementAllRoutes = array_merge($audienceAllRoutes, $campaignAllRoutes);
    $callcenterReportsRoutes = ['callcenter.reports', 'callcenter.reports.create', 'callcenter.reports.show', 'callcenter.reports.attendant'];
    $callcenterReportsPublicRoutes = ['callcenter.reports.public'];
    $callCenterRoutes = ['attendant.index', 'attendant.create', 'attendant.edit', 'callcenter.config', 'callcenter.reports', 'callcenter.reports.create', 'callcenter.reports.show', 'callcenter.dashboard', 'callcenter.reports.attendant', 'callcenter.reports.public'];
    $callCenterAllRoutes = array_merge($callCenterRoutes, $callcenterReportsPublicRoutes, $callcenterReportsRoutes);
    $resourcesAllRoutes = array_merge($integrationRoutes, $engagementAllRoutes, $callCenterAllRoutes, $pushNotificationRoutes);
    
    //Settings Routes
    $configAllRoutes = ['platform-profile.edit'];
    $userAllRoutes = ['platforms-users.index', 'platforms-users.edit', 'platforms-users.create'];
    $permissionAllRoutes = ['permission.index', 'permission.edit', 'permission.create'];
    $emailMessageAllRoutes = ['emails.index', 'emails.create', 'emails.edit'];
    $emailConfAllRoutes = ['emails.conf'];
    $emailRulerAllRoutes = ['ruler.index'];
    $emailsAllRoutes = array_merge($emailMessageAllRoutes, $emailConfAllRoutes, $emailRulerAllRoutes);
    $categoryAllRoutes = ['category.index', 'category.create', 'category.edit'];
    $developerAllRoutes = ['developer'];
    $settingsAllRoutes = array_merge($configAllRoutes, $userAllRoutes, $permissionAllRoutes, $emailsAllRoutes, $categoryAllRoutes, $developerAllRoutes);
    
    /** TODO TEMPORÁRIO */
    $payload = [
        'platformId' => Auth::user()->platform_id,
        'producerId' => Auth::user()->id,
        'platformName' => Auth::user()->platform->name ?? '',
        'producerName' => Auth::user()->name . ' ' . Auth::user()->surname ?? '',
    ];
    $secret = config('jwtplatform.jwt_clean_cache_la') ?? 'secret';
    $jwt = Firebase\JWT\JWT::encode($payload, $secret, 'HS256');
    $affiliateLink = "https://afiliados.xgrow.com/producer/?auth=$jwt";
@endphp

<div id="wide-list-group" class="list-group list-group-flush">
    @include('templates.xgrow.includes.menu-expanded')
    @if (isset($heightVh) and $heightVh === 1)
        <div class="w-100" style="height: 100vh;"></div>
    @endif
</div>

@if (isset($menu_collected) and $menu_collected === 1)
    <div id="responsive-list-group" class="list-group list-group-flush">
        @include('templates.xgrow.includes.menu-collected')
    </div>
@endif
