<!-- ============================================================== -->
<!-- Topbar header - style you can find in pages.scss -->
<!-- ============================================================== -->
<header class="topbar" style="background:#062d58;">
    <nav class="navbar top-navbar navbar-expand-md navbar-light">
        <!-- ============================================================== -->
        <!-- Logo -->
        <!-- ============================================================== -->
        <div class="navbar-inner">
            <!--<a class="navbar-brand" href="index.html">-->
            <!-- Logo icon -->

            <!--You can put here icon as well // <i class="wi wi-sunset"></i> //-->
            <!-- Dark Logo icon -->
            <img src="/images/logo_wide_darkmode.svg" class="img-fluid" alt="logo <?= env('APP_NAME') ?>" />

            <!--End Logo icon -->
            <!-- Logo text -->
            <span>

        </div>
        <!-- ============================================================== -->
        <!-- End Logo -->
        <!-- ============================================================== -->
        <div class="navbar-collapse">
            <!-- ============================================================== -->
            <!-- toggle and nav items -->
            <!-- ============================================================== -->
            <ul class="navbar-nav mr-auto mt-md-0 ">
                <li class="nav-item"> <a class="nav-link nav-toggler hidden-md-up text-muted waves-effect waves-dark"
                        href="javascript:void(0)"><i class="ti-menu"></i></a> </li>
                <li class="nav-item"> <a
                        class="nav-link sidebartoggler hidden-sm-down text-muted waves-effect waves-dark"
                        href="javascript:void(0)"><i class="icon-arrow-left-circle"></i></a> </li>
            </ul>
            <!-- ============================================================== -->
            <!-- User profile and search -->
            <!-- ============================================================== -->
            <ul class="navbar-nav my-lg-0">
                <!-- ============================================================== -->
                <!-- Search -->
                <!-- ============================================================== -->
                @includeWhen(true, 'templates.application.components.navbar-search')
                <!-- ============================================================== -->
                <!-- End Search -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Profile -->
                <!-- ============================================================== -->
                @includeWhen(true, 'templates.application.components.navbar-profile')
                <!-- ============================================================== -->
                <!-- End Profile -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Language -->
                <!-- ============================================================== -->
                @includeWhen(true, 'templates.application.components.navbar-lang')
                <!-- ============================================================== -->
                <!-- End Language -->
                <!-- ============================================================== -->
            </ul>
        </div>
    </nav>
</header>
<!-- ============================================================== -->
<!-- End Topbar header -->
<!-- ============================================================== -->
