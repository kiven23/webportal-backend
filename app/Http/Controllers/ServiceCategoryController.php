<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ServiceCategory as SCat;
use Session;
use Validator;

class ServiceCategoryController extends Controller
{

    // api
    public function all () {
			$categories = SCat::select('id', 'name')
										->with(['connectivity_tickets' => function ($qry) {
											$qry->select('id', 'service_category_id');
										}])
							 			->orderBy('name', 'asc')->get();
	  	return response()->json($categories, 200);
	  }

	  public function store_api (Request $req) {
	    $validator = Validator::make($req->all(), [
	      'name' => 'required|unique:service_categories,name',
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

	  	$category = new SCat;
	    $category->name = $req->name;
	  	$category->save();

			$category = SCat::select('id', 'name')
									->with(['connectivity_tickets' => function ($qry) {
										$qry->select('id', 'service_category_id');
									}])
		    					->where('id', $category->id)
		              ->first();

	  	return response()->json($category, 200);
	  }

	  public function update_api (Request $req) {
	    $validator = Validator::make($req->all(), [
	      'name' => 'required|unique:service_categories,name,'.$req->id,
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

	    $category = SCat::find($req->id);
	    $category->name = $req->name;
			$category->update();
			
			$category = SCat::select('id', 'name')
										->with(['connectivity_tickets' => function ($qry) {
											$qry->select('id', 'service_category_id');
										}])
										->where('id', $category->id)
										->first();

	    return response()->json($category, 200);
	  }

	  public function delete_multiple (Request $req) {
	    $ids = $req;
	    $category = SCat::whereIn('id', $ids)->select('id', 'name')->get();
	    $response = $category;
	    SCat::whereIn('id', $ids)->delete();

	    return response()->json($response, 200);
	  }
}
