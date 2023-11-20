<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model
{
    protected $fillable = ['subscriber_id', 'card_id', 'brand', 'last_four_digits', 'holder_name', 'exp_month', 'exp_year'];
}
