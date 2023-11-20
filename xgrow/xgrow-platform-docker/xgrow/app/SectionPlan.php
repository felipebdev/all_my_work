<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SectionPlan extends Model
{
    protected $fillable = ['section_id', 'plan_id'];
    protected $table = 'section_plan';

    public function section()
    {
        return $this->hasOne(Section::class, 'id', 'section_id');
    }

    public function plan()
    {
        return $this->hasOne(Plan::class,'id', 'plan_id');
    }
}
