<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IntegrationActionList extends Model
{
    protected $fillable = [
        'name',
        'description',
        'route',
        'method',
        'status',
        'action',
        'extra'
    ];

    public function getDateFormat()
{
     return 'Y-m-d H:i:s.u';
}
    protected $table = 'integrations_actions_list';
}
