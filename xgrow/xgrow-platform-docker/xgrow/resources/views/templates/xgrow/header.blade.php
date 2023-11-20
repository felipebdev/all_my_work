<!-- ============================ HEADER, SÓ É ATIVO QUANDO O WIDTH FICA ACIMA DE 767 ============================ -->
@php
$routeName = Route::current()->getName();
$platforms = Auth::user()->platforms;
$platform = $platforms->where('id', Auth::user()->platform_id);
$platformName = '';
if ($platform->first()) {
    $platformName = $platform->first()->name ?? '';
}
@endphp

@push('jquery')
    <script>
        $(document).ready(function() {
            $('.btn-choose-platform').click(function() {
                const platform = $(this).data('platform');
                const redirect = $(this).data('redirect');
                $('#ipth-platform').val(platform);
                $('#ipth-redirect').val(redirect);
                $('#form-platforms-alt').submit();
            });
        });
    </script>
@endpush

<div class="d-md-block d-sm-none d-none">
    <nav id="main-nav" class="navbar navbar-expand-lg navbar-light flex-nowrap">
        <button class="btn-icon-burguer">
            <i class="fa fa-bars"></i>
        </button>


        <div class="d-flex w-100 justify-content-between">
            <div class="xgrow-form-control mx-4">

            </div>
            <div id="user-info" class="d-flex">
                @if (!in_array(Route::current()->getName(), $externalRoutes))

                    @if (!empty(Auth::user()->platform_id) && count($platforms) > 1)
                        <div class="align-self-center me-4">
                            <div class="dropdown">
                                <form id="form-platforms-alt" action="{{ route('store.choose.platform') }}"
                                    method="POST">
                                    <input id="ipth-platform" type="hidden" name="platform">
                                    <input id="ipth-redirect" type="hidden" name="redirect">
                                    {{ csrf_field() }}
                                </form>
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ $platformName }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1"
                                    style="overflow-y: scroll; max-height: 320px;">
                                    @foreach ($platforms as $platform)
                                        @if ($platform->id != Auth::user()->platform_id)
                                            <li>
                                                <a class="dropdown-item btn-choose-platform" href="javascript:void(0)"
                                                    data-platform="{{ $platform->id }}" data-redirect="home">
                                                    {{ $platform->name }}
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                @endif
                <div class="align-self-center">
                    <a class="me-4 nav-eye" href="{{ route('choose.platform') }}" title="Plataformas">
                        <i class="fas fa-home"></i>
                    </a>
                </div>
                <div>
                    <div id="image-user-header" data-bs-toggle="dropdown" style="cursor: pointer;">
                        @if (isset(Auth::user()->thumb->filename))
                            <img src="{{ Auth::user()->thumb->filename }}" alt="user" style="object-fit: cover" />
                        @else
                            <img src="{{ asset('images/profile.png') }}" alt="user" style="object-fit: cover" />
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
                        <a class="dropdown-item {{ $routeName == 'user.support' ? 'active' : '' }}"
                            href="/user/support">
                            <i class="fa fa-cog" aria-hidden="true"></i>Suporte
                        </a>
                        <a class="dropdown-item" href="/logout">
                            <i class="fa fa-power-off" aria-hidden="true"></i>Sair da XGrow
                        </a>
                    </div>
                </div>
                <section class="d-flex flew-row flex-wrap">
                    <div>
                        <div id="text-user-header">
                            @if (Auth::check())
                                <p>Olá {{ Auth::user()->name }}!</p>
                            @endif
                            <p>Você não tem notificações</p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <div>
            @if (!empty(Auth::user()->platform) && $routeName !== 'choose.platform')
                <a class="me-3 nav-eye" href="{!! Auth::user()->platform->url !!}" target="_blank" title="Minha plataforma">
                    <i class="fa fa-eye"></i>
                </a>
            @endif
        </div>

    </nav>
</div>
