<?php

namespace App;

use App\File;
use App\Notifications\ResetPasswordNotification;
use App\Permission;
use App\Platform;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class PlatformUser extends Authenticatable
{
    use HasFactory;

    use Notifiable, SoftDeletes;

    protected $table = 'platforms_users';

    protected $dates = [
        'two_factor_expires_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','email', 'password','thumb_id','permission_id','surname','email','display_name','whatsapp','instagram','linkedin','facebook',
        'two_factor_code',
        'two_factor_expires_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    public function platform(){
        return $this->belongsTo(Platform::class);
    }

    public function platforms() {
        return $this->belongsToMany(
            Platform::class,
            'platform_user',
            'platforms_users_id',
            'platform_id'
        );
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class,
                                    'platform_user',
                                    'platforms_users_id',
                                    'permission_id');
    }

    public function file()
    {
        return $this->morphOne(File::class, 'filable');
    }

    public function thumb(){
        return $this->hasOne(File::class, 'id', 'thumb_id');
    }

    public function producers()
    {
        return $this->hasMany(Producer::class);
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }

    public function mobile_configuration()
    {
        return $this->hasOne(MobileConfiguration::class, 'platforms_users_id');
    }


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

    public function generateTwoFactorCode(): void
    {
        $this->timestamps = false;
        $this->two_factor_code = rand(100000, 999999);
        $this->two_factor_expires_at = Carbon::now()->addMinutes(10);
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
        return $this->two_factor_expires_at->lt(Carbon::now());
    }

    public function markedForLogout()
    {
        $logout = AccessLog::select('type')
            ->where('user_id', $this->id)
            ->where('user_type', 'platforms_users')
            ->orderBy('created_at', 'DESC')
            ->first();

        if ($logout !== null) {

            if ($logout->type === 'LOGIN') {

                $query = 'SELECT IF(DATE_ADD(MAX(created_at), INTERVAL 1 HOUR) < NOW(), 1, 0) AS logout
                  FROM content_logs a
                  WHERE user_type = "platforms_users"
                  AND user_id = ' . $this->id;

                $result = DB::select($query);

                if (count($result) > 0) {
                    return $result[0]->logout;
                } else {
                    return 0;
                }
            }
        }

        return 0;
//        $query = 'SELECT IF(DATE_ADD(MAX(created_at), INTERVAL 1 HOUR) < NOW(), 1, 0) AS logout
//                  FROM content_logs a
//                  WHERE user_type = "platforms_users"
//                  AND user_id = ' . $this->id;
//
//        $result = DB::select($query);
//
//        return (count($result) > 0)  ? $result[0]->logout : 0;
    }

    public function markForLogout()
    {
        $this->logout = 1;
    }

    public function unmarkForLogout()
    {
        $this->logout = 0;
    }

    public function getPlatformIdAttribute($value)
    {
        return session('platform_id') ?? '';
    }

}
