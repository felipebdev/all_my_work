<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Aws\Common\Exception\MultipartUploadException;
use Aws\S3\MultipartUploader;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use stdClass;

class PandaVideoService
{

    private $url;

    public function __construct()
    {
        $this->url = config('filesystems.disks.panda.url');
        $this->email = config('filesystems.disks.panda.email');
        $this->password = config('filesystems.disks.panda.password');
    }

    public function login(){
        $client = new \GuzzleHttp\Client();
        $response = $client->post(
            $this->url . '/auth/login',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'email' => $this->email,
                    'password' => $this->password,
                ],
            ]
        );
        $body = $response->getBody();
        $data = json_decode((string) $body);
        return $data;
    }

    /*
    private function logout($token){
        $client = new \GuzzleHttp\Client();
        $response = $client->post(
             $this->url . '/auth/logout',
            [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]
        );
        $body = $response->getBody();
        $data = json_decode((string) $body);
        return $data;
    }
    */

    private function getUser($token){
        $client = new \GuzzleHttp\Client();
        $response = $client->get(
             $this->url . '/auth/me',
            [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]
        );
        $body = $response->getBody();
        $data = json_decode((string) $body);
        return $data;
    }

    private function createDraftVideo($token){
        $client = new \GuzzleHttp\Client();
        $response = $client->post(
             $this->url . '/videos',
            [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'title' => 'Título',
                    'description' => 'Descrição',
                    'tags' => [],
                ],
            ]
        );
        $body = $response->getBody();
        $data = json_decode((string) $body);
        return $data;
    }

    private function videoUploadUrl($token, $id){
        $client = new \GuzzleHttp\Client();
        $response = $client->post(
             $this->url . "/videos/{$id}/upload",
            [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]
        );
        $body = $response->getBody();
        $data = json_decode((string) $body);
        return $data;
    }

    public function getVideo($token, $id){
        $client = new \GuzzleHttp\Client();
        $response = $client->get(
             $this->url . "/videos/{$id}",
            [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]
        );
        $body = $response->getBody();
        $data = json_decode((string) $body);
        return $data;
    }


    public function getDataUpload()
    {
        $auth = $this->login();
        $token = $auth->access_token;
        $id = $this->createDraftVideo($token)->id;
        $data = $this->videoUploadUrl($token, $id);

        $data->token = $token;
        $data->video_id = $id;
        $data->signature = (array) $data->signature;
        
        return $data;

    }

    public function upload($file)
    {
        $auth = $this->login();
        $token = $auth->access_token;
        $id = $this->createDraftVideo($token)->id;
        $videoup = $this->videoUploadUrl($token, $id);
        $signature = (array) $videoup->signature;

        $endpoint = $videoup->postEndpoint;


        foreach ($signature as $key => $value) {
            $postData[] = [
                'name' => $key,
                'contents' => $value,
            ];
        }
        $postData[] = [
            'name' => 'file',
            'contents' => fopen($file, 'r'),
        ];


        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $endpoint, [
            'multipart' => $postData,
        ]);

        $data['token'] = $token;
        $data['video_id'] = $id;

        return $data;

    }

}
