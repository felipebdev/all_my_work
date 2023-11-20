<form action="{{ route('user.email-support') }}" method="post">
    <div class="xgrow-card-body">
        <h3 class="mt-4">Abrir um chamado por e-mail</h3>
        <small class="mb-4" >Dica: talvez sua dúvida possa já ter sido solucionada no FAQ, dê uma olhada lá antes de enviar um ticket pro suporte.</small>
        <div class="row mt-4">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    <input autocomplete="off" type="text" spellcheck="false" id="" name="" tabindex="1"
                        value="{{ $user->name }}" required readonly>
                    <label>Nome</label>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    <input autocomplete="off" type="text" spellcheck="false" id="" name="" tabindex="2"
                        value="{{ $user->email }}" required readonly>
                    <label>Email</label>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="xgrow-form-control mui-textfield mui-textfield--float-label mb-3">
                    <select class="xgrow-select" name="reason" id="reason" tabindex="3" required>
                        <option value="2" selected>Dúvida de uso</option>
                        <option value="1">Problema técnico</option>
                        <option value="4">Plataforma fora do ar</option>
                        <option value="5">Mensagem</option>
                        <option value="6">Recuperação de senha</option>
                    </select>
                    <label for="">Razão do contato</label>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    <input autocomplete="off" type="text" spellcheck="false" id="subject" name="subject" tabindex="4"
                        value="" required>
                    <label>Assunto</label>
                </div>
            </div>
            <div class="col-12">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-1">
                    <textarea class="w-100 mui--is-empty mui--is-pristine" rows="10" maxlength="500"
                            id="message" name="message" tabindex="5" required></textarea>
                    <label for="message">Sugestão</label>
                </div>
            </div>
        </div>
    </div>
    {{ csrf_field() }}
    <div class="xgrow-card-footer">
        <button type="submit" class="xgrow-button">Enviar email</button>
    </div>
</form>