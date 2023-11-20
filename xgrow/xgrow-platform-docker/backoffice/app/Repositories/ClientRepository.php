<?php

namespace App\Repositories;

use App\Client;
use App\Services\Objects\ClientFilter;
use App\Services\Storage\UploadedImage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ClientRepository
{

    /**
     * Get Clients
     * @param ClientFilter|null $filter
     * @return Builder
     */
    public function listAll(?ClientFilter $filter = null): Builder
    {
        $fields = [
            'clients.id', 'clients.first_name', 'clients.last_name', 'clients.email', 'clients.verified', 'clients.cpf', 'clients.cnpj', 'clients.created_at', 'clients.phone_number',
            'platforms_users.name AS platform_name', 'platforms_users.whatsapp',
            'producers.type AS user_type'
        ];
        return Client::distinct()->select($fields)->when($filter->search, function ($query, $search) {
            $query->whereRAW("concat(clients.first_name,' ',clients.last_name) LIKE '%{$search}%'");
            $query->orWhere('clients.company_name', 'LIKE', "%{$search}%");
            $query->orWhere('clients.cpf', 'LIKE', "%{$search}%");
            $query->orWhere('clients.cnpj', 'LIKE', "%{$search}%");
            $query->orWhere('clients.email', 'LIKE', "%{$search}%");
        })
            ->leftJoin('platforms_users', 'platforms_users.email', '=', 'clients.email')
            ->leftJoin('producers', 'producers.platform_user_id', '=', 'platforms_users.id')
            ->when($filter->clientsId, function ($query, $clientsId) {
                $query->whereIn('clients.id', $clientsId);
            })
            ->when($filter->createdPeriod, function ($query, $search) {
                $query->whereBetween('clients.created_at', [$search->startDate, $search->endDate]);
            });
        // ->when($filter->clientType, function ($query, $clientType) {
        //     $query->whereIn('id', $clientType);
        // });
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function delete(int $id)
    {
        $client = Client::findOrFail($id);

        //has no platform
        if (!$client->platforms()->count())
            return $client->forceDelete();

        //have platform
        return $client->delete();
    }

    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function findById($id, array $columns = ['*'])
    {
        return Client::select($columns)
            ->findOrFail($id);
    }

    /**
     * @param array $data
     * @param UploadedFile|null $image
     * @return Client
     */
    public function create(array $data, ?UploadedFile $image = null): Client
    {
        $model = (new Client())->newInstance($data);
        $model->save();
        $this->setImage($model->id, $image);
        return $model->refresh();
    }

    /**
     * @param int $id
     * @param array $data
     * @param UploadedFile|null $image
     * @return mixed
     */
    public function update(int $id, array $data, ?UploadedFile $image = null)
    {
        $model = Client::findOrFail($id);
        $model->fill($data);
        $model->save();
        $this->setImage($id, $image);
        return $model->refresh();
    }

    /**
     * @param int $id
     * @param UploadedFile|null $image
     * @return void
     */
    private function setImage(int $id, ?UploadedFile $image)
    {
        $client = Client::findOrfail($id);
        if (isset($image)) {
            $uploadImage = new UploadedImage($id, $image, Storage::disk('images'));
            $cover = $uploadImage->store();
            $client->update(['cover' => $cover->converted]);
        }
    }
}
