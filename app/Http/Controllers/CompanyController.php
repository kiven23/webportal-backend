<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Company;

use Session;
use Validator;

class CompanyController extends Controller
{

		public function __construct () {
				$this->middleware(['auth', 'company_clearance']);

				// for active routing state
	      \View::share('is_company_route', true);
		}

		public function index () {
				$companies = Company::all();
				return view('companies.index', compact('companies'));
		}

		public function edit ($id) {
				$company = Company::find($id);
				return view('companies.edit', compact('company'));
		}

		public function trash ($id) {
				$company = Company::find($id);
				return view('companies.trash', compact('company'));
		}

		public function delete ($id) {
				try {
						$company = Company::find($id);
						$company_name = $company->name;
						$company->delete();
				} catch (\Exception $e) {
						return redirect()->route('companies.index', ['err' => '1']);
				}

				// update user's copmany as null
				$user = User::where('company_id', $id)->first();
				if (count($user)) {
					$user->company_id = null;
					$user->update();
				}

				$flash_message = [
					'title' => '',
					'message' => $company_name . ' company has been successfully deleted.',
					'status' => 'success',
				];
				Session::flash('delete_success', $flash_message);
				return redirect()->route('companies.index');
		}

		public function update (Request $req, $id) {
				$validator = Validator::make($req->all(), [
	              'name' => 'required|unique:companies,name,'.$id,
	              'address' => 'required',
	          ]);

		    if ($validator->fails()) {
		      $flash_message = [
		      	'title' => 'Oops!',
		      	'message' => 'Please correct all the errors below.',
		      	'status' => 'danger',
		      ];
		      Session::flash('update_fail', $flash_message);
		      return redirect()->back()
		      			 ->withErrors($validator)
		      			 ->withInput();
		    }

		    $company = Company::find($id);
		    $company->name = $req->name;
		    $company->address = $req->address;
		    $company->contact = $req->contact;
		    $company->email = $req->email;
		    $company->update();

		    $flash_message = [
	      	'title' => 'Well Done!',
	      	'message' => 'Company has been successfully updated.',
	      	'status' => 'success',
	      ];
	      Session::flash('create_success', $flash_message);
		    return redirect()->route('companies.index');
		}

		// AJAX STORE
		public function store_ajax (Request $req) {
		    $validator = Validator::make($req->all(), [
	              'name' => 'required|unique:companies,name',
	              'address' => 'required',
	          ]);

		    if ($validator->fails()) {
		      return response()->json(['validator' => $validator->errors()], 422);
		    }

		    $company = new Company;
		    $company->name = $req->name;
		    $company->address = $req->address;
		    $company->contact = $req->contact;
		    $company->email = $req->email;
		    $company->save();

		    // get currently saved data
		    // $response = Company::where('id', $company->id)
				// ->select('id', 'name', 'address', 'contact', 'email')->first();

				// or get from request
				$req['id'] = $company->id; // insert id first
				$response = $req->all();

		    return response()->json(['response' => $response], 200);
		}
}