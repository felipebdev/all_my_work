<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PlatformUser;
use App\Plan;
use App\Template;
use App\Payment;
use App\Section;
use App\Subscriber;
use App\Author;
use App\Coupon;

class PlatformIndicatorController extends Controller
{
    public function index()
    {
        $data = [];

        $data["plans"] = Plan::count();
        $data["coupons"] = Coupon::count();
        $data["users"] = PlatformUser::count();
        $data["contents"] = Template::where('content','=',1)->count();
        $data["sections"] = Section::count();
        // $data["platforms"] = Template::where('platform','=',0)->count();
        $data["courses"] = Template::where('course','=',1)->count();
        $data["authors"] = Author::count();
        $data["subscribers"] = Subscriber::where('status','=','active')->count();
        $data["leads"] = Subscriber::where('status','=','lead')->count();
        // $data["payments"] = Payment::count();

        return view('platforms-indicators.index', $data);
    }

}
