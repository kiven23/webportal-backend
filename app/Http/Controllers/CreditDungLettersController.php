<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

use PDF;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;

class CreditDungLettersController extends Controller
{
    private $sqlCon = null;
    private $userBranch = null;

    private $companies = [
        "ADDESSA CORPORATION" => "URDANETA CITY, PANGASINAN",
        "EASY TO OWN APPLIANCES CORP." => "SANTO CRISTO DISTRICT GUIMBA, NUEVA ECIJA",
        "METRO ILOCOS APPLIANCE INC." => "BALALENG BANTAY. ILOCOS SUR",
        "PAN APPLIANCE CORPORATION" => "ESTACION PANIQUI, TARLAC"
    ];

    private $letter_storage_path = "letters";

    public function __construct(){
        $this->sqlCon = DB::connection('sqlsrv2');

        if(Auth::check()) {
            $this->userBranch = Auth::user()->branch->name;

            if($this->userBranch == "Appletronics-Addessa") {
                $this->userBranch = "Appletronics";
            }
        }
    }

    public function index(Request $request) {
        
        $user_branch = $this->userBranch;

        $query =  $this->sqlCon->table('InstallmentReceivableDetailed')
            ->select(DB::raw("Branch + '_' + REPLACE(Aging, ' ', '_') as row_id, Branch as branch, Aging as aging, count(Aging) as aging_count"))
            ->whereIn("Aging", ["02 ONE(1) MONTH", "03 TWO(2) MONTHS", "04 THREE(3) MONTHS", "05 FOUR(4) MONTHS", "09 LAPCON ONE(1) MONTH"]);
        
        if($user_branch != "Admin") {
            $query->where("Branch", $user_branch);
        }
        
        $query->groupBy("Branch", "Aging")
            ->orderBy("Branch", "ASC")
            ->orderBy("Aging", "ASC");

        // $data = [];
        // foreach( as $key => $result) {
        //     $branch =  $result->Branch;
        //     $aging = $result->Aging;
        //     $data[$key]['index'] = $branch . "_" . str_replace(" ", "_", $aging); 
        //     $data[$key]['branch'] = $branch;
        //     $data[$key]['aging'] = $aging;
        //     $aging_arr = explode(" ", $aging);
        //     $data[$key]['overdue'] = $aging_arr[1] . " " . $aging_arr[2];
        //     $data[$key]['aging_count'] = $result->AgingCount;
        // }

        return $query->get();
    }

    public function downloadLetters(Request $request) {

        $branch = $request->branch;

        $this->checkIfHasAccessInBranch($branch);
        
        $aging = $request->aging;

        $data = array();
        
        $letter_title = "";
        $letter_type = $this->getLetterType($aging);

        $file_name = $this->getFileName($branch, $letter_type);
        //$full_path = $this->getFullPath($branch, $file_name);

        //$last_month_file_full_path = $this->getLastMonthFileFullPath($branch, $letter_type);
        
        // if(Storage::disk("local")->exists($last_month_file_full_path)) {
        //     Storage::delete($last_month_file_full_path);
        // }

        // if(! Storage::disk("local")->exists($full_path)) {

        $customers = $this->sqlCon->table('InstallmentReceivableDetailed')
        ->where([
            "Branch" => $branch,
            "Aging" => $aging,
        ])
        ->orderBy("CardName")
        ->get();

        foreach ($customers as $key => $customer) {

            $nameRes = $this->formatName($customer->CardName);
            $full_name = $nameRes['full_name'];

            $address = $this->getAddress($customer->CardCode);

            $data[$key] = [
                'name' => $full_name,
                'address' => $address['address'],
                'province' => $address['province'],
                'last_name' => ucfirst(strtolower($nameRes['last_name'])),
                'as_of_date' => Carbon::now()->subMonthNoOverflow()->endOfMonth()->format("F d, Y")
            ];

            if($aging != "03 TWO(2) MONTHS") {
                $this->addOverdueAndPenalty($data[$key], $customer);
            }

            if($aging != "02 ONE(1) MONTH" && $aging != "03 TWO(2) MONTHS") {
                $this->addCompanyInfo($data[$key], $customer);       
            }
        }

        $letter_title .= str_plural(ucwords(str_replace("_", " ",$letter_type))) . " - $branch Branch, As of " . Carbon::now()->format("M. Y");

        $pdf = PDF::loadView("credit_dung_letters.main_letter", ["letter_title" => $letter_title, "letter_type" => $letter_type,  "letters" => $data]);
        
            // $content = $pdf->download()->getOriginalContent();

            // Storage::put($full_path, $content);   
        //}

        // $headers = array(
        //     'Content-Type: application/pdf',
        //     'Access-Control-Expose-Headers' => 'Content-Disposition',
        //     'Content-Disposition' => 'attachment;filename='.$file_name,
        // );

        //return Storage::download($full_path, $file_name, $headers);
        return $pdf->download($file_name)->header('Access-Control-Expose-Headers', 'Content-Disposition'); 
    }

