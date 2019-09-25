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

    public function __construct($_process, $_data)
    {
        $this->data = $_data;
        $this->process = $_process;
    }

    public function build()
    {
      if($this->process = 'COSTING'){
        return $this->view('email.email')->with($this->data);
      }
      else if($this->process = 'ITEM'){
        return $this->view('email.email')->with($this->data);
      }
    }
}
