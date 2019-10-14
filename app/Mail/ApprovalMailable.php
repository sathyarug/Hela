<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
//use Illuminate\Contracts\Queue\ShouldQueue;

class ApprovalMailable extends Mailable /*implements ShouldQueue*/
{
    use Queueable, SerializesModels;

    private $data;
    private $process;
    private $email_subject;

    public function __construct($_process, $_data, $_subject)
    {
        $this->data = $_data;
        $this->process = $_process;
        $this->email_subject = $_subject;
    }

    public function build()
    {
      if($this->process == 'COSTING'){
        return $this->subject($this->email_subject)->view('email.email_approval_costing')->with($this->data);
      }
      else if($this->process == 'COSTING_CONFIRM'){
        return $this->subject($this->email_subject)->view('email.email_confirm_costing')->with($this->data);
      }
      else if($this->process == 'ITEM'){
        return $this->subject($this->email_subject)->view('email.email')->with($this->data);
      }
    }
}
