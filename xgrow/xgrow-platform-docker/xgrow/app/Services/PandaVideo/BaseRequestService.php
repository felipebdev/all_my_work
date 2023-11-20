<?php

namespace App\Services\PandaVideo;

use App\Integration;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Auth;
use Psr\Http\Message\ResponseInterface;

class BaseRequestService
{
    /** Get PandaKey if user add the panda key */
    private function getPlatformPandaKey()
    {
        $res = Integration::where(['platform_id' => Auth::user()->platform_id, 'id_integration' => 'PANDAVIDEO'])->first();
        return $res ? json_decode($res->source_token)->api_key : null;
    }

    /** Formatted header for calling Panda API */
    private function getHeaders(): array
    {
        return [
            'Authorization' => $this->getPlatformPandaKey(),
            'Accept' => 'application/json'
        ];
    }

    /** Make default endpint client */
    private function makeRequest(): Client
    {
        return new Client(['base_uri' => config('app.panda_video_url')]);
    }

    /** Get end-point
     * @param $action
     * @return ResponseInterface|string
     */
    protected function get($action)
    {
        try {
            return $this->makeRequest()->get($action, ['headers' => $this->getHeaders()]);
        } catch (GuzzleException $e) {
            return $this->getErrors($e);
        }
    }

    /** Upload Video - Attention URL is different from others end-point
     * @param $action
     * @param $data
     * @return ResponseInterface|string
     */
    protected function customPost($action, $data)
    {
        $extra = [
            'Origin' => 'dashboard.pandavideo.com.br',
            'X-Requested-With', 'XMLHttpRequest'
        ];
        $headers = array_merge($extra, $this->getHeaders());
        $client = new Client(['base_uri' => 'https://uploader-production-1.pandavideo.com.br/']);
        try {
            return $client->post($action, ['headers' => $headers, 'multipart' => $data]);
        } catch (GuzzleException $e) {
            return $this->getErrors($e);
        }
    }

    /** Post Method
     * @param $action
     * @param $data
     * @return ResponseInterface|string
     */
    protected function post($action, $data)
    {
        try {
            return $this->makeRequest()->post($action, ['headers' => $this->getHeaders(), 'json' => $data]);
        } catch (GuzzleException $e) {
            return $this->getErrors($e);
        }
    }

    /** Update end-point (Not update File Video... only META Data)
     * @param $action
     * @param null $data
     * @return ResponseInterface|string
     */
    protected function put($action, $data = null)
    {
        try {
            return $this->makeRequest()->put($action, ['headers' => $this->getHeaders(), 'json' => $data]);
        } catch (GuzzleException $e) {
            return $this->getErrors($e);
        }
    }

    /** UDelete action
     * @param $action
     * @return ResponseInterface|string
     */
    protected function delete($action)
    {
        try {
            return $this->makeRequest()->delete($action, ['headers' => $this->getHeaders()]);
        } catch (GuzzleException $e) {
            return $this->getErrors($e);
        }
    }

    /** Return API Error
     * @param GuzzleException $e
     * @return string
     */
    private function getErrors(GuzzleException $e): string
    {

        /* English Messages
         * 400 - "invalid input syntax for type uuid: \"string\""
         * 403 - "Authorization header requires 'Credential' parameter. Authorization header requires 'Signature' parameter. Authorization header requires 'SignedHeaders' parameter. Authorization header requires existence of either a 'X-Amz-Date' or a 'Date' header. Authorization=string"
         * 404 - "Video with id: video_id was not found"
         * 405 - "Unexpected token \r in JSON at position 0"
         * 406 - "Validation error: Validation len on name failed (Validations errors)"
         * 500 - "Unknown errors."
         */

        if ($e->getCode() === 403) return json_encode(['error' => "Authorization não enviado junto à requisição."]);
        if ($e->getCode() === 404) return json_encode(['error' => "Vídeo não encontrado."]);
        if ($e->getCode() === 405) return json_encode(['error' => "Token inesperado em JSON na posição 0."]);
        if ($e->getCode() === 406) return json_encode(['error' => "Verifique as informações digitadas."]);
        if ($e->getCode() === 422) return json_encode(['error' => "JSON mal formatado (informações faltantes)."]);
        if ($e->getCode() === 500) return json_encode(['error' => "Erro interno do servidor."]);
        return json_encode(['error' => 'Erro ao realizar requisição. ' . $e->getMessage()]);
    }
}
