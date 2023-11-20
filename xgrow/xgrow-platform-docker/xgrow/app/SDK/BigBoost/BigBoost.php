<?php

namespace App\SDK\BigBoost;


use Illuminate\Support\Facades\Log;

/**
 *
 */
class BigBoost
{
    /**
     * @param $cpf
     * @return array
     */
    public function economicRelationships($cpf): array
    {
        $response = $this->generateTokenAccessBigBoost();

        $success = $response['success'] ?? false;

        if (!$success) {
            Log::error('Impossible to create BigBoost token', [
                'response' => $response,
            ]);

            throw new \Exception('Erro ao obter token BigBoost');
        }

        $token = $response['token'] ?? null;
        $params =  [
            'Datasets' => 'business_relationships',
            'q' => 'doc{' . $cpf . '}',
            'AccessToken' => $token
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://bigboost.bigdatacorp.com.br/peoplev2',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response, true);

        $ownership = $response['Result'][0]['BusinessRelationships']['BusinessRelationships'] ?? [];

        return array_filter(array_map(function ($array) {

            if ($array['RelationshipType'] === 'OWNERSHIP') {

                return $array;
            }
        }, $ownership));
    }

    /**
     * @param $cnpj
     * @return bool
     */
    public function relationshipsCnpj(string $cnpj): array
    {
        $response = $this->generateTokenAccessBigBoost();

        $success = $response['success'] ?? false;

        if (!$success) {

            Log::error('Impossible to create BigBoost token', [
                'response' => $response,
            ]);

            throw new \Exception('Erro ao obter token BigBoost');
        }

        $token = $response['token'] ?? null;
        $params =  [
            'Datasets' => 'relationships',
            'q' => 'doc{' . $cnpj . '}',
            'AccessToken' => $token
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://bigboost.bigdatacorp.com.br/companies',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response, true);

        $ownership = $response['Result'][0]['Relationships']['Relationships'] ?? [];

        return array_filter(array_map(function ($array) {

            if ( strtoupper($array['RelationshipType'])  <> 'EMPLOYEE') {

                return $array;
            }
        }, $ownership));

    }

    public function personalData($cpf): array
    {
        $response = $this->generateTokenAccessBigBoost();

        $success = $response['success'] ?? false;

        if (!$success) {
            Log::error('Impossible to create BigBoost token', [
                'response' => $response,
            ]);

            throw new \Exception('Erro ao obter token BigBoost');
        }

        $token = $response['token'] ?? null;
        $params =  [
            'Datasets' => 'basic_data',
            'q' => 'doc{' . $cpf . '}',
            'AccessToken' => $token
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://bigboost.bigdatacorp.com.br/peoplev2',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response, true);

        return $response['Result'][0]['BasicData']?? [];

    }

    /**
     * @return mixed
     */
    public function generateTokenAccessBigBoost()
    {
        $curl = curl_init();

        $credentials = [
            "login" => env('BIG_ID_USER'),
            "password" => env('BIG_ID_PASSWORD'),
            "expires" => 8750
        ];

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://bigboost.bigdatacorp.com.br/tokens/generate',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($credentials),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response, true);
    }
}
