<?php

namespace App\Services\BrasilApi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\ClientException;

/**
 *
 */
class BrasilApiService
{
    /**
     * @var mixed
     */
    private $baseUrl;

    /**
     *
     */
    public function __construct()
    {
        $this->baseUrl = env('BRASIL_API_URL', 'https://brasilapi.com.br/api/cnpj/v1/');
    }

    /**
     * @param string $cnpj
     * @return bool
     * @throws GuzzleException
     */
    public function validateCnpj(string $cnpj, string $cpf): bool
    {
        $nameUser = explode(" ", strtoupper(Auth::user()->name));

        try {
            $response = $this::searchCnpj($cnpj);

            $response = json_decode($response->getBody(), true);

            if ($response['descricao_situacao_cadastral'] != 'ATIVA') {

                Log::error("A $cnpj da empresa {$response['nome_fantasia']} esta inativo.");

                return false;
            }

            if ($response['opcao_pelo_mei'] === true) {

                if (preg_match("/$cpf/i", $response['razao_social'])) {

                    return true;
                }

                $nameUser = array_map(function ($names) use ($response) {

                    if (!preg_match("/$names/i", $response['razao_social'])) {

                        return 0;
                    } else {

                        return 1;
                    }
                }, $nameUser);

                if (in_array(0, $nameUser)) {

                    return false;
                } else {

                    return true;
                }
            }

            $partners = [];

            $cpfDocuments = [];

            foreach ($response['qsa'] as $qsa) {

                $cpfDocuments[] = str_replace('*', '', $qsa['cpf_representante_legal']);

                if ($qsa['nome_representante_legal'] === '') {

                    $partners[] = $qsa['nome_socio'];
                } else {

                    $partners[] = $qsa['nome_representante_legal'];
                }
            }

            foreach ($cpfDocuments as $cpfDocument) {

                if (substr($cpf, 3, -2) === $cpfDocument) {

                    return true;
                }
            }

            $validateMembers = [];

            foreach ($partners as $partner) $validateMembers[] = array_filter(

                array_map(function ($names) use ($partner) {

                    if (preg_match("/$names/i", $partner)) {

                        return 1;
                    } else {

                        return null;
                    }
                }, $nameUser)
            );

            $validateMembers = array_values(
                array_filter(
                    array_map(
                        "unserialize",
                        array_unique(
                            array_map("serialize", $validateMembers)
                        )
                    )
                )
            );

            if (!array_key_exists(0, $validateMembers)) {

                return false;
            }

            /**
             * Compara se os nomes existem num determinado sócio do cnpj
             */
            if (sizeof($nameUser) != sizeof($validateMembers[0])) {

                return false;
            }

            return true;
        } catch (ClientException $e) {
            return false;
        }
    }

    /**
     * @param $cnpj
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function searchCnpj($cnpj): ResponseInterface
    {
        $client = new Client();

        return $client->get($this->baseUrl . $cnpj);
    }
}
