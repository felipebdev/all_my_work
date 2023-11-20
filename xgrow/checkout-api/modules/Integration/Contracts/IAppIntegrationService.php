<?php

namespace Modules\Integration\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface IAppIntegrationService
{
    /**
     * @param string $platformId
     * @return Illuminate\Database\Eloquent\array
     */
    public function all(string $platformId): array; 

    /**
     * @param array $data
     * @return Illuminate\Database\Eloquent\Model
     */
    public function store(array $data): Model;

    /**
     * @param integer $id
     * @param array $data
     * @throws Illuminate\Database\Eloquent\ModelNotFoundException
     * @return Illuminate\Database\Eloquent\Model
     */
    public function update(int $id, array $data): Model;

    /**
     * @param integer $id
     * @return mixed
     */
    public function destroy(int $id);
}
