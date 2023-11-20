<?php

namespace App\Repositories\Authors;

use App\Author;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\AuthorRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AuthorRepository extends BaseRepository implements AuthorRepositoryInterface
{

    public function model(): string
    {
        return Author::class;
    }

    /**
     * Get authors
     * @param string $platformId
     * @param ?string $searchTerm
     * @return Builder
     */
    public function list(string $platformId, ?string $searchTerm): Builder{
        $query = $this->model
                        ->when($searchTerm, function ($query, $searchTerm) {
                            $query->where('name_author', 'like', '%'.$searchTerm.'%');
                        });
        $this->setWhere($query, ['platform_id' => $platformId]);
        return $query;
    }

}
