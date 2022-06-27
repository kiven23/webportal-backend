<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ConnectivityTicket as ConnTicket;
use App\ServiceProvider as SProvider;
use App\ServiceType as SType;
use App\ServiceCategory as SCategory;
use App\Branch;
use App\Survey;

use Session;
use Validator;

class ConnectivityTicketController extends Controller
{

    public function __construct () {
      $this->middleware(['auth', 'connectivity_ticket_clearance']);

      // for active routing state
      \View::share('is_sc_connectivity_ticket_route', true);
    }

    public function index () {
    	$connectivities = ConnTicket::orderBy('status', 'asc')
                        ->with(['user' => function ($qry) {
                          $qry->select('id', 'first_name', 'last_name');
                        }])
                        ->with(['branch' => function ($qry) {
                          $qry->select('id', 'name');
                        }])
                        ->with(['service_provider' => function ($qry) {
                          $qry->select('id', 'name');
                        }])
                        ->with(['service_type' => function ($qry) {
                          $qry->select('id', 'name');
                        }])
                        ->with(['service_category' => function ($qry) {
                          $qry->select('id', 'name');
                        }])
                        ->with(['updatedBy' => function ($qry) {
                          $qry->select('id', \DB::raw('CONCAT(first_name, " ", last_name) AS full_name'));
                        }])
                        ->with(['confirmedBy' => function ($qry) {
                          $qry->select('id', \DB::raw('CONCAT(first_name, " ", last_name) AS full_name'));
                        }])
                        ->with(['survey' => function ($qry) {
                          $qry->select('connectivity_ticket_id',
                                       'rater_id',
                                       'rate',
                                       'remarks')
                              ->with(['rater' => function ($qry) {
                                $qry->select('id', \DB::raw('CONCAT(first_name," ",last_name) AS full_name'));
                              }]);
                        }])
                        ->get();
        $total_seconds = 0;
    	return view('tickets.connectivities.index', compact('connectivities', 'total_seconds'));
    }

    public function create () {
    	$branches = Branch::orderBy('name', 'asc')->get();
    	$service_providers = SProvider::orderBy('name', 'asc')->get();
    	$service_types = SType::orderBy('name', 'asc')->get();
    	$service_categories = SCategory::orderBy('name', 'asc')->get();
    	return view('tickets.connectivities.create', compact('branches', 'service_providers', 'service_types', 'service_categories'));
    }

