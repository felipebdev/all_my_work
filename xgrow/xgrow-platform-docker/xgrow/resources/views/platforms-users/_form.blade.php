<div class="col-lg-8 col-md-8 col-sm-12">
    <div class="row">
        <p class="mb-3">Dados pessoais</p>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                <input autocomplete="off" type="text" spellcheck="false" id="user_name" name="user_name" tabindex="1"
                    value="{{ $user->name ?? '' }}" required>
                <label>Nome</label>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                <input autocomplete="off" type="text" spellcheck="false" id="user_surname" name="user_surname"
                    tabindex="2" value="{{ $user->surname ?? '' }}">
                <label>Sobrenome</label>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                <input autocomplete="off" type="text" spellcheck="false" id="user_email" name="user_email" tabindex="3"
                    value="{{ $user->email ?? '' }}" required>
                <label>Email</label>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                <input autocomplete="off" type="text" spellcheck="false" id="user_displayname" name="user_displayname"
                    tabindex="4" value="{{ $user->display_name ?? '' }}">
                <label>Nome para exibição</label>
            </div>
        </div>
    </div>

    <div class="row">
        <p class="mb-3">Alterar senha <small>(para manter a mesma senha, deixe estes dois campos vazios)</small></p>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                <input autocomplete="off" type="password" spellcheck="false" id="user_password" name="user_password"
                    tabindex="5">
                <label>Senha</label>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                <input autocomplete="off" type="password" spellcheck="false" id="user_confirmpassword"
                    name="user_confirmpassword" tabindex="6">
                <label>Confirmar senha</label>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="password-policies">
                <div class="policy-length">
                    5 caracteres.
                </div>
                <div class="policy-number">
                    Contém números.
                </div>
                <div class="policy-letter">
                    Contém letras.
                </div>
                <div class="policy-special">
                    Contém caracteres especiais.
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <p class="mb-3">Redes sociais</p>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                <input autocomplete="off" type="text" spellcheck="false" id="whatsapp" name="whatsapp" tabindex="7"
                    value="{{ $user->whatsapp ?? '' }}">
                <label>Whatsapp</label>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                <input autocomplete="off" type="text" spellcheck="false" id="instagram" name="instagram" tabindex="8"
                    value="{{ $user->instagram ?? '' }}">
                <label>Instagram</label>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                <input autocomplete="off" type="text" spellcheck="false" id="linkedin" name="linkedin" tabindex="9"
                    value="{{ $user->linkedin ?? '' }}">
                <label>Linkedin</label>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                <input autocomplete="off" type="text" spellcheck="false" id="facebook" name="facebook" tabindex="10"
                    value="{{ $user->facebook ?? '' }}">
                <label>Facebook</label>
            </div>
        </div>

        <div class="row mb-3">
            {{-- Uncomment when light theme is done --}}
            {{-- <p class="mb-3">Tema do sistema</p>
            <div class="col-sm-12 pe-0 ps-">
                <div class="container-switch">
                    <label class="switcher">
                        <input type="checkbox" id="theme-slider" name="theme-switch" onchange="toggleTheme()">
                        <div>
                            <i class="fas fa-sun"></i>
                            <i class="fas fa-arrow-right arrow"></i>
                            <i class="fas fa-moon"></i>
                        </div>
                    </label>
                </div>
            </div> --}}

            {{-- <button class="xgrow-button" id="theme-switch" type="button" onclick="toggleTheme()">
                <img src="https://i.ibb.co/FxzBYR9/night.png" alt="">
                <span class="">Mudar tema do sistema</span>
            </button> --}}
            {{-- <p>Trocar tema da plataforma</p>
            <label id="theme-switch" class="xgrow-switch-theme">
                <input class="xgrow-switch-theme" id="theme-slider-pc" type="checkbox" onchange="toggleTheme()">
                <span class="xgrow-switch-slider round"></span>
            </label> --}}
        </div>
    </div>
</div>

<div class="col-lg-4 col-md-4 col-sm-12">
    <div class="row">
        <p class="mb-3">Foto de perfil</p>
        <div class="d-flex flex-column justify-content-center align-items-center">
            {!! UpImage::getImageTag($user, 'thumb', null, 'img_profile') !!}<br>
            {!! UpImage::getUploadButton('thumb', 'btn btn-themecolor', 'Upload', 'gallery,unsplash') !!}
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-6 d-flex flex-column flex-nowrap align-items-center text-center mb-5">
            <h5><i class="fas fa-video"></i>&nbsp; 60h</h5>
            <div class="xgrow-medium-italic">
                <p>Aulas cadastradas na plataforma</p>
            </div>
        </div>

        <div class="col-6 d-flex flex-column flex-nowrap align-items-center text-center">
            <h5><i class="fas fa-chart-line"></i>&nbsp; 400h</h5>
            <div class="xgrow-medium-italic">
                <p>Ativo na plataforma</p>
            </div>
        </div>
    </div>
</div>
