<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class Register extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

     public $email;
     public $password;

     public function __construct($email,$password)
     {
         $this->email = $email;
         $this->password = $password;
     }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $object = "Création de compte utilisateur - SGA EXPERTISE";
        return $this
        ->subject($object)
        ->markdown('email.auth.register');
    }
}
