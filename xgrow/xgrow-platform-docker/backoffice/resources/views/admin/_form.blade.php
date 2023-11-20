@push('after-styles')
    <link rel="stylesheet" href="{{asset('vendor/password-validator/password-validator.css')}}">
@endpush
@section('jquery')
    <script src="{{asset('vendor/password-validator/jquery-password-validator.js')}}"></script>
    <script>
        $('#user_password').passwordValidator('#user_password-repeat', true, true, true, 5);
    </script>
@endsection

@if (count($errors) > 0)
    <div class="row">
        <div class="col col-sm-6 offset-sm-3">
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        {{ $error }} <br/>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="user_name">Nome</label>
            <input type="text" class="form-control" id="user_name" name="name" autocomplete="false" autocorrect="off"
                   spellcheck="false" value="{{$data['user']['name'] ?? ''}}" required data-form-input>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="user_email">Email</label>
            <input type="email" class="form-control" id="user_email" name="email" autocomplete="false" autocorrect="off"
                   spellcheck="false" value="{{$data['user']['email'] ?? ''}}" required data-form-input>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="user_password">Senha</label>
            <input type="password" class="form-control" id="user_password" name="password" autocomplete="off"
                   @if (isset($data['type']) && $data['type'] == 'create')
                       required
                @endif >
            @if (isset($data['type']) && $data['type'] == 'edit')
                <small>Para manter a mesma senha, deixe estes dois campos vazios!</small>
            @endif
        </div>
        <div class="password-policies">
            <div class="policy-length">
                <span id="validator-lenght"></span> caracteres.
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
            <div class="policy-compare">
                As senhas não coincidem.
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="user_password-repeat">Repita a senha</label>
            <input type="password" class="form-control" id="user_password-repeat" name="password_confirm" data-form-input
                   @if (isset($data['type']) && $data['type'] == 'create')
                       required
                @endif >
        </div>
    </div>
</div>

<!--
Id_Customer*
First_name
Last_name
Compnay_name
Address_street
Adress_number
<address>
Date_created
-->
