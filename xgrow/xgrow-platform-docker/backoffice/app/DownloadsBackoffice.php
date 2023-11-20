<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DownloadsBackoffice extends Model
{
    use HasFactory;

    protected $table = "downloads_backoffice";

    protected $fillable = [
        'status', 'period', 'filters', 'filename', 'filesize', 'url', 'user_id'
    ];

}
