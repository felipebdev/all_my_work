@php
    $routeName = Route::current()->getName();
@endphp
<div class="d-md-none d-sm-block">
    <nav id="main-nav" class="navbar navbar-expand-lg navbar-light border-bottom">
        <button class="px-2 mx-2 border-0 navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                aria-label="Toggle navigation">
            <i class="fa fa-bars" id="burguer-icon" style="font-size: 24px;"></i>
        </button>

        <img id="wide-logo-mobile" src="{{ asset('xgrow-vendor/assets/img/logo/dark.svg') }}" alt="XGrow"
             height="43px" style="width: 86px !important;"/>
        <div id="user-info" class="d-flex">
            <div class="align-self-center">
                @if (!empty(Auth::user()->platform) && $routeName !== 'choose.platform')
                    <a class="me-3 nav-eye" href="{!! Auth::user()->platform->url !!}" target="_blank">
                        <i class="fa fa-eye" style="color: var(--font-color-whited);"></i>
                    </a>
                    <a class="me-4 nav-eye" href="{{ route('choose.platform') }}" title="Plataformas">
                        <i class="fas fa-home"></i>
                    </a>
                @endif
            </div>
            <div>
                <div id="image-user-header" data-bs-toggle="dropdown" style="cursor: pointer;">
                    @if (isset(Auth::user()->thumb->filename))
                        <img src="{{ Auth::user()->thumb->filename }}" alt="user" style="object-fit: cover"/>
                    @else
                        <img src="{{ asset('images/profile.png') }}" alt="user" style="object-fit: cover"/>
                    @endif
                </div>
                <button class="xgrow-badge badge bg-secondary" aria-expanded="false" type="button"
                        id="dropdownMenuButton" data-bs-toggle="dropdown">
                    <i class="fa fa-chevron-down" aria-hidden="true" style="color: #000"></i>
                </button>
                <div id="xgrow-dropdown-menu" class="xgrow-badge-dropdown dropdown-menu"
                     aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item {{ $routeName == 'user.index' ? 'active' : '' }}" href="/user">
                        <i class="fa fa-user" aria-hidden="true"></i>Meu perfil
                    </a>
                    <a class="dropdown-item" href="#">
                        <i class="fa fa-envelope" aria-hidden="true"></i>Notificações
                    </a>
                    <a class="dropdown-item {{ $routeName == 'user.financial' ? 'active' : '' }}"
                       href="/user/financial">
                        <i class="fa fa-dollar" aria-hidden="true"></i>Financeiro
                    </a>
                    <a class="dropdown-item {{ $routeName == 'user.support' ? 'active' : '' }}" href="/user/support">
                        <i class="fa fa-cog" aria-hidden="true"></i>Suporte
                    </a>
                    <a class="dropdown-item" href="/logout">
                        <i class="fa fa-power-off" aria-hidden="true"></i>Sair da XGrow
                    </a>
                </div>
            </div>
        </div>

        <div class="collapse navbar-collapse mt-3" id="navbarNav">
            @include('templates.xgrow.includes.menu', ['heightVh' => 1])
        </div>
    </nav>

</div>
