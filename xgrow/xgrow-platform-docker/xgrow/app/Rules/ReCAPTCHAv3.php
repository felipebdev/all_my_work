<?php

namespace App\Rules;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Request;

class ReCAPTCHAv3 implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $client = new Client();
        try {
            $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => [
                    'secret' => config('recaptcha.v3.private_key'),
                    'response' => $value,
                    'remoteip' => Request::ip(),
                ],
            ]);
        } catch (BadResponseException $e) {
            return false;
        }
        return $this->getScore($response) >= config('recaptcha.v3.minimum_score');
    }

    /** Return reCAPTCHA score */
    private function getScore($response)
    {
        $body = \GuzzleHttp\json_decode($response->getBody(), true);
        return (isset($body['score'])) ? $body['score'] : null;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Failed on reCAPTCHA verification.';
    }
}
