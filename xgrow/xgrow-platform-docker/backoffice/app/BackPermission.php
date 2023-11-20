<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BackPermission extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function grants(): HasMany
    {
        return $this->hasMany(BackGrant::class);
    }

    public function scopes(): HasMany
    {
        return $this->hasMany(BackScope::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function actions(): BelongsToMany{
        return $this->belongsToMany(BackAction::class, 'back_grants');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(BackRole::class, 'back_scopes');
    }

}
