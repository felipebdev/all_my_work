<?php

namespace App\Helpers;

use App\Services\BrasilApi\BrasilApiService;
use App\Services\SerproApi\SerproApiService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Auth;
use App\SDK\BigBoost\BigBoost;
use App\SDK\BigID\BigID;
use Illuminate\Support\Facades\Log;
use JsonException as JsonExceptionAlias;

/**
 *
 */
class BigBoostHelper
{

    /**
     * @param array $file
     * @param string $document
     * @return array
     * @throws GuzzleException
     * @throws JsonExceptionAlias
     */
    public function ocrDocument(array $file, string $document): array
    {
        $document = preg_replace("/[^0-9]/", '', $document);

        $bigID = new BigID();

        $bigIDResult = $bigID->ocrDocumentAutoDetect($file['tmp_name']);

        if (!array_key_exists("DOCTYPE", $bigIDResult['DocInfo'])) {

            Log::info('A propriedade DOCTYPE não existe ', [ 'data'=> $bigIDResult ]);

            return [
                'validate_error' => true,
                'message' => 'O documento enviado não é válido ou a imagem é de baixa qualidade.',
                'code' => 422
            ];
        }

        if (!array_key_exists("CPF", $bigIDResult['DocInfo'])) {

            Log::info('A propriedade CPF não existe ', [ 'data'=> $bigIDResult ]);

            return [
                'validate_error' => true,
                'message' => 'A imagem enviada não contém o número de CPF ou é de baixa qualidade',
                'code' => 422
            ];
        }

        if (strlen($document) === 11) {
            if ($bigIDResult['DocInfo']['CPF'] != $document) {

                Log::info('O CPF no documento não é o mesmo do cliente ', [ 'data'=> $bigIDResult ]);

                return [
                    'validate_error' => true,
                    'message' => 'O Documento ' . mask($bigIDResult['DocInfo']['CPF'], '###.###.###-##') . ' não pertence ao usuário ' . Auth::user()->name,
                    'code' => 422
                ];
            }
        } else {

            $cpf = $bigIDResult['DocInfo']['CPF'];
            $cnpj = $document;

            $validate = self::validateRelationship($cnpj, $cpf);

            $personalData = (new BigBoost())->personalData($cpf);

            if (!$validate) {
                $serproApiService = new SerproApiService();

                $validate = $serproApiService->validateRelationsSerpro(
                    preg_replace("/[^0-9]/", "", $cnpj),
                    preg_replace("/[^0-9]/", "", $cpf),
                    $personalData['Name'] ?? null
                );

                if (!$validate) {
                    return [
                        'validate_error' => true,
                        'message' => 'O CNPJ ' . mask($cnpj, '##.###.###/####-##') . ' não pertence ao CPF ' . mask($cpf, '###.###.###-##'),
                        'code' => 404
                    ];
                }
            }
        }

        return $bigIDResult;
    }

    public static function validateRelationship($cnpj, $cpf): bool
    {
        $relationships = (new BigBoost())->relationshipsCnpj($cnpj);

        foreach ($relationships as $relationship) {
            if ($relationship['RelatedEntityTaxIdNumber'] === preg_replace("/[^0-9]/", "", $cpf)) {

                return true;
            } elseif (strlen($relationship['RelatedEntityTaxIdNumber']) > 11) {
                $anotherRelationships = (new BigBoost())->relationshipsCnpj($relationship['RelatedEntityTaxIdNumber']);

                foreach ($anotherRelationships as $anotherRelationship) {
                    if ($anotherRelationship['RelatedEntityTaxIdNumber'] === preg_replace("/[^0-9]/", "", $cpf)) {

                        return true;
                    }
                }

            }
        }

        return false;
    }


    public static function document(string $file, string $document)
    {
        $document = preg_replace("/[^0-9]/", '', $document);
        $file = preg_replace("/[^0-9]/", '', $file);

        if (strlen($document) === 11) {
            if ($file != $document) {

                Log::info('O CPF no documento não é o mesmo do cliente ', [ 'data'=> $file ]);

                return [
                    'validate_error' => true,
                    'message' => 'O Documento ' . mask($file, '###.###.###-##') . ' não pertence ao usuário ' . Auth::user()->name,
                    'code' => 422
                ];
            }
        } else {
            $validate = false;

            $cpf = $file;
            $cnpj = $document;
            $validate = self::validateRelationship($cnpj, $cpf);

            $personalData = (new BigBoost())->personalData($cpf);

            if (!$validate) {
                $serproApiService = new SerproApiService();

                $validate = $serproApiService->validateRelationsSerpro(
                    preg_replace("/[^0-9]/", "", $cnpj),
                    preg_replace("/[^0-9]/", "", $cpf),
                    $personalData['Name'] ?? null
                );

                if (!$validate) {
                    return [
                        'validate_error' => true,
                        'message' => 'O CNPJ ' . mask($cnpj, '##.###.###/####-##') . ' não pertence ao CPF ' . mask($cpf, '###.###.###-##'),
                        'code' => 404
                    ];
                }
            }
        }

        dd($validate);
    }
}
