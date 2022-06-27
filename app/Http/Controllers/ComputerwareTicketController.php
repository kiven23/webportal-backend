<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ComputerwareTicket as CompTicket;
use App\Branch;
use App\ProductItem as Item;
use Session;
use Validator;

class ComputerwareTicketController extends Controller
{

    public function __construct () {
      $this->middleware(['auth', 'computerware_ticket_clearance']);

      // for active routing state
      \View::share('is_sc_computerware_ticket_route', true);
    }

    public function index () {
    	$computerwares = CompTicket::orderBy('id', 'asc')->get();
    	return view('tickets.computerwares.index', compact('computerwares'));
    }

    public function create () {
    	$branches = Branch::orderBy('name', 'asc')->where('id', '!=', 1)->get();
    	$items = Item::all();
    	return view('tickets.computerwares.create', compact('branches', 'items'));
    }

    public function store (Request $req) {
      $validator = Validator::make($req->all(), [
    			'item_select' => 'required',
    			'problem' => 'required',
    			'reported_by_name' => 'required',
    			'reported_by_position' => 'required',
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

    	$computerware = new CompTicket;
    	$computerware->user_id = \Auth::user()->id;

    	// BRANCH
    	if ($req->branch_select) {
            $computerware->branch_id = $req->branch_select;
        } else {
            $computerware->branch_id = $brand->id;
        }

        $computerware->product_item_id = $req->item_select;
    	$computerware->product_item_serial_number = $req->serial_number;
    	$computerware->problem = $req->problem;
    	$computerware->reported_by_name = $req->reported_by_name;
    	$computerware->reported_by_position = $req->reported_by_position;
        $computerware->assigned_tech = $req->assigned_tech;
        $computerware->report_status = $req->report_status;
    	$computerware->remarks = $req->remarks;
    	$computerware->save();

        $computerware_update = CompTicket::find($computerware->id);
        $computerware_update->ticket_number = 'R' . substr(str_pad($computerware_update->id, 9, '000000000', STR_PAD_LEFT), -9);
        $computerware_update->update();

    	$flash_message = [
			'title' => 'Well done!',
			'status' => 'success',
			'message' => 'New ticket has been successfully added into our database.',
		];
    	Session::flash('create_success', $flash_message);

    	return redirect()->route('ticket.computerwares');
    }

    public function edit ($id) {
    	$computerware = CompTicket::find($id);
    	$branches = Branch::orderBy('name', 'asc')->where('id', '!=', 1)->get();
    	$items = Item::orderBy('model', 'asc')->get();
    	return view('tickets.computerwares.edit', compact('computerware', 'branches', 'items'));
    }

    public function update ($id, Request $req) {
        $validator = Validator::make($req->all(), [
    			'item_select' => 'required',
    			'problem' => 'required',
    			'reported_by_name' => 'required',
    			'reported_by_position' => 'required',
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

    	$computerware = CompTicket::find($id);
    	$computerware->user_id = \Auth::user()->id;

    	// BRANCH
    	if ($req->branch_select) {
            $computerware->branch_id = $req->branch_select;
        } else {
            $computerware->branch_id = $brand->id;
        }

        $computerware->product_item_id = $req->item_select;
    	$computerware->product_item_serial_number = $req->product_item_serial_number;
    	$computerware->problem = $req->problem;
    	$computerware->reported_by_name = $req->reported_by_name;
    	$computerware->reported_by_position = $req->reported_by_position;
        $computerware->assigned_tech = $req->assigned_tech;
        $computerware->report_status = $req->report_status;
    	$computerware->remarks = $req->remarks;
    	$computerware->update();

    	$flash_message = [
			'title' => 'Well done!',
			'status' => 'success',
			'message' => 'Ticket with ID#' . $computerware->id . ' has been successfully updated.',
		];
    	Session::flash('update_success', $flash_message);
    	return redirect()->route('ticket.computerwares');
    }

    public function trash ($id) {
        $computerware = CompTicket::where('id', $id)->first();
        return view('tickets.computerwares.trash', compact('computerware'));
    }

    public function delete ($id) {
        try {
            $computerware = CompTicket::find($id);
            $computerware_id = $computerware->id;
        } catch (\Exception $e) {
            return redirect()->route('tickets.computerwares', ['err' => '1']);
        }
        $flash_message = [
            'title' => 'Well done!',
            'status' => 'success',
            'message' => 'Ticket #' . $computerware_id . ' has been successfully deleted.',
        ];
        Session::flash('delete_success', $flash_message);
        $computerware->delete();
        return redirect()->route('ticket.computerwares');
    }



















    // api
    public function all () {
      $tickets = CompTicket::select('id',
                                   'ticket_number',
                                   'user_id',
                                   'branch_id',
                                   'product_item_id',
                                   'product_item_serial_number',
                                   'reported_by_name',
                                   'reported_by_position',
                                   \DB::raw('CONCAT(reported_by_name," (",reported_by_position,")") AS reported_by'),
                                   'problem',
                                   'assigned_tech',
                                   'remarks',
                                   'report_status',
                                   \DB::raw("CONCAT(DATE_FORMAT(date_closed, '%M %d, %Y'),' @ ',TIME_FORMAT(date_closed, '%h:%i %p')) AS date_closed"),
                                   \DB::raw("CONCAT(DATE_FORMAT(created_at, '%M %d, %Y'),' @ ',TIME_FORMAT(created_at, '%h:%i %p')) AS date_reported"))
                 ->with(['logged_by' => function ($qry) {
                   $qry->select('id', \DB::raw('CONCAT(first_name," ",last_name) AS name'));
                 }])
                 ->with(['branch' => function ($qry) {
                   $qry->select('id', 'name');
                 }])
                 ->with(['item' => function ($qry) {
                   $qry->select('id',
                                'model',
                                'product_brand_id',
                                'product_category_id')
                       ->with(['brand' => function ($qry) {
                         $qry->select('id', 'name');
                       }])
                       ->with(['category' => function ($qry) {
                         $qry->select('id', 'name');
                       }]);
                 }])
                 ->get();
      return response()->json($tickets, 200);
    }

    public function store_api (Request $req) {
      $validator = Validator::make($req->all(), [
        'branch' => 'required',
        'productItem' => 'required',
        'problem' => 'required',
        'product_item_serial_number' => 'required|unique:computerware_tickets,product_item_serial_number',
        'reported_by_name' => 'required',
        'assigned_tech' => 'required',
        'remarks' => 'required',
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

      $ticket = new CompTicket;
      $ticket->user_id = \Auth::user()->id;
      $ticket->branch_id = $req->branch;
      $ticket->product_item_id = $req->productItem;
      $ticket->product_item_serial_number = $req->product_item_serial_number;
      $ticket->reported_by_name = $req->reported_by_name;
      $ticket->reported_by_position = $req->reported_by_position;
      $ticket->problem = $req->problem;
      $ticket->assigned_tech = $req->assigned_tech;
      $ticket->remarks = $req->remarks;
      $ticket->report_status = $req->report_status;
      if ($ticket->report_status === 0) {
        $ticket->date_closed = null;
      } else {
        $ticket->date_closed = \Carbon\Carbon::now();
      }
      $ticket->save();

      $ticket_update = CompTicket::find($ticket->id);
      $ticket_update->ticket_number = 'R' . substr(str_pad($ticket->id, 9, '000000000', STR_PAD_LEFT), -9);
      $ticket_update->update();

      $ticket = CompTicket::select('id',
                                   'ticket_number',
                                   'user_id',
                                   'branch_id',
                                   'product_item_id',
                                   'product_item_serial_number',
                                   'reported_by_name',
                                   'reported_by_position',
                                   \DB::raw('CONCAT(reported_by_name," (",reported_by_position,")") AS reported_by'),
                                   'problem',
                                   'assigned_tech',
                                   'remarks',
                                   'report_status',
                                   \DB::raw("CONCAT(DATE_FORMAT(date_closed, '%M %d, %Y'),' @ ',TIME_FORMAT(date_closed, '%h:%i %p')) AS date_closed"),
                                   \DB::raw("CONCAT(DATE_FORMAT(created_at, '%M %d, %Y'),' @ ',TIME_FORMAT(created_at, '%h:%i %p')) AS date_reported"))
                 ->with(['logged_by' => function ($qry) {
                   $qry->select('id', \DB::raw('CONCAT(first_name," ",last_name) AS name'));
                 }])
                 ->with(['branch' => function ($qry) {
                   $qry->select('id', 'name');
                 }])
                 ->with(['item' => function ($qry) {
                   $qry->select('id',
                                'model',
                                'product_brand_id',
                                'product_category_id')
                       ->with(['brand' => function ($qry) {
                         $qry->select('id', 'name');
                       }])
                       ->with(['category' => function ($qry) {
                         $qry->select('id', 'name');
                       }]);
                 }])
                 ->where('id', $ticket->id)
                 ->first();

      return response()->json($ticket, 200);
    }

    public function update_api (Request $req) {
      $validator = Validator::make($req->all(), [
        'branch' => 'required',
        'productItem' => 'required',
        'problem' => 'required',
        'product_item_serial_number' => 'required|unique:computerware_tickets,product_item_serial_number,'.$req->id,
        'reported_by_name' => 'required',
        'assigned_tech' => 'required',
        'remarks' => 'required',
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

      $ticket = CompTicket::find($req->id);
      $ticket->branch_id = $req->branch;
      $ticket->product_item_id = $req->productItem;
      $ticket->product_item_serial_number = $req->product_item_serial_number;
      $ticket->reported_by_name = $req->reported_by_name;
      $ticket->reported_by_position = $req->reported_by_position;
      $ticket->problem = $req->problem;
      $ticket->assigned_tech = $req->assigned_tech;
      $ticket->remarks = $req->remarks;
      $ticket->report_status = $req->report_status;
      if ($ticket->report_status === 0) {
        $ticket->date_closed = null;
      } else {
        $ticket->date_closed = \Carbon\Carbon::now();
      }
      $ticket->update();

      $ticket = CompTicket::select('id',
                                   'ticket_number',
                                   'user_id',
                                   'branch_id',
                                   'product_item_id',
                                   'product_item_serial_number',
                                   'reported_by_name',
                                   'reported_by_position',
                                   \DB::raw('CONCAT(reported_by_name," (",reported_by_position,")") AS reported_by'),
                                   'problem',
                                   'assigned_tech',
                                   'remarks',
                                   'report_status',
                                   \DB::raw("CONCAT(DATE_FORMAT(date_closed, '%M %d, %Y'),' @ ',TIME_FORMAT(date_closed, '%h:%i %p')) AS date_closed"),
                                   \DB::raw("CONCAT(DATE_FORMAT(created_at, '%M %d, %Y'),' @ ',TIME_FORMAT(created_at, '%h:%i %p')) AS date_reported"))
                 ->with(['logged_by' => function ($qry) {
                   $qry->select('id', \DB::raw('CONCAT(first_name," ",last_name) AS name'));
                 }])
                 ->with(['branch' => function ($qry) {
                   $qry->select('id', 'name');
                 }])
                 ->with(['item' => function ($qry) {
                   $qry->select('id',
                                'model',
                                'product_brand_id',
                                'product_category_id')
                       ->with(['brand' => function ($qry) {
                         $qry->select('id', 'name');
                       }])
                       ->with(['category' => function ($qry) {
                         $qry->select('id', 'name');
                       }]);
                 }])
                 ->where('id', $req->id)
                 ->first();

      return response()->json($ticket, 200);
    }

    public function delete_multiple (Request $req) {
      $ids = $req;
      $tickets = CompTicket::select('id',
                                   'ticket_number',
                                   'user_id',
                                   'branch_id',
                                   'product_item_id',
                                   'product_item_serial_number',
                                   'reported_by_name',
                                   'reported_by_position',
                                   \DB::raw('CONCAT(reported_by_name," (",reported_by_position,")") AS reported_by'),
                                   'problem',
                                   'assigned_tech',
                                   'remarks',
                                   'report_status',
                                   \DB::raw("CONCAT(DATE_FORMAT(date_closed, '%M %d, %Y'),' @ ',TIME_FORMAT(date_closed, '%h:%i %p')) AS date_closed"),
                                   \DB::raw("CONCAT(DATE_FORMAT(created_at, '%M %d, %Y'),' @ ',TIME_FORMAT(created_at, '%h:%i %p')) AS date_reported"))
                 ->with(['logged_by' => function ($qry) {
                   $qry->select('id', \DB::raw('CONCAT(first_name," ",last_name) AS name'));
                 }])
                 ->with(['branch' => function ($qry) {
                   $qry->select('id', 'name');
                 }])
                 ->with(['item' => function ($qry) {
                   $qry->select('id',
                                'model',
                                'product_brand_id',
                                'product_category_id')
                       ->with(['brand' => function ($qry) {
                         $qry->select('id', 'name');
                       }])
                       ->with(['category' => function ($qry) {
                         $qry->select('id', 'name');
                       }]);
                 }])
                 ->whereIn('id', $ids)
                 ->get();
      $response = $tickets;
      CompTicket::whereIn('id', $ids)->delete();

      return response()->json($response, 200);
    }
}
