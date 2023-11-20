<?php

namespace App\Services\Subscriber;

use App\Subscriber;
use Illuminate\Validation\ValidationException;
use Respect\Validation\Rules\Cnpj;
use Respect\Validation\Rules\Cpf;

/**
 * Validates subscriber documents
 */
class DocumentAuthenticityService
{
    /**
     * @param  string  $countryCode
     * @param  string  $document
     * @return string
     * @throws \Illuminate\Validation\ValidationException
     */
    public function identifyAndValidateDocument(string $countryCode, string $document): string
    {
        $documentType = $this->identifyDocumentType($countryCode, $document);
        $isValid = $this->validateDocumentByType($documentType, $document);

        if (!$isValid) {
            throw new ValidationException('Invalid document');
        }

        return $documentType;
    }

    public function validateDocumentByType(string $documentType, string $document): bool
    {
        $documentType = strtoupper($documentType);

        if ($documentType == Subscriber::DOCUMENT_TYPE_CNPJ) {
            $validator = new Cnpj();
            return $validator->validate($document);
        }

        if ($documentType == Subscriber::DOCUMENT_TYPE_CPF) {
            $validator = new Cpf();
            return $validator->validate($document);
        }

        if ($documentType == Subscriber::DOCUMENT_TYPE_OTHER) {
            return true;
        }

        return false;
    }

    public function identifyDocumentType(string $countryCode, string $document): ?string
    {
        if ($countryCode != 'BRA') {
            return Subscriber::DOCUMENT_TYPE_OTHER;
        }

        $strlen = strlen($document);
        if ($strlen === 14) {
            return Subscriber::DOCUMENT_TYPE_CNPJ;
        } elseif ($strlen === 11) {
            return Subscriber::DOCUMENT_TYPE_CPF;
        }

        return null;
    }

}
