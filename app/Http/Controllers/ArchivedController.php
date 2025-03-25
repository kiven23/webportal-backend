<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Archived;
use App\GovDataReport;
use App\Archived_Add;
use DB;

class ArchivedController extends Controller
{
    public function all(){
        
        $archived = Archived::select('*')
                        ->with('tct_data')
                        ->with('tax_dec_data')
                        ->with('deed_of_sale_data')
                        ->with('real_property_data')
                        ->with('vicinity_map_data')
                        ->with(['archived_new' => function($sql){
                                $sql->with('tct_data2')
                                ->with('tax_dec_data2')
                                ->with('deed_of_sale_data2')
                                ->with('real_property_data2')
                                ->with('vicinity_map_data2');
                          }])
                        ->get();
       return response()->json($archived );
    }
    public function store(Request $req){
        
        function formatBytes($size, $precision = 2)
        {
          $base = log($size, 1024);
          $suffixes = array('', 'K', 'M', 'G', 'T');   
  
          return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
        }

        $uploads = [];
        if($req->file('tctFile-file')){
          $fileData = ['filesize' =>  $req->file('tctFile-file')->getSize(),
                       'filename'=>   $req->file('tctFile-file')->getClientOriginalName(),
                       'tctFile-file' => $req->file('tctFile-file')->getRealPath(),
                       'file'=> 'tctFile-file' ,
                       'unique_id' => md5($req->file('tctFile-file')->getClientOriginalName().$req->tctNumber)];
           array_push($uploads,$fileData);
        } 
        if($req->file('taxdecFile-file')){
           $fileData = ['filesize' =>  $req->file('taxdecFile-file')->getSize(),
                        'filename'=> $req->file('taxdecFile-file')->getClientOriginalName(), 
                        'taxdecFile-file'  => $req->file('taxdecFile-file')->getRealPath(),
                        'file'=> 'taxdecFile-file',
                        'unique_id'=>  md5($req->file('taxdecFile-file')->getClientOriginalName().$req->taxDecNumber)];
            array_push($uploads,$fileData);
         } 
         if($req->file('realPropertyTaxFile-file')){
           $fileData = ['filesize' =>  $req->file('realPropertyTaxFile-file')->getSize(),
                        'filename'=> $req->file('realPropertyTaxFile-file')->getClientOriginalName(),
                        'realPropertyTaxFile-file' => $req->file('realPropertyTaxFile-file')->getRealPath(),
                        'file'=> 'realPropertyTaxFile-file',
                        'unique_id'=>md5($req->file('realPropertyTaxFile-file')->getClientOriginalName().$req->realPropertyTaxNumber)];
            array_push($uploads,$fileData);
         } 
         if($req->file('vicinityMapFile-file')){
           $fileData = ['filesize' =>  $req->file('vicinityMapFile-file')->getSize(),
                        'filename'=> $req->file('vicinityMapFile-file')->getClientOriginalName(),
                        'vicinityMapFile-file' => $req->file('vicinityMapFile-file')->getRealPath(),
                        'file'=> 'vicinityMapFile-file',
                        'unique_id'=> md5( $req->file('vicinityMapFile-file')->getClientOriginalName())];
            array_push($uploads,$fileData);
         } 
         if($req->file('deedOfSaleFile-file')){
           $fileData = ['filesize' =>  $req->file('deedOfSaleFile-file')->getSize(),
                        'filename'=>$req->file('deedOfSaleFile-file')->getClientOriginalName(),
                        'deedOfSaleFile-file'   => $req->file('deedOfSaleFile-file')->getRealPath(),
                        'file'=> 'deedOfSaleFile-file',
                        'unique_id'=> md5($req->file('deedOfSaleFile-file')->getClientOriginalName())];
            array_push($uploads,$fileData);
         } 
    
         if($req->file('tctFile-file2')){
            $fileData = ['filesize' =>  $req->file('tctFile-file2')->getSize(),
                         'filename'=>   $req->file('tctFile-file2')->getClientOriginalName(),
                         'tctFile-file' => $req->file('tctFile-file2')->getRealPath(),
                         'file'=> 'tctFile-file2' ,
                         'unique_id' => md5($req->file('tctFile-file2')->getClientOriginalName().$req->tctNumber)];
             array_push($uploads,$fileData);
          } 
          if($req->file('taxdecFile-file2')){
             $fileData = ['filesize' =>  $req->file('taxdecFile-file2')->getSize(),
                          'filename'=> $req->file('taxdecFile-file2')->getClientOriginalName(), 
                          'taxdecFile-file2'  => $req->file('taxdecFile-file2')->getRealPath(),
                          'file'=> 'taxdecFile-file2',
                          'unique_id'=>  md5($req->file('taxdecFile-file2')->getClientOriginalName().$req->taxDecNumber)];
              array_push($uploads,$fileData);
           } 
           if($req->file('realPropertyTaxFile-file2')){
             $fileData = ['filesize' =>  $req->file('realPropertyTaxFile-file2')->getSize(),
                          'filename'=> $req->file('realPropertyTaxFile-file2')->getClientOriginalName(),
                          'realPropertyTaxFile-file2' => $req->file('realPropertyTaxFile-file2')->getRealPath(),
                          'file'=> 'realPropertyTaxFile-file2',
                          'unique_id'=>md5($req->file('realPropertyTaxFile-file2')->getClientOriginalName().$req->realPropertyTaxNumber)];
              array_push($uploads,$fileData);
           } 
           if($req->file('vicinityMapFile-file2')){
             $fileData = ['filesize' =>  $req->file('vicinityMapFile-file2')->getSize(),
                          'filename'=> $req->file('vicinityMapFile-file2')->getClientOriginalName(),
                          'vicinityMapFile-file2' => $req->file('vicinityMapFile-file2')->getRealPath(),
                          'file'=> 'vicinityMapFile-file2',
                          'unique_id'=> md5( $req->file('vicinityMapFile-file2')->getClientOriginalName())];
              array_push($uploads,$fileData);
           } 
           if($req->file('deedOfSaleFile-file2')){
             $fileData = ['filesize' =>  $req->file('deedOfSaleFile-file2')->getSize(),
                          'filename'=>$req->file('deedOfSaleFile-file2')->getClientOriginalName(),
                          'deedOfSaleFile-file2'   => $req->file('deedOfSaleFile-file2')->getRealPath(),
                          'file'=> 'deedOfSaleFile-file2',
                          'unique_id'=> md5($req->file('deedOfSaleFile-file2')->getClientOriginalName())];
              array_push($uploads,$fileData);
           } 


        $upload = [$uploads];
        $insert = new Archived;
        $insert2 = new Archived_Add;
        $insert->tct_no = $req->tctNumber;
        $insert2->tct_no = $req->tctNumber;
        foreach($upload[0] as $files){
            $path = Storage::putFile('GovDoc', $req->file($files['file']));
            $upload = new GovDataReport;
            $upload->unique_id = $files['unique_id'];
            $upload->path =  $path;
            $upload->filename = $files['filename'];
            $upload->size = formatBytes($files['filesize']);
            $upload->save();

            if($files['file'] == 'tctFile-file'){
                $insert->tct_data = $files['unique_id'];
            }
            if($files['file'] == 'taxdecFile-file'){
                $insert->tax_dec_data = $files['unique_id'];
            } 

            if($files['file'] == 'realPropertyTaxFile-file'){
                $insert->real_property_tax_data = $files['unique_id'];
            }
            if($files['file'] == 'deedOfSaleFile-file'){
                $insert->deed_of_sale_data = $files['unique_id'];
            }
            if($files['file'] == 'vicinityMapFile-file'){
                $insert->vicinity_map_data = $files['unique_id'];
            }


            if($files['file'] == 'tctFile-file2'){
                $insert2->tct_data = $files['unique_id'];
            }
            if($files['file'] == 'taxdecFile-file2'){
                $insert2->tax_dec_data = $files['unique_id'];
            } 

            if($files['file'] == 'realPropertyTaxFile-file2'){
                $insert2->real_property_tax_data = $files['unique_id'];
            }
            if($files['file'] == 'deedOfSaleFile-file2'){
                $insert2->deed_of_sale_data = $files['unique_id'];
            }
            if($files['file'] == 'vicinityMapFile-file2'){
                $insert2->vicinity_map_data = $files['unique_id'];
            }
        }
       $insert->location = $req->location;
       $insert->date_aquired = $req->dateAquired;
       $insert->area = $req->area;
       $insert->tax_dec_no = $req->taxDecNumber;
       $insert->tax_dec_option = $req->taxDecOption;
       $insert->owner = $req->owner;
       $insert->area = $req->area;
       $insert->previous_owner = $req->previousOwner;
       $insert->real_property_tax_no = $req->realPropertyTaxNumber;
       $insert->real_property_tax_date =  $req->realPropertyTaxDate;
       $insert->real_property_tax_amount =  $req->realPropertyTaxAmount;
       $insert->real_property_tax_option =  $req->realPropertyTaxOption;
       $insert->zonal_value =  $req->zonalValue;
       $insert->reportid ='ARCHIVED-'. md5($req->tctNumber . date('l jS \of F Y h:i:s A'));
       $insert->save();

       //-->ADDITIONAL
 
       $insert2->tax_dec_no = $req->taxDecNumber2;
       $insert2->tax_dec_option = $req->taxDecOption2;
       $insert2->owner = $req->owner2;
       $insert2->previous_owner = $req->previousOwner2;
       $insert2->real_property_tax_no = $req->realPropertyTaxNumber2;
       $insert2->real_property_tax_date =  $req->realPropertyTaxDate2;
       $insert2->real_property_tax_amount =  $req->realPropertyTaxAmount2;
       $insert2->real_property_tax_option =  $req->realPropertyTaxOption2;
       $insert2->zonal_value =  $req->zonalValue2;
       $insert2->reportid =  $insert->reportid;
       $insert2->save();
       $msg = ['msg' => 'inserted data'];
       return response()->json($msg, 200);
    }
    public function update(Request $req){
        
        $additionalID = Archived::where('id', $req['form-id'])->pluck('reportid')->first();

        function formatBytes($size, $precision = 2)
        {
          $base = log($size, 1024);
          $suffixes = array('', 'K', 'M', 'G', 'T');   
  
          return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
        }
        $uploads = [];
        if($req->file('tctFile-file')){
          $fileData = ['filesize' =>  $req->file('tctFile-file')->getSize(),
                       'filename'=>   $req->file('tctFile-file')->getClientOriginalName(),
                       'tctFile-file' => $req->file('tctFile-file')->getRealPath(),
                       'file'=> 'tctFile-file' ,
                       'update_id'=> $req['tctFile-file-id']];
           array_push($uploads, $fileData);
        } 
        if($req->file('taxdecFile-file')){
           $fileData = ['filesize' =>  $req->file('taxdecFile-file')->getSize(),
                        'filename'=> $req->file('taxdecFile-file')->getClientOriginalName(), 
                        'taxdecFile-file'  => $req->file('taxdecFile-file')->getRealPath(),
                        'file'=> 'taxdecFile-file',
                        'update_id'=> $req['taxdecFile-file-id']];
            array_push($uploads,$fileData);
         } 
         if($req->file('realPropertyTaxFile-file')){
           $fileData = ['filesize' =>  $req->file('realPropertyTaxFile-file')->getSize(),
                        'filename'=> $req->file('realPropertyTaxFile-file')->getClientOriginalName(),
                        'realPropertyTaxFile-file' => $req->file('realPropertyTaxFile-file')->getRealPath(),
                        'file'=> 'realPropertyTaxFile-file',
                        'update_id'=> $req['realPropertyTaxFile-file-id']];
            array_push($uploads,$fileData);
         } 
         if($req->file('vicinityMapFile-file')){
           $fileData = ['filesize' =>  $req->file('vicinityMapFile-file')->getSize(),
                        'filename'=> $req->file('vicinityMapFile-file')->getClientOriginalName(),
                        'vicinityMapFile-file' => $req->file('vicinityMapFile-file')->getRealPath(),
                        'file'=> 'vicinityMapFile-file',
                        'update_id'=> $req['vicinityMapFile-file-id']];
            array_push($uploads,$fileData);
         } 
         if($req->file('deedOfSaleFile-file')){
           $fileData = ['filesize' =>  $req->file('deedOfSaleFile-file')->getSize(),
                        'filename'=>$req->file('deedOfSaleFile-file')->getClientOriginalName(),
                        'deedOfSaleFile-file'   => $req->file('deedOfSaleFile-file')->getRealPath(),
                        'file'=> 'deedOfSaleFile-file',
                        'update_id'=> $req['deedOfSaleFile-file-id']];
            array_push($uploads,$fileData);
         } 

         if($req->file('tctFile-file2')){
            $fileData = ['filesize' =>  $req->file('tctFile-file2')->getSize(),
                         'filename'=>   $req->file('tctFile-file2')->getClientOriginalName(),
                         'tctFile-file' => $req->file('tctFile-file2')->getRealPath(),
                         'file'=> 'tctFile-file2' ,
                         'update_id'=> $req['tctFile-file-id2'],
                         'unique_id' => md5($req->file('tctFile-file2')->getClientOriginalName().$req->tctNumber)];
             array_push($uploads,$fileData);
          } 
          if($req->file('taxdecFile-file2')){
             $fileData = ['filesize' =>  $req->file('taxdecFile-file2')->getSize(),
                          'filename'=> $req->file('taxdecFile-file2')->getClientOriginalName(), 
                          'taxdecFile-file2'  => $req->file('taxdecFile-file2')->getRealPath(),
                          'file'=> 'taxdecFile-file2',
                          'update_id'=> $req['taxdecFile-file-id2'],
                          'unique_id'=>  md5($req->file('taxdecFile-file2')->getClientOriginalName().$req->taxDecNumber)];
              array_push($uploads,$fileData);
           } 
           if($req->file('realPropertyTaxFile-file2')){
             $fileData = ['filesize' =>  $req->file('realPropertyTaxFile-file2')->getSize(),
                          'filename'=> $req->file('realPropertyTaxFile-file2')->getClientOriginalName(),
                          'realPropertyTaxFile-file2' => $req->file('realPropertyTaxFile-file2')->getRealPath(),
                          'file'=> 'realPropertyTaxFile-file2',
                          'update_id'=> $req['realPropertyTaxFile-file-id2'],
                          'unique_id'=>md5($req->file('realPropertyTaxFile-file2')->getClientOriginalName().$req->realPropertyTaxNumber)];
              array_push($uploads,$fileData);
           } 
           if($req->file('vicinityMapFile-file2')){
             $fileData = ['filesize' =>  $req->file('vicinityMapFile-file2')->getSize(),
                          'filename'=> $req->file('vicinityMapFile-file2')->getClientOriginalName(),
                          'vicinityMapFile-file2' => $req->file('vicinityMapFile-file2')->getRealPath(),
                          'file'=> 'vicinityMapFile-file2',
                          'update_id'=> $req['vicinityMapFile-file-id2'],
                          'unique_id'=> md5( $req->file('vicinityMapFile-file2')->getClientOriginalName())];
              array_push($uploads,$fileData);
           } 
           if($req->file('deedOfSaleFile-file2')){
             $fileData = ['filesize' =>  $req->file('deedOfSaleFile-file2')->getSize(),
                          'filename'=>$req->file('deedOfSaleFile-file2')->getClientOriginalName(),
                          'deedOfSaleFile-file2'   => $req->file('deedOfSaleFile-file2')->getRealPath(),
                          'file'=> 'deedOfSaleFile-file2',
                          'update_id'=> $req['deedOfSaleFile-file-id2'],
                          'unique_id'=> md5($req->file('deedOfSaleFile-file2')->getClientOriginalName())];
              array_push($uploads,$fileData);
           } 
        $upload = [$uploads];
        $update = Archived::find($req['form-id']);
        $update2 = Archived_Add::where('reportid', $additionalID)->first();
        $update->tct_no = $req->tctNumber;
        $update2->tct_no = $req->tctNumber;
        foreach($upload[0] as $files){
            if($files['file']){
                //create
                if($files['update_id'] == 'update'){
                    $path = Storage::putFile('GovDoc', $req->file($files['file']));
                    $updateSt = new GovDataReport;
                    $updateSt->unique_id = md5($files['filename'].$req->taxDecNumber);
                    $updateSt->path = $path;
                    $updateSt->filename = $files['filename'];
                    $updateSt->size = formatBytes($files['filesize']);
                    $updateSt->save();
                    if($files['file'] == 'tctFile-file'){
                        $update->tct_data =  md5($files['filename'].$req->taxDecNumber);
                    }
                    if($files['file'] == 'taxdecFile-file'){
                        $update->tax_dec_data =  md5($files['filename'].$req->taxDecNumber);
                    } 
                    if($files['file'] == 'deedOfSaleFile-file'){
                        $update->deed_of_sale_data =  md5($files['filename'].$req->taxDecNumber);
                    }
                    if($files['file'] == 'realPropertyTaxFile-file'){
                        $update->real_property_tax_data =  md5($files['filename'].$req->taxDecNumber);
                    }
                    if($files['file'] == 'vicinityMapFile-file'){
                        $update->vicinity_map_data =  md5($files['filename'].$req->taxDecNumber);
                    }

                    if($files['file'] == 'tctFile-file2'){
                        $update2->tct_data =  md5($files['filename'].$req->taxDecNumber);
                    }
                    if($files['file'] == 'taxdecFile-file2'){
                        $update2->tax_dec_data =  md5($files['filename'].$req->taxDecNumber);
                    } 
                    if($files['file'] == 'deedOfSaleFile-file2'){
                        $update2->deed_of_sale_data =  md5($files['filename'].$req->taxDecNumber);
                    }
                    if($files['file'] == 'realPropertyTaxFile-file2'){
                        $update2->real_property_tax_data =  md5($files['filename'].$req->taxDecNumber);
                    }
                    if($files['file'] == 'vicinityMapFile-file2'){
                        $update2->vicinity_map_data =  md5($files['filename'].$req->taxDecNumber);
                    }
                //update 
                }else{
                    $path = Storage::putFile('GovDoc', $req->file($files['file']));
                    $updateSt = GovDataReport::find($files['update_id']);
                    $updateSt->path = $path;
                    $updateSt->size = formatBytes($files['filesize']);
                    $updateSt->filename = $files['filename'];
                    $updateSt->update();
                }
            }
        }

  
       $update->location = $req->location;
       $update->date_aquired = $req->dateAquired;
       $update->tax_dec_no = $req->taxDecNumber;
       $update->tax_dec_option = $req->taxDecOption;
       $update->owner = $req->owner;
       $update->area = $req->area;
       $update->previous_owner = $req->previousOwner;
       $update->real_property_tax_no = $req->realPropertyTaxNumber;
       $update->real_property_tax_date =  $req->realPropertyTaxDate;
       $update->real_property_tax_amount =  $req->realPropertyTaxAmount;
       $update->real_property_tax_option =  $req->realPropertyTaxOption;
       $update->zonal_value =  $req->zonalValue;
       $update->update();


       $update2->tax_dec_no = $req->taxDecNumber2;
       $update2->tax_dec_option = $req->taxDecOption2;
       $update2->owner = $req->owner2;
       $update2->previous_owner = $req->previousOwner2;
       $update2->real_property_tax_no = $req->realPropertyTaxNumber2;
       $update2->real_property_tax_date =  $req->realPropertyTaxDate2;
       $update2->real_property_tax_amount =  $req->realPropertyTaxAmount2;
       $update2->real_property_tax_option =  $req->realPropertyTaxOption2;
       $update2->zonal_value =  $req->zonalValue2;
       $update2->update();
       $msg = ['msg' => 'Data is updated','id' =>  $req->tctNumber];
       return response()->json($msg, 200);
    }
    public function delete(request $req){
        foreach($req['id'] as $id){
          $data[] = DB::table('archiveds')->where('id', $id)->first();
          DB::table('archiveds')->where('id', $id)->delete();
        }
        foreach($data as $id){
            try{
                $path1 = DB::table('gov_data_reports')->where('unique_id', $id->tct_data)->pluck('path')->first();
                $path2 = DB::table('gov_data_reports')->where('unique_id', $id->vicinity_map_data)->pluck('path')->first();
                $path3 = DB::table('gov_data_reports')->where('unique_id', $id->deed_of_sale_data)->pluck('path')->first();
                $path4 = DB::table('gov_data_reports')->where('unique_id', $id->tax_dec_data)->pluck('path')->first();
                DB::table('gov_data_reports')->where('unique_id', $id->vicinity_map_data)->delete();
                DB::table('gov_data_reports')->where('unique_id', $id->deed_of_sale_data)->delete();
                DB::table('gov_data_reports')->where('unique_id', $id->tct_data)->delete();
                DB::table('gov_data_reports')->where('unique_id', $id->tax_dec_data)->delete();
                if($path1){
                    unlink('../storage/app/'.$path1);
                }
                if($path2){
                    unlink('../storage/app/'.$path2);
                }
                if($path3){
                    unlink('../storage/app/'.$path3);
                }
                if($path4){
                    unlink('../storage/app/'.$path4);
                }
                
                $msg[] = ['msg'=> 'deleted'];
            }catch(Exception $e){
                $msg[] = ['msg'=> $e];
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
