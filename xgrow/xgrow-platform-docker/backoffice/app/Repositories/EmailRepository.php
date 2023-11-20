<?php

namespace App\Repositories;

use App\Email;
use App\Services\Objects\EmailFilter;
use Illuminate\Database\Eloquent\Builder;

class EmailRepository
{
    /**
     * Get Emails
     * @param EmailFilter|null $filter
     * @return Builder
     */
    public function listAll(?EmailFilter $filter = null): Builder{
        return  Email::when($filter,function ($query, $filter) {
            return Email::when($filter->search, function ($query, $search) {
                $query->where('from', 'LIKE', "%{$search}%");
                $query->orWhere('subject', 'LIKE', "%{$search}%");
            });
        });
    }

    /**
     * @param $id
     * @param array $columns
     * @return Model
     */
    public function findById(int $id, array $columns = ['*'])
    {
        return Email::select($columns)
            ->findOrFail($id);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return Email::create($data);
    }
    
    /**
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function update(int $id, array $data)
    {
        $model = Email::findOrFail($id);
        $model->fill($data);
        return $model->save();
    }
    
    /**
     * @param int $id
     * @return mixed
     */
    public function delete(int $id)
    {
        $email = Email::findOrFail($id);
        return $email->delete();
    }


}
