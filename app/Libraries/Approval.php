<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use Exception;

use App\Models\App\Process;
use App\Models\App\ApprovalTerm;
use App\Models\App\ApprovalTemplate;
use App\Models\App\ApprovalTemplateStage;
use App\Models\App\ApprovalTemplateStageTerm;
use App\Models\App\ProcessApproval;
use App\Models\App\ProcessApprovalStage;
use App\Models\App\ProcessApprovalStageUser;
use App\Models\Admin\ApprovalStage;
use App\Models\App\ApprovalStageUser;
use App\Models\Admin\UsrProfile;

use App\Jobs\ApprovalMailSendJob;

use App\Services\Merchandising\Costing\CostingService;

//will be remove in later developments
use App\Models\Merchandising\Costing\Costing;
use App\Models\Merchandising\Costing\CostingFinishGood;
use App\Models\Merchandising\CustomerOrderDetails;
use App\Models\Merchandising\BOMHeader;
use App\Models\Merchandising\Costing\CostingFinishGoodComponent;
use App\Models\Merchandising\Costing\CostingFinishGoodComponentItem;

use Webklex\IMAP\Facades\Client;



class Approval
{

  public function start($process_name, $document_id, $document_created_by) {
    //$process_name = $request->process_name;
    //$document_id = $request->document_id;
    $date = date("Y-m-d H:i:s");
    $process = Process::find($process_name);
    $template = ApprovalTemplate::find($process->approval_template);
    $user_id = auth()->user()->user_id;
    $stage_order = 1;
    $chek_next_stage = true;

    $first_template_stages = ApprovalTemplateStage::where('template_id', '=', $template->template_id)->where('stage_order', '=', 1)->first();
    //check stage has terms
    if($first_template_stages->has_terms == 1) {
      //get all stage rules and execute one by one
      $approval_template_stage_terms = ApprovalTemplateStageTerm::where('template_stage_id', '=', $first_template_stages->template_stage_id);
      foreach($approval_template_stage_terms as $_term){
        $approval_term = ApprovalTerm::find($_term->term_id);
        $query = $approval_term->query();
        $query = str_replace("{document_id}", $document_id, $query);
        $query = str_replace("{ratio}", $approval_term->ratio, $query);
        $query = str_replace("{value}", $approval_term->value, $query);

        if($approval_term->execute_query($query) == false) { //run query and did not pass the term
          //no need to send for approval. and update document
          $this->update_document_status($process_name, $document_id, 'APPROVED');
          return [
            'status' => 'term_fail',
            'message' => 'Not pass all the terms'
          ];
        }
      }
    }

    $approval_stage = ApprovalStage::find($first_template_stages->stage_id);
    $approval_stage_users = ApprovalStageUser::where('stage_id', '=', $approval_stage->stage_id)->get();

    $process_approval = new ProcessApproval();
    $process_approval->process = $process_name;
    $process_approval->template_id = $template->template_id;
    $process_approval->document_id = $document_id;
    $process_approval->current_stage_id = $approval_stage->stage_id;
    $process_approval->document_created_by = $document_created_by;
    $process_approval->created_date = $date;
    //$process_approval->created_by = $user_id;
    $process_approval->updated_date = $date;
    //$process_approval->updated_by = $process_approval;
    $process_approval->status = 'PENDING';
    $process_approval->save();

    $process_approval_stage = new ProcessApprovalStage();
    $process_approval_stage->approval_id = $process_approval->id;
    $process_approval_stage->request_date = $date;
    $process_approval_stage->request_remark = '';
    $process_approval_stage->status = 'PENDING';
    $process_approval_stage->template_stage_id = $first_template_stages->template_stage_id;
    $process_approval_stage->stage_id = $first_template_stages->stage_id;
    $process_approval_stage->stage_order = $first_template_stages->stage_order;
    $process_approval_stage->created_date = $date;
    $process_approval_stage->updated_date = $date;
    $process_approval_stage->save();

    $data = $this->get_data($process_name, $document_id);
    $to = [];
    for($x = 0 ; $x < sizeof($approval_stage_users) ; $x++){
      $process_approval_user = new ProcessApprovalStageUser();
      $process_approval_user->approval_stage_id = $process_approval_stage->id;
      $process_approval_user->user_type = $approval_stage_users[$x]['type'];
      $process_approval_user->user_position = $approval_stage_users[$x]['user_position'];
      $process_approval_user->user_id = $approval_stage_users[$x]['user_id'];
      $process_approval_user->status = 'PENDING';
      $process_approval_user->created_date = $date;
      $process_approval_user->updated_date = $date;

      if($process_approval_user->user_type == 'USER'){ //exact user
        $user = UsrProfile::find($process_approval_user->user_id);
        array_push($to, ['email' => $user->email]);
      }
      else if($process_approval_user->user_type == 'REPORTING_LEVEL'){//user reporting level
        $reporting_level = $process_approval_user->user_id; //user id = reporting level
        $created_user =  UsrProfile::find($process_approval->document_created_by); //get document created user
        $report_user = null;
        if($reporting_level == 1) {
          $report_user = UsrProfile::find($created_user->reporting_level_1); //get reporting level 1 user
        }
        else if($reporting_level == 2) {
          $report_user = UsrProfile::find($created_user->reporting_level_2); //get reporting level 2 user
        }
        $process_approval_user->user_id = $report_user->user_id;
        array_push($to, ['email' => $report_user->email]);
      }
      else if($process_approval_user->user_type == 'DESIGNATION'){//department designation

      }

      $process_approval_user->save();

      $data['approval_id'] = $process_approval_user->id;
      $data['request_remark'] = $process_approval_stage->request_remark;
      $mail_subject = 'APPROVAL PENDING ' . $process_name . '-'.$document_id.' #'.$process_approval_user->id.'#';
      $job = new ApprovalMailSendJob($process_name, $mail_subject, $data, $to);
      dispatch($job);
    }



    if($template->has_terms == 1) { //has terms

    }
    else {//no terms

    }
  }


