<?php

namespace App\Mail;

use App\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmailInvitationToBeCoProducer extends Mailable
{
    use Queueable, SerializesModels;


    protected $accessData;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($accessData)
    {
        $this->accessData = $accessData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $product = Product::find($this->accessData['product_id']);

        $data = [
            'name' => $this->accessData['name'],
            'product_name' => $product->name
        ];

        return $this->from(env('MAIL_FROM_ADDRESS', 'naoresponda@xgrow.com'), "Email de solicitação de Co-Produção.")
            ->subject("Você foi convidado para ser um Co-Produtor Xgrow")
            ->view('emails.invitation-to-be-coproducer', compact('data'));
    }
}
