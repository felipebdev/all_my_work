<?php

namespace App\Http\Controllers\Getnet;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Services\Getnet\SaleService;
use App\PaymentCards;
use App\Course;

class SaleController extends Controller
{

    private $saleService;
    private $paymentCards;
	private $course;

    public function __construct(PaymentCards $paymentCards, Course $course)
    {
		$this->course = $course;
        $this->paymentCards = $paymentCards;
        $this->middleware(['auth']);
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            $this->saleService = new SaleService($user->platform_id);
            return $next($request);
        });
    }

    public function index()
    {

        $sales = $this->paymentCards->where('payment_cards.platform_id', Auth::user()->platform_id)
            ->join('courses', 'courses.id', '=', 'payment_cards.course_id')
            ->join('subscribers', 'subscribers.id', '=', 'payment_cards.subscriber_id')
            ->select('payment_cards.payment_id', 'courses.name AS course_name', 'subscribers.name AS subscribers_name', 'payment_cards.amount', 'payment_cards.received_at', 'payment_cards.status')
            ->get();
        
        $courses = $this->course->where('platform_id', Auth::user()->platform_id)->get();

        return view('getnet.sales.index', compact('sales', 'courses'));
    }

    public function cancelPayment($payment_id)
    {
        return $this->saleService->cancel($payment_id);
    }

}
