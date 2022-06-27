<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Branch;
use App\Region;
use App\BranchSchedule as BSched;
use Session;
use Validator;

class BranchController extends Controller
{

    public function __construct () {
        $this->middleware(['auth', 'branch_clearance']);

        // for active routing state
        \View::share('is_branch_route', true);
    }

    public function index () {
    	$branches = Branch::orderBy('name', 'asc')->get();
    	return view('branches.index', compact('branches'));
    }

    public function create () {
      $regions = Region::orderBy('name', 'asc')->get();
    	return view('branches.create', compact('regions'));
    }

    public function store (Request $req) {
        $rules = [
            'name' => 'required|unique:branches,name',
            'machine_number' => 'required|unique:branches,machine_number',
            'whscode' => 'required|unique:branches,whscode',
            'bm_oic' => 'required',
        ];
        $messages = [
            'name.unique' => 'Branch ' . $req->name . ' is already in our database. ' . 'Please choose another.',
            'machine_number.unique' => 'Machine number ' . $req->machine_number . ' is already in our database. ' . 'Please choose another.',
            'whscode.unique' => 'Warehouse Code ' . $req->whscode . ' is already in our database. ' . 'Please choose another.',
            'bm_oic.required' => 'The BM/OIC field is required.',
        ];
      	$validator = Validator::make($req->all(), $rules, $messages);

        // REGION DUPLICATE
        $check_region = Region::where('name', $req->region_input)->first();
        if (count($check_region) > 0) {
            $duplicate = 'Duplicate! Please choose another name.';
            $validator->after(function ($validator) use ($duplicate) {
                $validator->getMessageBag()->add('region_duplicate', $duplicate);
            });
        }
        // REGION EMPTY
        if (!$req->region_select && $req->region_input == '') {
            $duplicate = 'The Region input is required.';
            $validator->after(function ($validator) use ($duplicate) {
                $validator->getMessageBag()->add('region_input', $duplicate);
            });
        }

      	if ($validator->fails()) {
      		$flash_message = [
      			'title' => 'Oops!',
      			'status' => 'danger',
      			'message' => 'Please correct all the errors below.',
      		];
      		Session::flash('create_fail', $flash_message);

          // REGION
          if ($req->region_select) {
              Session::flash('region_select', 1);
          } else {
              Session::flash('region_select', 0);
          }

      		return redirect()->back()
      						 ->withErrors($validator)
      						 ->withInput();
      	}

        // REGION
      	if ($req->region_input) {
            $region = new Region;
            $region->name = $req->region_input;
            $region->save();
        }

      	$branch = new Branch;
        // REGION
      	if ($req->region_select) {
            $branch->region_id = $req->region_select;
        } else {
            $branch->region_id = $region->id;
        }
        $branch->machine_number = $req->machine_number;
      	$branch->name = $req->name;
        $branch->whscode = $req->whscode;
      	$branch->bm_oic = $req->bm_oic;
      	$branch->save();

      	$flash_message = [
    			'title' => 'Well done!',
    			'status' => 'success',
    			'message' => $branch->name . ' branch has been successfully added into our database.',
    		];

      	Session::flash('create_success', $flash_message);

      	if ($req->savebtn == 0) {
      		return redirect()->route('branch.create');
      	} else {
      		return redirect()->route('branches.index');
      	}
    }

    public function edit ($id) {
      	$branch = Branch::find($id);
        $regions = Region::orderBy('name', 'asc')->get();
        $bscheds = BSched::orderBy('time_from', 'asc')->get();

        // get previous branch id
        $previous = Branch::where('id', '<', $id)->max('id');
        // get next branch id
        $next = Branch::where('id', '>', $id)->min('id');

      	return view('branches.edit',
               compact('branch',
                       'regions',
                       'bscheds',
                       'next',
                       'previous'));
    }

    public function update ($id, Request $req) {
        $rules = [
            'name' => 'required|unique:branches,name,'.$id,
            'machine_number' => 'required|unique:branches,machine_number,'.$id,
            'whscode' => 'required|unique:branches,whscode,'.$id,
            'bm_oic' => 'required',
        ];

        $message = [
            'name.unique' => 'Branch ' . $req->name . ' is already in our database. ' . 'Please choose another.',
            'machine_number.unique' => 'Machine number ' . $req->machine_number . ' is already in our database. ' . 'Please choose another.',
            'whscode.unique' => 'Warehouse Code ' . $req->whscode . ' is already in our database. ' . 'Please choose another.',
            'bm_oic.required' => 'The BM/OIC field is required.',
        ];
      	$validator = Validator::make($req->all(), $rules, $message);

        // REGION DUPLICATE
        $check_region = Region::where('name', $req->region_input)->first();
        if (count($check_region) > 0) {
            $duplicate = 'Duplicate! Please choose another name.';
            $validator->after(function ($validator) use ($duplicate) {
                $validator->getMessageBag()->add('region_duplicate', $duplicate);
            });
        }
        // REGION EMPTY
        if (!$req->region_select && $req->region_input == '') {
            $duplicate = 'The Region input is required.';
            $validator->after(function ($validator) use ($duplicate) {
                $validator->getMessageBag()->add('region_input', $duplicate);
            });
        }

      	if ($validator->fails()) {
      		$flash_message = [
      			'title' => 'Oops!',
      			'status' => 'danger',
      			'message' => 'Please correct all the errors below.',
      		];
      		Session::flash('update_fail', $flash_message);

          // REGION
          if ($req->region_select) {
              Session::flash('region_select', 1);
          } else {
              Session::flash('region_select', 0);
          }

      		return redirect()->back()
      						 ->withErrors($validator)
      						 ->withInput();
      	}

        // REGION
      	if ($req->region_input) {
            $region = new Region;
            $region->name = $req->region_input;
            $region->save();
        }

      	$branch = Branch::find($id);
        // REGION
      	if ($req->region_select) {
            $branch->region_id = $req->region_select;
        } else {
            $branch->region_id = $region->id;
        }
        $branch->machine_number = $req->machine_number;
        $branch->name = $req->name;
        $branch->whscode = $req->whscode;
        $branch->bm_oic = $req->bm_oic;
      	$branch->bsched_id = $req->bsched_id;
      	$branch->update();

      	$flash_message = [
    			'title' => 'Well done!',
    			'status' => 'success',
    			'message' => 'One (1) record has been successfully updated.',
    		];

      	Session::flash('update_success', $flash_message);
      	return redirect()->route('branches.index');
    }

