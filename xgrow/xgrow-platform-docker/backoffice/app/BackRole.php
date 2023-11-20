<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BackRole extends Model
{
    use HasFactory;

    protected $fillable = ['slug', 'name'];

    public function grants(): HasMany
    {
        return $this->hasMany(BackGrant::class);
    }

    public function scopes(): HasMany
    {
        return $this->hasMany(BackScope::class);
    }

    public function actions(): BelongsToMany
    {
        return $this->belongsToMany(BackAction::class, 'back_grants');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(BackPermission::class, 'back_scopes');
    }

}
