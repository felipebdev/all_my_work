<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
	use HasFactory;
    
    protected $fillable = ['id', 'area', 'subject', 'message', 'from'];

    const AREA_SISTEM = '1';
    const AREA_PLANS = '2';
    const AREA_LOGIN = '3';
    const AREA_FINANCIAL = '4';

    static function allAreas()
    {
        return [
            self::AREA_SISTEM => 'Sistema',
            self::AREA_PLANS => 'Planos',
            self::AREA_LOGIN => 'Login',
            self::AREA_FINANCIAL => 'Financeiro',
        ];
    }

}