  public function approve($id, $status, $approval_remark, $user_id) {
    //$status = $request->status;
    //$approval_stage_user_id = $request->approval_id;
  //  $approval_remark = '';
    $date = date("Y-m-d H:i:s");
  //  $user_id = auth()->user()->user_id;

    if($status == 'A' || $status == 'a' || $status == 'APPROVED'){
      $status = 'APPROVED';
    }
    else if($status == 'R' || $status == 'r' || $status == 'REJECTED'){
      $status = 'REJECTED';
    }

    $process_approval_stage_user = ProcessApprovalStageUser::find($id);
    if($process_approval_stage_user->status == 'PENDING') { //not approved or reject
      //
      $process_approval_stage_user->status = $status;
      $process_approval_stage_user->approval_date = $date;
    //  $process_approval_stage_user->approval_user = $user_id;
      $process_approval_stage_user->approval_remark = $approval_remark;
      $process_approval_stage_user->save();

      $process_approval_stage = ProcessApprovalStage::find($process_approval_stage_user->approval_stage_id);
      $process_approval_stage->status = $status;
      $process_approval_stage->save();

      $process_approval = ProcessApproval::find($process_approval_stage->approval_id);//get the process approval header

      if($status == 'REJECTED') { //stop the approval process if rejected
        $process_approval->status = $status;
        $process_approval->save();
        //update document status
        $this->update_document_status($process_approval->process, $process_approval->document_id, $status);
        return true;
      }

      $approval_template_stage = ApprovalTemplateStage::where('template_id', '=', $process_approval->template_id)
      ->where('stage_order', '=', ($process_approval_stage->stage_order + 1))->first();

      if($approval_template_stage != null) { //has next stage and continue

        //check stage has terms
        if($approval_template_stage->has_terms == 1) {
          //get all stage rules and execute one by one
          $approval_template_stage_terms = ApprovalTemplateStageTerm::where('template_stage_id', '=', $approval_template_stage->template_stage_id);
          foreach($approval_template_stage_terms as $_term){
            $approval_term = ApprovalTerm::find($_term->term_id);
            $query = $approval_term->query();
            $query = str_replace("{document_id}", $document_id, $query);
            $query = str_replace("{ratio}", $approval_term->ratio, $query);
            $query = str_replace("{value}", $approval_term->value, $query);

            if($approval_term->execute_query($query) == false) { //run query and did not pass the term
              //no need to send for approval. and update document
              $this->update_document_status($process_approval->process, $process_approval->document_id, $status);
              return [
                'status' => 'term_fail',
                'message' => 'Not pass all the terms'
              ];
            }
          }
        }
        //create next approval stage
        $process_approval_stage2 = new ProcessApprovalStage();
        $process_approval_stage2->approval_id = $process_approval->id;
        $process_approval_stage2->request_date = $date;
        $process_approval_stage2->request_remark = '';
        $process_approval_stage2->status = 'PENDING';
        $process_approval_stage2->template_stage_id = $approval_template_stage->template_stage_id;
        $process_approval_stage2->stage_id = $approval_template_stage->stage_id;
        $process_approval_stage2->stage_order = $approval_template_stage->stage_order;
        $process_approval_stage2->created_date = $date;
        $process_approval_stage2->created_by = $user_id;
        $process_approval_stage2->updated_date = $date;
        $process_approval_stage2->updated_by = $user_id;
        $process_approval_stage2->save();

        $stage_users = ApprovalStageUser::where('stage_id', '=', $approval_template_stage->stage_id)->get();
        $data = $this->get_data($process_approval->process, $process_approval->document_id);
        $to = [];
        for($x = 0 ; $x < sizeof($stage_users) ; $x++){
          $process_approval_user = new ProcessApprovalStageUser();
          $process_approval_user->approval_stage_id = $process_approval_stage2->id;
          $process_approval_user->user_type = $stage_users[$x]['type'];
          $process_approval_user->user_position = $stage_users[$x]['user_position'];
          $process_approval_user->user_id = $stage_users[$x]['user_id'];
          $process_approval_user->status = 'PENDING';
          $process_approval_user->created_by = $user_id;
          $process_approval_user->created_date = $date;
          $process_approval_user->updated_by = $user_id;
          $process_approval_user->updated_date = $date;
          $process_approval_user->save();

          if($process_approval_user->user_type == 'USER'){ //exact user
            $user = UsrProfile::find($process_approval_user->user_id);
            array_push($to, ['email' => $user->email]);
          }
          else if($process_approval_user->user_type == 'REPORTING_LEVEL'){//user reporting level
            $reporting_level = $process_approval_user->user_id; //user id = reporting level
            $approved_user =  UsrProfile::find($process_approval_stage_user->user_id); //get document approved user
            $report_user = null;
            if($reporting_level == 1) {
              $report_user = UsrProfile::find($approved_user->reporting_level_1); //get reporting level 1 user
            }
            else if($reporting_level == 2) {
              $report_user = UsrProfile::find($approved_user->reporting_level_2); //get reporting level 2 user
            }
            array_push($to, ['email' => $report_user->email]);
          }

          $data['approval_id'] = $process_approval_user->id;
          $data['request_remark'] = $process_approval_stage2->request_remark;
          $mail_subject = 'APPROVAL PENDING ' . $process_approval->process . '-'.$process_approval->document_id.' #'.$process_approval_user->id.'#';
          $job = new ApprovalMailSendJob($process_approval->process, $mail_subject, $data, $to);
          dispatch($job);

        }

        $process_approval->current_stage_id = $process_approval_stage2->stage_id;//update next stage
        $process_approval->save();
        return true;
      }
      else { //no next stage and
        $process_approval->status = $status;
        $process_approval->save();
        //update document status
        $this->update_document_status($process_approval->process, $process_approval->document_id, $status);
        return true;
      }
    }
    else { //already approved or rejected
      //send response
      return false;
    }

  }


