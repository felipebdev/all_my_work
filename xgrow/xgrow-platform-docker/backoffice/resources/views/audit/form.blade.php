<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="user-customer_id">Plataforma</label>
            <select class="form-control multiple-select" name="user-platform_id[]" required multiple>
                @foreach ($platforms as $platform)
                        <option value="{{ $platform->id }}"
                            {{ (isset($user) && $user->platforms->contains('id', $platform->id)) || in_array($platform->id, old('user-platform_id', [])) ? 'selected' : '' }}>
                            {{ $platform->name }}
                        </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
             <input name="user_active" id="user_active" type="checkbox" {{ $user->active == 1 ? "checked='checked'": ""}}>
             <label for="user_active">Ativo</label>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="user_name">Nome</label>
            <input type="text" class="form-control" id="user_name" name="user_name"
                value="{{$user->name ?? old('user_name')}}" required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="user_email">Email</label>
            <input type="email" class="form-control" id="user_email" name="user_email"
                value="{{$user->email ?? old('user_email')}}" required>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="user_password">Senha</label>
            <input type="password" class="form-control" id="user_password" name="user_password" value=""
            @if (isset($type) && $type == 'create')
            required
            @endif >
                @if (isset($type) && $type == 'edit')
                <small>Para manter a mesma senha, deixe estes dois campos vazios!</small>
                @endif
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="user_password-repeat">Repita a senha</label>
            <input type="password" class="form-control" id="user_password-repeat" value=""
            @if (isset($type) && $type == 'create')
            required
            @endif >
        </div>
    </div>
</div>
