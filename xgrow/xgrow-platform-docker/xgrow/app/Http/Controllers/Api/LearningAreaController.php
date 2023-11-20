<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\CustomResponseTrait;
use App\Services\LA\ContentAPIService;
use App\Services\LA\LaProducerBaseService;
use App\Services\Storage\UploadedImage;
use App\Subscriber;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LearningAreaController extends Controller
{
    use CustomResponseTrait;

    /**
     * Start Page
     */
    public function index()
    {
        $this->producerAccess();
        return view('learning-area.index');
    }

    /**
     * HINT :: This route generate the LA Token for consuming on frontend
     * without necessary LA Backend on Platform (transparent mode)
     */
    public function getAccess(LaProducerBaseService $service): JsonResponse
    {
        try {
            $token = $service->generateToken(Auth::user()->platform_id, strval(Auth::user()->id));
            return $this->customJsonResponse('', 200, ['atx' => $token, 'url' => config('learningarea.url_config_homolog')]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * This route generate the LA Token for consuming on frontend with GraphQL
     * @return void
     */
    public function producerAccess(): void
    {
        $service = new ContentAPIService();
        $token = $service->generateToken(Auth::user()->platform_id, strval(Auth::user()->id));
        $tokenNoHash = $token;
        $token = 'XpTDm' . base64_encode($token);
        $url = 'TpBDg' . base64_encode(config('learningarea.url'));
        $dsn = 'ZpYDy' . base64_encode(config('learningarea.sentry_dsn'));
        $dsnEnv = 'Fp7De' . base64_encode(config('learningarea.sentry_environment'));

        setcookie('content.token', $tokenNoHash, time() + 60 * 2, '', '', true);
        setcookie('auth.uuid', json_encode(['atx' => $token, 'url' => $url, 'dsn' => $dsn, 'env' => $dsnEnv]));
    }

    /**
     * Upload image for Frontend
     * @param Request $request
     * @return JsonResponse|void
     */
    public function uploadImage(Request $request)
    {
        try {
            if ($request->hasFile('image')) {
                $uploadImage = new UploadedImage(Auth::user()->platformId, $request->file('image'), Storage::disk('images'));
                $stored = $uploadImage->store();
                return $this->customJsonResponse('', 200, ['file' => $stored->converted]);
            }
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * Design / Onboard
     * Update first steps on LA
     */
    public function updateOnboard(Request $request): JsonResponse
    {
        try {
            $data = $this->firstStepService->updateFirstStep($request->all());
            return $this->customJsonResponse('', 200, [$data]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * Get Subscriber data
     * Used because the LA don't send subscriber data
     */
    public function getSubscriberInfo(Request $request)
    {
        try {
            $userList = $request->input('userList') ?? [];
            $subscribers = Subscriber::select(['id', 'email', 'name'])->whereIn('id', $userList)->get();
            $data = count(collect($subscribers)->unique()) > 0 ? $subscribers->toArray() : [];

            return $this->customJsonResponse('', 200, $data ?? []);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }
}