    public function store (Request $req) {
    	$validator = Validator::make($req->all(), [
    			'problem' => 'required',
    			'reported_by_name' => 'required',
    			'reported_by_position' => 'required',
    			'problem_reported' => 'required',
    		]);

    	// BRANCH DUPLICATE
        $check_branch = Branch::where('name', $req->branch_input)->first();
        if (count($check_branch) > 0) {
            $duplicate = 'Duplicate! Please choose another name.';
            $validator->after(function ($validator) use ($duplicate) {
                $validator->getMessageBag()->add('branch_duplicate', $duplicate);
            });
        }
    	// BRANCH EMPTY
        if (!$req->branch_select && $req->branch_input == '') {
            $empty = 'The Branch input is required.';
            $validator->after(function ($validator) use ($empty) {
                $validator->getMessageBag()->add('branch_input', $empty);
            });
        }

        // SERVICE PROVIDER DUPLICATE
        $check_service_provider = SProvider::where('name', $req->service_provider_input)->first();
        if (count($check_service_provider) > 0) {
            $duplicate = 'Duplicate! Please choose another name.';
            $validator->after(function ($validator) use ($duplicate) {
                $validator->getMessageBag()->add('service_provider_duplicate', $duplicate);
            });
        }
    	// SERVICE PROVIDER EMPTY
        if (!$req->service_provider_select && $req->service_provider_input == '') {
            $empty = 'The Service Provider input is required.';
            $validator->after(function ($validator) use ($empty) {
                $validator->getMessageBag()->add('service_provider_input', $empty);
            });
        }

        // SERVICE TYPE DUPLICATE
        $check_service_type = SType::where('name', $req->service_type_input)->first();
        if (count($check_service_type) > 0) {
            $duplicate = 'Duplicate! Please choose another name.';
            $validator->after(function ($validator) use ($duplicate) {
                $validator->getMessageBag()->add('service_type_duplicate', $duplicate);
            });
        }
    	// SERVICE TYPE EMPTY
        if (!$req->service_type_select && $req->service_type_input == '') {
            $empty = 'The Service Type input is required.';
            $validator->after(function ($validator) use ($empty) {
                $validator->getMessageBag()->add('service_type_input', $empty);
            });
        }

        // SERVICE CATEGORY DUPLICATE
        $check_service_category = SCategory::where('name', $req->service_category_input)->first();
        if (count($check_service_category) > 0) {
            $duplicate = 'Duplicate! Please choose another name.';
            $validator->after(function ($validator) use ($duplicate) {
                $validator->getMessageBag()->add('service_category_duplicate', $duplicate);
            });
        }
    	// SERVICE CATEGORY EMPTY
        if (!$req->service_category_select && $req->service_category_input == '') {
            $empty = 'The Service Category input is required.';
            $validator->after(function ($validator) use ($empty) {
                $validator->getMessageBag()->add('service_category_input', $empty);
            });
        }

    	if ($validator->fails()) {
    		$flash_message = [
    			'title' => 'Oops!',
    			'status' => 'danger',
    			'message' => 'Please correct all the errors below.',
    		];
    		Session::flash('create_fail', $flash_message);

    		// BRANCH
            if ($req->branch_select) {
                Session::flash('branch_select', 1);
            } else {
                Session::flash('branch_select', 0);
            }

            // SERVICE PROVIDER
            if ($req->service_provider_select) {
                Session::flash('service_provider_select', 1);
            } else {
                Session::flash('service_provider_select', 0);
            }

            // SERVICE TYPE
            if ($req->service_type_select) {
                Session::flash('service_type_select', 1);
            } else {
                Session::flash('service_type_select', 0);
            }

            // SERVICE CATEGORY
            if ($req->service_category_select) {
                Session::flash('service_category_select', 1);
            } else {
                Session::flash('service_category_select', 0);
            }

    		return redirect()->back()
    						 ->withErrors($validator)
    						 ->withInput();
    	}

    	// BRANCH
    	if ($req->branch_input) {
            $brand = new Branch;
            $brand->name = $req->branch_input;
            $brand->save();
        }

        // SERVICE PROVIDER
    	if ($req->service_provider_input) {
            $service_provider = new SProvider;
            $service_provider->name = $req->service_provider_input;
            $service_provider->save();
        }

        // SERVICE TYPE
    	if ($req->service_type_input) {
            $service_type = new SType;
            $service_type->name = $req->service_type_input;
            $service_type->save();
        }

        // SERVICE TYPE
    	if ($req->service_category_input) {
            $service_category = new SCategory;
            $service_category->name = $req->service_category_input;
            $service_category->save();
        }

    	$connectivity = new ConnTicket;
    	$connectivity->user_id = \Auth::user()->id;
    	$connectivity->updated_by = \Auth::user()->id;

    	// BRANCH
    	if ($req->branch_select) {
            $connectivity->branch_id = $req->branch_select;
        } else {
            $connectivity->branch_id = $brand->id;
        }

        // SERVICE PROVIDER
    	if ($req->service_provider_select) {
            $connectivity->service_provider_id = $req->service_provider_select;
        } else {
            $connectivity->service_provider_id = $service_provider->id;
        }

        // SERVICE TYPE
    	if ($req->service_type_select) {
            $connectivity->service_type_id = $req->service_type_select;
        } else {
            $connectivity->service_type_id = $service_type->id;
        }

        // SERVICE CATEGORY
    	if ($req->service_category_select) {
            $connectivity->service_category_id = $req->service_category_select;
        } else {
            $connectivity->service_category_id = $service_category->id;
        }

    	$connectivity->problem = $req->problem;
    	$connectivity->provider_ticket = $req->provider_ticket;
    	$connectivity->problem_reported_ho = $req->problem_reported;
    	$connectivity->problem_reported_isp = $req->problem_reported_to_isp;
    	$connectivity->resolution_reported = $req->resolution_reported;
    	$connectivity->reported_by_name = $req->reported_by_name;
    	$connectivity->reported_by_position = $req->reported_by_position;
    	$connectivity->remarks = $req->remarks;
    	if ($req->resolution_reported) {
            $connectivity->status = 2;
        } else {
            $connectivity->status = 1;
        }
    	$connectivity->save();

        $connectivity_update = ConnTicket::find($connectivity->id);
        $connectivity_update->ticket_number = 'C' . substr(str_pad($connectivity_update->id, 9, '000000000', STR_PAD_LEFT), -9);
        $connectivity_update->update();

    	$flash_message = [
			'title' => 'Well done!',
			'status' => 'success',
			'message' => 'New ticket has been successfully added into our database.',
		];
    	Session::flash('create_success', $flash_message);

    	return redirect()->route('ticket.connectivities');
    }

