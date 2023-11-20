<?php

namespace App;

use App\Attendance;
use App\Audience;
use App\Subscriber;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Attendant extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $table = 'attendants';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','email', 'password', 'platform_id', 'active', 'allaudience', 'uuid'
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

    public function audience(){
        return $this->belongsTo(Audience::class);
    }

    public function audiences()
    {
        return $this->belongsToMany(Audience::class, 'attendant_audience', 'attendant_id', 'audience_id');
    }

    public function subscribers()
    {
        return $this->belongsToMany(Subscriber::class, 'attendant_subscriber', 'attendant_id', 'subscriber_id');
    }

    public function attendances(){
        return $this->hasMany(Attendance::class);
    }

    public function attendancesWithoutGlobalScopes(){
        return $this->hasMany(Attendance::class)->withoutGlobalScopes();
    }

}
