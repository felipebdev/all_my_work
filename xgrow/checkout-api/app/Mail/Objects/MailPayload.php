<?php

namespace App\Mail\Objects;

class MailPayload
{
    public $platformId;
    public $subscriber;
    public $payment;
    public $url;
    public $password;
    public $coupon;
    public $name;
    public $email;
    public $attendant;
    public $refundCode;
    public $refundValue;
    public $planValue;

    public function __construct(string $platformId, array $data) {
        $this->platformId = $platformId;
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
