<?php

namespace App;

use App\Http\Traits\ElasticsearchTrait;
use App\Notifications\ResetPasswordNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use DB;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, HasFactory, SoftDeletes, ElasticsearchTrait;

    protected $dates = [
        'two_factor_expires_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
        'two_factor_enabled',
        'two_factor_code',
        'two_factor_expires_at',
        'type_access',
        'back_permission_id',
        'active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 
        'remember_token', 
        'two_factor_enabled',
        'two_factor_code',
        'two_factor_expires_at',

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Send the password reset notification.
     * @note: This override Authenticatable methodology
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function generateTwoFactorCode(?int $minutes = 10): void
    {
        $this->timestamps = false;
        $this->two_factor_code = rand(100000, 999999);
        $this->two_factor_expires_at = Carbon::now()->addMinutes($minutes);
        $this->save();
    }

    public function resetTwoFactorCode(): void
    {
        $this->timestamps = false;
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        $this->save();
    }

    public function isTwoFactorCodeExpired(): bool
    {
        return !$this->two_factor_expires_at or $this->two_factor_expires_at->lt(Carbon::now());
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function permission(): BelongsTo
    {
        return $this->belongsTo(BackPermission::class, 'back_permission_id', 'id');
    }

    static function checkRole($user_id, $role_id)
    {
        $user = SELF::find($user_id);
        if ($user->type_access === 'full') {
            // restrictions not set, user has all rights
            return true;
        }

        $permissions = DB::table('users')
            ->join('back_permissions', 'users.back_permission_id', 'back_permissions.id')
            ->join('back_scopes', 'back_permissions.id', 'back_scopes.back_permission_id')
            ->where('users.id', $user_id)
            ->where('back_scopes.back_role_id', $role_id)->count();

        if ($permissions > 0) {
            return true;
        }


        return false;
    }


    static function checkAction($user_id, $role_id, $action_id)
    {
        $user = SELF::find($user_id);

        if ($user->type_access === 'full') {
            // restrictions not set
            return true;
        }

        if($user->back_permission_id){

            $permissions = DB::table('back_permissions')
                ->select('back_scopes.type_access','back_permissions.id')
                ->join('back_scopes', 'back_permissions.id', 'back_scopes.back_permission_id')
                ->where('back_permissions.id', $user->back_permission_id)
                ->where('back_scopes.back_role_id', $role_id)->first();

            if ($permissions->type_access === 'full') {
                return true;
            }
            else{
                $grants = DB::table('back_grants')
                ->where('back_permission_id', $user->back_permission_id)
                ->where('back_action_id', $action_id)
                ->where('back_role_id', $role_id)
                ->count();

                if($grants > 0)
                    return true;
            }

        }

        return false;
    }

}
