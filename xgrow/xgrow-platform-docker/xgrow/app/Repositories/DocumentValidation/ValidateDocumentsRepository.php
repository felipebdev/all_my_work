<?php

namespace App\Repositories\DocumentValidation;

use App\Client;
use App\Helpers\BigBoostHelper;
use App\PlatformUser;
use App\Producer;
use Exception;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class ValidateDocumentsRepository
{
    /**
     * @param Request $request
     * @param bool $saveClient
     * @return array|void
     * @throws Exception
     */
    public function validation(Request $request, $saveClient = true): array
    {
        $image = $_FILES['file'];

        $extension = pathinfo($image['name'], PATHINFO_EXTENSION);

        $image['name'] = Uuid::uuid4() . '.' . $extension;

        $client = Client::where('email', Auth::user()->email)->first();

        if (!$client) {

            return [
                'error' => true,
                'message' => 'Usuário possui cadastro incompleto!',
                'code' => 422
            ];
        }

        $document = $client->cpf ?? $client->cnpj;

        $directory = isset($client->upload_directory)
            ? $client->upload_directory
            : strtolower($client->first_name . '-' . $client->last_name);

        $upload = (object) $this->validateAndUpload($image, $directory, $document);

        if( !isset($upload->bigIDResult['error']) ) {
            $client->check_document_number = array_key_exists('CPF', $upload->bigIDResult['DocInfo'])
                ? $upload->bigIDResult['DocInfo']['CPF']
                : $client->check_document_number;
        }

        return $this->updateClientInformation($client, $upload->imageUrl);
    }

    public function validateAndUpload($image, $directory, $document) {
        //Validate document OCR
        $bigIDResult = $this->bigBoostCheckDocument($image, $document);

        //Image upload
        $imageUrl = $this->uploadDocumentImage($image, $directory);

        return compact('bigIDResult', 'imageUrl');
    }

    public function bigBoostCheckDocument($imageFile, $documentNumber) {
        $bigBoostHelper = new BigBoostHelper;
        $bigIDResult = $bigBoostHelper->ocrDocument($imageFile, $documentNumber);

        if (array_key_exists('validate_error', $bigIDResult) && $bigIDResult['validate_error'] == true) {
            Log::info('Houve um erro ao validar o documento', [$bigIDResult]);
            throw new Exception($bigIDResult['message'], $bigIDResult['code']);
        }

        return $bigIDResult;
    }

    /**
     * Upload document image
     * @param $image
     * @param Client $client
     * @return String Image URL
     */
    private function uploadDocumentImage($image, $directory) {
        $s3 = Storage::disk('documents');
        $imageContent = file_get_contents($image['tmp_name']);

        $s3->put($image['name'], $imageContent, $directory);

        return $s3->url($directory . '/' . $image['name']);

    }

    /**
     * @param  Filesystem  $s3
     * @param  string  $uploadDirectory
     * @param  string  $name
     * @param $client
     * @return array
     */
    public function updateClientInformation(Client $client, String $imageUrl): array
    {
        try {
            $client->verified = 1;
            $client->check_document_type = 1;
            $client->document_front_image_url = $imageUrl;
            $client->save();

            Producer::where('platform_user_id', PlatformUser::where('email', Auth::user()->email)->first()->id)
                ->update(['document_verified' => 1]);

            return [
                'error' => false,
                'message' => 'Documento validado com sucesso!',
                'code' => 201
            ];
        } catch (Exception $e) {

            return [
                'error' => true,
                'message' => $e->getMessage() ?? 'Falha ao realizar ação',
                'code' => 400
            ];
        }
    }
}
