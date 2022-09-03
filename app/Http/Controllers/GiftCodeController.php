<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\GiftCode;
use Carbon\Carbon;
use GuzzleHttp\Client;
class GiftCodeController extends Controller
{
    public function sync(){
    
           $SQL = DB::connection('sqlsrv')
            ->select('SELECT TOP 3 OINV.CARDNAME, OINV.CARDCODE, COUNT(*) AS REPEAT, OINV.CREATEDATE, OCRD.U_BDAY, OCRD.CELLULAR
                        FROM OINV 
                        
                        JOIN OCRD ON OINV.CARDCODE = OCRD.CARDCODE
                        WHERE OCRD.U_BDAY IS NOT NULL AND OCRD.CELLULAR IS NOT NULL AND OINV.CARDNAME IS NOT NULL
                        
                        AND YEAR(OINV.CREATEDATE) >= 2019
                        GROUP BY
                        OINV.CARDNAME, OINV.CARDCODE, OINV.CREATEDATE,OCRD.U_BDAY, OCRD.CELLULAR
                        HAVING 
                        COUNT (*) > 1   
            ');
            //FILTERED FUNCTION AND REMAP CARDCODE TO BRANCH
                function filterMb($mb){
                    $val = array("(",")","-"," ","/");
                    $number1 = str_replace($val, "", $mb);
                    $filtered = mb_substr($number1, -11);
                    return $filtered;
                }
                function remap_branch($cardcode){
                    return DB::table('branches')->where('sapcode', 'LIKE' ,  '%'.explode_cardcode($cardcode).'%')->pluck('name')->first();
                }
                function explode_cardcode($cardcode){
                    $exp = explode('-', $cardcode);
                    return $exp[0];
                }
            //END FILTERED FUNCTION
        foreach($SQL as $in){
            $check = DB::table('gift_codes')->where('cardcode', $in->CARDCODE)->first();
            // filterMb($in->CELLULAR)
                if(!$check){
                    DB::table('gift_codes')->insert([
                        'branch'=> remap_branch($in->CARDCODE),
                        'cardname'=> $in->CARDNAME,
                        'cardcode'=> $in->CARDCODE,
                        'mobile'=> '09152212673',
                        'birthmonth'=> $in->U_BDAY,
                        'status'=> 0,
                    ]);
                }
        }
        return response()->json('sync');
    }
    public function send(){
       $month = Carbon::now()->month;
       $sql = DB::table('gift_codes')
                ->whereMonth('birthmonth', $month)
                ->where('status', 0)
                ->get();
        function giftcode($cardcode){
            $ex = explode('-', $cardcode);
            $giftcode = 'HBDSUKI'.substr($ex[2],-4).$ex[0];
            return $giftcode;
        }
        function msg($name, $cardcode){
            $ex = explode(',', $name);
            $s = "'";
            $message = 'It'.$s.'s your birth month, '.$ex[0].'! We'.$s.'d like to greet you a happy birthday in advance. We assure you that Addessa will forever be your partner towards the way to comfort living, and as our way celebrating your life, here is your gift code '.giftcode($cardcode).' to get your surprise!  Please present the code to any Addessa branch near you within 30 days upon receipt of this message to claim your gift. Happy Birthday Suki, from your Addessa Family';             return  $message;
        }
        function sendtoApi($cardname ,$n ,$cardcode){
            $full_link = 'http://mcpro1.sun-solutions.ph/mc/send.aspx?user=ADDESSA&pass=MPoq5g7y&from=ADDESSA&to='.$n.'&msg='.msg($cardname,$cardcode).'';
            $client = new Client;
            $response = $client->request('GET', $full_link);
            return $n .' ok '.$cardname;
        }
        foreach($sql as $data){
                sendtoApi($data->cardname,$data->mobile, $data->cardcode);
                DB::table('gift_codes')
                        ->where('id', $data->id)->update([
                        'status'=> 1
                ]);
                DB::table('gift_code_logs')
                ->insert([
                    'cardcode' => $data->cardcode,
                    'name'=> $data->cardname,
                    'code'=> giftcode($data->cardcode),
                    'message'=> msg($data->cardname, $data->cardcode),
                    'birthday'=> $data->birthmonth,
                    'created_at'=> Carbon::now()
                ]);
        }
        return response()->json('ok');
    }

    
}
