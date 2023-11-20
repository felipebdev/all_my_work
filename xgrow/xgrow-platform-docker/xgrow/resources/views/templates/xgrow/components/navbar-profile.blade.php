<li class="nav-item dropdown">
    <a id="img-profile-link" class="nav-link  text-muted waves-effect waves-dark" href="#"  >
        @if(isset(Auth::user()->thumb->filename))
            <img src="{{ Auth::user()->thumb->filename }}" alt="user" style="height:30px;" class="profile-pic" />
        @else
            <img src="{{ asset('images/profile.png') }}" class="profile-pic" />
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-right"  id="dropdown-user-profile">
        <ul class="dropdown-user">

            <li><a href="/user"><i class="ti-user"></i> Meu perfil</a></li>
            <li><a href="#"><i class="ti-wallet"></i> Financeiro</a></li>
            <li><a href="#" data-toggle="modal" data-target="#suportModal"><i class="ti-email"></i> Suporte</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="/platform-config"><i class="ti-settings"></i> Configurações Plataforma</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="/logout"><i class="fa fa-power-off"></i> Sair</a></li>
        </ul>
    </div>
</li>

@push('after-scripts')
    <script type="text/javascript">
        function authors()
        {
            $('#myModal').modal('show');
        }
    </script>
@endpush