  public function readMail(){
    $oClient = Client::account('default');
    $oClient->connect();

    $oFolder = $oClient->getFolder('INBOX');  // get the read inbox
    //$aMessage = $oFolder->search()->text('TEST')->get();//subject('TEST')->limit(20, 1)->get();
    $aMessage = $oFolder->query()->get();
    //echo json_encode($aMessage);
    foreach($aMessage as $message){
    //  echo $message->getSubject();
      if(strpos($message->getSubject(), "APPROVAL PENDING") > 0) {
        $supject_parts = explode ("#", $message->getSubject());
        //$process_name = $supject_parts[1];

        $approval_id = $supject_parts[1];
        $status = null;
        $approval_remark = null;
        if(sizeof($supject_parts) > 2) {
          $status = $supject_parts[2];
        }
        if(sizeof($supject_parts) > 3){
          $approval_remark = $supject_parts[3];
        }

        $approval_stage_user = ProcessApprovalStageUser::find($approval_id);
        $user_profile = UsrProfile::where('email', '=', $message->getFrom()[0]->mail)->first();
        if($approval_stage_user == null || $user_profile == null){
          continue;
        }
        //echo json_encode($approval_id);die();
        //chek email replied user is same as approval user
        if($user_profile != null && $user_profile->user_id == $approval_stage_user->user_id) {
          $response = $this->approve($approval_id, $status, $approval_remark, $user_profile->user_id);
          if($response == true){
            $message->delete();
            echo 'success';
          }
        }
      }

    }

  }

