<?php /** @noinspection ALL */

namespace App\Services\SerproApi;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SerproApiService
{

    /**
     * @param $cnpj
     * @return bool
     * @throws GuzzleException
     */
    public static function validateDocumentSerpro($cnpj)
    {
        try {

            if (env('SERPRO_VALIDATION') === 'no') {
                return true;
            }

            $authenticateSerpro = self::authenticateSerpro();

            $baseUrl = env('SERPRO_API_URL');

            $client = new Client();

            $response = $client->request(
                'GET',
                "$baseUrl/consulta-cnpj-df/v2/basica/$cnpj",
                [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer '.$authenticateSerpro['access_token']
                    ]
                ]
            );

            $statusCode = $response->getStatusCode();

            if ($statusCode != 200) {

                return false;
            }

            if (json_decode($response->getBody()->getContents(), true)['situacaoCadastral']['codigo'] === "2") {

                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $cnpj
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getCNPJInfoSerpro($cnpj)
    {
        try {

            $authenticateSerpro = self::authenticateSerpro();

            $baseUrl = env('SERPRO_API_URL');

            $client = new Client();

            $response = $client->request(
                'GET',
                "$baseUrl/consulta-cnpj-df/v2/qsa/$cnpj",
                [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer '.$authenticateSerpro['access_token']
                    ]
                ]
            );

            $statusCode = $response->getStatusCode();

            if ($statusCode != 200) {

                return [];
            }

            return json_decode($response->getBody()->getContents(), true);

        } catch (\Exception $e) {
            Log::error('Erro na validacão do CNPJ na SERPRO', [ 'message' => $e->getMessage() ]);
            return [];
        }
    }

    public static function validateRelationsSerpro($cnpj, $cpf, $ownerName = null)
    {
        $result = self::getCNPJInfoSerpro($cnpj);

        if (empty($result)) {
            return false;
        }

        if (isset($result['socios'])) {
            foreach ($result['socios'] as $socio) {
                if (isset($socio['cpf']) && $socio['cpf'] === $cpf) {
                    return true;
                } elseif(isset($socio['cnpj'])) {
                    $anotherResult = self::getCNPJInfoSerpro($cnpj);
                    foreach ($anotherResult['socios'] as $anotherSocio) {
                        if (isset($anotherSocio['cpf']) && $anotherSocio['cpf'] === $cpf) {
                            return true;
                        }
                    }
                }
            }
        } else {
            //Se for MEI
            $nameCPF = preg_replace("/[^0-9]/", '', $result['nomeEmpresarial']);

            if ( $nameCPF === $cpf) {
                return true;
            }

            //Se for Empresa individual nao é obrigatorio declarar o socio, porem tem uma regra que o ultimo do da empre deve conter o ultimo nome do dono.
            $bussinessLastName = self::getLastName($result['nomeEmpresarial']);
            $userLastName = self::getLastName($ownerName);

            if( $bussinessLastName === $userLastName ) {
                //colocar um log para que quando nao tiver relacionamento economico
                Log::info('Empresa sem relacionamento economico', ['CNPJ' => $cnpj, 'CPF' => $cpf,'Nome empresa' => $result['nomeEmpresarial'], 'Nome do Usuario' => $ownerName]);

                return true;
            }

        }

        return false;
    }

    public static function getLastName($name)
    {
        $name = strtr($name,'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ','aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
        $temp = explode(" ", $name);

        return strtoupper($temp[count($temp)-1]);
    }

    /**
     * @return mixed
     * @throws GuzzleException
     */
    protected static function authenticateSerpro()
    {
        $baseUrl = env('SERPRO_API_URL');
        $accountKey = env('SERPRO_ACCOUNT_KEY');
        $secretKey = env('SERPRO_SECRET_KEY');

        $client = new Client();

        $response = $client->request(
            'POST',
            "$baseUrl/token?grant_type=client_credentials",
            [
                'auth' => [
                    $accountKey,
                    $secretKey
                ]
            ]
        );

        return json_decode($response->getBody()->getContents(), true);
    }
}
