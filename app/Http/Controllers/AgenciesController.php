<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use DB;
use App\GovDataReport;
use App\Agencies;
use App\Auth;
use Carbon\Carbon;
class AgenciesController extends Controller
{
    public function all(){
        if (\Auth::user()->hasRole(['Agencies Branch User'])){
                if(\Auth::user()->hasAnyPermission(['Show Agencies File'])){
                    $branches = Agencies::select('*')->with('dl_data')
                    ->with('branch')
                    ->where('branch', \Auth::user()->branch->id)->get();
                }
        }
        if (\Auth::user()->hasRole(['Agencies Admin User','Agencies Guest User'])){
                if(\Auth::user()->hasAnyPermission(['Show All Agencies File'])){
                    $branches = Agencies::select('*')->with('dl_data')
                    ->with('branch')
                    ->get();
                }
        }
        return $branches;
    }
    public function getDate(request $req){
                    $branches = Agencies::selectRaw('year(created_at) as year, month(created_at) as month')
                    ->where('branch', $req->id)
                    ->groupBy('year','month')
                    ->orderByRaw('min(created_at) desc')
                    ->get();
            $data = [];
            foreach($branches as $b){
                $data [] = $b['month'];
            }
            for ($x = 1; $x <= 12; $x++){
                $month[$x] = in_array($x, $data) ? $x : null;
            } 
            return $month;
        
    }
    public function store(request $req){
        $branch = \Auth::user()->branch->id;
        $user = \Auth::user()->first_name .' '.\Auth::user()->last_name;
        function formatBytes($size, $precision = 2)
        {
          $base = log($size, 1024);
          $suffixes = array('', 'K', 'M', 'G', 'T');   
  
          return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
        }
        $random = md5(Str::random(40));
        for ($x = 0; $x <= 10; $x++) {
            if($req->file('file-dole'.$x)){
                $dole[] = ['filename' => $req->file('file-dole'.$x)->getClientOriginalName(),
                           'size'=> $req->file('file-dole'.$x)->getSize(),
                           'key'=> 'file-dole'.$x];
            }
        }
        for ($x = 0; $x <= 10; $x++) {
            if($req->file('file-bir'.$x)){
                $bir[] =  [
                           'filename' => $req->file('file-bir'.$x)->getClientOriginalName(),
                           'size'=> $req->file('file-bir'.$x)->getSize(),
                           'key'=> 'file-bir'.$x];
            }
        }
        for ($x = 0; $x <= 10; $x++) {
            if($req->file('file-lgu'.$x)){
                $lgu[] =  ['filename' =>  $req->file('file-lgu'.$x)->getClientOriginalName(),
                           'size' => $req->file('file-lgu'.$x)->getSize(),
                           'key'=> 'file-lgu'.$x ];
            }
        }
        $data[] = ['dole'=>  @$dole, 'bir'=> @$bir,'lgu'=> @$lgu];
        $agencies = new Agencies;
        $agencies->unique_id = $random;
        $agencies->branch = $branch;
        $agencies->doc_code = 'Document-'.$random;
        $agencies->upload_by = $user;
        $agencies->save();
        foreach($data as $i => $f){
                if($f['dole']){
                    foreach($f['dole'] as $final){
                        $path = Storage::putFile('GovDoc', $req->file(@$final['key']));
                        $insert = new GovDataReport;
                        $insert->unique_id = $random;
                        $insert->path = $path;
                        $insert->filename = @$final['filename'];
                        $insert->size = formatBytes(@$final['size']);
                        $insert->doc_type = 1;
                        $insert->save();
                        $msg[] = 1;
                    }
                }
                if($f['lgu']){
                    foreach($f['lgu'] as $final){
                        $path = Storage::putFile('GovDoc', $req->file(@$final['key']));
                        $insert = new GovDataReport;
                        $insert->unique_id = $random;
                        $insert->path = $path;
                        $insert->filename = @$final['filename'];
                        $insert->size = formatBytes(@$final['size']);
                        $insert->doc_type = 3;
                        $insert->save();
                        $msg[] = 3;
                    }
                }
                if($f['bir']){
                    foreach($f['bir'] as $final){
                        $path = Storage::putFile('GovDoc', $req->file(@$final['key']));
                        $insert = new GovDataReport;
                        $insert->unique_id = $random;
                        $insert->path = $path;
                        $insert->filename = @$final['filename'];
                        $insert->size = formatBytes(@$final['size']);
                        $insert->doc_type = 2;
                        $insert->save();
                        $msg[] = 2;
                    }
       
                }
        }
 
        return $msg;
    }
    public function update(request $req){
        
        $branch = \Auth::user()->branch->id;
        $user = \Auth::user()->first_name .' '.\Auth::user()->last_name;
        function formatBytes($size, $precision = 2)
        {
          $base = log($size, 1024);
          $suffixes = array('', 'K', 'M', 'G', 'T');   
  
          return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
        }
        $random = $req->id;
        for ($x = 0; $x <= 10; $x++) {
            if($req->file('file-dole'.$x)){
                $dole[] = ['filename' => $req->file('file-dole'.$x)->getClientOriginalName(),
                           'size'=> $req->file('file-dole'.$x)->getSize(),
                           'key'=> 'file-dole'.$x];
            }
        }
        for ($x = 0; $x <= 10; $x++) {
            if($req->file('file-bir'.$x)){
                $bir[] =  [
                           'filename' => $req->file('file-bir'.$x)->getClientOriginalName(),
                           'size'=> $req->file('file-bir'.$x)->getSize(),
                           'key'=> 'file-bir'.$x];
            }
        }
        for ($x = 0; $x <= 10; $x++) {
            if($req->file('file-lgu'.$x)){
                $lgu[] =  ['filename' =>  $req->file('file-lgu'.$x)->getClientOriginalName(),
                           'size' => $req->file('file-lgu'.$x)->getSize(),
                           'key'=> 'file-lgu'.$x ];
            }
        }
        $data[] = ['dole'=>  @$dole, 'bir'=> @$bir,'lgu'=> @$lgu];
        $agencies = Agencies::where('unique_id', $random)->first();
        $agencies->upload_by = $user;
        $agencies->update();
        foreach($data as $i => $f){
                if($f['dole']){
                    foreach($f['dole'] as $final){
                        $path = Storage::putFile('GovDoc', $req->file(@$final['key']));
                        $insert = new GovDataReport;
                        $insert->unique_id = $random;
                        $insert->path = $path;
                        $insert->filename = @$final['filename'];
                        $insert->size = formatBytes(@$final['size']);
                        $insert->doc_type = 1;
                        $insert->save();
                        $msg[] = 1;
                    }
                }
                if($f['lgu']){
                    foreach($f['lgu'] as $final){
                        $path = Storage::putFile('GovDoc', $req->file(@$final['key']));
                        $insert = new GovDataReport;
                        $insert->unique_id = $random;
                        $insert->path = $path;
                        $insert->filename = @$final['filename'];
                        $insert->size = formatBytes(@$final['size']);
                        $insert->doc_type = 3;
                        $insert->save();
                        $msg[] = 3;
                    }
                }
                if($f['bir']){
                    foreach($f['bir'] as $final){
                        $path = Storage::putFile('GovDoc', $req->file(@$final['key']));
                        $insert = new GovDataReport;
                        $insert->unique_id = $random;
                        $insert->path = $path;
                        $insert->filename = @$final['filename'];
                        $insert->size = formatBytes(@$final['size']);
                        $insert->doc_type = 2;
                        $insert->save();
                        $msg[] = 2;
                    }
       
                }
        }
 
        return $msg;
    }
    public function trash(request $req){
        $path = "../storage/app/".GovDataReport::where('id',  $req->id['id'])->pluck('path')->first();
        $delete = GovDataReport::where('id',  $req->id['id'])->delete();
        unlink($path);
        return 'ok';
    }
    public function delete(request $req){
        foreach($req['id'] as $id){
            try{
                $deleteIDExternalID =  Agencies::where('id',  $id)->pluck('unique_id')->first();
                $externalPath =  GovDataReport::where('unique_id', $deleteIDExternalID)->pluck('path')->first();
               if($externalPath){
                unlink('../storage/app/'.$externalPath);
               } 
                //DATA DELETE 
                   //DELETE ANGENCY
                   GovDataReport::where('unique_id', $deleteIDExternalID)->delete();
                   Agencies::where('id', $id)->delete();
                   //DELETE AGENCY ATTACHMENT
                 $msg[] = ['id' => $deleteIDExternalID, 'message'=> 'deleted'];
                //DATA DELETE END
            }catch(Exception $e){
                $msg[] = ['id' => $deleteIDExternalID, 'message'=> $e];
            }
        }
        return $msg;
    }
    public function download(request $req){
        $filename = GovDataReport::where('id', $req->id)->pluck('filename')->first();
        $location = GovDataReport::where('id', $req->id)->pluck('path')->first();
        $file = '../storage/app/'. $location;
        return response()->download($file);

    }
}
