<?php

namespace App;

class Constants
{
    const CONSTANT_INTEGRATION_HOTMART = 'HOTMART';
    const CONSTANT_INTEGRATION_SUPELOGICA='SUPERLOGICA';
    const CONSTANT_INTEGRATION_BILLSBY='BILLSBY';
    const CONSTANT_INTEGRATION_GETNET ='GETNET';
    const CONSTANT_INTEGRATION_MUNDIPAGG ='FANDONE';
    const CONSTANT_INTEGRATION_EDUZZ ='EDUZZ';
    const CONSTANT_INTEGRATION_PLX ='PLX';
    const CONSTANT_INTEGRATION_ACTIVECAMPAIGN ='ACTIVECAMPAIGN';

    static function getSellerId()
    {
        return config('getnet.' . config('app.env').'.seller_id');
    }

    static function getClientId()
    {
        return config('getnet.' . config('app.env').'.client_id');
    }

    static function getSecretId()
    {
        return config('getnet.' . config('app.env').'.secret_id');
    }

    static function getUrlApi()
    {
        return config('getnet.' . config('app.env').'.url_api');
    }

    static function getUrlCheckout()
    {
        return config('getnet.' . config('app.env').'.url_checkout');
    }

    static function getSellerIdV2()
    {

    }

    static function getKeyIntegration($integration)
    {
        switch($integration) {
            case self::CONSTANT_INTEGRATION_HOTMART:
                return 1;
                break;
            case self::CONSTANT_INTEGRATION_SUPELOGICA:
                return 2;
                break;
            case self::CONSTANT_INTEGRATION_BILLSBY:
                return 3;
                break;
            case self::CONSTANT_INTEGRATION_GETNET:
                return 4;
                break;
            case self::CONSTANT_INTEGRATION_MUNDIPAGG:
                return 5;
                break;
            case self::CONSTANT_INTEGRATION_EDUZZ:
                return 6;
            case self::CONSTANT_INTEGRATION_PLX:
                return 7;
            case self::CONSTANT_INTEGRATION_ACTIVECAMPAIGN:
                return 8;
                break;
        }
    }

    static function getNameIntegration($id_integration)
    {
        switch($id_integration) {
            case 'HOTMART':
                return ucfirst(strtolower(self::CONSTANT_INTEGRATION_HOTMART));
                break;
            case 'SUPELOGICA':
                return ucfirst(strtolower(self::CONSTANT_INTEGRATION_SUPELOGICA));
                break;
            case 'BILLSBY':
                return ucfirst(strtolower(self::CONSTANT_INTEGRATION_BILLSBY));
                break;
            case 'GETNET':
                return ucfirst(strtolower(self::CONSTANT_INTEGRATION_GETNET));
                break;
            case 'FANDONE':
                return ucfirst(strtolower(self::CONSTANT_INTEGRATION_MUNDIPAGG));
                break;
            case 'EDUZZ':
                return ucfirst(strtolower(self::CONSTANT_INTEGRATION_EDUZZ));
            case 'PLX':
                return ucfirst(strtolower(self::CONSTANT_INTEGRATION_PLX));
            case 'ACTIVECAMPAIGN':
                return ucfirst(strtolower(self::CONSTANT_INTEGRATION_ACTIVECAMPAIGN));
                break;
        }
    }


}
