<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use DB;
use Auth;
use Excel;
use Carbon;
use Session;
use Validator;

use App\Customer;
use App\File;
use App\User;

class CustomerController extends Controller
{

    protected $customerAdmin;

    public function __construct () {
        $this->middleware(['auth', 'customer_clearance']);

        $host = \Request::ip();
        $database = 'webportal';
        $username = 'root';
        $password = 'jaa0324';
        
        \Config::set('database.connections.mysql_branch', array(
            'driver'    => 'mysql',
            'host'      => $host,
            'database'  => $database,
            'username'  => $username,
            'password'  => $password,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ));

        $this->middleware(function ($request, $next) {
          if (Auth::user()->hasPermissionTo("Import Customers")) {
            $this->customerAdmin = true;
          } else { $this->customerAdmin = false; }

          return $next($request);
        });

        // for active routing state
        \View::share('is_customer_photo_route', true);
    }

    public function index (Request $req) {
        $take = 5;
        $skip = 0;
        $currentPage = \Request::get('page', 1); // Default to 1
        if ($req->search_field) {
            $customers = Customer::orderBy('first_name', 'asc')
                            // ->where('branch_id', \Auth::user()->branch_id)
                            ->when($req->customerAdmin, function($query) {
                              $query->where('branch_id', \Auth::user()->branch_id);
                            })
                            ->where(function($query) use ($req) {
                                $query->where('title', 'like', '%'. $req->search_field .'%')
                                      ->orWhere('first_name', 'like', '%'. $req->search_field .'%')
                                      ->orWhere('last_name', 'like', '%'. $req->search_field .'%')
                                      ->orWhere('middle_name', 'like', '%'. $req->search_field .'%')
                                      ->orWhere('birth_date', 'like', '%'. $req->search_field .'%')
                                      ->orWhere('contact_number', 'like', '%'. $req->search_field .'%');
                            })
                            ->paginate(5);
                            $pagination = $customers->appends(array(
                              'search_field' => $req->search_field 
                            ));
        } else {
            $customers = Customer::latest()
                            // ->where('branch_id', \Auth::user()->branch_id)
                            ->when($req->customerAdmin, function($query) {
                              $query->where('branch_id', \Auth::user()->branch_id);
                            })
                            ->paginate(5);
        }

        $search_field = $req->search_field;
        return view('customers.index', compact('customers', 'search_field'));
    }

    public function basic () {
        return view('customers.basic');
    }

