<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $file;
    public $reference;


    public function __construct($file,$reference)
    {
        $this->file = $file;
        $this->reference = $reference;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $object = "Fiche des travaux ".$this->reference." - SGA EXPERTISE";
        return $this
        ->subject($object)
        ->attach(
            $this->file
        )
        ->markdown('email.repair.index');
    }
}