    public function edit ($id) {
    	$connectivity = ConnTicket::find($id);
    	$branches = Branch::orderBy('name', 'asc')->get();
    	$service_providers = SProvider::orderBy('name', 'asc')->get();
    	$service_types = SType::orderBy('name', 'asc')->get();
    	$service_categories = SCategory::orderBy('name', 'asc')->get();
    	return view('tickets.connectivities.edit',
    				compact('connectivity',
    						'branches',
    						'service_providers',
    						'service_types',
    						'service_categories'));
    }

    public function update (Request $req, $id) {
    	$validator = Validator::make($req->all(), [
    			'problem' => 'required',
    			'reported_by_name' => 'required',
    			'reported_by_position' => 'required',
    			'problem_reported' => 'required',
    		]);

    	// BRANCH DUPLICATE
        $check_branch = Branch::where('name', $req->branch_input)->first();
        if (count($check_branch) > 0) {
            $duplicate = 'Duplicate! Please choose another name.';
            $validator->after(function ($validator) use ($duplicate) {
                $validator->getMessageBag()->add('branch_duplicate', $duplicate);
            });
        }
    	// BRANCH EMPTY
        if (!$req->branch_select && $req->branch_input == '') {
            $empty = 'The Branch input is required.';
            $validator->after(function ($validator) use ($empty) {
                $validator->getMessageBag()->add('branch_input', $empty);
            });
        }

        // SERVICE PROVIDER DUPLICATE
        $check_service_provider = SProvider::where('name', $req->service_provider_input)->first();
        if (count($check_service_provider) > 0) {
            $duplicate = 'Duplicate! Please choose another name.';
            $validator->after(function ($validator) use ($duplicate) {
                $validator->getMessageBag()->add('service_provider_duplicate', $duplicate);
            });
        }
    	// SERVICE PROVIDER EMPTY
        if (!$req->service_provider_select && $req->service_provider_input == '') {
            $empty = 'The Service Provider input is required.';
            $validator->after(function ($validator) use ($empty) {
                $validator->getMessageBag()->add('service_provider_input', $empty);
            });
        }

        // SERVICE TYPE DUPLICATE
        $check_service_type = SType::where('name', $req->service_type_input)->first();
        if (count($check_service_type) > 0) {
            $duplicate = 'Duplicate! Please choose another name.';
            $validator->after(function ($validator) use ($duplicate) {
                $validator->getMessageBag()->add('service_type_duplicate', $duplicate);
            });
        }
    	// SERVICE TYPE EMPTY
        if (!$req->service_type_select && $req->service_type_input == '') {
            $empty = 'The Service Type input is required.';
            $validator->after(function ($validator) use ($empty) {
                $validator->getMessageBag()->add('service_type_input', $empty);
            });
        }

        // SERVICE CATEGORY DUPLICATE
        $check_service_category = SCategory::where('name', $req->service_category_input)->first();
        if (count($check_service_category) > 0) {
            $duplicate = 'Duplicate! Please choose another name.';
            $validator->after(function ($validator) use ($duplicate) {
                $validator->getMessageBag()->add('service_category_duplicate', $duplicate);
            });
        }
    	// SERVICE CATEGORY EMPTY
        if (!$req->service_category_select && $req->service_category_input == '') {
            $empty = 'The Service Category input is required.';
            $validator->after(function ($validator) use ($empty) {
                $validator->getMessageBag()->add('service_category_input', $empty);
            });
        }

    	if ($validator->fails()) {
    		$flash_message = [
    			'title' => 'Oops!',
    			'status' => 'danger',
    			'message' => 'Please correct all the errors below.',
    		];
    		Session::flash('create_fail', $flash_message);

    		// BRANCH
        if ($req->branch_select) {
            Session::flash('branch_select', 1);
        } else {
            Session::flash('branch_select', 0);
        }

        // SERVICE PROVIDER
        if ($req->service_provider_select) {
            Session::flash('service_provider_select', 1);
        } else {
            Session::flash('service_provider_select', 0);
        }

        // SERVICE TYPE
        if ($req->service_type_select) {
            Session::flash('service_type_select', 1);
        } else {
            Session::flash('service_type_select', 0);
        }

        // SERVICE CATEGORY
        if ($req->service_category_select) {
            Session::flash('service_category_select', 1);
        } else {
            Session::flash('service_category_select', 0);
        }

    		return redirect()->back()
               ->withErrors($validator)
               ->withInput();
    	}

    	// BRANCH
    	if ($req->branch_input) {
        $brand = new Branch;
        $brand->name = $req->branch_input;
        $brand->save();
      }

      // SERVICE PROVIDER
    	if ($req->service_provider_input) {
        $service_provider = new SProvider;
        $service_provider->name = $req->service_provider_input;
        $service_provider->save();
      }

      // SERVICE TYPE
    	if ($req->service_type_input) {
        $service_type = new SType;
        $service_type->name = $req->service_type_input;
        $service_type->save();
      }

      // SERVICE TYPE
    	if ($req->service_category_input) {
        $service_category = new SCategory;
        $service_category->name = $req->service_category_input;
        $service_category->save();
      }

    	$connectivity = ConnTicket::find($id);
    	$connectivity->updated_by = \Auth::user()->id;

    	// BRANCH
    	if ($req->branch_select) {
        $connectivity->branch_id = $req->branch_select;
      } else {
        $connectivity->branch_id = $brand->id;
      }

      // SERVICE PROVIDER
    	if ($req->service_provider_select) {
        $connectivity->service_provider_id = $req->service_provider_select;
      } else {
        $connectivity->service_provider_id = $service_provider->id;
      }

      // SERVICE TYPE
    	if ($req->service_type_select) {
        $connectivity->service_type_id = $req->service_type_select;
      } else {
        $connectivity->service_type_id = $service_type->id;
      }

      // SERVICE CATEGORY
    	if ($req->service_category_select) {
        $connectivity->service_category_id = $req->service_category_select;
      } else {
        $connectivity->service_category_id = $service_category->id;
      }

    	$connectivity->problem = $req->problem;
    	$connectivity->provider_ticket = $req->provider_ticket;
    	$connectivity->problem_reported_ho = $req->problem_reported;
    	$connectivity->problem_reported_isp = $req->problem_reported_to_isp;
    	$connectivity->resolution_reported = $req->resolution_reported;
    	$connectivity->reported_by_name = $req->reported_by_name;
    	$connectivity->reported_by_position = $req->reported_by_position;
    	$connectivity->remarks = $req->remarks;
    	if ($req->resolution_reported) {
        $connectivity->status = 2;
      } else {
        $connectivity->status = 1;
      }
    	$connectivity->update();

    	$flash_message = [
			'title' => 'Well done!',
			'status' => 'success',
			'message' => 'Ticket with ID#' . $connectivity->id . ' has been successfully updated.',
		];
		Session::flash('update_success', $flash_message);
    	return redirect()->route('ticket.connectivities');
    }

