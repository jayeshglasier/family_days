<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ForgetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $address = env('MAIL_USERNAME');
        $subject = 'Resetting your password for ChoreUp';
        $name = 'ChoreUp';
        
        return $this->view('emails.user.forget-password')
        ->from($address, $name)
        ->subject($subject)
        ->with(['userdetail' => $this->data['userdetail'],'password' => $this->data['password']]);
    }
}