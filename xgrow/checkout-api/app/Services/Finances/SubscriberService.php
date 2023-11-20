<?php


namespace App\Services\Finances;


use App\Subscriber;
use App\Utils\TriggerIntegrationJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SubscriberService
{
    use TriggerIntegrationJob;

    public function saveSubscriberData(Request $data): Subscriber
    {
        $subscriber = Subscriber::firstOrNew(
            [
                'platform_id' => $data->platform_id,
                'email' => $data->email
            ]
        );

        $subscriber->name = $data->name;
        $subscriber->email = $data->email;
        if (strlen($data->password) > 0) {
            $subscriber->password = Hash::make($data->password);
        }
        $subscriber->main_phone = $data->main_phone;
        $subscriber->address_zipcode = normalizeZipCode($data->address_zipcode ?? '', $data->country);
        $subscriber->address_city = $data->address_city;
        $subscriber->address_district = $data->address_district;
        $subscriber->address_street = $data->address_street;
        $subscriber->address_number = $data->address_number;
        $subscriber->address_comp = $data->address_comp;
        $subscriber->platform_id = $data->platform_id;
        $subscriber->plan_id = $data->plan_id;
        $subscriber->address_state = $data->address_state;
        $subscriber->source_register = Subscriber::SOURCE_CHECKOUT;
        $subscriber->cel_phone = '('.$data->phone_area_code.') '.$data->phone_number;
        $subscriber->phone_country_code = $data->phone_country_code;
        $subscriber->phone_area_code = $data->phone_area_code;
        $subscriber->phone_number = $data->phone_number;

        $documentType = $this->mapToSubscriberDocumentType($data->document_type);

        $subscriber->document_number = $data->document_number ?? null;
        $subscriber->document_type = $documentType;

        if (in_array($documentType, $this->getBrazilianTypes())) {
            $subscriber->address_country = "BRA";

            $subscriber->type = $documentType == Subscriber::DOCUMENT_TYPE_CNPJ
                ? Subscriber::TYPE_LEGAL_PERSON
                : Subscriber::TYPE_NATURAL_PERSON;

        } elseif (in_array($documentType, $this->getForeignerTypes())) {
            $subscriber->address_country = Subscriber::converCountryCode($data->country);

            $subscriber->type = $documentType == Subscriber::DOCUMENT_TYPE_OTHER_LEGAL
                ? Subscriber::TYPE_LEGAL_PERSON
                : Subscriber::TYPE_NATURAL_PERSON;

            $subscriber->tax_id_number = $data->document_number;
        }

        if (!isset($subscriber->status)) {
            $subscriber->status = Subscriber::STATUS_LEAD;
            $this->triggerLeadCreatedEvent($subscriber);
        }

        $subscriber->save();

        return $subscriber;
    }

    private function mapToSubscriberDocumentType(?string $type): ?string
    {
        $types = [
            '' => null, // nullable
            'cpf' => Subscriber::DOCUMENT_TYPE_CPF,
            'cnpj' => Subscriber::DOCUMENT_TYPE_CNPJ,
            'passport' => Subscriber::DOCUMENT_TYPE_PASSPORT,
            'other_natural' => Subscriber::DOCUMENT_TYPE_OTHER_NATURAL,
            'other_legal' => Subscriber::DOCUMENT_TYPE_OTHER_LEGAL,
        ];

        return $types[$type] ?? null;
    }

    private function getAcceptableDocumentTypes(): array
    {
        return [
            'brazilian' => [
                'natural' => [
                    Subscriber::DOCUMENT_TYPE_CPF
                ],
                'legal' => [
                    Subscriber::DOCUMENT_TYPE_CNPJ
                ]
            ],
            'foreigner' => [
                'natural' => [
                    Subscriber::DOCUMENT_TYPE_PASSPORT,
                    Subscriber::DOCUMENT_TYPE_OTHER_NATURAL,
                ],
                'legal' => [
                    Subscriber::DOCUMENT_TYPE_OTHER_LEGAL,
                ]
            ]
        ];
    }

    private function getBrazilianTypes(): array
    {
        $documentTypes = $this->getAcceptableDocumentTypes();
        return array_merge($documentTypes['brazilian']['natural'], $documentTypes['brazilian']['legal']);
    }

    private function getForeignerTypes(): array
    {
        $documentTypes = $this->getAcceptableDocumentTypes();
        return array_merge(
            $documentTypes['foreigner']['natural'],
            $documentTypes['foreigner']['legal'],
            [null]
        );
    }

}
