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
use App\Models\App\ProcessApproval;
use App\Models\App\ProcessApprovalStage;
use App\Models\App\ProcessApprovalStageUser;
use App\Models\Admin\ApprovalStage;
use App\Models\App\ApprovalStageUser;
use App\Models\Admin\UsrProfile;

use App\Jobs\ApprovalMailSendJob;

use App\Models\Merchandising\Costing\Costing;
use App\Models\Merchandising\Costing\CostingFinishGood;

use App\Libraries\Approval;
use App\Services\Merchandising\Costing\CostingService;

class ApprovalController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index', 'approve']]);
    }

    //get Color list
    public function index(Request $request)
    {
        auth()->payload()->get('loc_id');
    }


   public function start(Request $request){

   }


   public function approve(Request $request){
      /*$costingService = new CostingService();
      $costingService->genarate_bom(26);*/
      $approval = new Approval();
      $approval->readMail();
   }


   public function generate_costing_bom(Request $request){
     $costing_id = $request->costing_id;
     $costing = Costing::find($costing_id);
     if($costing != null && $costing->status == 'APPROVED'){
       $costingService = new CostingService();
       $res = $costingService->genarate_bom($costing_id);
       echo json_encode($res);
     }
   }



}
