<?php

namespace App\Http\Controllers\CoProducer;

use App\Http\Controllers\CoProducersAffiliations\CoProducersAffiliationsController;
use App\Repositories\CoProducers\CoProducersRepository;

/**
 *
 */
class CoProducerApiController extends CoProducersAffiliationsController
{
    /**
     * @param  CoProducersRepository  $coProducersRepository
     */
    public function __construct(CoProducersRepository $coProducersRepository)
    {
        parent::__construct($coProducersRepository);
    }
}
