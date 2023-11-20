<?php

namespace App;

use App\Enums\AntecipationTaxEnum;

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
    const CONSTANT_INTEGRATION_FACEBOOKPIXEL = 'FACEBOOKPIXEL';
    const CONSTANT_INTEGRATION_GOOGLEADS = 'GOOGLEADS';
    const CONSTANT_INTEGRATION_SMARTNOTAS = 'SMARTNOTAS';
    const CONSTANT_INTEGRATION_OCTADESK = 'OCTADESK';
    const CONSTANT_INTEGRATION_DIGITALMANAGERGURU = 'DIGITALMANAGERGURU';
    const CONSTANT_INTEGRATION_KAJABI = 'KAJABI';
    const CONSTANT_INTEGRATION_PANDAVIDEO = 'PANDAVIDEO';
    const CONSTANT_INTEGRATION_CADEMI = 'CADEMI';

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
            case self::CONSTANT_INTEGRATION_FACEBOOKPIXEL:
                return 9;
                break;
            case self::CONSTANT_INTEGRATION_GOOGLEADS:
                return 10;
                break;
            case self::CONSTANT_INTEGRATION_SMARTNOTAS:
                return 11;
                break;
            case self::CONSTANT_INTEGRATION_OCTADESK:
                return 12;
                break;
            case self::CONSTANT_INTEGRATION_DIGITALMANAGERGURU:
                return 13;
                break;
            case self::CONSTANT_INTEGRATION_KAJABI:
                return 14;
                break;
            case self::CONSTANT_INTEGRATION_CADEMI:
                return 15;
                break;
            case self::CONSTANT_INTEGRATION_PANDAVIDEO:
                return 16;
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
            case 'FACEBOOKPIXEL':
                return ucfirst(strtolower(self::CONSTANT_INTEGRATION_FACEBOOKPIXEL));
                break;
            case 'GOOGLEADS':
                return ucfirst(strtolower(self::CONSTANT_INTEGRATION_GOOGLEADS));
                break;
            case 'SMARTNOTAS':
                return ucfirst(strtolower(self::CONSTANT_INTEGRATION_SMARTNOTAS));
                break;
            case 'OCTADESK':
                return ucfirst(strtolower(self::CONSTANT_INTEGRATION_OCTADESK));
                break;
            case 'KAJABI':
                return ucfirst(strtolower(self::CONSTANT_INTEGRATION_KAJABI));
                break;
            case 'PANDAVIDEO':
                return ucfirst(strtolower(self::CONSTANT_INTEGRATION_PANDAVIDEO));
                break;
            case 'CADEMI':
                return ucfirst(strtolower(self::CONSTANT_INTEGRATION_CADEMI));
                break;
        }
    }

    public static function getIntegrationNameById(int $idIntegration) {
        $data = [
            1 => self::CONSTANT_INTEGRATION_HOTMART,
            2 => self::CONSTANT_INTEGRATION_SUPELOGICA,
            3 => self::CONSTANT_INTEGRATION_BILLSBY,
            4 => self::CONSTANT_INTEGRATION_GETNET,
            5 => self::CONSTANT_INTEGRATION_MUNDIPAGG,
            6 => self::CONSTANT_INTEGRATION_EDUZZ,
            7 => self::CONSTANT_INTEGRATION_PLX,
            8 => self::CONSTANT_INTEGRATION_ACTIVECAMPAIGN,
            9 => self::CONSTANT_INTEGRATION_FACEBOOKPIXEL,
            10 => self::CONSTANT_INTEGRATION_GOOGLEADS,
            11 => self::CONSTANT_INTEGRATION_SMARTNOTAS,
            12 => self::CONSTANT_INTEGRATION_OCTADESK,
            13 => self::CONSTANT_INTEGRATION_DIGITALMANAGERGURU,
            14 => self::CONSTANT_INTEGRATION_KAJABI,
            15 => self::CONSTANT_INTEGRATION_CADEMI,
            16 => self::CONSTANT_INTEGRATION_PANDAVIDEO,
        ];

        return $data[$idIntegration];
    }

    public static function getAntecipationTax($installments) {
        $data = [
            1 => AntecipationTaxEnum::INSTALLMENTS_1,
            2 => AntecipationTaxEnum::INSTALLMENTS_2,
            3 => AntecipationTaxEnum::INSTALLMENTS_3,
            4 => AntecipationTaxEnum::INSTALLMENTS_4,
            5 => AntecipationTaxEnum::INSTALLMENTS_5,
            6 => AntecipationTaxEnum::INSTALLMENTS_6,
            7 => AntecipationTaxEnum::INSTALLMENTS_7,
            8 => AntecipationTaxEnum::INSTALLMENTS_8,
            9 => AntecipationTaxEnum::INSTALLMENTS_9,
            10 => AntecipationTaxEnum::INSTALLMENTS_10,
            11 => AntecipationTaxEnum::INSTALLMENTS_11,
            12 => AntecipationTaxEnum::INSTALLMENTS_12,
        ];

        return $data[$installments];
    }
}