    public function trash ($id) {
        $connectivity = ConnTicket::where('id', $id)->first();
        return view('tickets.connectivities.trash', compact('connectivity'));
    }

    public function delete ($id) {
        try {
            $connectivity = ConnTicket::find($id);
            $connectivity_id = $connectivity->id;
        } catch (\Exception $e) {
            return redirect()->route('tickets.connectivities', ['err' => '1']);
        }
        $flash_message = [
            'title' => 'Well done!',
            'status' => 'success',
            'message' => 'Ticket #' . $connectivity_id . ' has been successfully deleted.',
        ];
        Session::flash('delete_success', $flash_message);
        $connectivity->delete();
        return redirect()->route('ticket.connectivities');
    }

    // FOR BRANCH
    public function index_branch () {
    	$connectivities = ConnTicket::orderBy('status', 'asc')
                        ->with(['user' => function ($qry) {
                          $qry->select('id', 'first_name', 'last_name');
                        }])
                        ->with(['branch' => function ($qry) {
                          $qry->select('id', 'name');
                        }])
                        ->with(['service_provider' => function ($qry) {
                          $qry->select('id', 'name');
                        }])
                        ->with(['service_type' => function ($qry) {
                          $qry->select('id', 'name');
                        }])
                        ->with(['service_category' => function ($qry) {
                          $qry->select('id', 'name');
                        }])
                        ->with(['confirmedBy' => function ($qry) {
                          $qry->select('id', \DB::raw('CONCAT(first_name, " ", last_name) AS full_name'));
                        }])
                        ->with(['updatedBy' => function ($qry) {
                          $qry->select('id', \DB::raw('CONCAT(first_name, " ", last_name) AS full_name'));
                        }])
                        ->with(['survey' => function ($qry) {
                          $qry->select('connectivity_ticket_id',
                                       'rater_id',
                                       'rate',
                                       'remarks')
                              ->with(['rater' => function ($qry) {
                                $qry->select('id', \DB::raw('CONCAT(first_name," ",last_name) AS full_name'));
                              }]);
                        }])
                        ->where('branch_id', \Auth::user()->branch->id)
                        ->get();
    	return view('tickets.connectivities.branches.index', compact('connectivities'));
    }

