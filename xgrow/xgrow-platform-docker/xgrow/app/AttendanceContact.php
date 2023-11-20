<?php

namespace App;

use App\Attendance;
use App\CallcenterReasonsLoss;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AttendanceContact extends Model
{


   protected $fillable = [
        'attendance_id','status', 'description', 'reasons_loss_id', 'ip'
    ];

    public function attendance(){
    	return $this->belongsTo(Attendance::class);
    }

    public function reasons_loss(){
    	return $this->belongsTo(CallcenterReasonsLoss::class, 'reasons_loss_id', 'id');
    }

    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->setTimezone('Europe/London')->format('Y-m-d H:i:s');
    }

}
