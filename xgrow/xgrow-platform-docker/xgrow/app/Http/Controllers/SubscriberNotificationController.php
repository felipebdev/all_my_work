<?php

namespace App\Http\Controllers;

use App\Mail\SendMailSubscriberNeverAccessed;
use App\Notifications\ResendAccessDataSubscriber;
use App\Notifications\SubscriberNotAccess;
use App\Services\EmailTaggedService;
use App\Subscriber;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class SubscriberNotificationController extends Controller
{
    /**
     * Notification email for users not
     * accessed the course in 3 and 7 days
     */
    public function sendNotAccessedCourseEmail()
    {
        $subscribers = DB::table('subscribers')
            ->whereNotNull('created_at')
            ->where(function ($q) {
                $q->whereNull('login');
            })
            ->get();

        foreach ($subscribers as $subscriber) {
            $createdAt = Carbon::make($subscriber->created_at)->setHour(8)->setMinute(0)->setSecond(0);
            $now = Carbon::now()->setHour(8)->setMinute(0)->setSecond(0);
            $login = $subscriber->login;
            $lastAccess = $subscriber->login;

            if (($createdAt->diffInDays($now) == 3 || $createdAt->diffInDays($now) == 7) && ($login == null || $lastAccess == null)) {
                $plan = DB::table('subscriptions')
                    ->select('plans.name as planName')
                    ->leftJoin('plans', 'subscriptions.plan_id', '=', 'plans.id')
                    ->where('subscriptions.subscriber_id', '=', $subscriber->id)->first();
                Notification::route('mail', $subscriber->email)->notify(new SubscriberNotAccess($subscriber, $plan));
            }
        }
    }

    /**
     * Confirm if user has or not problem
     * to access course
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */

    public function confirmAccessedCourseEmail(Request $request)
    {
        $email = $request->query('email');
        $res = $request->query('rp');
        $validRes = [0, 1, "0", "1", false, true];
        $subscriber = Subscriber::where('email', $email)->first();

        if (!$email || !$res || !in_array($res, $validRes) || !$subscriber) {
            return redirect('/login')->withErrors(['' => 'Erro ao validar resposta.']);
        }

        $subscriber->has_problem_access = intval($res);
        $subscriber->save();

        return view('notifications.not-access-course', ['res' => $res]);
    }

    /**
     * Notification email for users not
     * accessed the course regardless of date
     */
    public function sendNotAccessedCourseEmailFull()
    {
        try {
            $subscribers = Subscriber::query()
                ->whereNotNull('created_at')
                ->where('platform_id', '=', Auth::user()->platform_id)
                ->where('status', 'active')
                ->where(function ($q) {
                    $q->whereNull('login');
                })
                ->get();

            foreach ($subscribers as $subscriber) {
                $mail = new SendMailSubscriberNeverAccessed(Auth::user()->platform_id, $subscriber);
                EmailTaggedService::mail(Auth::user()->platform_id, 'NEVER_ACCESSED', $mail);
            }
            return response()->json(['error' => false, 'response' => 'Email enviado com sucesso.']);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()], 400);
        }
    }
}
