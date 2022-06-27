<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use View;
use Session;
use Validator;
use App\Region;

class RegionController extends Controller
{

    public function __construct () {
    	 $this->middleware(['auth', 'region_clearance']);

        // for active routing state
        \View::share('is_region_route', true);
    }

    public function index () {
      	$regions = Region::orderBy('name', 'asc')->get();
      	return view('regions.index', compact('regions'));
    }

    public function create () {
    	 return view('regions.create');
    }

    public function store (Request $request) {
      	$validator = Validator::make($request->all(), [
      		'name' => 'required|max:255|unique:regions',
      		]);

      	if ($validator->fails()) {
        		$flash_message = [
                'title' => 'Oops!',
                'status' => 'danger',
                'message' => 'Please correct all the errors below.',
            ];
            Session::flash('create_fail', $flash_message);
        		return redirect()->back()
          			->withErrors($validator)
          			->withInput();
      	}

      	$region = New Region;
      	$region->name = $request->name;
      	$region->save();

        $flash_message = [
            'title' => 'Well Done!',
            'status' => 'success',
            'message' => $region->name . ' region has been successfully added into our records.',
        ];
        Session::flash('create_fail', $flash_message);

        if ($request->savebtn == 0) {
          return redirect()->route('region.create');
        } else {
          return redirect()->route('regions');
        }
    }

    public function edit ($id) {
        $region = Region::find($id);

        // get previous region id
        $previous = Region::where('id', '<', $id)->max('id');
        // get next region id
        $next = Region::where('id', '>', $id)->min('id');

        return view('regions.edit', compact('region', 'next', 'previous'));
    }

    public function update ($id, Request $request) {
        $validator = Validator::make($request->all(), [
                'name' => 'required|max:255|unique:regions,name,'.$id,
            ]);

        if ($validator->fails()) {
            $flash_message = [
                'title' => 'Oops!',
                'status' => 'danger',
                'message' => 'Please correct all the errors below .',
            ];
            Session::flash('update_fail', $flash_message);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $region = Region::find($id);
        $region->name = $request->name;
        $region->save();

        $flash_message = [
            'title' => 'Well Done!',
            'status' => 'success',
            'message' => 'One (1) record has been successfully updated.',
        ];
        Session::flash('update_success', $flash_message);
        return redirect()->route('regions');
    }

    public function trash ($id) {
        $region = Region::find($id);
        return view('regions.trash', compact('region'));
    }

    public function delete ($id) {
        try {
            $branch = Region::find($id)->delete();
        } catch (\Exception $e) {
            return redirect()->route('regions', ['err' => '1']);
        }
        $flash_message = [
            'title' => 'Well Done!',
            'status' => 'success',
            'message' => 'Existing region has been successfully deleted.',
        ];
        Session::flash('update_success', $flash_message);
        return redirect()->route('regions');
    }















    // api
    public function all () {
    	$regions = Region::select('id', 'name')
                 ->with(['branches' => function ($qry) {
                  $qry->select('region_id', 'name');
                 }])
                 ->get();
    	return response()->json($regions, 200);
    }

    public function store_api (Request $req) {
      $validator = Validator::make($req->all(), [
        'name' => 'required|unique:regions,name',
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

    	$region = new Region;
      $region->name = $req->name;
    	$region->save();

      $region = Region::select('id', 'name')
                ->with(['branches' => function ($qry) {
                  $qry->select('region_id', 'name');
                }])
                ->where('id', $region->id)
                ->first();

    	return response()->json($region, 200);
    }

    public function update_api (Request $req) {
      $validator = Validator::make($req->all(), [
        'name' => 'required|unique:regions,name,'.$req->id,
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

      $region = Region::select('id', 'name')
                ->where('id', $req->id)->first();
      $region->name = $req->name;
      $region->update();

      return response()->json($region, 200);
    }

    public function delete_multiple (Request $req) {
      $ids = $req;
      $region = Region::whereIn('id', $ids)
                ->with(['branches' => function ($qry) {
                  $qry->select('region_id', 'name');
                }])
                ->select('id', 'name')->get();
      $response = $region;
      Region::whereIn('id', $ids)->delete();

      return response()->json($response, 200);
    }
}