    public function confirm ($id) {
      $connectivity = ConnTicket::orderBy('id', 'asc')
                      ->with(['user' => function ($qry) {
                        $qry->select('id', 'first_name', 'last_name');
                      }])
                      ->with(['branch' => function ($qry) {
                        $qry->select('id', 'name');
                      }])
                      ->with(['service_provider' => function ($qry) {
                        $qry->select('id', 'name');
                      }])
                      ->with(['service_type' => function ($qry) {
                        $qry->select('id', 'name');
                      }])
                      ->with(['service_category' => function ($qry) {
                        $qry->select('id', 'name');
                      }])
                      ->with(['survey' => function ($qry) {
                        $qry->select('connectivity_ticket_id',
                                     'rater_id',
                                     'rate',
                                     'remarks')
                            ->with(['rater' => function ($qry) {
                              $qry->select('id', \DB::raw('CONCAT(first_name," ",last_name) AS full_name'));
                            }]);
                      }])
                      ->where('id', $id)
                      ->first();
      return view('tickets.connectivities.branches.confirm', compact('connectivity'));
    }

    public function confirm_proceed ($id) {
      $connectivity = ConnTicket::find($id);
      $connectivity->status = 3;
      $connectivity->confirmed_by = \Auth::user()->id;
      $connectivity->update();

      $flash_message = [
          'title' => 'Well done!',
          'status' => 'success',
          'message' => 'You have successfully confirmed on uptime of your connectivity',
      ];
      Session::flash('update_success', $flash_message);
      return redirect()->route('ticket.branch.connectivities');
    }

    public function rate ($id) {
      $connectivity = ConnTicket::orderBy('id', 'asc')
                      ->with(['user' => function ($qry) {
                        $qry->select('id', 'first_name', 'last_name');
                      }])
                      ->with(['branch' => function ($qry) {
                        $qry->select('id', 'name');
                      }])
                      ->with(['service_provider' => function ($qry) {
                        $qry->select('id', 'name');
                      }])
                      ->with(['service_type' => function ($qry) {
                        $qry->select('id', 'name');
                      }])
                      ->with(['service_category' => function ($qry) {
                        $qry->select('id', 'name');
                      }])
                      ->where('id', $id)
                      ->first();
      return view('tickets.connectivities.branches.rate', compact('connectivity'));
    }

