<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    
    protected $fillable = ["name", "type", "has_start", "start_at", "has_finish", "finish_at", "format", "subject", "audio_id", "text", "replyto", "platform_id", "automatic_type", "automatic_id", "status", "sent", "canceled", "views"];

    const STATUS_PENDING = 'pending';
    const STATUS_STARTED = 'started';
    const STATUS_CONCLUDED = 'concluded';
    const STATUS_CANCELED = 'canceled';

    const TYPE_SCHEDULED = 1;
    const TYPE_AUTOMATIC = 2;

    public static function listTypes() {
        return array(
            self::TYPE_SCHEDULED => 'Agendada', 
            self::TYPE_AUTOMATIC => 'Automática'
        );
    }

    const FORMAT_EMAIL = 1;
    const FORMAT_AUDIO = 2;
    const FORMAT_SMS = 3;
    const FORMAT_WATSAPP = 4;

    public static function listFormats() {
        return array(
            self::FORMAT_EMAIL => ['name' => 'E-mail', 'subject' => 1, 'reply_to' => 1, 'active' => 1, 'audio' => 0, 'msg_type' => 2], 
            self::FORMAT_AUDIO => ['name' => 'Voz', 'subject' => 0, 'reply_to' => 0, 'active' => 1, 'audio' => 1, 'msg_type' => 0], 
            self::FORMAT_SMS => ['name' => 'SMS', 'subject' => 0,'reply_to' => 0, 'active' => 1, 'audio' => 0, 'msg_type' => 1], 
            //self::FORMAT_WATSAPP => ['name' => 'Whatsapp', 'subject' => 0, 'reply_to' => 0, 'active' => 1, 'audio' => 0, 'msg_type' => 2], 
        );
    }

    const FIRST_ACCESS_TO_THE_SITE = 1;
    const FIRST_ACCESS_TO_THE_CONTENT = 2;
    const FIRST_ACCESS_TO_THE_COURSE = 3;
    const FIRST_ACCESS_TO_THE_SECTION = 4;

    public static function listAutomaticTypes() {
        return array(
            self::FIRST_ACCESS_TO_THE_SITE => 'Primeiro acesso ao site', 
            self::FIRST_ACCESS_TO_THE_CONTENT => 'Acesso ao conteúdo',
            self::FIRST_ACCESS_TO_THE_COURSE => 'Acesso ao curso',
            self::FIRST_ACCESS_TO_THE_SECTION => 'Acesso à seção'
        );
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }


    public function audiences()
    {
        return $this->belongsToMany(Audience::class, 'campaign_audience', 'campaign_id', 'audience_id');
    }

    public function audio(){
        return $this->belongsTo(File::class);
    }

}
