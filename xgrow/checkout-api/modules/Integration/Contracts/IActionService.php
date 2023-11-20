<?php

namespace Modules\Integration\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Modules\Integration\Models\Integration;

interface IActionService
{
    /**
     * @param Integration $integration
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function allByIntegration(Integration $integration): Collection;

    /**
     * @param Integration $integration
     * @param array $data
     * @return Illuminate\Database\Eloquent\Model
     */
    public function store(Integration $integration, array $data): Model;

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
