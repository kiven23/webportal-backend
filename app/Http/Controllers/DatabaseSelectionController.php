<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
 
 
class DatabaseSelectionController extends Controller
{
  public function connections(){
    $db = DB::table('database_selections')->get();
    $active = \Auth::user()->dbselection->id;
    $dblist = ['databases' => $db, 'connection' =>  $active];
    return response()->json($dblist);
  }
  public function update(request $req){
    try {
      DB::table('users')->update([
        'sqldb'=> $req->id
      ]);
      $re = 'ok';
    }catch(Exception $e){
      $re = $e;
    }
    return response()->json($re);
 
    
  }
  
}
 