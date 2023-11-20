<?php

namespace App\Http\Controllers;

use App\Coupon;
use App\CouponMailing;
use App\Helpers\CollectionHelper;
use App\Http\Requests\StoreCouponMailingFileRequest;
use App\Http\Requests\StoreCouponMailingRequest;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Traits\CustomResponseTrait;
use App\Imports\MailingImport;
use App\Mail\SendMailCoupon;
use App\Plan;
use App\Services\Auth\ClientStatus;
use App\Services\EmailTaggedService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CouponController extends Controller
{

    use CustomResponseTrait;

    public function index()
    {
        $status = ClientStatus::withPlatform(Auth::user()->platform_id, Auth::user()->email);

        return view('coupons.index', [
            'clientApproved' => $status->clientApproved,
            'recipientStatusMessage' => $status->recipientStatusMessage,
            'verifyDocument' => $status->mustVerify,
        ]);
    }

    public function indexNext()
    {
        $status = ClientStatus::withPlatform(Auth::user()->platform_id, Auth::user()->email);

        return view('coupons.indexNext', [
            'verifyDocument' => $status->mustVerify,
            'recipientStatusMessage' => $status->recipientStatusMessage,
        ]);
    }

    public function getPlans()
    {
        $plans = Plan::join('products', 'plans.product_id', '=', 'products.id')
            ->where('products.platform_id', Auth::user()->platform_id)
            ->select('plans.id', 'plans.name')
            ->get();

        return $this->customJsonResponse('Dados carregados com sucesso.', Response::HTTP_OK, ['plans' => $plans]);
    }

    public function getCoupons(Request $request)
    {
        $offset = $request->input('offset') ?? 25;
        $search = $request->input('search') ?? '';

        $coupons = Coupon::join('coupon_plan', 'coupons.id', '=', 'coupon_plan.coupon_id')
            ->leftJoin('plans', 'coupon_plan.plan_id', '=','plans.id')
            ->when($request->maturity, function($query, $date)  {
                $query->whereBetween('coupons.maturity', [$date[0], $date[1]]);
            })
            ->when($request->plans_id, function($query, $plansId)  {

                $query->whereIn('plans.id', $plansId);
            })
            ->where('coupons.code', 'like', "%$search%")
            ->where('coupons.platform_id', Auth::user()->platform_id)
            ->whereNull('plans.deleted_at')
            ->groupBy('coupons.id')
            ->select('coupons.*','plans.name as plan_name', 'plans.id as plans_id')
            ->get();

        $collection = CollectionHelper::paginate($coupons, $offset);
        return $this->customJsonResponse('Dados carregados com sucesso.', Response::HTTP_OK, ['coupons' => $collection]);

    }

    public function couponsData()
    {
        $query = Coupon::with([
            "plans" => function ($query) {
                return $query->select('plans.id AS plan_id', 'plans.name AS plan_name');
            }
        ])
            ->where('platform_id', Auth::user()->platform_id);

        return datatables()
            ->eloquent($query)
            ->toJson();
    }

    public function create()
    {
        $coupon = new Coupon();
        $plans = Plan::where('platform_id', Auth::user()->platform_id)
            ->get();

        return view('coupons.create', compact('coupon', 'plans'));
    }

    public function store(StoreCouponRequest $request)
    {
        $input = $request->validated();
        $input['platform_id'] = Auth::user()->platform_id;
        $input['maturity'] = ($input['maturity'] == '') ? null : $input['maturity'];
        try {
            DB::beginTransaction();

            $coupon = Coupon::create($input);
            $coupon->plans()->sync($input['plans']);

            DB::commit();

            return redirect()->route('coupons.edit', ['id' => $coupon->id]);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput($request->input())
                ->withErrors($e->getMessage());
        }
    }

    public function edit($id, Request $request)
    {
        $coupon = Coupon::with('plans')->findOrFail($id);
        $plans = Plan::where('platform_id', Auth::user()->platform_id)
            ->get();

        return view('coupons.create', compact('coupon', 'plans'));
    }

    public function update($id, StoreCouponRequest $request)
    {
        $input = $request->validated();
        $input['maturity'] = ($input['maturity'] == '') ? null : $input['maturity'];

        try {
            DB::beginTransaction();

            $coupon = Coupon::findOrFail($id);
            $coupon->update($input);
            $coupon->plans()->sync($input['plans']);

            DB::commit();

            return redirect()->route('coupons.index');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput($request->input())
                ->withErrors($e->getMessage());
        }
    }

    public function destroy($id, Request $request)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();
        if ($request->ajax()) return response()->json(204);


        return $this->customJsonResponse('Cupom excluído com sucesso.', Response::HTTP_OK, ['coupon' => $coupon]);
    }

    public function mailingData(Coupon $coupon)
    {
        $mailings = $coupon->mailings()->get();

        return datatables()
            ->collection($mailings)
            ->toJson();
    }

    public function storeMailing(Coupon $coupon, StoreCouponMailingRequest $request)
    {
        $input = $request->validated();
        $input['coupon_id'] = $coupon->id;

        try {
            try {
                $mail = new SendMailCoupon(Auth::user()->platform_id, $coupon, $input['email'], $input['name']);
                EmailTaggedService::mail(Auth::user()->platform_id, 'COUPON', $mail);

                $input['isSent'] = true;
            } catch (Exception $e) {
            }

            CouponMailing::firstOrCreate([
                'coupon_id' => $coupon->id,
                'email' => $input['email']
            ], $input);

            if ($request->ajax()) {
                return response()->json(201);
            }

            return redirect()
                ->route('coupons.edit', ['id' => $coupon->id, 'mailingtab' => 'true'])
                ->withSuccess('Mailing criado com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput($request->input())
                ->withErrors($e->getMessage());
        }
    }

    public function storeMailingFromFile(Coupon $coupon, StoreCouponMailingFileRequest $request)
    {
        ini_set('upload_max_filesize', '41M');
        ini_set('post_max_size', '50M');

        $input = $request->validated();
        $file = $request->file('file');
        $filename = 'mailing_coupon_upload_' . $file->getFilename() . '.csv';
        $sPath = $file->storeAs('uploads', $filename, 'images');
        try {
            Excel::queueImport(
                new MailingImport(Auth::user()->platform_id, $coupon),
                $sPath,
                'images'
            )
                ->allOnConnection('redis')
                ->allOnQueue('xgrow-emails');

            return redirect()
                ->route('coupons.edit', ['id' => $coupon->id, 'mailingtab' => 'true'])
                ->withSuccess('A importação está em execução. Atualize a página em alguns instantes para visualizar a lista de e-mails atualizada.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withErrors($e->getMessage());
        }
    }

    public function resendMail(Coupon $coupon, CouponMailing $mailing)
    {
        try {
            $mail = new SendMailCoupon(Auth::user()->platform_id, $coupon, $mailing->email, $mailing->name);
            EmailTaggedService::mail(Auth::user()->platform_id, 'COUPON', $mail);

            $mailing->isSent = true;
            $mailing->save();

            return response()->json(['status' => 'success', 'message' => 'Cupom reenviado com sucesso!'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Não foi possível reenviar o cupom.'], 500);
        }
    }

    public function destroyMail(Coupon $coupon, CouponMailing $mailing)
    {
        try {
            $mailing->delete();
            return response()->json(['status' => 'success', 'message' => 'E-mail excluído com sucesso!'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Não foi possível excluir o e-mail.'], 500);
        }
    }

    public static function findCoupon($platform_id, $plan_id, $code)
    {
        $return = null;
        $coupons = Coupon::where('platform_id', '=', $platform_id)->where('code', '=', $code)->get();
        if ($coupons) {
            foreach ($coupons as $c => $coupon) {
                //Check coupon plan
                foreach ($coupon->plans as $p => $plan) {
                    if ($plan->id == $plan_id) {
                        $return = $coupon;
                    }
                }
            }
        }
        return $return;
    }

    public static function isAvailable(Coupon $coupon, $email = null)
    {
        $check = false;
        if (($coupon->usage_limit == 0 || $coupon->usage < $coupon->usage_limit) && Carbon::now()->lessThan(new Carbon($coupon->maturity))) {
            $check = true;
        }

        //Check email exists
        if ($email) {
            if ($coupon->mailings()->exists()) {
                return $coupon->mailings()->where('email', '=', $email)->exists();
            }
        }

        return $check;
    }

    /**
     * Return coupon verification if is subscription
     * @param Request $request
     * @return JsonResponse
     */
    public function verify(Request $request): JsonResponse
    {
        try {
            $plans = Plan::whereIn('id', $request->input('plans'))->get();
            $hasSubscription = false;

            foreach ($plans as $plan) {
                if ($plan->type_plan === 'R') {
                    $hasSubscription = true;
                    break;
                }
            }

            $data = ['hasSubscription' => $hasSubscription];
            return $this->customJsonResponse('Verificação realizada com sucesso.', 200, $data);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, ['response' => 'fail']);
        }
    }
}
