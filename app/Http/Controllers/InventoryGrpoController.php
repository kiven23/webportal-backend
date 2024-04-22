<?php

namespace App\Http\Controllers;
 
 
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use DB;
class InventoryGrpoController extends Controller
{
  public function mssqlcon(){
    return \Auth::user()->dbselection->connection;
  }
  public function createGrpo(request $req){
    
    $database = explode(' - ', \Auth::user()->dbselection->dbname);
    $req['ip'] = $database[1];
    $req['db'] = $database[0];
    //$sapuser = \Auth::user()->sapuser;
    // $sappassword = \Auth::user()->sappasword;
    $client = new Client(['timeout' => 300000]);
 
    $response = $client->post('http://192.168.1.26:8082/api/grpo', [
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode($req->all()),
         
    ]);
    
    $body = ($response->getBody());
    return $body;
  }
  public function getLines(request $req){
    $client = new Client();
    $data = $client->request('GET', 'http://192.168.1.26:8082/api/document/getlines?data='.$req->data)->getBody()->getContents();
     
    return response()->json(json_decode($data));
  }
  public function createGrpo1(request $req){
  
    $database = explode(' - ', \Auth::user()->dbselection->dbname);
    $req['ip'] = $database[1];
    $req['db'] = $database[0];
    //$sapuser = \Auth::user()->sapuser;
    // $sappassword = \Auth::user()->sappasword;
    $client = new Client(['timeout' => 300000]);
 
    $response = $client->post('http://192.168.1.26:8082/api/document/grpo', [
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode($req->all()),
         
    ]);
    
    $body = ($response->getBody());
    return $body;
  }
  public function search(request $req){
 
      $database = explode(' -', \Auth::user()->dbselection->dbname);
      $req['db'] = $database[0];
      
      $code = DB::table('branches')->where('id', \Auth::user()->branch_id)->pluck('whscode')->first();
      
      $dd= explode(",", $code);
      $whs = $dd[0];
      $data = \DB::connection($this->mssqlcon())
      ->table('POR1')
      ->where('DocEntry',  $req->data)
      ->whereRaw('LEFT(WhsCode, 4) = ?', [$whs])
      ->get();
      $client = new Client(['timeout' => 300000]);
 
    $response = $client->post('http://192.168.1.26:8082/api/document/getkey', [
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode($req->all()),
         
    ]);
    $all = ['key'=> json_decode($response->getBody()), 'data'=> $data];
   
    return $all;
  }
  public function viewpos(){
      
      $data = \DB::connection($this->mssqlcon())->select("SELECT CardCode,CardName,DocEntry
                FROM OPOR
                -- WHERE YEAR(DocDate) = YEAR(GETDATE())
                -- WHERE MONTH(DocDate) = MONTH(GETDATE())  
                ORDER BY DocDate DESC");
      return Response()->json($data);
  }
  public function progress(Request $req){
    $client = new Client();
    $data = $client->request('GET', 'http://192.168.1.26:8082/api/progress?data='.$req->data)->getBody()->getContents();
    $p['status'] = json_decode($data);
    return response()->json($p);
  }
  public function print(){
    $d = new DNS1D();
    $d->setStorPath(__DIR__.'/cache/');
    return $d->getBarcodeHTML('9780691147727', 'EAN13');
  }
}
