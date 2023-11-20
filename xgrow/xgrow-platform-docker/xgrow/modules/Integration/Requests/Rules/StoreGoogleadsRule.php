<?php

namespace Modules\Integration\Requests\Rules;

use Illuminate\Validation\Rule;
use Modules\Integration\Enums\CodeEnum;
use Modules\Integration\Enums\TypeEnum;
use Symfony\Component\HttpFoundation\ParameterBag as Request;

class StoreGoogleadsRule extends BaseRule
{


    public function getRules(Request $request)
    {
        $req = $request->all();
        $req['metadata']['adsCheckoutVisit'] = array_key_exists('adsCheckoutVisit', $req['metadata']) && $req['metadata']['adsCheckoutVisit'] === 'true';
        $req['metadata']['adsSalesConversion'] = array_key_exists('adsSalesConversion', $req['metadata']) && $req['metadata']['adsSalesConversion'] === 'true';
        $request->replace($req);

        return [
            'is_active' => 'boolean',
            'description' => 'max:190',
            'code' => ['required', Rule::in([CodeEnum::GOOGLEADS])],
            'type' => ['required', Rule::in([TypeEnum::GOOGLEADS])],
            'metadata.adsId' => 'required',
            'metadata.adsConversionLabel' => 'required',
            'metadata.adsCheckoutVisit' => 'sometimes',
            'metadata.adsSalesConversion' => 'sometimes',
            'metadata.adsAllPaymentMethods' => 'sometimes',
            'metadata.adsCardPaymentMethods' => 'sometimes',
            'metadata.adsSaleRealPrice' => 'sometimes',
            'metadata.adsSaleClientPrice' => 'sometimes',
        ];
    }

    public function getAttributes()
    {
        return [
            'is_active' => 'ativo',
            'description' => 'nome da integração',
            'metadata.adsId' => 'id do pixel do Adwords',
            'metadata.adsConversionLabel' => 'label de conversão do Adwords',
        ];
    }

    public function appendData()
    {
        return [
        ];
    }
}