  //***************************************************************************

  private function get_data($process, $document_id){
    $response_data = [];
    if($process == 'COSTING') {
      $response_data = [
        'costing' => Costing::find($document_id)
      ];
    }
    else if($process == 'CUSTOMER_ORDER'){

    }
    return $response_data;
  }


  public function update_document_status($process_name, $document_id, $status){
    if($process_name == 'COSTING') {
      DB::table('costing')->where('id', $document_id)->update(['status' => $status]);
      //send status to document created user
      $data = [
        'costing' => Costing::find($document_id)
      ];

      $costingService = new CostingService();      
      $costingService->genarate_bom($document_id);
      //$this->generate_bom_for_costing($document_id);

      $mail_subject = 'COSTING ' . $status . ' - '.$document_id;
      $created_user = UsrProfile::find($data['costing']->created_by);
      $to = [['email' => $created_user->email]];
      $job = new ApprovalMailSendJob($process_name.'_CONFIRM', $mail_subject, $data, $to);
      dispatch($job);
    }
  }




  //privae functions, these functions will remove in future developments.
  //those are use temporally


  /*private function generate_bom_for_costing($costing_id) {
    $deliveries = CustomerOrderDetails::where('costing_id', '=', $costing_id)->get();
    $costing = Costing::find($costing_id);
    for($y = 0; $y < sizeof($deliveries); $y++) {
      $bom = new BOMHeader();
      $bom->costing_id = $deliveries[$y]->costing_id;
      $bom->delivery_id = $deliveries[$y]->details_id;
      $bom->sc_no = $costing->sc_no;
      $bom->status = 1;
      $bom->save();

      $components = CostingFinishGoodComponent::where('fg_id', '=', $deliveries[$y]->fg_id)->get()->pluck('id');
      $items = CostingFinishGoodComponentItem::whereIn('fg_component_id', $components)->get();
      $items = json_decode(json_encode($items), true); //conver to array
      for($x = 0 ; $x < sizeof($items); $x++) {
        $items[$x]['bom_id'] = $bom->bom_id;
        $items[$x]['costing_item_id'] = $items[$x]['id'];
        $items[$x]['id'] = 0; //clear id of previous data, will be auto generated
        $items[$x]['bom_unit_price'] = $items[$x]['unit_price'];
        $items[$x]['order_qty'] = $deliveries[$y]->order_qty * $items[$x]['gross_consumption'];
        $items[$x]['required_qty'] = $deliveries[$y]->order_qty * $items[$x]['gross_consumption'];
        $items[$x]['total_cost'] = (($items[$x]['unit_price'] * $items[$x]['gross_consumption'] * $deliveries[$y]->order_qty) + $items[$x]['freight_charges'] + $items[$x]['surcharge']);
        $items[$x]['created_date'] = null;
        $items[$x]['created_by'] = null;
        $items[$x]['updated_date'] = null;
        $items[$x]['updated_by'] = null;
      }
      DB::table('bom_details')->insert($items);
    }
  }*/

}
