<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    /**
     * @param string|int $id
     * @param array $columns
     * @throws Illuminate\Database\Eloquent\ModelNotFoundException
     * @return Illuminate\Database\Eloquent\Model
     */
    public function findById($id, array $columns = ['*']): Model;

    /**
     * @param array $data
     * @return Illuminate\Database\Eloquent\Model
     */
    public function baseCreate(array $data): Model;

    /**
     * @param integer $id
     * @param array $data
     * @throws Illuminate\Database\Eloquent\ModelNotFoundException
     * @return Illuminate\Database\Eloquent\Model
     */
    public function baseUpdate(int $id, array $data): Model;

    /**
     * @param integer $id
     * @return mixed
     */
    public function baseDelete(int $id);

    /**
     * @return Illuminate\Database\Eloquent\Model
     */ 
    public function getModel(): Model;

    /**
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function baseAll(array $columns = ['*']): Collection;

    /**
     * @param array $where
     * @param array $columns
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function baseFindWhere(array $where, array $columns = ['*']): Collection;
}
