<a href="/user">
    <div class="user-profile">
        <!-- User profile image -->

        <div class="profile-img">
        @if(isset(Auth::user()->thumb->filename))
            <img src="{{ Auth::user()->thumb->filename }}" style="width:50px;height:50px;object-fit: cover;" alt="user" />
        @else
           <img src="{{ asset('images/profile.png') }}" style="width:50px;height:50px;object-fit: cover;" alt="user" alt="user" />
        @endif
        </div>
        <!-- User profile text-->
        <div class="profile-text"> <a href="#" class="link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
            @if(Auth::check())
                {{Auth::user()->name}}
            @endif
        <span class="caret"></span></a>
            <!-- <div class="dropdown-menu animated flipInY">
                <a href="#" class="dropdown-item"><i class="ti-user"></i> Meu perfil</a>
                <a href="#" class="dropdown-item"><i class="ti-wallet"></i> Financeiro</a>
                <a href="#" class="dropdown-item"><i class="ti-email"></i> Suporte</a>
                <div class="dropdown-divider"></div> <a href="#" class="dropdown-item"><i class="ti-settings"></i> Configurações</a>
                <div class="dropdown-divider"></div> <a href="/logout" class="dropdown-item"><i class="fa fa-power-off"></i> Sair</a>
            </div> -->
        </div>
    </div>
</a>
