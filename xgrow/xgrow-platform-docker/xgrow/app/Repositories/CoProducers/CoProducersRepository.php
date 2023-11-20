<?php

/** @noinspection ALL */

namespace App\Repositories\CoProducers;

use App\Repositories\CoProducersAffiliations\AbstractProducersRepository;
use GuzzleHttp\Exception\GuzzleException;

/**
 *
 */
class CoProducersRepository extends AbstractProducersRepository
{
    /**
     * @return string
     */
    public function getType(): string
    {
        return 'P';
    }

    /**
     * @return string
     */
    public function getActingAs(): string
    {
        return 'producer';
    }

    /**
     * @param  array  $request
     * @return mixed|null
     * @throws GuzzleException
     */
    public function report(array $request)
    {
        return $this->callFinancialApi('financial/coproducer', $request);
    }
}
