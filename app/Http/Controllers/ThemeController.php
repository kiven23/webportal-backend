<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Theme;
use Session;
use Validator;

class ThemeController extends Controller
{
    public function update ($id, Request $req) {
    	$theme = Theme::updateOrCreate(
    				['user_id' => $id],
    				[
    					'skin' => $req->skin,
    					'sidebar_mini' => $req->sidebar_mini == "on" ? 1 : 0,
    					'sidebar_collapse' => $req->sidebar_collapse == "on" ? 1 : 0,
    					'fixed' => $req->fixed == "on" ? 1 : 0,
    				]
    			);

    	return redirect()->route('home');

    }
}