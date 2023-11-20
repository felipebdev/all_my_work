<div class="xgrow-sidenav border-right" id="sidebar-wrapper">
    <div class="sidebar-heading">
        <a href="/">
        <img class="menu-toggle" id="wide-logo" src="{{ asset('xgrow-vendor/assets/img/logo/dark.svg') }}"
             alt="XGrow"/>
        <img class="menu-toggle" id="responsive-logo" src="{{ asset('xgrow-vendor/assets/img/logo/symbol.svg') }}"
             alt="XGrow"/>
        </a>
    </div>
    @include('templates.xgrow.includes.menu', ['menu_collected' => 1])
</div>
