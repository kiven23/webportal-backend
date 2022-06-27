<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ServiceProvider as SProvider;
use Session;
use Validator;

class ServiceProviderController extends Controller
{

    // api
    public function all () {
			$providers = SProvider::select('id', 'name')
									 ->with(['connectivity_tickets' => function ($qry) {
										$qry->select('id', 'service_provider_id');
									 }])
									 ->orderBy('name', 'asc')->get();
	  	return response()->json($providers, 200);
	  }

	  public function store_api (Request $req) {
	    $validator = Validator::make($req->all(), [
	      'name' => 'required|unique:service_providers,name',
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

	  	$provider = new SProvider;
	    $provider->name = $req->name;
	  	$provider->save();

			$provider = SProvider::select('id', 'name')
									->with(['connectivity_tickets' => function ($qry) {
										$qry->select('id', 'service_provider_id');
									}])
		    					->where('id', $provider->id)
		              ->first();

	  	return response()->json($provider, 200);
	  }

	  public function update_api (Request $req) {
	    $validator = Validator::make($req->all(), [
	      'name' => 'required|unique:service_providers,name,'.$req->id,
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

	    $provider = SProvider::find($req->id);
	    $provider->name = $req->name;
			$provider->update();
			
			$provider = SProvider::select('id', 'name')
										->with(['connectivity_tickets' => function ($qry) {
											$qry->select('id', 'service_provider_id');
										}])
										->where('id', $provider->id)
										->first();

	    return response()->json($provider, 200);
	  }

	  public function delete_multiple (Request $req) {
	    $ids = $req;
	    $provider = SProvider::whereIn('id', $ids)->select('id', 'name')->get();
	    $response = $provider;
	    SProvider::whereIn('id', $ids)->delete();

	    return response()->json($response, 200);
	  }
}