    private function checkIfHasAccessInBranch($branchFromRequest){
        if(\Auth::user()->hasRole('Dunning Letter Branch') && $branchFromRequest != $this->userBranch) {
            abort('403');
        }
    }

    private function getLetterType($aging){
        switch($aging) {
            case "02 ONE(1) MONTH":
                return "reminder_letter";
            case "03 TWO(2) MONTHS":
                return "dunning_letter";
                break;
            case "04 THREE(3) MONTHS":
                return "first_demand_letter";       
                break;
            case "05 FOUR(4) MONTHS":
                return "second_demand_letter";
                break;
            case "09 LAPCON ONE(1) MONTH":
                return "final_demand_letter";
                break;
        }
    }

    private function getFileName($branch, $letter_type) {
        return $this->createFileNameWithAsOfDate($branch, $letter_type, Carbon::now()->format("M_Y"));
    }

    private function createFileNameWithAsOfDate($branch, $letter_type, $dateString) {
        return strtoupper(str_plural($letter_type) . "_OF_" . $branch . "_BRANCH_AS_OF_" . $dateString) . ".pdf";
    }

    private function getFullPath($branch, $file_name) {
        return $this->letter_storage_path . "/$branch/$file_name";
    }

    private function getLastMonthFileFullPath($branch, $letter_type) {
        return $this->letter_storage_path . "/$branch/" . $this->createFileNameWithAsOfDate($branch, $letter_type, Carbon::now()->subMonthNoOverflow()->endOfMonth()->format("M_Y")); 
    }

    private function formatName($name) {
        
        $suffix = "";
        $comma_pos = strpos($name, ",");
        $last_name = substr($name, 0, $comma_pos);
        $first_middle_name = substr($name, $comma_pos + 2, strlen($name));
        $first_middle_name = str_replace(".", "", $first_middle_name);
        
        preg_match('(JR|SR)', $first_middle_name, $match);
        if(count($match) > 0) {
            $suffix = $match[0];
            $first_middle_name = str_replace($suffix . " ", "", $first_middle_name);
        }
        
        $full_name = "$first_middle_name $last_name $suffix";
        
        return compact('full_name', 'last_name');
    }

