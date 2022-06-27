<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CustomerDigitizedReq as cdr;
use App\DocCcsAttachment as attach;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DB;
use App\Branch;
class CustomerDigitizedReqController extends Controller
{
    public function index(){
        $data = cdr::select('*')->with('dl_data')
        ->with('branch')
        ->get();
        return $data;
    }
    public function branches(){
        $branches = Branch::orderBy('name', 'asc')->get();
        foreach($branches as $branch){
            $b[] = ['name'=> $branch->name, 'value'=> $branch->id, 'sapcode' => $branch->sapcode];
        }
    	return response()->json($b);
    }
    public function upload(request $req){
     
        function formatBytes($size, $precision = 2)
        {
          $base = log($size, 1024);
          $suffixes = array('', 'K', 'M', 'G', 'T');   
  
          return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
        }
        $random = md5(Str::random(40));
        for ($x = 0; $x <= 10; $x++) {
            if($req->file('file-valid-id'.$x)){
                $valid_id[] = ['filename' => $req->file('file-valid-id'.$x)->getClientOriginalName(),
                               'size'=> $req->file('file-valid-id'.$x)->getSize(),
                               'key'=> 'file-valid-id'.$x];
            }
        }
        for ($x = 0; $x <= 10; $x++) {
            if($req->file('file-proof-billing'.$x)){
                $proof_of_billing[] = ['filename' => $req->file('file-proof-billing'.$x)->getClientOriginalName(),
                               'size'=> $req->file('file-proof-billing'.$x)->getSize(),
                               'key'=> 'file-proof-billing'.$x];
            }
        }
        $picture_file = ['filename' => $req->file('file-picture_file')->getClientOriginalName(),
                         'size'=> $req->file('file-picture_file')->getSize(),
                         'key'=> 'file-picture_file'];
        $file_application_form = ['filename' => $req->file('file-application-form')->getClientOriginalName(),
                         'size'=> $req->file('file-application-form')->getSize(),
                         'key'=> 'file-application-form'];
        $data[] = [
            'VALID'=> $valid_id,
            'PROOFOFBILLING'=> $proof_of_billing,
            'PICTURE' => $picture_file,
            'APPLICATIONFORM' => $file_application_form
        ];
        $insert = new cdr;
        $insert->doc_id = $random;
        $insert->customer_name = $req['customer-name'];
        $insert->birthday = $req->birthday;
        $insert->branch = $req->branch;
        $insert->unit_availed = $req['unit-avail'];
        $insert->save();
        foreach($data as $attach){  
            if($attach['VALID']){
                foreach($attach['VALID'] as $final){
                    $path = Storage::putFile('ccs_file', $req->file(@$final['key']));
                    $upload = new attach;
                    $upload->doc_id = $random;
                    $upload->path = $path;
                    $upload->filename = $final['filename'];
                    $upload->size = formatBytes($final['size']);
                    $upload->doc_type = 3;
                    $upload->save();
                }
            }
            if($attach['PROOFOFBILLING']){
                foreach($attach['PROOFOFBILLING'] as $final){
                    $path = Storage::putFile('ccs_file', $req->file(@$final['key']));
                    $upload = new attach;
                    $upload->doc_id = $random;
                    $upload->path = $path;
                    $upload->filename = $final['filename'];
                    $upload->size = formatBytes($final['size']);
                    $upload->doc_type = 4;
                    $upload->save();
                }
            }
            if($attach['PICTURE']){
                    $path = Storage::putFile('ccs_file', $req->file(@$attach['PICTURE']['key']));
                    $upload = new attach;
                    $upload->doc_id = $random;
                    $upload->path = $path;
                    $upload->filename = $attach['PICTURE']['filename'];
                    $upload->size = formatBytes($final['size']);
                    $upload->doc_type = 2;
                    $upload->save();
            }
            if($attach['APPLICATIONFORM']){
                    $path = Storage::putFile('ccs_file', $req->file(@$attach['APPLICATIONFORM']['key']));
                    $upload = new attach;
                    $upload->doc_id = $random;
                    $upload->path = $path;
                    $upload->filename = $attach['APPLICATIONFORM']['filename'];
                    $upload->size = formatBytes($attach['APPLICATIONFORM']['size']);
                    $upload->doc_type = 1;
                    $upload->save();
            }
        }
        return $data;
    }

