<?php

namespace App\Jobs\Reports\Clients\Models;

use App\Repositories\ClientRepository;
use App\Services\Objects\ClientFilter;

abstract class BaseReport {
    protected $clientRepository;

    public function __construct() {
        $this->clientRepository = app()->make(ClientRepository::class);
    }

    abstract public function header();
    abstract public function rows();
    abstract public function query(ClientFilter $filters);
    abstract public function name();
}
