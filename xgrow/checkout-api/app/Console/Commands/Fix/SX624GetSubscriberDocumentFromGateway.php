<?php

namespace App\Console\Commands\Fix;

use App\Services\MundipaggService;
use App\Subscriber;
use App\Utils\Formatter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use MundiAPILib\Exceptions\ErrorException;

class SX624GetSubscriberDocumentFromGateway extends Command
{
    protected $signature = 'xgrow:fix:sx624 '.
    '{--platform_id= : Restrict to single platform} '.
    '{--subscriber_id=*} ';

    protected $description = 'Get subscriber document number from Payment gateway';

    public function handle(MundipaggService $mundipaggService)
    {
        $subscribers = Subscriber::query()
            ->whereNull('document_number')
            ->when($this->option('platform_id'), function ($query, $platformId) {
                $query->where('platform_id', $platformId);
            })
            ->when($this->option('subscriber_id'), function ($query, $subscriberIds) {
                $query->whereIn('id', $subscriberIds);
            })
            ->get();

        $result = [];
        foreach ($subscribers as $subscriber) {
            try {
                $subscriberEmail = $subscriber->email ?? '';

                $info = $mundipaggService->getCustomerById($subscriber->customer_id);

                $document = $info->document ?? null;

                if (!$document) {
                    Log::warning('Customer document not found', [
                        'email' => $subscriberEmail,
                        'platform_id' => $subscriber->platform_id ?? '',
                        'customer_id' => $subscriber->customer_id ?? '',
                    ]);

                    $result[] = [$subscriberEmail, 'Customer document not found'];

                    continue;
                }

                $documentType = $this->guessDocumentType($document);

                $emptyDocumentType = is_null($subscriber->document_type);
                $documentTypeMatches = ($subscriber->document_type ?? '') === $documentType;

                if ($emptyDocumentType || $documentTypeMatches) {
                    $subscriber->update([
                        'document_number' => $document,
                        'document_type' => $documentType,
                    ]);

                    Log::warning('Customer document updated', [
                        'email' => $subscriberEmail,
                        'platform_id' => $subscriber->platform_id ?? '',
                        'customer_id' => $subscriber->customer_id ?? '',
                        'document_number' => $document,
                        'document_type' => $documentType,
                    ]);

                    $result[] = [$subscriberEmail, 'Customer document updated'];
                } else {
                    Log::warning('Customer document type mismatch', [
                        'email' => $subscriberEmail,
                        'platform_id' => $subscriber->platform_id ?? '',
                        'customer_id' => $subscriber->customer_id ?? '',
                        'document_number' => $document,
                        'document_type' => $documentType,
                        'subscriber_document_number' => $subscriber->document_number ?? '',
                        'subscriber_document_type' => $subscriber->document_number ?? '',
                    ]);

                    $result[] = [$subscriberEmail, 'Customer document type mismatch'];
                }
            } catch (ErrorException $e) {
                Log::error('Customer retrieval error', [
                    'email' => $subscriberEmail,
                    'platform_id' => $subscriber->platform_id ?? '',
                    'customer_id' => $subscriber->customer_id ?? '',
                ]);

                $result[] = [$subscriberEmail, 'Customer document type mismatch'];
            }

            $header = ['Email', 'Response'];
            $this->table($header, $result);
        }

        return self::SUCCESS;
    }

    private function guessDocumentType(string $documentNumber): ?string
    {
        $onlyDigits = Formatter::onlyDigits($documentNumber);

        if (strlen($onlyDigits) === Formatter::CNPJ_LENGTH) {
            return 'CNPJ';
        } elseif (strlen($onlyDigits) === Formatter::CPF_LENGTH) {
            return 'CPF';
        }

        return null;
    }
}
