<?php

namespace App\Notifications;

use App\PlatformUser;
use Carbon\Carbon;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\DB;

class ResetPasswordNotification extends ResetPassword
{
    /**
     * Build the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        $user = PlatformUser::where('email', request()->email)->first();

        DB::table('password_resets')->where(['email'=>request()->email])->delete();

        DB::table('password_resets')->insert([
            'email' => request()->email,
            'token' => bcrypt($this->token),
            'created_at' => Carbon::now()
        ]);

        return (new MailMessage)
            ->view('emails.password-recovery-platform', ['token' => $this->token, 'name' => $user->name])
            ->subject('Recuperação de senha');
    }
}
