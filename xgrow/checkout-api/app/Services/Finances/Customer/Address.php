<?php

namespace App\Services\Finances\Customer;

use App\Services\Finances\Objects\AddressInfo;
use Illuminate\Support\Str;
use MundiAPILib\Models\CreateAddressRequest;

use function normalizeZipCode;

class Address
{
    public static function getAddress(AddressInfo $addressInfo)
    {
        $address = new CreateAddressRequest();
        $address->zipCode = normalizeZipCode($addressInfo->getZipcode(), $addressInfo->getCountry());
        $address->city = $addressInfo->getCity();

        $address->country = (!empty($addressInfo->getCountry()) && is_string($addressInfo->getCountry()))
            ? Str::limit($addressInfo->getCountry(), 2, '')
            : $addressInfo->getCountry(); //gateway validation maximum length 2

        $address->state = (!empty($addressInfo->getState()) && is_string($addressInfo->getState()))
            ? Str::limit($addressInfo->getState(), 6, '')
            : $addressInfo->getState(); //gateway validation maximum length 6

        $address->line1 = "{$addressInfo->getNumber()}, {$addressInfo->getStreet()}, {$addressInfo->getDistrict()}";
        $address->line2 = $addressInfo->getComp();

        return $address;
    }

}
