<?php

namespace App;

use App\Category;
use Illuminate\Database\Eloquent\Relations\MorphPivot;


class Categorizable extends MorphPivot
{
    protected $table = 'categorizables';

    protected $fillable = ['order'];

    public function tag()
    {
        return $this->belongsTo(Category::class);
    }

    public function related()
    {
        return $this->morphTo(__FUNCTION__, 'categorizable_type', 'categorizable_id');
    }
}