    private function getAddress($card_code) {
        $res = collect(\DB::connection('sqlsrv3')->select(DB::raw("
            SELECT (CRD1.Block + ', ' + CRD1.City) AS Address, OCST.Name AS Province  FROM CRD1 LEFT JOIN OCST ON CRD1.State = OCST.Code WHERE CRD1.CardCode = :CardCode
        "), ['CardCode' => $card_code]))->first();

        $address = ucwords(strtolower($res->Address));
        preg_match('#\((.*?)\)#', $address, $match);
        if(count($match) > 0) {
            $former_brgy_name = ucfirst($match[1]);
            $address = str_replace($match[1], $former_brgy_name, $address);
        }
        
        $province = ucwords(strtolower($res->Province));
        
        return compact('address', 'province');
    }

    private function addCompanyInfo(&$data, $customer_info){
        $branch =  $this->sqlCon->table('Branch')->select("NCompany")->where("Branch", strtoupper($customer_info->Branch))->first();
        $branch_company_name = $customer_info->Branch == "Admin" || ( $branch->NCompany && !array_key_exists($branch->NCompany, $this->companies))? "ADDESSA CORPORATION" : $branch->NCompany;

        $data['attorney'] = 'Atty. Dar Diga';
        $data['branch_company_name'] = $branch_company_name;
        $data['branch_company_address'] = $this->companies[$branch_company_name];
    }

    private function addOverdueAndPenalty(&$data, $customer_info){ 
        $data['principal'] = "P" . number_format($customer_info->OverDueAmt, 2);
        $data['penalty'] = "P" . number_format(round($this->calculatePenalty($customer_info)), 2);
    }

    private function calculatePenalty($customer_info) {
        
        $overdue_amt = $customer_info->OverDueAmt;
        $promo_code = $customer_info->PriceList;

        $percent = 0.05;
        
        if (strpos($promo_code, "SPECIAL") !== false || strpos($promo_code, "REGULAR") !== false) {
            $percent = 0.07;
        }

        $aging = $customer_info->Aging;
        
        preg_match('#\((.*?)\)#', $aging, $match);
        $no_of_mos = (int) $match[1];

        $is_lapcon = strpos($aging, "LAPCON") !== false;

        $total_penalty = 0;
        $mi = $customer_info->MI;

        $final_mo_overdue_penalty = 0;

        if(($no_of_mos == 1 && !$is_lapcon) || ($is_lapcon && $overdue_amt <= $mi)) {
            $total_penalty = round($overdue_amt * $percent);
            if($is_lapcon) {
                $final_mo_overdue_penalty = $total_penalty;
            }
        } else {
            // $overdue_amt_per_month = $overdue_amt - ($mi * ($is_lapcon? 1 : $no_of_mos - 1));
            // do {
            //     $total_penalty += round($overdue_amt_per_month * $percent);
            //     $overdue_amt_per_month += $mi;
            // } while ($overdue_amt_per_month <= $overdue_amt);
            // for($counter = 1; $counter <= ceil($overdue_amt/$mi); $counter++) {
            //     if($counter == 1) {
            //         $excess_mi_payed = $overdue_amt%$mi;
            //         $_mi_payed = $excess_mi_payed == 0? $mi : $excess_mi_payed;
            //     } else {
            //         $_mi_payed += $mi;
            //     }
            //     echo $_mi_payed . ">>>>";
            //     $total_penalty += round($_mi_payed * $percent); 
            // }
            $excess_mi_payed = $overdue_amt%$mi;
            $overdue_amt_per_month = $excess_mi_payed == 0? $mi : $excess_mi_payed;
            do {
                //echo $overdue_amt_per_month . ">>>>";
                // $total_penalty += round($overdue_amt_per_month * $percent);
                $penalty = round($overdue_amt_per_month * $percent);
                $total_penalty += $penalty;
                
                if($is_lapcon && $overdue_amt_per_month == $overdue_amt) {
                    $final_mo_overdue_penalty = $penalty;    
                }

                $overdue_amt_per_month += $mi;
            } while($overdue_amt_per_month <= $overdue_amt);
            //echo "<br/>";
        }
        
        

        // if($is_lapcon) {
        //     $mi = $customer_info->MI;
        //     if($overdue_amt <=  $mi) {
        //         $total_penalty = round($overdue_amt * $percent);
        //     }else {
        //         $_mi_payed = 0;
        //         for($counter = 1; $counter <= ceil($overdue_amt/$mi); $counter++) {
        //             if($counter == 1) {
        //                 $_mi_payed = $overdue_amt - (floor($overdue_amt/$mi) * $mi);
        //             } else {
        //                 $_mi_payed += $mi;
        //             }
        //             $total_penalty += round($_mi_payed * $percent); 
        //         }
        //     }
        //     return $total_penalty + ($total_penalty * $no_of_mos);  
        // }else {
        //     $due_next_mo = $customer_info->DueNextMonth;
        //     $downpayment = ($due_next_mo * $no_of_mos) - $overdue_amt;
        //     $_due = $due_next_mo;
        //     for($counter = 1; $counter <= $no_of_mos; $counter++){
        //         if($counter == 1) {
        //             $_due -= $downpayment;   
        //         } else {
        //             $_due += $due_next_mo;
        //         }
        //         $total_penalty += round($_due * $percent);
        //     }
        // }
        
        if($is_lapcon) {
            $total_penalty = $total_penalty + ($final_mo_overdue_penalty * $no_of_mos);
        }

        return $total_penalty;

    }

    //to be removed
    public function test(){
        // $res = $this->sqlCon->select(DB::raw("
        //     SET NOCOUNT ON;
        //     USE ReportsFinance;
        //     SELECT TOP 10 (CRD1.Block + ', ' + CRD1.City) AS Address, OCST.Name AS Province  FROM CRD1 LEFT JOIN OCST ON CRD1.State = OCST.Code
        // "));
        //return response()->json(['test' => $this->getAddress("E02-0014549")]);

        $html = "<h1>Test PDF</h1>
                 <p>This a test pdf</p>";

        return Pdf::loadHTML($html)->download('test.pdf')->header('Access-Control-Expose-Headers', 'Content-Disposition');
        
    }

    public function downloadLetter($branch, $aging){
        
        $data = array();
        
        $letter_title = "";
        $letter_type = $this->getLetterType($aging);

        $file_name = $this->getFileName($branch, $letter_type);
        $full_path = $this->getFullPath($branch, $file_name);

        $response = [
            "success" => true,
            "file_name" => $file_name,
            "full_path" => $full_path,
        ];

        $customers = $this->sqlCon->table('InstallmentReceivableDetailed')->where([
            "Branch" => $branch,
            "Aging" => $aging,
        ])->orderBy("CardName")->get();

        foreach ($customers as $key => $customer) {

            $nameRes = $this->formatName($customer->CardName);
            $full_name = $nameRes['full_name'];

            $addr = $this->getAddress($customer->CardCode);

            $data[$key] = [
                'name' => $full_name,
                'address' => $addr['address'],
                'province' => $addr['province'],
                'last_name' => ucfirst(strtolower($nameRes['last_name'])),
                'as_of_date' => Carbon::now()->subMonthNoOverflow()->endOfMonth()->format("F d, Y")
            ];

            if($aging != "03 TWO(2) MONTHS") {
                $this->addOverdueAndPenalty($data[$key], $customer);
            }

            if($aging != "02 ONE(1) MONTH" && $aging != "03 TWO(2) MONTHS") {
                $this->addCompanyInfo($data[$key], $customer);       
            }
        }

        $letter_title .= str_plural(ucwords(str_replace("_", " ",$letter_type))) . " - $branch Branch, As of " . Carbon::now()->format("M. Y");

        $pdf = PDF::loadView("credit_dung_letters.main_letter", ["letter_title" => $letter_title, "letter_type" => $letter_type,  "letters" => $data]);
    
        //return;
        return $pdf->stream();
        //return $pdf->download(strtoupper($letter_type)."_". str_replace(" ", "_", trim($full_name)) .'.pdf');
     

    }

    public function downloadLettersGet($branch,  $aging){

        // $branch = $request->branch;
        // $aging = $request->aging;

        $file_name = $this->getFileName($branch, $this->getLetterType($aging));
        $full_path = $this->getFullPath($branch, $file_name);

        if(! Storage::disk("local")->exists($full_path)) {
            return response()->json(['success' => false, 'message' => "File not found"]);
        }

        return response()->download('../storage/app/' . $full_path);
    }

    public function getBranches(){
        // $branches = Branch::orderBy('name', 'asc')->get();
        // return $branches;
        $branches =  $this->sqlCon->table('Branch')->get();
        //return $branches;
        $results = [];
        foreach($branches as $key => $branch){
            $branch_name = ucfirst(strtolower($branch->Branch)); 
            $results[] = $branch_name;
        }
        return $results;
    }
}
