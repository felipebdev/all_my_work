<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SectionProduct extends Model
{
    public $timestamps = false;

    protected $table = 'section_product';

    protected $fillable = ['section_id', 'product_id', 'content_section_id'];
}
