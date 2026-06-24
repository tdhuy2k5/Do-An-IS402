<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyCode extends Mailable
{
    use Queueable, SerializesModels;


    public $verificationCode;


    public function __construct(string $code)
    {

        $this->verificationCode = $code;
    }


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Account Verification Code',
        );
    }


    public function build()
    {

        return $this->html('<h1>Verification Code: '.$this->verificationCode.'</h1>');
    }


    public function attachments(): array
    {
        return [];
    }
}