    public function rate_proceed ($id, Request $req) {
      $rules = [
        'rate' => 'required',
        'remarks' => 'required',
      ];
      $messages = [
        'rate.required' => 'Please leave a star ratings.',
        'remarks.required' => 'Please leave a remarks.',
      ];
      $validator = Validator::make($req->all(), $rules, $messages);
      
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

      $checkSurvey = Survey::where('connectivity_ticket_id', $id)->first();
      
      if (count($checkSurvey) > 0) {
        $survey = Survey::where('connectivity_ticket_id', $id)->first();
      } else {
        $survey = new Survey;
      }
      
      $survey->rater_id = \Auth::user()->id;
      $survey->connectivity_ticket_id = $id;
      $survey->rate = $req->rate;
      $survey->remarks = $req->remarks;
      
      if (count($checkSurvey) > 0) {
        $survey->update();
      } else {
        $survey->save();
      }

      return redirect()->route('ticket.branch.connectivities');
      
    }





































    // api
    public function all () {
      $tickets = ConnTicket::with(['user' => function ($qry) {
                   $qry->select('id', \DB::raw('CONCAT(first_name, " ",last_name) as name'));
                 }])
                 ->with(['branch' => function ($qry) {
                   $qry->select('id', 'name');
                 }])
                 ->with(['service_provider' => function ($qry) {
                   $qry->select('id', 'name');
                 }])
                 ->with(['service_category' => function ($qry) {
                   $qry->select('id', 'name');
                 }])
                 ->with(['service_type' => function ($qry) {
                   $qry->select('id', 'name');
                 }])
                 ->with(['updatedBy' => function ($qry) {
                   $qry->select('id', \DB::raw('CONCAT(first_name, " ",last_name) as name'));
                 }])
                 ->with(['confirmedBy' => function ($qry) {
                   $qry->select('id', \DB::raw('CONCAT(first_name, " ", last_name) AS full_name'));
                 }])
                 ->with(['survey' => function ($qry) {
                   $qry->select('connectivity_ticket_id',
                                'rater_id',
                                'rate',
                                'remarks')
                       ->with(['rater' => function ($qry) {
                         $qry->select('id', \DB::raw('CONCAT(first_name," ",last_name) AS full_name'));
                       }]);
                 }])
                 ->get();
      return response()->json($tickets, 200);
    }

