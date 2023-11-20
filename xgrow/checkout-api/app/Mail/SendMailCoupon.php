<?php

namespace App\Mail;

use App\Email;
use App\Coupon;
use NumberFormatter;
use App\Mail\BaseMail;

class SendMailCoupon extends BaseMail
{
    private $coupon;
    private $email;
    private $name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($platformId, Coupon $coupon, $email, $name) {
        parent::__construct($platformId, [$email], Email::CONSTANT_EMAIL_COUPON);
        $this->coupon = $coupon;
        $this->email = $email;
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        $plans = [];
        foreach ($this->coupon->plans as $plan) {
            $plans[] = $plan->name;
        }

        $message = $this->template->message;
        $message = str_replace('##COUPON_NAME##', $this->name, $message);
        $message = str_replace('##COUPON_CODE##', $this->coupon->code, $message);
        $message = str_replace('##COUPON_DISCOUNT##', ($this->coupon->value_type === 'V') ? "R$ ".number_format($this->coupon->value, 2, ',', '.') : "{$this->coupon->value}%", $message);
        $message = str_replace('##COUPON_DATE_LIMIT##', date_format(date_create($this->coupon->maturity), 'd/m/Y'), $message);
        $message = str_replace('##COUPON_PRODUCTS##', join(', ', $plans), $message);

        return $this->sendMail($this->template->subject, $message);
    }

}
