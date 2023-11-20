<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditCard extends Model
{
    use SoftDeletes;
    protected $fillable = ['subscriber_id', 'card_id', 'brand', 'last_four_digits', 'holder_name', 'exp_month', 'exp_year'];
}
