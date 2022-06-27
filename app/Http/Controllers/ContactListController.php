<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ContactList;
use Validator;
use Session;

class ContactListController extends Controller
{

    public function __construct () {
        $this->middleware(['auth', 'contact_list_clearance']);

        // for active routing state
        \View::share('is_mc_contact_list_route', true);
    }

    public function index () {
    	$contactlists = ContactList::all();
    	return view('contact_lists.message_casts.index', compact('contactlists'));
    }

    public function create () {
    	return view('contact_lists.message_casts.create');
    }

    public function store (Request $req) {
    	$rules = [
    		'name' => 'required',
    		'contact_number' => 'required|unique:contact_lists,contact_number|min:4',
    	];

    	$messages = [
    		'name.required' => 'This field is required',
    		'contact_number.required' => 'This field is required',
    		'contact_number.unique' => 'Contact number is a duplicate',
    	];
    	$validator = Validator::make($req->all(), $rules, $messages);

    	if ($validator->fails()) {
    		$flash_message = [
            'title' => 'Oops!',
            'status' => 'danger',
            'message' => 'Please correct all the errors below.',
        ];
    		Session::flash('create_fail', $flash_message);
    		Session::flash('formclass', 'was-validated');
    		return redirect()->back()
    						 ->withErrors($validator)
    						 ->withInput();
    	}

    	$contactlist = new ContactList;
    	$contactlist->name = $req->name;
    	$contactlist->contact_number = $req->contact_number;
    	$contactlist->location = $req->location;
    	$contactlist->save();

    	$flash_message = [
          'title' => 'Well Done!',
          'status' => 'success',
          'message' => 'Contact with mobile number ' . $contactlist->contact_number . ' has been added to our records.',
      ];
    	Session::flash('create_success', $flash_message);
    	if ($req->savebtn == 0) {
    		return redirect()->route('contact_list.message_cast.create');
    	} else {
    		return redirect()->route('contact_lists.message_casts');
    	}
    }

    public function edit ($id) {
    	$contactlist = ContactList::find($id);
    	return view('contact_lists.message_casts.edit', compact('contactlist'));
    }

    public function update ($id, Request $req) {
    	$rules = [
    		'name' => 'required',
    		'contact_number' => 'required|min:4|unique:contact_lists,contact_number,'.$id,
    	];

    	$messages = [
    		'name.required' => 'This field is required',
    		'contact_number.required' => 'This field is required',
    		'contact_number.unique' => 'Contact number is a duplicate',
    	];
    	$validator = Validator::make($req->all(), $rules, $messages);

    	if ($validator->fails()) {
    		$flash_message = [
            'title' => 'Oops!',
            'status' => 'danger',
            'message' => 'Please correct all the errors below.',
        ];
    		Session::flash('update_fail', $flash_message);
    		return redirect()->back()
    						 ->withErrors($validator)
    						 ->withInput();
    	}

    	$contactlist = ContactList::find($id);
    	$contactlist->name = $req->name;
    	$contactlist->contact_number = $req->contact_number;
    	$contactlist->location = $req->location;
    	$contactlist->update();

    	$flash_message = [
          'title' => 'Well Done!',
          'status' => 'success',
          'message' => 'Contact list has been updated successfully.',
      ];
    	Session::flash('update_success', $flash_message);
    	return redirect()->route('contact_lists.message_casts');
    }

    public function trash ($id) {
    	$contactlist = ContactList::find($id);
    	return view('contact_lists.message_casts.trash', compact('contactlist'));
    }

    public function delete ($id) {
    	ContactList::find($id)->delete();

   		$flash_message = [
          'title' => 'Well Done!',
          'status' => 'success',
          'message' => 'Record from Contact List was successfully deleted.',
      ];
   		Session::flash('delete_success', $flash_message);
    	return redirect()->route('contact_lists.message_casts');
    }
}
