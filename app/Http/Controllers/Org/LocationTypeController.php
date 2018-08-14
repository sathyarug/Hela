<?php

namespace App\Http\Controllers\Org ;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Org\LocationType;

class LocationTypeController extends Controller
{

    public function get_list(){
        $location_type_list = LocationType::all();
        echo json_encode($location_type_list);
    }

    public function get_active_list(){
        $location_type_list = LocationType::where('status' , '=' , 1)->get();
        echo json_encode($location_type_list);
    }

}

?>
