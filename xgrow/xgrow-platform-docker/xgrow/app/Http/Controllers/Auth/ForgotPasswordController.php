<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    protected function broker()
    {
        return Password::broker(request()->get('user_type'));
    }

    public function showLinkRequestForm()
    {
        $view = 'auth.passwords.recovery';
        if(request()->user_type == 'subscribers'){
            $view = "subscribers." . $view;
        }
        return view($view)->with('user_type', request()->user_type);
    }
}
