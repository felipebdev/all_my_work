<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use App\Payment;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use stdClass;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

use function env;
use function response;

class PaymentStatusController extends Controller
{

    protected const CHALLENGE = 'jwt-maintenance';

    public $defaultJwtAlgorithm = 'HS256';

    private $key;

    public function __construct()
    {
        $this->key = env('JWT_MAINTENANCE');

        if (!$this->key) {
            Log::alert('JWT_MAINTENANCE env variable is not set');
        }
    }

    public function validateJwt(Request $request)
    {
        $token = $this->getToken($request);
        return $this->decodeToken($token);
    }

    protected function getToken(Request $request): string
    {
        $jwt = $request->token ?? $request->bearerToken();

        if (!$jwt) {
            throw new UnauthorizedHttpException(self::CHALLENGE, 'Token absent');
        }

        return $jwt;
    }

    protected function decodeToken(string $jwt): stdClass
    {
        try {
            return JWT::decode($jwt, $this->key, [$this->defaultJwtAlgorithm]);
        } catch (SignatureInvalidException $e) {
            throw new UnauthorizedHttpException(self::CHALLENGE, 'Token invalid');
        } catch (ExpiredException $e) {
            throw new UnauthorizedHttpException(self::CHALLENGE, 'Token expired');
        } catch (Exception $e) {
            throw new UnauthorizedHttpException(self::CHALLENGE, 'Unauthorized user');
        }
    }

    public function changeStatus(Request $request, $payment_id)
    {
        $jwt = $this->validateJwt($request);

        $status = $jwt->status;

        if (!in_array($status, array_keys(Payment::listStatus()))) {
            throw ValidationException::withMessages(['status' => 'Status is invalid']);
        }

        $payment = Payment::findOrFail($payment_id);

        $notes = [
            $payment->notes ?? '',
            "{$status} via API",
        ];

        $payment->status = $status;
        $payment->notes = join('; ', array_filter($notes));
        $payment->save();

        return response()->noContent();
    }


}
