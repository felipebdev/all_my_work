<?php

namespace App\Repositories\Platforms;

use App\Platform;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PlatformRepository
{

    /**
     * @param string|null $term
     * @return mixed
     */
    public function getCollaborationPlatforms(string $term = null): Builder
    {
        $query = Platform::select(
            'platforms.id as id',
            'platforms.name as name',
            'platforms.url as url',
            'platforms.customer_id as customer_id',
            'platforms.cover as image',
            'platforms.created_at as created_at',
            'platforms.active as active',
        )
            ->leftJoin('platform_user', 'platforms.id', '=', 'platform_user.platform_id')
            ->where('platform_user.platforms_users_id', Auth::user()->id);

        if ($term) {
            $query->where(function (Builder $q) use ($term) {
                return $q
                    ->where('platforms.name', 'like', "%$term%");
            });
        }

        return $query;
    }

    /**
     * @param string|null $term
     * @return mixed
     */
    public function getOwnerPlatforms(string $term = null): Builder
    {
        $query = Platform::select(
            'platforms.id as id',
            'platforms.name as name',
            'platforms.url as url',
            'platforms.customer_id as customer_id',
            'platforms.cover as image',
            'platforms.created_at as created_at',
            'platforms.active as active',
            'clients.email as customer_email',
            'clients.verified as verified',
        )
            ->leftJoin('clients', 'platforms.customer_id', '=', 'clients.id')
            ->where('clients.email', Auth::user()->email);

        if ($term) {
            $query->where(function (Builder $q) use ($term) {
                return $q
                    ->where('platforms.name', 'like', "%$term%");
            });
        }

        return $query;
    }
}
