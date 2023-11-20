<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RecurrencePlanChange extends Model
{
    //use HasFactory;

    protected $table = 'recurrence_plan_change';

    protected $fillable = [
        'origin',
        'recurrence_id',
        'old_plan_id',
        'new_plan_id',
        'created_at',
        'updated_at',
    ];
}
