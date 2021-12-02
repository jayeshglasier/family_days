<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReportSpam extends Mailable
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
        $subject = 'Spam report GirlVent';
        $name = 'GirlVent';
        
        return $this->view('emails.user.report-spam')
        ->from($address, $name)
        ->subject($subject)
        ->with(['userdetail' => $this->data['userdetail'],'post_detail' => $this->data['post_detail'],'issue' => $this->data['issue'] ]);
    }
}