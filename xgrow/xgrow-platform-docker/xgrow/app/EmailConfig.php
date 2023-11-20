<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailConfig extends Model
{

	const DRIVER_SMTP = 'smtp';
	const DRIVER_LOG = 'log';
	const DRIVER_MAILGUN = 'mailgun';
	const DRIVER_SENDMAIL = 'sendmail';

    protected $fillable = ['driver', 'from_name', 'from_address', 'server_name', 'server_port', 'server_user', 'server_password', 'platform_id'];

}
