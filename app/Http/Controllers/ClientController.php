<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Request;
use DB;
use Session;

use App\User;
use App\Camera;

class ClientController extends Controller
{
	public function __construct () {
		$host = Request::ip();
		$database = 'addessa_customerphoto';
		$username = 'branch';
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
	}

    public function index () {
        $client_email = Auth::user()->email;
        $user = DB::connection('mysql_branch')->table('users')->where('email', $client_email)->get();

		$lists = DB::connection('mysql_branch')->table('cameras')->where('user_id', $user[0]->id)->get();
    	return view('clients.index', compact('lists'));
    }

    public function sync ($id) {
    	$list = DB::connection('mysql_branch')->table('cameras')->where('id', $id)->get()[0];
    	$client_email = Auth::user()->email;
    	$user = User::where('email', $client_email)->first();

    	// save local data from client into server database
    	$wc = new Camera;
        $wc->user_id = $user->id;
        $wc->title = $list->title;
        $wc->first_name = $list->first_name;
        $wc->middle_name = $list->middle_name;
        $wc->last_name = $list->last_name;
        $wc->suffix = $list->suffix;
        $wc->picture = $list->picture;
        $wc->birth_date = $list->birth_date;
        $wc->created_at = $list->created_at;
        $wc->updated_at = $list->updated_at;
        $wc->save();

        // Delete local data from client
        DB::connection('mysql_branch')->table('cameras')->where('id', $id)->delete();

        $flash_message = 'Data successfully synced.';
        Session::flash('sync_success', $flash_message);

        return redirect()->route('client');
    }
}