    public function update(request $req){
        $checkbranch = [];
        if($req->branch !== 'undefined'){
          $checkbranch[] = 'meron';
        } 
 
        function formatBytes($size, $precision = 2)
        {
          $base = log($size, 1024);
          $suffixes = array('', 'K', 'M', 'G', 'T');   
  
          return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
        }
        $random = $req->id;
        for ($x = 0; $x <= 10; $x++) {
            if($req->file('file-valid-id'.$x)){
                $valid_id[] = ['filename' => $req->file('file-valid-id'.$x)->getClientOriginalName(),
                               'size'=> $req->file('file-valid-id'.$x)->getSize(),
                               'key'=> 'file-valid-id'.$x];
            }
        }
        for ($x = 0; $x <= 10; $x++) {
            if($req->file('file-proof-billing'.$x)){
                $proof_of_billing[] = ['filename' => $req->file('file-proof-billing'.$x)->getClientOriginalName(),
                                       'size'=> $req->file('file-proof-billing'.$x)->getSize(),
                                       'key'=> 'file-proof-billing'.$x];
            }
        }
        if($req->file('file-picture_file')){
            $picture_file = ['filename' => $req->file('file-picture_file')->getClientOriginalName(),
            'size'=> $req->file('file-picture_file')->getSize(),
            'key'=> 'file-picture_file'];
        }
        if($req->file('file-application-form')){
            $file_application_form = ['filename' => $req->file('file-application-form')->getClientOriginalName(),
            'size'=> $req->file('file-application-form')->getSize(),
            'key'=> 'file-application-form'];
        }
 
        $data[] = [
            'VALID'=> @$valid_id,
            'PROOFOFBILLING'=> @$proof_of_billing,
            'PICTURE' => @$picture_file,
            'APPLICATIONFORM' => @$file_application_form
        ];
        $update = cdr::where('doc_id', $random)->first();
        $update->customer_name = $req['customer-name'];
        $update->birthday = $req->birthday;
        if($checkbranch){
            $update->branch = $req->branch;
        }
        $update->unit_availed = $req['unit-avail'];
        $update->update();
        foreach($data as $attach){  
            if($attach['VALID']){
                foreach($attach['VALID'] as $final){
                    $path = Storage::putFile('ccs_file', $req->file(@$final['key']));
                    $upload = new attach;
                    $upload->doc_id = $random;
                    $upload->path = $path;
                    $upload->filename = $final['filename'];
                    $upload->size = formatBytes($final['size']);
                    $upload->doc_type = 3;
                    $upload->save();
                }
            }
            if($attach['PROOFOFBILLING']){
                foreach($attach['PROOFOFBILLING'] as $final){
                    $path = Storage::putFile('ccs_file', $req->file(@$final['key']));
                    $upload = new attach;
                    $upload->doc_id = $random;
                    $upload->path = $path;
                    $upload->filename = $final['filename'];
                    $upload->size = formatBytes($final['size']);
                    $upload->doc_type = 4;
                    $upload->save();
                }
            }
            if($attach['PICTURE']){
                    $path = Storage::putFile('ccs_file', $req->file(@$attach['PICTURE']['key']));
                    $upload = new attach;
                    $upload->doc_id = $random;
                    $upload->path = $path;
                    $upload->filename = $attach['PICTURE']['filename'];
                    $upload->size = formatBytes($attach['PICTURE']['size']);
                    $upload->doc_type = 2;
                    $upload->save();
            }
            if($attach['APPLICATIONFORM']){
                    $path = Storage::putFile('ccs_file', $req->file(@$attach['APPLICATIONFORM']['key']));
                    $upload = new attach;
                    $upload->doc_id = $random;
                    $upload->path = $path;
                    $upload->filename = $attach['APPLICATIONFORM']['filename'];
                    $upload->size = formatBytes($attach['APPLICATIONFORM']['size']);
                    $upload->doc_type = 1;
                    $upload->save();
            }

        }

        return $data;
    }
    public function download(request $req){
        $filename = attach::where('id', $req->id)->pluck('filename')->first();
        $location = attach::where('id', $req->id)->pluck('path')->first();
        $file = '../storage/app/'. $location;
        return response()->download($file);
    }
    public function trash(request $req){
        $path = "../storage/app/".attach::where('id',  $req->id['id'])->pluck('path')->first();
        $delete = attach::where('id',  $req->id['id'])->delete();
        unlink($path);
        return 'ok';
    }
    public function delete(request $req){
        foreach($req['id'] as $id){
            try{
                $deleteIDExternalID = cdr::where('id',  $id)->pluck('doc_id')->first();
                $externalPath =  attach::where('doc_id', $deleteIDExternalID)->pluck('path')->first();
               if($externalPath){
                unlink('../storage/app/'.$externalPath);
               } 
                //DATA DELETE 
                   //DELETE ANGENCY
                   attach::where('doc_id', $deleteIDExternalID)->delete();
                   cdr::where('id', $id)->delete();
                   //DELETE AGENCY ATTACHMENT
                 $msg[] = ['id' => $deleteIDExternalID, 'message'=> 'deleted'];
                //DATA DELETE END
            }catch(Exception $e){
                $msg[] = ['id' => $deleteIDExternalID, 'message'=> $e];
            }
        }
        return $msg;
    }
}
