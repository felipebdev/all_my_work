<?php

namespace App\Repositories;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected $model;

    public function __construct()
    {
        $this->makeModel();
    }

    abstract public function model();

    private function makeModel()
    {
        $model = app()->make($this->model());
        if (!$model instanceof Model) {
            throw new Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    protected function setWhere(&$query, array $where = [])
    {
        foreach ($where as $key => $value) {
            if ($key === 'raw') {
                $query->whereRaw($value);
            } else if (is_array($value)) {
                if (
                    array_key_exists('op', $value) &&
                    array_key_exists('value', $value)
                ) {
                    $query->where($key, $value['op'], $value['value']);
                } else {
                    $query->whereIn($key, $value);
                }
            } else {
                $query->where($key, '=', $value);
            }
        }
    }

    protected function setOrderBy(&$query, array $orderBy = [])
    {
        foreach ($orderBy as $value) {
            if (is_array($value)) {
                if (
                    array_key_exists('op', $value) &&
                    array_key_exists('value', $value)
                ) {
                    $query->orderBy($value['value'], $value['op']);
                }
            }
        }
    }


    /**
     * @param string|int $id
     * @param array $columns
     * @throws Illuminate\Database\Eloquent\ModelNotFoundException
     * @return Illuminate\Database\Eloquent\Model
     */
    public function findById($id, array $columns = ['*']): Model
    {
        return $this->model
            ->select($columns)
            ->findOrFail($id);
    }

    /**
     * @param array $data
     * @return Illuminate\Database\Eloquent\Model
     */
    public function baseCreate(array $data): Model
    {
        $model = $this->model->newInstance($data);
        $model->save();

        return $model;
    }

    /**
     * @param integer $id
     * @param array $data
     * @throws Illuminate\Database\Eloquent\ModelNotFoundException
     * @return Illuminate\Database\Eloquent\Model
     */
    public function baseUpdate(int $id, array $data): Model
    {
        $model = $this->model->findOrFail($id);
        $model->fill($data);
        $model->save();

        return $model;
    }

    /**
     * @param integer $id
     * @return mixed
     */
    public function baseDelete(int $id)
    {
        $model = $this->model->findOrFail($id);
        return $model->delete();
    }

    /**
     * @return Illuminate\Database\Eloquent\Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function baseAll(array $columns = ['*']): Collection
    {
        if ($this->model instanceof Builder) {
            $results = $this->model->get($columns);
        } else {
            $results = $this->model->all($columns);
        }

        return $results;
    }

    /**
     * @param array $where
     * @param array $columns
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function baseFindWhere(array $where, array $columns = ['*']): Collection 
    {
        $query = $this->model->select($columns);
        $this->setWhere($query, $where);
        return $query->get();
    }
}
