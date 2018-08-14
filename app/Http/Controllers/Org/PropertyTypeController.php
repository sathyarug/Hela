<?php

namespace App\Http\Controllers\Org ;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Org\PropertyType;

class PropertyTypeController extends Controller
{

    public function get_list(){
        $property_type_list = PropertyType::all();
        echo json_encode($property_type_list);
    }

    public function get_active_list(){
        $property_type_list = PropertyType::where('status' , '=' , 1)->get();
        echo json_encode($property_type_list);
    }

}

?>
