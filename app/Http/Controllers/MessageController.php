<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\MessageCastResponse;

use App\Message;
use App\Customer;
use App\MessageCastSetting as Setting;
use App\ContactList;
use App\UserEmployment AS UEmployment;
use DB;
use File;
use Session;
use Validator;
use Response;
use Redirect;
use GuzzleHttp\Client;

class MessageController extends Controller
{

	use MessageCastResponse;

	public function __construct () {
		$this->middleware(['auth', 'message_clearance']);

        // for active routing state
        \View::share('is_mc_message_route', true);
	}

    public function index () {
        // employess with customers
        $employees = \DB::table('user_employments')
                     ->select(
                        'user_id',
                        \DB::raw('CONCAT(first_name, " ", last_name) AS name')
                      )
                     ->whereExists(function ($query) {
                        $query->select(DB::raw(1))
                        ->from('user_customer_maps')
                        ->whereRaw('user_customer_maps.user_id = user_employments.user_id');
                     })
                     ->join('users', 'users.id', 'user_employments.user_id')
                     ->get();
        // $employees->prepend(['user_id' => 0, 'name' => 'send to all']);
        // $contacts = DB::table('contact_lists')->select('id', 'name', 'contact_number')->get();
        // $contacts = DB::table('customers')
        //             ->select(
        //               'id',
        //               \DB::raw('CONCAT(first_name, " ", last_name, "(", contact_number, ")") AS name'),
        //               'contact_number'
        //             )
        //             ->get();
        // $contacts->prepend(['id' => 24, 'name' => 'send to all', 'contact_number' => '0']);
        // $contacts = collect($contacts);

        $messages = Message::select('id', 'send_to',
                      \DB::raw('(CHAR_LENGTH(send_to) - CHAR_LENGTH(REPLACE(send_to, ",", "")) + 1) as total'),
                      'message',
                      'response'
                    )
                    ->orderBy('id', 'desc')
                    ->get();
    		return view('messages.message_casts.compose', compact('contacts', 'employees', 'messages'));
    }

    public function send (Request $req) {
        $validator = Validator::make($req->all(), [
                'send_to' => 'required',
                'message' => 'required|min:4',
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

        // Check if the send to all or to individual
        if (in_array('0', $req->send_to)) {
            $send_to = ContactList::pluck('contact_number')->toArray();
            $send_to = preg_replace('/\D/', '', $send_to);
        } else {
            $send_to = $req->send_to;
            $send_to = preg_replace('/\D/', '', $send_to);
        }

        $send_to = implode(',', $send_to);
        $setting = Setting::first();
    		$full_link = $setting->send_url . 'user=' . $setting->user . '&pass=' . $setting->pass . '&from=' . $setting->from . '&to=' . $send_to . '&msg=' . urlencode($req->message);

        $client = new Client;
        $response = $client->request('GET', $full_link);
        $response_body = $response->getBody();
        $response = $this->response($response_body);

        // save the records to database
        $message = new Message;
        $message->send_to = collect($req->send_to);
        $message->message = $req->message;
        $message->response = $response_body;
        $message->save();

        if ($response['status'] == 'danger') {
            $message = $req->message;
            Session::flash('response', $response);
            Session::flash('message', $message);
            return redirect()->back();
        }

      Session::flash('response', $response);
    	return redirect()->route('messages.message_casts');
		}

    public function check_status ($id) {
        $message = Message::find($id);
        $exploded = explode(',', $message->response);
        $code = $exploded[0];
        if ($code == 20110) {
            Session::flash('status', ['status' => 'danger', 'message' => $exploded[1]]);
            return redirect()->route('messages');
        } elseif ($code == 20300) {
            $transid = $exploded[2];
        }
        $setting = Setting::first();
        $full_link = 'http://mcpro1.sun-solutions.ph/mc/status.aspx?' . 'user=' . $setting->user . '&pass=' . $setting->pass . '&transid=' . $transid;

        $client = new Client;
        $response = $client->request('GET', $full_link);
        $response = $response->getBody();
        $response = substr($response,0,5);
        $response = $this->response($response);

        Session::flash('status', $response);
        return redirect()->route('messages.message_casts');
    }



    public function contacts_ajax ($user_id) {
      if ($user_id == 0) {
        $contacts = Customer::orderBy('name', 'asc')
                    ->select(
                      'id',
                      \DB::raw('CONCAT(first_name, " ", last_name, " (", contact_number, ")") AS name'),
                      'contact_number'
                    )
                    ->where('contact_number', '!=', '')
                    ->get();
      } else {
        $contacts = DB::table('user_customer_maps as ucm')
                    ->orderBy('name', 'asc')
                    ->select(
                      'c.id',
                      \DB::raw('CONCAT(first_name, " ", last_name, " (", contact_number, ")") AS name'),
                      'c.contact_number'
                    )
                    ->join('customers as c', 'c.id', 'ucm.customer_id')
                    ->get();
      }
      return response()->json($contacts, 200);
    }
}
