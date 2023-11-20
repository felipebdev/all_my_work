<?php

namespace App\Jobs\Reports\Platforms\Models;

use App\Repositories\PlatformRepository;
use App\Services\Objects\PlatformFilter;

abstract class BaseReport {
    protected $platformRepository;

    public function __construct() {
        $this->platformRepository = app()->make(PlatformRepository::class);
    }

    abstract public function header();
    abstract public function rows();
    abstract public function query(PlatformFilter $filters);
    abstract public function name();
}
