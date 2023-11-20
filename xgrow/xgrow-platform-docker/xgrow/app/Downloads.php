<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Downloads extends Model
{

    protected $fillable = [
        'status', 'period', 'filters', 'filename', 'filesize', 'url', 'platform_id', 'platforms_users_id'
    ];

    public function selectByPlatform(string $platform_id){
        $downloads = Downloads::select([
            'status', 'period', 'filters', 'filename', 'filesize', 'url', 'created_at'
        ])->where('platform_id', $platform_id)->orderBy('created_at', 'desc')->get();

        return $downloads;
    }
}
