<p>Hello!</p>
<p>You are receiving this email because we received a password reset request for your account.</p>
<p>Você recebeu esse email porque nós recebemos um requisição de redefinição de senha de sua conta.</p>
<p>
	<a href="{{ url('/password/reset/' . $token) }}" class="btn btn-primary">Redefinir senha</a>
</p>
<p>
	Esse password link de redefinição se expirará em 60 minutos.
</p>
<p>
	Se você não solicitou uma redefinição de senha, nenhuma ação adicional será necessária.
</p>
<p>
	Saudações, <br />
	{{ config('app.name') }}
</p>