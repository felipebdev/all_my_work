@push('after-scripts')
    <script>
        $(document).ready(function () {
            changeLogo();

            $('#img-profile-link').click(function () {
                $('#dropdown-user-profile').toggle();
            });

            $(window).on('resize', () => {
                changeLogo();
            });

            $('.sidebartoggler').on('click', () => {
                changeLogo();
            });
        });

        function changeLogo() {

            if ($('.custom-header').width() < 65) {
                $('#logo-img').attr('src', '/images/xgrow-icon.png');
                $('#logo-img').removeClass().addClass('xgrow-icon');
            } else {
                $('#logo-img').attr('src', '/images/xgrow-logo.png');
                $('#logo-img').removeClass().addClass('xgrow-logo');
            }
        }
    </script>
@endpush

@push('after-styles')
    <style>
        .custom-header {
            display: flex;
            height: 70px;
            align-items: center;
            justify-content: center;
        }

        .xgrow-icon {
            height: 48px;
            width: 48px;
        }

        .xgrow-logo {
            height: 82px;
        }
    </style>

@endpush

<!-- ============================================================== -->
<!-- Topbar header - style you can find in pages.scss -->
<!-- ============================================================== -->
<header class="topbar">
    <nav class="navbar top-navbar navbar-expand-md navbar-light">
        <!-- ============================================================== -->
        <!-- Logo -->
        <!-- ============================================================== -->
        <div class="navbar-header custom-header">
            <a class="navbar-brand" href="/dashboard">
                <img src="" id="logo-img">
            </a>
        </div>
        <!-- ============================================================== -->
        <!-- End Logo -->
        <!-- ============================================================== -->
        <div class="navbar-collapse">
            <!-- ============================================================== -->
            <!-- toggle and nav items -->
            <!-- ============================================================== -->
            <ul class="navbar-nav mr-auto mt-md-0 ">
                <!-- This is  -->
                @if(true)
                    <li class="nav-item"><a class="nav-link nav-toggler hidden-md-up text-muted waves-effect waves-dark"
                                            href="javascript:void(0)"><i class="ti-menu"></i></a></li>
                    <li class="nav-item"><a
                            class="nav-link sidebartoggler hidden-sm-down text-muted waves-effect waves-dark"
                            href="javascript:void(0)"><i class="icon-arrow-left-circle"></i></a></li>
            @endif

            <!-- ============================================================== -->
                <!-- Comment -->
                <!-- ============================================================== -->
            {{--@includeWhen(true, 'templates.application.components.navbar-comments')--}}
            <!-- ============================================================== -->
                <!-- End Comment -->
                <!-- ============================================================== -->

                <!-- ============================================================== -->
                <!-- Messages -->
                <!-- ============================================================== -->
            @includeWhen(true, 'templates.application.components.navbar-messages')
            <!-- ============================================================== -->
                <!-- End Messages -->
                <!-- ============================================================== -->

                <!-- ============================================================== -->
                <!-- Megamenu -->
                <!-- ============================================================== -->
            @includeWhen(true, 'templates.application.components.navbar-megamenu')
            <!-- ============================================================== -->
                <!-- End Megamenu -->
                <!-- ============================================================== -->
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
