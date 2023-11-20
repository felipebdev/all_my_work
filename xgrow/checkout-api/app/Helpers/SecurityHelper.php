<?php

namespace App\Helpers;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SecurityHelper
{
    /**
     * Function responsable for deny user if another platform
     * @param Model $model
     * @throws Exception
     */
    public function securityUser(Model $model = null)
    {
        $platformId = Auth::user()->platform_id;

        if (!$model || !$platformId) {
            throw new Exception('Registro não encontrado.');
        }

        if (!($model->platform_id == $platformId)) {
            throw new Exception('Você não tem permissão para acessar essa seção.');
        }
    }

    public static function securityMultipleUser(Model $model = null)
    {
        $loggedUser = DB::table('platform_user')
            ->where(['platforms_users_id' => Auth::user()->id])
            ->pluck('platform_id')
            ->toArray();

        if (!$model) {
            throw new Exception('Registro não encontrado.');
        }

        if (!in_array($model->platform_id, $loggedUser)) {
            throw new Exception('Você não tem permissão para acessar essa seção.');
        }
    }

    /**
     * Function responsable for deny user if another platform !!ONLY PLATFORM USER SECTION
     * @param Model $model
     * @throws Exception
     */
    public function securityUserByDB($id)
    {
        $platformId = Auth::user()->platform_id;

        if (!$id) {
            throw new Exception('Registro não encontrado.');
        }

        $userExists = DB::table('platform_user')
            ->where(['platforms_users_id' => $id, 'platform_id' => $platformId])
            ->count();

        if (!$userExists) {
            throw new Exception('Você não tem permissão para acessar essa seção.');
        }
    }
}
