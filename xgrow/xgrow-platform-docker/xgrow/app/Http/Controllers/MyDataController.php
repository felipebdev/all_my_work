<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use App\Http\Requests\MyData\IdentityRequest;
use App\Http\Requests\MyData\AddressRequest;
use App\Http\Requests\MyData\BankDetailsRequest;
use App\Http\Requests\MyData\VerifyAutorizationToken;
use App\Http\Traits\CustomResponseTrait;
use App\Repositories\MyData\MyDataRepository;
use App\Services\MyData\MyDataService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MyDataController extends Controller
{
    use CustomResponseTrait;

    protected  $repository;
    protected  $service;

    public function __construct(
        MyDataRepository $repository,
        MyDataService $service
    )
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getAddress()
    {
        $userEmail = Auth::user()->email;

        try {
            $data = $this->repository->getAddress($userEmail);

            return response()->json([
                'error' => false,
                'data' => $data
            ]);
        } catch (Exception $e) {

            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    public function updateAddress(AddressRequest $request)
    {
        $userEmail = Auth::user()->email;

        try {
            $data = $this->repository->updateAddress($request->all(), $userEmail);

            return response()->json([
                'error' => false,
                'message' => $data['message'],
                'data' => $data['data']
            ]);
        } catch (Exception $e) {

            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    public function storeIdentity(IdentityRequest $request)
    {
        try {
            Log::info("Store Identity Request",['data',$request->all()]);

            $data = (object) $this->service->storeIdentity($request);
            return $this->customJsonResponse($data->message, $data->code, $data->response);
        } catch (\Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    public function getBankDetails()
    {
        try {
            $data = $this->repository->getBankDetails();
            $data->email = Auth::user()->email;

            return response()->json([
                'error' => false,
                'data' => $data
            ]);
        } catch (Exception $e) {

            $data = $this->repository->getBankDetailsFromClients();

            if (empty($data)) {
                return $this->customJsonResponse('Dados bancários não encontrados!', 400, []);
            }

            Log::info('getBankDetails from clients', ['BankDetails' => $data, 'UserMail' => Auth::user()->email ]);

            return response()->json([
                'error' => false,
                'data' => $data
            ]);
        }
    }

    public function sendAuthorizationToken()
    {
        try {
            $this->repository->sendAuthorizationToken();

            return response()->json([
                'error' => false,
                'data' => [
                    'message' => 'Código de verificação enviado com sucesso!'
                ]
            ]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    public function verifyAuthorizationToken(VerifyAutorizationToken $request)
    {
        try {

            $verify = $this->repository->verifyAuthorizationToken($request->input('two_factor_code'));

            if($verify){
                return response()->json([
                    'error' => false,
                    'data' => [
                        'message' => 'Código de verificação confirmado com sucesso'
                    ]
                ]);
            }

            throw new Exception('Código de verificação inválido');

        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    public function updateBankDetails(BankDetailsRequest $request)
    {
        try {
            $verify = $this->repository->verifyAuthorizationToken($request->input('two_factor_code'));
            if($verify) {
                $data = $this->repository->updateBankDetails($request->all());
                $this->repository->resetAuthorizationToken();
                return response()->json([
                    'error' => false,
                    'message' => "Dados bancários atualizados com sucesso!",
                    'data' => $data
                ]);
            }
            throw new Exception('Código de verificação inválido');

        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    public function getIdentity()
    {
        $userEmail = Auth::user()->email;

        try {
            $data = $this->repository->getIdentity($userEmail);

            return response()->json([
                'error' => false,
                'data' => $data
            ]);
        } catch (Exception $e) {

            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

}
