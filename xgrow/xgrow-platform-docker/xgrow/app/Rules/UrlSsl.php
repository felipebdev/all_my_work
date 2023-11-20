<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Check url is valid and with https
 */
class UrlSsl implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_URL)) return false;
        $urlParsed = parse_url($value);
        if ($urlParsed['scheme'] !== 'https') return false;
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.url_ssl');
    }
}
