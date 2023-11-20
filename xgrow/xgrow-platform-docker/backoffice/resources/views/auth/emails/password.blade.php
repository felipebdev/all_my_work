<p>Hello!</p>
<p>You are receiving this email because we received a password reset request for your account.</p>
<p>
	<a href="{{ url('/password/reset/' . $token) }}" class="btn btn-primary">Reset Password</a>
</p>
<p>
	This password reset link will expire in 60 minutes.
</p>
<p>
	If you did not request a password reset, no further action is required.
</p>
<p>
	Regards, <br />
	Laravel
</p>