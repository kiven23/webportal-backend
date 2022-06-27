<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\File;
use App\FileSetting;

use Validator;

class FileSettingController extends Controller
{
    public function settings () {
      return view('files.settings.index');
    }

    // UPDATE SETTINGS AJAX
    public function update_ajax (Request $req) {
    	if ($req->email_notif == 'true') {
    		$email_notif = 1;
    	} else {
    		$email_notif = 0;
    	}

      $extn_email = User::where('id', \Auth::user()->id)->select('extn_email1', 'extn_email2', 'extn_email3')->first();
      $extn_email = collect($extn_email->toArray())->flatten()->all();

      if (empty(array_filter($extn_email))) {
        return response()->json(['response' => 'empty'], 422);
      } else {
      	$file_setting = FileSetting::where('user_id', \Auth::user()->id)->first();
      	if (count($file_setting) > 0) {
      		// update email notification
      		$file_setting->email_notif = $email_notif;
      		$file_setting->update();
      	} else {
      		$file_setting = new FileSetting;
      		$file_setting->user_id = \Auth::user()->id;
      		$file_setting->email_notif = $email_notif;
      		$file_setting->save();
      	}

      	return response()->json(['email_notif' => $email_notif], 200);
      }
    }

    public function new_email_ajax ($email, $email_notif) {
    	$profile = User::find(\Auth::user()->id);
    	$profile->extn_email1 = $email;
    	$profile->update();

    	if ($email_notif == 'true') {
    		$email_notif = 1;
    	} else {
    		$email_notif = 0;
    	}
    	$file_setting = FileSetting::where('user_id', \Auth::user()->id)->first();
    	if (count($file_setting) > 0) {
    		// update email notification
    		$file_setting->email_notif = $email_notif;
    		$file_setting->update();
    	} else {
    		$file_setting = new FileSetting;
    		$file_setting->user_id = \Auth::user()->id;
    		$file_setting->email_notif = $email_notif;
    		$file_setting->save();
    	}

    	return response()->json(['email_notif' => $email_notif], 200);
    }
}
