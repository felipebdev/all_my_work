<form action="{{ route('user.email-support') }}" method="post">
    <div class="xgrow-card-body">
        <h3 class="my-4">Sugerir melhoria</h3>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    <input autocomplete="off" type="text" spellcheck="false" id="user_name" name="user_name" tabindex="1"
                        value="{{ $user->name }}" required readonly>
                    <label>Nome</label>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    <input autocomplete="off" type="text" spellcheck="false" id="user_email" name="user_email" tabindex="2"
                        value="{{ $user->email }}" required readonly>
                    <label>Email</label>
                </div>
            </div>
            <div class="col-12">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    <input autocomplete="off" type="text" spellcheck="false" id="subject" name="subject" tabindex="3"
                        value="" required>
                    <label>Assunto</label>
                </div>
            </div>
            <div class="col-12">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-1">
                    <textarea class="w-100 mui--is-empty mui--is-pristine" rows="10" maxlength="500"
                            id="message" name="message" tabindex="4" required></textarea>
                    <label for="message">Sugestão</label>
                </div>
            </div>
            <input type="hidden" name="reason" value="3">
        </div>
    </div>
    {{ csrf_field() }}
    <div class="xgrow-card-footer">
        <button type="submit" class="xgrow-button">Enviar sugestão</button>
    </div>
</form>