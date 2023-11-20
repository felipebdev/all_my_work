<?php

namespace App\Repositories;

use App\EmailProvider;
use App\Services\Objects\EmailProviderFilter;
use Illuminate\Database\Eloquent\Builder;

class EmailProviderRepository
{

    /**
     * Get EmailProviders
     * @param EmailProviderFilter|null $filter
     * @return Builder
     */
    public function listAll(?EmailProviderFilter $filter = null): Builder{
        return  EmailProvider::when($filter,function ($query, $filter) {
            return EmailProvider::when($filter->search, function ($query, $search) {
                $query->where('name', 'LIKE', "%{$search}%");
            });
        });
    }

    /**
     * Get Email Provider by ID
     *
     * @param int $id
     * @return mixed
     */
    public function findById(int $id)
    {
        return EmailProvider::findOrFail($id);
    }

    /**
     * Create Email Provider
     * @param array $data
     * @return EmailProvider
     */
    public function create(array $data): EmailProvider
    {
        return EmailProvider::create($data);
    }

    /**
     * Update Email Provider
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data)
    {
        $emailProvider = $this->findById($id);
        return $emailProvider->update($data);
    }

    /**
     * Delete Email Provider
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $emailProvider = $this->findById($id);
        return $emailProvider->delete();
    }


}
