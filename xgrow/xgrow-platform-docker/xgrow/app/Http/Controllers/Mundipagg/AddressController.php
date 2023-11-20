<?php

namespace App\Http\Controllers\Mundipagg;

use App\Http\Controllers\Controller;
use MundiAPILib\Models\CreateAddressRequest;
use Illuminate\Support\Str;
class AddressController extends Controller
{
    public static function getAddress($data) {
        $address = new CreateAddressRequest();
        $address->zipCode = normalizeZipCode($data->address_zipcode ?? '', $data->country);
        $address->city = $data->address_city;
        $address->country = (!empty($data->country) && is_string($data->country)) ? 
            Str::limit($data->country, 2, '') : $data->country; //gateway validation maximum length 2
        $address->state = (!empty($data->address_state) && is_string($data->address_state)) ? 
            Str::limit($data->address_state, 6, '') : $data->address_state; //gateway validation maximum length 6
        $address->line1 = ( $data->address_number ?? '0' ) . ', '. ( $data->address_street ?? 'Rua não informada' ) .', '. ($data->address_district ?? 'Bairro não informado');
        $address->line2 = $data->address_comp;

        return $address;
    }

}
