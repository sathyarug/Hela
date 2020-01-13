<?php

namespace App\Services\Merchandising\Bom;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Merchandising\BOMHeader;
use App\Models\Merchandising\BOMDetails;

class BomService
{

  private function calculate_fabric_cost($bom_id){
    $cost = BOMDetails::join('item_master', 'item_master.master_id', '=', 'bom_details.inventory_part_id')
    ->join('item_category', 'item_category.category_id', '=', 'item_master.category_id')
    ->where('bom_details.bom_id', '=', $bom_id)
    ->where('item_category.category_code', '=', 'FAB')
    ->sum('bom_details.total_cost');
    return $cost;
  }

  private function calculate_trim_cost($bom_id){
    $cost = BOMDetails::join('item_master', 'item_master.master_id', '=', 'bom_details.inventory_part_id')
    ->join('item_category', 'item_category.category_id', '=', 'item_master.category_id')
    ->where('bom_details.bom_id', '=', $bom_id)
    ->where('item_category.category_code', '=', 'TRM')
    ->sum('bom_details.total_cost');
    return $cost;
  }

  private function calculate_elastic_cost($bom_id){
    $cost = BOMDetails::join('item_master', 'item_master.master_id', '=', 'bom_details.inventory_part_id')
    ->join('item_category', 'item_category.category_id', '=', 'item_master.category_id')
    ->where('bom_details.bom_id', '=', $bom_id)
    ->where('item_category.category_code', '=', 'ELA')
    ->sum('bom_details.total_cost');
    return $cost;
  }

  private function calculate_packing_cost($bom_id){
    $cost = BOMDetails::join('item_master', 'item_master.master_id', '=', 'bom_details.inventory_part_id')
    ->join('item_category', 'item_category.category_id', '=', 'item_master.category_id')
    ->where('bom_details.bom_id', '=', $bom_id)
    ->where('item_category.category_code', '=', 'PAC')
    ->sum('bom_details.total_cost');
    return $cost;
  }

  private function calculate_other_cost($bom_id){
    $cost = BOMDetails::join('item_master', 'item_master.master_id', '=', 'bom_details.inventory_part_id')
    ->join('item_category', 'item_category.category_id', '=', 'item_master.category_id')
    ->where('bom_details.bom_id', '=', $bom_id)
    ->where('item_category.category_code', '=', 'OTHER')
    ->sum('bom_details.total_cost');
    return $cost;
  }

  private function calculate_rm_cost($bom_id){
    $cost = BOMDetails::where('bom_id', '=', $bom_id)
    ->sum('total_cost');
    return $cost;
  }


  public function calculate_epm($fob, $total_rm_cost, $smv){
    $epm = ($smv == 0) ? 0 : ($fob - $total_rm_cost) / $smv; //(fob - rm cost) / smv
    return round($epm, 4, PHP_ROUND_HALF_UP ); //round and return
  }

  public function calculate_np($fob, $total_cost){
    $np = ($total_cost == 0) ? 0 : ($total_cost - $fob) / $total_cost; //(total cost - fob) / total cost
    return round($np, 4, PHP_ROUND_HALF_UP ); //round and return
  }


  private function update_bom_summary($bom_id){
    //$costing_item = CostingItem::find($costing_item_id);
    $bom = BOMHeader::find($bom_id);

    $fabric_cost = $this->calculate_fabric_cost($bom_id);
    $trim_cost = $this->calculate_trim_cost($bom_id);
    $packing_cost = $this->calculate_packing_cost($bom_id);
    $elastic_cost = $this->calculate_elastic_cost($bom_id);
    $other_cost = $this->calculate_other_cost($bom_id);

    $total_rm_cost = $this->calculate_rm_cost($bom_id);
    $finance_cost = ($total_rm_cost * $bom->finance_charges) / 100;
    $total_cost = $total_rm_cost + $bom->labour_cost + $finance_cost + $bom->coperate_cost;//rm cost + labour cost + finance cost + coperate cost
    $epm = $this->calculate_epm($bom->fob, $total_rm_cost, $bom->total_smv);//calculate fg epm
    $np = $this->calculate_np($bom->fob, $total_cost); //calculate fg np value

    $bom->total_rm_cost = round($total_rm_cost, 4, PHP_ROUND_HALF_UP ); //round and assign
    $bom->finance_cost = round($finance_cost, 4, PHP_ROUND_HALF_UP ); //round and assign
    $bom->fabric_cost = round($fabric_cost, 4, PHP_ROUND_HALF_UP ); //round and assign
    $bom->trim_cost = round($trim_cost, 4, PHP_ROUND_HALF_UP ); //round and assign
    $bom->packing_cost = round($packing_cost, 4, PHP_ROUND_HALF_UP ); //round and assign
    $bom->elastic_cost = round($elastic_cost, 4, PHP_ROUND_HALF_UP ); //round and assign
    $bom->other_cost = round($other_cost, 4, PHP_ROUND_HALF_UP ); //round and assign
    $bom->total_cost = round($total_cost, 4, PHP_ROUND_HALF_UP ); //round and assign
    $bom->epm = $epm;
    $bom->np_margine = $np;
    $bom->save();
  }

}
