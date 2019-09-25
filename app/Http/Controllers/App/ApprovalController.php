<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Exception;

use App\Models\App\Process;
use App\Models\App\ApprovalTemplate;
use App\Models\App\ApprovalTemplateStage;
use App\Models\App\ProcessApprovalHeader;
use App\Models\App\ProcessApproval;
use App\Models\Admin\ApprovalStage;
use App\Models\App\ApprovalStageUser;

use App\Jobs\ApprovalMailSendJob;

class ApprovalController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
    //  $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get Color list
    public function index(Request $request)
    {
        auth()->payload()->get('loc_id');
    }


   public function start(Request $request){
     $process_name = $request->process_name;
     $document_id = $request->document_id;

     $process = Process::find($process_name);
     $template = ApprovalTemplate::find($process->approval_template);

     $first_template_stages = ApprovalTemplateStage::where('template_id', '=', $template->template_id)->first();
     $first_stage = ApprovalStage::find($first_template_stages->stage_id);
     $stage_users = ApprovalStageUser::where('stage_id', '=', $first_stage->stage_id)->get();

     $process_approval_header = new ProcessApprovalHeader();
     $process_approval_header->process = $process_name;
     $process_approval_header->template_id = $template->template_id;
     $process_approval_header->document_id = $document_id;
     $process_approval_header->current_stage_id = $first_stage->stage_id;

     if(sizeof($stage_users) > 0){
       if($stage_users[0]->type == 'USER'){
         $process_approval_header->current_stage_user_type = 'USER';
         $process_approval_header->current_stage_user_id = $stage_users[0]['user_id'];
       }
       else {
         $process_approval_header->current_stage_user_type = 'USER';
         $process_approval_header->current_stage_user_position = $stage_users[0]['user_position'];
       }
     }
     //$process_approval_header->current_stage_user_id = $first_stage->approval_users[0]->user_id;
     $process_approval_header->save();

     $process_approval = new ProcessApproval();
     $process_approval->process = $process_name;
     $process_approval->template_id = $template->template_id;
     $process_approval->document_id = $document_id;
     $process_approval->request_date = date('Y-M-d');
     $process_approval->request_remark = '';
     /*$process_approval->status = null;
     $process_approval->approval_remark = null;
     $process_approval->approval_date = null;
     $process_approval->approval_user = null;*/
     $process_approval->save();

     $job = new ApprovalMailSendJob('COSTING', [], ['chamilap@helaclothing.com']);
     dispatch($job);

     if($template->has_terms == 1) { //has terms

     }
     else {//no terms

     }
   }


}
