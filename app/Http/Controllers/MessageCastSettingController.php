<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\MessageCastSetting as Setting;
use Session;
use Validator;

class MessageCastSettingController extends Controller
{

    public function __construct() {
        $this->middleware(['auth', 'message_cast_setting_clearance']);

        // for active routing state
        \View::share('is_mc_setting_route', true);
    }

    public function index () {
      	$setting = Setting::first();
      	return view('settings.message_casts.index', compact('setting'));
    }

    public function update (Request $req) {
        $validator = Validator::make($req->all(), [
                'user' => 'required',
                'pass' => 'required',
                'from' => 'required',
                'send_url' => 'required',
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
      	$setting = Setting::find(1);
      	$setting->user = $req->user;
      	$setting->pass = $req->pass;
      	$setting->from = $req->from;
      	$setting->send_url = $req->send_url;
      	$setting->update();

      	$flash_message = [
          'title' => 'Well Done!',
          'status' => 'success',
          'message' => 'Settings successfully updated.',
        ];
      	Session::flash('update_success', $flash_message);
      	return redirect()->route('settings.message_casts');
    }
}
