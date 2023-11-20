<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BackGrant extends Model
{
    use HasFactory;

    public function permission(): BelongsTo
    {
        return $this->belongsTo(BackPermission::class);
    }

    public function action(): BelongsTo
    {
        return $this->belongsTo(BackAction::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(BackRole::class);
    }
}
