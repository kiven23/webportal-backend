<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ServiceType as SType;
use Session;
use Validator;

class ServiceTypeController extends Controller
{

    // api
    public function all () {
			$types = SType::select('id', 'name')
								->with(['connectivity_tickets' => function ($qry) {
									$qry->select('id', 'service_type_id');
								}])
							 ->orderBy('name', 'asc')->get();
	  	return response()->json($types, 200);
	  }

	  public function store_api (Request $req) {
	    $validator = Validator::make($req->all(), [
	      'name' => 'required|unique:service_types,name',
	    ]);

	    if ($validator->fails()) {
	      $errors = $validator->errors()->getMessages();
	      $obj = $validator->failed();
	      $result = [];
	      foreach($obj as $input => $rules){
	        $i = 0;
	        foreach($rules as $rule => $ruleInfo){
	          $key = $rule;
	          $key = $input.'|'.strtolower($key);
	          $result[$key] = $errors[$input][$i];
	          $i++;
	        }
	      }
	      return response()->json($result, 422);
	    }

	  	$type = new SType;
	    $type->name = $req->name;
	  	$type->save();

			$type = SType::select('id', 'name')
									->with(['connectivity_tickets' => function ($qry) {
										$qry->select('id', 'service_type_id');
									}])
		    					->where('id', $type->id)
		              ->first();

	  	return response()->json($type, 200);
	  }

	  public function update_api (Request $req) {
	    $validator = Validator::make($req->all(), [
	      'name' => 'required|unique:service_types,name,'.$req->id,
	    ]);

	    if ($validator->fails()) {
	      $errors = $validator->errors()->getMessages();
	      $obj = $validator->failed();
	      $result = [];
	      foreach($obj as $input => $rules){
	        $i = 0;
	        foreach($rules as $rule => $ruleInfo){
	          $key = $rule;
	          $key = $input.'|'.strtolower($key);
	          $result[$key] = $errors[$input][$i];
	          $i++;
	        }
	      }
	      return response()->json($result, 422);
	    }

	    $type = SType::find($req->id);
	    $type->name = $req->name;
			$type->update();
			
			$type = SType::select('id', 'name')
										->with(['connectivity_tickets' => function ($qry) {
											$qry->select('id', 'service_type_id');
										}])
										->where('id', $type->id)
										->first();

	    return response()->json($type, 200);
	  }

	  public function delete_multiple (Request $req) {
	    $ids = $req;
	    $type = SType::whereIn('id', $ids)->select('id', 'name')->get();
	    $response = $type;
	    SType::whereIn('id', $ids)->delete();

	    return response()->json($response, 200);
	  }
}