    public function store (Request $req) {
        $validator = Validator::make($req->all(), [
                'picture' => 'required',
                'title' => 'required',
                'first_name' => 'required|max:50',
                'middle_name' => 'required|max:50',
                'last_name' => 'required|max:50',
                'suffix' => 'required',
                'contact_number' => 'required|unique:customers,contact_number',
                'address' => 'required',
                'birth_date' => 'required',
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

        $customer = new Customer;
        $customer->branch_id = Auth::user()->branch->id;
        $customer->title = $req->title;
        $customer->first_name = $req->first_name;
        $customer->middle_name = $req->middle_name;
        $customer->last_name = $req->last_name;
        $customer->suffix = $req->suffix;
        $customer->picture = $req->picture;
        $customer->contact_number = $req->contact_number;
        $customer->address = $req->address;
        $customer->birth_date = $req->birth_date;
        $customer->save();

        $flash_message = [
          'title' => 'Well Done!',
          'status' => 'success',
          'message' => 'Data was successfully stored in our database. You can now print the Image.',
        ];
        Session::flash('create_success', $flash_message);

        return redirect()->route('customer.printimage', ['id' => $customer->id]);
    }

    public function printimage ($id) {
        $customer = Customer::where('id', $id)->first();

        // if customer doesn't belong to user's branch
        abort_if($customer->branch_id <> Auth::user()->branch->id, 403);        

        return view('customers.printimage', compact('customer'));
    }

    public function printimage2 ($id) {
        $customer = Customer::where('id', $id)->first();
        // if customer doesn't belong to user's branch
        abort_if($customer->branch_id <> Auth::user()->branch->id, 403);

        return view('customers.printimage2', compact('customer'));
    }

    public function printimage3 ($id) {
        $customer = Customer::where('id', $id)->first();
        // if customer doesn't belong to user's branch
        abort_if($customer->branch_id <> Auth::user()->branch->id, 403);

        return view('customers.printimage3', compact('customer'));
    }

    public function edit ($id) {
        $customer = Customer::where('id', $id)
                    ->where('branch_id', Auth::user()->branch_id)
                    ->firstOrFail();
        return view('customers.edit', compact('customer'));
    }

    public function update ($id, Request $req) {
        $validator = Validator::make($req->all(), [
                'picture' => 'required',
                'title' => 'required',
                'first_name' => 'required|max:50',
                'middle_name' => 'required|max:50',
                'last_name' => 'required|max:50',
                'suffix' => 'required',
                'contact_number' => 'required|unique:customers,contact_number,'.$id,
                'address' => 'required',
                'birth_date' => 'required',
            ]);

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

        $customer = Customer::where('id', $id)->where('branch_id', Auth::user()->branch->id)->firstOrFail();
        $customer->title = $req->title;
        $customer->first_name = $req->first_name;
        $customer->middle_name = $req->middle_name;
        $customer->last_name = $req->last_name;
        $customer->suffix = $req->suffix;
        $customer->picture = $req->picture;
        $customer->contact_number = $req->contact_number;
        $customer->address = $req->address;
        $customer->birth_date = $req->birth_date;
        $customer->update();

        $flash_message = [
          'title' => 'Well Done!',
          'status' => 'success',
          'message' => 'Data was successfully updated.',
        ];
        Session::flash('update_success', $flash_message);

        return redirect()->route('customer.printimage', ['id' => $customer->id]);
    }

    public function import () {
      return view('customers.import');
    }

    public function import_proceed (Request $req) {
      $rules = [
          'customer_file' => 'required',
      ];
      $messages = [
          'customer_file.required' => 'You must select a file to import.',
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
                           ->withErrors($validator);
      }

      $duplicates = [];

      $csv_path = $req->customer_file->getRealPath();
      try {
        Excel::load($csv_path, function($reader) use ($duplicates) {
          foreach ($reader->toArray() as $csv) {
            $cust_name = $csv['customername'];
            $full_name = explode(", ", $cust_name);
            $family_name = $full_name[0];
            $fm_name = explode(" ", $full_name[1]);
            $first_name = $fm_name[0];
            $middle_name = isset($fm_name[count($fm_name) - 1]) ? $fm_name[count($fm_name) - 1] : '';

            $addresses = array(
              $csv['street'],
              $csv['brgy'],
              $csv['city'],
              $csv['province']
            );

            $contact_number = $csv['cellular'] ? "0" . $csv['cellular'] : null;
            $birthday = $csv['birthday'] ? $csv['birthday'] : null;
            $complete_address = implode(" ", $addresses);
            $existing_customer = Customer::select(
                                  'id',
                                  \DB::raw('CONCAT(first_name, " ", last_name) AS name'),
                                  'contact_number'
                                 )
                                 ->where('contact_number', $contact_number)
                                 ->first();

            if (isset($existing_customer)) {
              array_push($duplicates, $existing_customer);
            } else {
              if ($contact_number != null) {
                $customer = new Customer;
                $customer->branch_id = 0;
                $customer->first_name = $first_name;
                $customer->last_name = $family_name;
                $customer->middle_name = $middle_name;
                $customer->contact_number = $contact_number;
                $customer->address = $complete_address;
                $customer->birth_date = $birthday;
                $customer->save();
              }
            }
          }
          Session::flash('duplicates', $duplicates);
        });
        $flash_message = [
            'title' => 'Well Done!',
            'status' => 'success',
            'message' => 'Import Successful.',
        ];
        Session::flash('create_success', $flash_message);
        return redirect()->route('customers');
      } catch (\Exception $e) {
        $flash_message = [
          'title' => 'Oops!',
          'status' => 'danger',
          'message' => $e->getMessage(),
        ];
        Session::flash('create_fail', $flash_message);
        return redirect()->back();
      }
    }

    // Additional for revision
    public function files ($customer_id) {
        $customer = Customer::where('id', $customer_id)->first();
        // if customer doesn't belong to user's branch
        abort_if($customer->branch_id <> Auth::user()->branch->id, 403);

        $files = File::where('customer_id', $customer_id)->get();
        return view('customers.files.index', compact('customer', 'files'));
    }

    public function file_add ($customer_id) {
        $customer = Customer::where('id', $customer_id)->where('branch_id', Auth::user()->branch->id)->first();
        return view('customers.files.create', compact('customer'));
    }

    public function file_store (Request $req, $customer_id) {
        $validator = Validator::make($req->all(), [
                'file' => 'required|mimes:pdf,jpeg,png',
            ]);

        if ($validator->fails()) {
            $flash_message = [
              'title' => 'Oops',
              'status' => 'danger',
              'message' => 'Please correct all the errors below.',
            ];
            Session::flash('create_fail', $flash_message);

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $file_attrib = base64_encode(file_get_contents($req->file->getRealPath()));
        $file = new File;
        $file->customer_id = $customer_id;
        $file->name = $req->name;
        $file->file = $file_attrib;
        $file->save();

        $flash_message = [
          'title' => 'Well Done!',
          'status' => 'success',
          'message' => 'File was successfully added.',
        ];
        Session::flash('create_success', $flash_message);

        if ($req->savebtn == 0) {
          return redirect()->route('customer.file_add', ['customer_id' => $customer_id]);
        } else {
          return redirect()->route('customer.files', ['customer_id' => $customer_id]);
        }
    }

    public function file_edit ($customer_id, $file_id) {
        $customer = Customer::where('id', $customer_id)->first();
        // if customer doesn't belong to user's branch
        abort_if($customer->branch_id <> Auth::user()->branch->id, 403);

        $file = File::find($file_id);
        // if file doesn't belong to a customer
        abort_if($file->customer_id <> $customer_id, 403);

        return view('customers.files.edit', compact('customer', 'file'));
    }

    public function file_update (Request $req, $customer_id, $file_id) {
        if (isset($file->file)) {
            $validator = Validator::make($req->all(), [
                    'file' => 'required|mimes:pdf,jpeg,png',
                ]);


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
        }
        $customer = Customer::where('id', $customer_id)->first();
        // if customer doesn't belong to user's branch
        abort_if($customer->branch_id <> Auth::user()->branch->id, 403);

        $file = File::where('id', $file_id)->where('customer_id', $customer_id)->first();
        // if file doesn't belong to a customer
        abort_if($file->customer_id <> $customer_id, 403);

        $file->name = $req->name;
        if (isset($req->file)) {
            $file_attrib = base64_encode(file_get_contents($req->file->getRealPath()));
            $file->file = $file_attrib;
        }
        $file->update();

        $flash_message = [
          'title' => 'Well Done!',
          'status' => 'success',
          'message' => 'File was successfully updated.',
        ];
        Session::flash('update_success', $flash_message);

        return redirect()->route('customer.files', ['customer_id' => $customer_id]);
    }

    public function file_download ($customer_id, $file_id) {
        $customer = Customer::where('id', $customer_id)->first();
        // if customer doesn't belong to user's branch
        abort_if($customer->branch_id <> Auth::user()->branch->id, 403);

        $customer_fullname = $customer->first_name . '' . $customer->last_name . '' . Carbon\Carbon::now()->format('m/d/y-h:s:i');

        $file = File::find($file_id);
        $file_contents = base64_decode($file->file);
        $f = finfo_open();
        $mime_type = finfo_buffer($f, $file_contents, FILEINFO_MIME_TYPE);

        $file_name = $file->name ? $file->name : $customer_fullname;
        return response($file_contents)
            ->header('Cache-Control', 'no-cache private')
            ->header('Content-Description', 'File Transfer')
            ->header('Content-Type', $mime_type)
            ->header('Content-length', strlen($file_contents))
            ->header('Content-Disposition', 'attachment; filename=' . $file_name)
            ->header('Content-Transfer-Encoding', 'binary');
    }

    public function file_trash ($customer_id, $file_id) {
        $customer = Customer::where('id', $customer_id)->first();
        // if customer doesn't belong to user's branch
        abort_if($customer->branch_id <> Auth::user()->branch->id, 403);

        $customer_fullname = $customer->first_name . '' . $customer->last_name . '' . Carbon\Carbon::now()->format('m/d/y-h:s:i');

        $file = File::find($file_id);
        // if file doesn't belong to a customer
        abort_if($file->customer_id <> $customer_id, 403);

        $file_name = $file->name ? $file->name : $customer_fullname;

        return view('customers.files.trash', compact('customer', 'file', 'file_name'));
    }

    public function file_delete ($customer_id, $file_id) {
        $customer = Customer::where('id', $customer_id)->first();
        // if customer doesn't belong to user's branch
        abort_if($customer->branch_id <> Auth::user()->branch->id, 403);

        $file = File::find($file_id);
        // if file doesn't belong to a customer
        abort_if($file->customer_id <> $customer_id, 403);

        $file->delete();

        $flash_message = [
          'title' => 'Well Done!',
          'status' => 'success',
          'message' => 'File was successfully deleted.',
        ];
        Session::flash('delete_success', $flash_message);

        return redirect()->route('customer.files', ['customer_id' => $customer_id]);
    }

    public function sync_index () {
        $client_email = Auth::user()->email;
        $user = DB::connection('mysql_branch')->table('users')->where('email', $client_email)->first();

        $lists = DB::connection('mysql_branch')->table('cameras')->where('user_id', $user->id)->get();
        return view('customers.syncs.index', compact('lists'));
    }

    public function sync_proceed ($id) {
        $list = DB::connection('mysql_branch')->table('cameras')->where('id', $id)->get()[0];

        // save local data from client into server database
        $customer = new Customer;
        $customer->branch_id = Auth::user()->branch->id;
        $customer->title = $list->title;
        $customer->first_name = $list->first_name;
        $customer->middle_name = $list->middle_name;
        $customer->last_name = $list->last_name;
        $customer->suffix = $list->suffix;
        $customer->picture = $list->picture;
        $customer->birth_date = $list->birth_date;
        $customer->created_at = $list->created_at;
        $customer->updated_at = $list->updated_at;
        $customer->save();

        // Delete local data from client
        DB::connection('mysql_branch')->table('cameras')->where('id', $id)->delete();

        $flash_message = [
            'title' => 'Well Done!',
            'status' => 'success',
            'message' => 'Data successfully synced.',
        ];
        Session::flash('create_success', $flash_message);

        return redirect()->route('customers');
    }
}