    public function trash ($id) {
        $branch = Branch::where('id', $id)
                          ->with('users')->first();
        return view('branches.trash', compact('branch'));
    }

    public function delete ($id) {
        try {
            $branch = Branch::find($id)->delete();
        } catch (\Exception $e) {
            return redirect()->route('branches.index', ['err' => '1']);
        }
        $flash_message = [
            'title' => 'Well done!',
            'status' => 'success',
            'message' => 'One (1) record has been successfully deleted.',
        ];
        Session::flash('delete_success', $flash_message);
        return redirect()->route('branches.index');
    }

























    // api
    public function all () {
      $branches = Branch::select('id', 'bsched_id', 'region_id', 'name', 'machine_number', 'whscode', 'bm_oic')
                  ->with(['users' => function ($qry) {
                    $qry->select('branch_id', 'first_name', 'last_name');
                  }])
                  ->with(['schedule' => function ($qry) {
                    $qry->select('id',
                      \DB::raw("CONCAT(TIME_FORMAT(time_from, '%h:%i %p'),' - ', TIME_FORMAT(time_to, '%h:%i %p')) AS time")
                    );
                  }])
                  ->with(['region' => function ($qry) {
                    $qry->select('id', 'name');
                  }])
                  ->get();
      return response()->json($branches, 200);
    }

    public function store_api (Request $req) {
      $validator = Validator::make($req->all(), [
        'name' => 'required|unique:branches,name',
        'region' => 'required',
        'machine_number' => 'required|unique:branches,machine_number',
        'whscode' => 'required|unique:branches,whscode',
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

      $branch = new Branch;
      $branch->name = $req->name;
      $branch->region_id = $req->region;
      if ($req->schedule) {
        $branch->bsched_id = $req->schedule;
      }
      $branch->machine_number = $req->machine_number;
      $branch->whscode = $req->whscode;
      $branch->bm_oic = $req->bm_oic;
      $branch->save();

      $branch = Branch::select('id', 'bsched_id', 'region_id', 'name', 'machine_number', 'whscode', 'bm_oic')
                  ->with(['users' => function ($qry) {
                    $qry->select('branch_id', 'first_name', 'last_name');
                  }])
                  ->with(['region' => function ($qry) {
                    $qry->select('id', 'name');
                  }])
                  ->with(['schedule' => function ($qry) {
                    $qry->select('id',
                      \DB::raw("CONCAT(TIME_FORMAT(time_from, '%h:%i %p'),' - ', TIME_FORMAT(time_to, '%h:%i %p')) AS time")
                    );
                  }])
                  ->where('id', $branch->id)
                  ->first();

      return response()->json($branch, 200);
    }

    public function update_api (Request $req) {
      $validator = Validator::make($req->all(), [
        'name' => 'required|unique:branches,name,'.$req->id,
        'region' => 'required',
        'machine_number' => 'required|unique:branches,machine_number,'.$req->id,
        'whscode' => 'required|unique:branches,whscode,'.$req->id,
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

      $branch = Branch::find($req->id);
      $branch->name = $req->name;
      $branch->region_id = $req->region;
      if ($req->schedule) {
        $branch->bsched_id = $req->schedule;
      } else {
        $branch->bsched_id = null;
      }
      $branch->machine_number = $req->machine_number;
      $branch->whscode = $req->whscode;
      $branch->bm_oic = $req->bm_oic;
      $branch->update();

      $branch = Branch::select('id', 'bsched_id', 'region_id', 'name', 'machine_number', 'whscode', 'bm_oic')
                ->with(['region' => function ($qry) {
                  $qry->select('id', 'name');
                }])
                ->with(['schedule' => function ($qry) {
                  $qry->select('id',
                    \DB::raw("CONCAT(TIME_FORMAT(time_from, '%h:%i %p'),' - ', TIME_FORMAT(time_to, '%h:%i %p')) AS time")
                  );
                }])
                ->where('id', $req->id)->first();

      return response()->json($branch, 200);
    }

    public function delete_multiple (Request $req) {
      $ids = $req;
      $branch = Branch::whereIn('id', $ids)
                ->select('id', 'bsched_id', 'region_id', 'name', 'machine_number', 'whscode', 'bm_oic')
                ->with(['region' => function ($qry) {
                  $qry->select('id', 'name');
                }])
                ->with(['schedule' => function ($qry) {
                  $qry->select('id',
                    \DB::raw("CONCAT(TIME_FORMAT(time_from, '%h:%i %p'),' - ', TIME_FORMAT(time_to, '%h:%i %p')) AS time")
                  );
                }])
                ->get();
      $response = $branch;
      Branch::whereIn('id', $ids)->delete();

      return response()->json($response, 200);
    }
}
