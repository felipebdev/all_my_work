<?php

namespace App\Http\Controllers;

use App\Client;
use App\Platform;
use App\SDK\BigID\BigID;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Webpatser\Uuid\Uuid;
use Illuminate\Support\Facades\Storage;
use App\Data\Validator;
use Respect\Validation\Validator as V;

/**
 *
 */
class ProfileController extends Controller
{
    /**
     * @var array
     */
    public const DOCUMENT_TYPES = ['RG' => 1, 'CNH' => 2, 'NEWRG' => 3];

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function read(Request $request): array
    {
        $client = Client::where('email', Auth::user()->email)->first();

        return ['user' => $client];
    }

    /**
     * Index method
     */
    public function index()
    {
        $platforms = Auth::user()->platforms;
        $client = Client::where('email', Auth::user()->email)->first();

        $this->prepare();

        env('APP_ENV') !== 'production'
            ? $permissionToCreatePlatform = !empty($client)
            : $permissionToCreatePlatform = Client::isUserAClient();

        return view('platforms.index', compact(['platforms', 'permissionToCreatePlatform']));
    }

    private function prepare()
    {
        $client = Client::where('email', Auth::user()->email)->first();
        if (!isset($client->upload_directory)) $client->upload_directory = Uuid::generate()->string . '_' . $client->id;
        return $client;
    }

    /**
     * Create method
     */
    public function create()
    {
    }

    /**
     * @param $id
     */
    public function edit($id)
    {
    }

    /**
     * @param \Illuminate\Http\Request $request
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $client = Client::where('email', Auth::user()->email)->first();

        $this->prepare();

        $validation = [];

        $onlyNumbersRegexp = '/[^0-9]/';

        if ($input['fantasy-name']) $client->fantasy_name = $input['fantasy-name'];
        else $validation['fantasy-name'] = V::exists()->validate($input['fantasy-name']);

        if ($input['phone']) $client->phone_number = preg_replace($onlyNumbersRegexp, '', $input['phone']);
        else $validation['phone'] = V::exists()->validate($input['phone']);

        if ($input['phone2']) $client->phone2 = preg_replace($onlyNumbersRegexp, '', $input['phone2']);

        if ($input['cnpj']) {
            $input['cnpj'] = (int)preg_replace($onlyNumbersRegexp, '', $input['cnpj']);
            $validation['cnpj'] = V::cnpj()->validate($input['cnpj']);
            $client->cnpj = $input['cnpj'];
        }

        if ($input['cpf']) {
            $input['cpf'] = (int)preg_replace($onlyNumbersRegexp, '', $input['cpf']);
            $validation['cpf'] = V::cpf()->validate($input['cpf']);
            $client->cpf = $input['cpf'];
        }

        if ($input['bank']) $client->bank = $input['bank'];
        else $validation['bank'] = V::exists()->validate($input['bank']);

        if ($input['account-type']) $client->account_type = $input['account-type'];
        else $validation['account-type'] = V::exists()->validate($input['account-type']);

        if ($input['agency']) $client->branch = preg_replace($onlyNumbersRegexp, '', $input['agency']);
        else $validation['agency'] = V::exists()->validate($input['agency']);

        if ($input['account']) $client->account = preg_replace($onlyNumbersRegexp, '', $input['account']);
        else $validation['account'] = V::exists()->validate($input['account']);

        if ($input['cep']) {
            $input['cep'] = (int)preg_replace($onlyNumbersRegexp, '', $input['cep']);
            $validation['cep'] = V::intType()->length(8, 8)->validate($input['cep']);
            $client->zipcode = $input['cep'];
        }

        if ($input['address']) $client->address = $input['address'];
        else $validation['address'] = V::exists()->validate($input['address']);

        if ($input['neighborhood']) $client->district = $input['neighborhood'];
        else $validation['neighborhood'] = V::exists()->validate($input['neighborhood']);

        if ($input['address-complement']) $client->complement = $input['address-complement'];
        else $validation['address-complement'] = V::exists()->validate($input['address-complement']);

        if ($input['address-number']) $client->number = $input['address-number'];
        else $validation['address-number'] = V::exists()->validate($input['address-number']);

        if ($input['city']) $client->city = $input['city'];
        else $validation['city'] = V::exists()->validate($input['city']);

        if ($input['address-state']) $client->state = $input['address-state'];
        else $validation['address-state'] = V::exists()->validate($input['address-state']);

        $isAllValid = Validator::isAllValid($validation);

        $isSaved = $client->save();

        return ['client' => $client, 'registrationComplete' => Client::isRegistrationComplete(), 'isSaved' => $isSaved, 'isAllValid' => $isAllValid, 'validation' => $validation, 'input' => $input];
    }

    public function thumbnailUpload(Request $request)
    {

    }

    public function getUpload(Request $request, string $documentFace)
    {
        return [];
    }

    public function postUpload(Request $request, string $documentFace)
    {
        $input = $request->all();
        $image = $_FILES['filepond'];
        $client = $this->prepare();
        $s3 = Storage::disk('documents');
        $bigID = new BigID();

        $bigIDResult = $bigID->ocrDocumentAutoDetect($image['tmp_name']);

        $isValidDocument = isset($bigIDResult['DocInfo']['DOCTYPE']) && ($bigIDResult['DocInfo']['DOCTYPE'] !== 'UNDEFINED');

        $isRightSide = ($documentFace === 'front' && ($bigIDResult['DocInfo']['SIDE'] ?? '') === 'A') || ($documentFace === 'back' && ($bigIDResult['DocInfo']['SIDE'] ?? '') === 'B');

        if ($isValidDocument && $isRightSide) {
            $documentType = $bigIDResult['DocInfo']['DOCTYPE'];
            $documentAllowed = in_array($documentType, self::DOCUMENT_TYPES, true);
            $documentSide = $bigIDResult['DocInfo']['SIDE'];
            $documentNumber = $bigIDResult['DocInfo']['IDENTIFICATIONNUMBER'] ?? '';
            $cpf = $bigIDResult['DocInfo']['CPF'] ?? '';

            $directory = $s3->makeDirectory($client->upload_directory);

            $imageContent = file_get_contents($image['tmp_name']);

            $s3Result = $s3->put($image['name'], $imageContent, $client->upload_directory);

            $s3ImageURL = $s3->url($client->upload_directory . '/' . $image['name']);

            $client->check_document_number = $documentNumber;
            $client->check_document_type = self::DOCUMENT_TYPES[$documentType];

            if ($documentFace === 'front') $client->document_front_image_url = $s3ImageURL;
            else $client->document_back_image_url = $s3ImageURL;

            $s3Directory = $s3->url($client->upload_directory);

            $client->save();

            return ['documentFace' => $documentFace, 'documentType' => $documentType, 'documentAllowed' => $documentAllowed, 'documentSide' => $documentSide, 'documentNumber' => $documentNumber, 'cpf' => $cpf, 'isValid' => true, 'image' => $s3ImageURL, 'registrationComplete' => Client::isRegistrationComplete()];
        }

        return ['documentFace' => $documentFace, 'isValid' => false];
    }

    public function deleteUpload(Request $request, string $documentFace)
    {
        $s3 = Storage::disk('documents');
        $client = $this->prepare();

        $s3Result = $s3->delete([$documentFace === 'front' ? $client->document_front_image_url : $client->document_back_image_url]);

        return ['result' => $s3Result];
    }

    /**
     * @param $id
     */
    public function destroy($id)
    {
    }
}
