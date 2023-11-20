<?php

namespace App\Services\Producer;

use App\Exceptions\InvalidOperationException;
use App\Platform;
use App\PlatformUser;
use App\Producer;
use App\ProducerProduct;
use App\Product;
use App\Repositories\Contracts\ProducerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ProducerService
{
    private ProducerRepositoryInterface $producerRepository;

    public function __construct(ProducerRepositoryInterface $producerRepository)
    {
        $this->producerRepository = $producerRepository;
    }

    /**
     * Check business rules and stores new Producer (creates PlatformUser if necessary)
     *
     * @param  \App\PlatformUser  $currentUser  Platform owner
     * @param  string  $name  Producer's name
     * @param  string  $email  Producer's email
     * @return \App\Producer
     * @throws \App\Exceptions\InvalidOperationException
     */
    public function storeProducer(PlatformUser $currentUser, string $name, string $email): Producer
    {
        $this->validateStoreProducer($currentUser, $name, $email);

        $producerPlatformUser = $this->getOrCreatePlatformUser($email, $name);

        $producerExists = $this->isProducerOnPlatform($producerPlatformUser->id, $currentUser->platform_id);
        if ($producerExists) {
            throw new InvalidOperationException(['Coprodutor já cadastrado na plataforma']);
        }

        $producer = $this->producerRepository->baseCreate([
            'platform_id' => $currentUser->platform_id,
            'platform_user_id' => $producerPlatformUser->id,
            'accepted_terms' => true,
        ]);

        return $producer;
    }

    /**
     * @param  \App\PlatformUser  $currentUser
     * @param  string  $name
     * @param  string  $email
     * @throws \App\Exceptions\InvalidOperationException
     */
    private function validateStoreProducer(PlatformUser $currentUser, string $name, string $email)
    {
        if ($currentUser->email === $email) {
            throw new InvalidOperationException(['Usuário não pode se adicionar como coprodutor']);
        }
    }

    private function getOrCreatePlatformUser(string $email, string $name): PlatformUser
    {
        $platformUser = PlatformUser::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
            ]
        );

        return $platformUser;
    }

    /**
     * Check if PlatformUser already is registered as a Producer in the given platform
     *
     * @param  int  $platformUserId
     * @param  string  $platformId
     * @return bool true if registered, false otherwise
     */
    public function isProducerOnPlatform(int $platformUserId, string $platformId): bool
    {
        return Producer::where('platform_id', $platformId)
            ->where('platform_user_id', $platformUserId)
            ->exists();
    }

    /**
     * List all producer products allowed to a given PlatformUser
     *
     * @param  int  $platformUserId
     * @param  bool  $activeOnly
     * @param  bool  $includeExpired
     * @return \Illuminate\Database\Eloquent\Collection<Product>
     */
    public function listProductsFromProducer(
        int $platformUserId,
        bool $activeOnly = true,
        bool $includeExpired = true
    ): Collection
    {
        $producers = Producer::where('platform_user_id', $platformUserId)->get('id');
        $contracts = ProducerProduct::whereIn('producer_id', $producers)
            ->when($activeOnly, function ($q) {
                return $q->where('status', 'active');
            })
            ->when(!$includeExpired, function ($q) {
                return $q->whereRaw('(contract_limit >= CURDATE() OR contract_limit IS null)');
            })
            ->get('product_id');
        $products = Product::whereIn('id', $contracts)->get();
        return $products;
    }

    /**
     * @param  int  $platformUserId
     * @return \Illuminate\Database\Eloquent\Collection<Platform>
     */
    public function listProducerPlatforms(int $platformUserId): Collection
    {
        $platforms = Producer::where('platform_user_id', $platformUserId)
            ->distinct('platform_id')
            ->get('platform_id');

        return Platform::whereIn('id', $platforms)->get();
    }

    /**
     * List validation errors for producer's bank data
     *
     * @param  int  $producerId
     * @return array<string> Human-readable errors, empty if is ok
     */
    public function getErrorsProducerBankData(int $producerId): array
    {
        $producer = $this->producerRepository->findById($producerId);

        $errors = [];

        if (strlen($producer->holder_name ?? '') == 0) {
            $errors[] = 'Nome do titular inválido';
        }

        if (strlen($producer->document ?? '') == 0) {
            $errors[] = 'Documento inválido';
        }

        if (strlen($producer->account_type ?? '') == 0) {
            $errors[] = 'Tipo de conta inválida';
        }

        if (strlen($producer->bank ?? '') == 0) {
            $errors[] = 'Banco inválido';
        }

        if (strlen($producer->branch ?? '') == 0) {
            $errors[] = 'Agência inválida';
        }

        if (strlen($producer->account ?? '') == 0) {
            $errors[] = 'Dígito verificador de agência inválido';
        }

        if (strlen($producer->branch_check_digit ?? '') == 0) {
            $errors[] = 'Conta inválida';
        }

        if (strlen($producer->account_check_digit ?? '') == 0) {
            $errors[] = 'Dígito verificador de conta inválido';
        }

        return $errors;
    }
}