    public function store_api (Request $req) {
      $validator = Validator::make($req->all(), [
        'branch' => 'required',
        'service_provider' => 'required',
        'service_category' => 'required',
        'service_type' => 'required',
        'problem' => 'required',
        'problem_reported_ho' => 'required',
        'reported_by_name' => 'required',
        'reported_by_position' => 'required'
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

      $ticket = new ConnTicket;
      $ticket->user_id = \Auth::user()->id;
      $ticket->branch_id = $req->branch;
      $ticket->problem = $req->problem;
      $ticket->service_provider_id = $req->service_provider;
      $ticket->service_category_id = $req->service_category;
      $ticket->service_type_id = $req->service_type;
    	$ticket->provider_ticket = $req->provider_ticket;
    	$ticket->problem_reported_ho = $req->problem_reported_ho;
    	$ticket->problem_reported_isp = $req->problem_reported_isp;
    	$ticket->resolution_reported = $req->resolution_reported;
    	$ticket->reported_by_name = $req->reported_by_name;
    	$ticket->reported_by_position = $req->reported_by_position;
    	$ticket->remarks = $req->last_update;
    	if ($req->resolution_reported) {
        $ticket->status = 2;
      } else {
        $ticket->status = 1;
      }
    	$ticket->save();

      $ticket_update = ConnTicket::find($ticket->id);
      $ticket_update->ticket_number = 'C' . substr(str_pad($ticket->id, 9, '000000000', STR_PAD_LEFT), -9);
      $ticket_update->update();

      $ticket = ConnTicket::with(['user' => function ($qry) {
                    $qry->select('id', \DB::raw('CONCAT(first_name, " ",last_name) as name'));
                  }])
                  ->with(['branch' => function ($qry) {
                    $qry->select('id', 'name');
                  }])
                  ->with(['service_provider' => function ($qry) {
                    $qry->select('id', 'name');
                  }])
                  ->with(['service_category' => function ($qry) {
                    $qry->select('id', 'name');
                  }])
                  ->with(['service_type' => function ($qry) {
                    $qry->select('id', 'name');
                  }])
                  ->with(['updatedBy' => function ($qry) {
                    $qry->select('id', \DB::raw('CONCAT(first_name, " ",last_name) as name'));
                  }])
                  ->with(['confirmedBy' => function ($qry) {
                    $qry->select('id', \DB::raw('CONCAT(first_name, " ", last_name) AS full_name'));
                  }])
                  ->with(['survey' => function ($qry) {
                    $qry->select('connectivity_ticket_id',
                                'rater_id',
                                'rate',
                                'remarks')
                        ->with(['rater' => function ($qry) {
                          $qry->select('id', \DB::raw('CONCAT(first_name," ",last_name) AS full_name'));
                        }]);
                  }])
                  ->where('id', $ticket->id)
                  ->first();

      return response()->json($ticket, 200);
    }

    public function update_api (Request $req) {
      $validator = Validator::make($req->all(), [
        'branch' => 'required',
        'service_provider' => 'required',
        'service_category' => 'required',
        'service_type' => 'required',
        'problem' => 'required',
        'problem_reported_ho' => 'required',
        'reported_by_name' => 'required',
        'reported_by_position' => 'required'
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

      $ticket = ConnTicket::find($req->id);
    	$ticket->updated_by = \Auth::user()->id;
      $ticket->branch_id = $req->branch;
      $ticket->problem = $req->problem;
      $ticket->service_provider_id = $req->service_provider;
      $ticket->service_category_id = $req->service_category;
      $ticket->service_type_id = $req->service_type;
    	$ticket->provider_ticket = $req->provider_ticket;
    	$ticket->problem_reported_ho = $req->problem_reported_ho;
    	$ticket->problem_reported_isp = $req->problem_reported_isp;
    	$ticket->resolution_reported = $req->resolution_reported;
    	$ticket->reported_by_name = $req->reported_by_name;
    	$ticket->reported_by_position = $req->reported_by_position;
    	$ticket->remarks = $req->last_update;
    	if ($req->resolution_reported) {
        $ticket->status = 2;
      } else {
        $ticket->status = 1;
      }
    	$ticket->update();

      $ticket = ConnTicket::with(['user' => function ($qry) {
                  $qry->select('id', \DB::raw('CONCAT(first_name, " ",last_name) as name'));
                }])
                ->with(['branch' => function ($qry) {
                  $qry->select('id', 'name');
                }])
                ->with(['service_provider' => function ($qry) {
                  $qry->select('id', 'name');
                }])
                ->with(['service_category' => function ($qry) {
                  $qry->select('id', 'name');
                }])
                ->with(['service_type' => function ($qry) {
                  $qry->select('id', 'name');
                }])
                ->with(['updatedBy' => function ($qry) {
                  $qry->select('id', \DB::raw('CONCAT(first_name, " ",last_name) as name'));
                }])
                ->with(['confirmedBy' => function ($qry) {
                  $qry->select('id', \DB::raw('CONCAT(first_name, " ", last_name) AS full_name'));
                }])
                ->with(['survey' => function ($qry) {
                  $qry->select('connectivity_ticket_id',
                              'rater_id',
                              'rate',
                              'remarks')
                      ->with(['rater' => function ($qry) {
                        $qry->select('id', \DB::raw('CONCAT(first_name," ",last_name) AS full_name'));
                      }]);
                }])
                ->where('id', $ticket->id)
                ->first();

      return response()->json($ticket, 200);
    }

    public function delete_multiple (Request $req) {
      $ids = $req;
      $tickets = ConnTicket::select('id')
                 ->whereIn('id', $ids)
                 ->get();
      $response = $tickets;
      ConnTicket::whereIn('id', $ids)->delete();

      return response()->json($response, 200);
    }
}
