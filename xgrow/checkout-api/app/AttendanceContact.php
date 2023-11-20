<?php

namespace App;

use App\Attendance;
use App\CallcenterReasonsLoss;
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

}
