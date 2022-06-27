<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Theme;

class HomeController extends Controller {

    public function index () {
        $theme = Theme::where('user_id', \Auth::user()->id)->first();
        return view('home', compact('theme'));
    }
}
