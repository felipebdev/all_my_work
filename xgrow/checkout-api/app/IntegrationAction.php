<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IntegrationAction extends Model
{
    protected $fillable = [
        'description',
        'trigger',
        'extra'
    ];

    public function platforms()
    {
        return $this->belongsTo(Platform::class, 'platform_id');
    }

    public function integrations()
    {
        return $this->belongsTo(Integration::class, 'integration_id');
    }

    public function integrations_actions_list()
    {
        return $this->belongsTo(IntegrationsActionsList::class, 'integrations_actions_list_id');
    }

    public function getDateFormat()
    {
         return 'Y-m-d H:i:s.u';
    }

    protected $table = 'integrations_actions';
}
