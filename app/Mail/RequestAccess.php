<?php

namespace pompong\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RequestAccess extends Mailable
{
    use Queueable, SerializesModels;
    public $request;
    public $urlYes;
    public $urlNo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->request = $request;
        $this->urlYes = \URL::to('/') . '/api/user/accept?token=' . $request->token;
        $this->urlNo = \URL::to('/') . '/api/user/deny?token=' . $request->token;;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.request-access');
    }
}
