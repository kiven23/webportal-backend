<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RaffleDrawController extends Controller
{
    public function run(){
        $array = [
            0 => ['customer' => 'John Smith', 'invoice' => '12345'],
            1 => ['customer' => 'Emily Carter', 'invoice' => '67890'],
            2 => ['customer' => 'William Jones', 'invoice' => '13579'],
            3 => ['customer' => 'Sophie Black', 'invoice' => '24680'],
            4 => ['customer' => 'Michael Brown', 'invoice' => '19283'],
            5 => ['customer' => 'Olivia White', 'invoice' => '37465'],
            6 => ['customer' => 'Alexander Green', 'invoice' => '56837'],
            7 => ['customer' => 'Charlotte Wilson', 'invoice' => '49283'],
            8 => ['customer' => 'Daniel Lewis', 'invoice' => '38592'],
            9 => ['customer' => 'Grace Johnson', 'invoice' => '58374'],
            10 => ['customer' => 'David Turner', 'invoice' => '73519'],
            11 => ['customer' => 'Sophia Hughes', 'invoice' => '95827'],
            12 => ['customer' => 'James Hall', 'invoice' => '13468'],
            13 => ['customer' => 'Ella Baker', 'invoice' => '24958'],
            14 => ['customer' => 'Ethan Young', 'invoice' => '18293'],
            15 => ['customer' => 'Mia King', 'invoice' => '72634'],
            16 => ['customer' => 'Liam Walker', 'invoice' => '95738'],
            17 => ['customer' => 'Chloe Adams', 'invoice' => '67412'],
            18 => ['customer' => 'Noah Scott', 'invoice' => '48391'],
            19 => ['customer' => 'Isabella Wright', 'invoice' => '15873'],
            20 => ['customer' => 'Benjamin Wood', 'invoice' => '67283'],
            21 => ['customer' => 'Ava Price', 'invoice' => '39627'],
            22 => ['customer' => 'Lucas Harris', 'invoice' => '91827'],
            23 => ['customer' => 'Amelia Robinson', 'invoice' => '27381'],
            24 => ['customer' => 'Henry Kelly', 'invoice' => '84921'],
            25 => ['customer' => 'Mason Evans', 'invoice' => '52179'],
            26 => ['customer' => 'Emily Fisher', 'invoice' => '32785'],
            27 => ['customer' => 'Sebastian Cole', 'invoice' => '71346'],
            28 => ['customer' => 'Lily Patterson', 'invoice' => '45981'],
            29 => ['customer' => 'Harper Bennett', 'invoice' => '62417'],
        ];
        
        $winnerIndices = array_rand($array, 3);
        $result = [];
        
        foreach ($winnerIndices as $index) {
            $result[] = $array[$index];
        }
        
        return response()->json($result);
        
    }
}
