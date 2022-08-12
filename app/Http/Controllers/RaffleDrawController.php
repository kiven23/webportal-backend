<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RaffleDrawController extends Controller
{
    public function run(){
         $array = [0=> ['customer'=> 'Calimlim Steven', 'invoice'=> '08394'],
                        1 => ['customer'=> 'Rustom Mamongay', 'invoice'=> '02924'],
                        2 => ['customer'=> 'Jermiah Heneral', 'invoice'=> '06482'],
                        3 => ['customer'=> 'Eugene Pol', 'invoice'=> '02846'],
                        4 => ['customer'=> 'Linux Doe', 'invoice'=> '09126'],
                        5 => ['customer'=> 'Mike Doe', 'invoice'=> '06375'],
                        6 => ['customer'=> 'Rustom Mamongay', 'invoice'=> '02924'],
                        7 => ['customer'=> 'Jermiah Heneral', 'invoice'=> '06482'],
                        8 => ['customer'=> 'Eugene Pol', 'invoice'=> '02846'],
                        9 => ['customer'=> 'Linux Doe', 'invoice'=> '09126'],
                        10 => ['customer'=> 'Mike Doe', 'invoice'=> '06375'],
                        11 => ['customer'=> 'Rustom Mamongay', 'invoice'=> '02924'],
                        12 => ['customer'=> 'Jermiah Heneral', 'invoice'=> '06482'],
                        13 => ['customer'=> 'Eugene Pol', 'invoice'=> '02846'],
                        14 => ['customer'=> 'Linux Doe', 'invoice'=> '09126'],
                        15 => ['customer'=> 'Mike Doe', 'invoice'=> '06375'],
                        16 => ['customer'=> 'Rustom Mamongay', 'invoice'=> '02924'],
                        17 => ['customer'=> 'Jermiah Heneral', 'invoice'=> '06482'],
                        18 => ['customer'=> 'Eugene Pol', 'invoice'=> '02846'],
                        19 => ['customer'=> 'Linux Doe', 'invoice'=> '09126'],
                        20 => ['customer'=> 'Mike Doe', 'invoice'=> '06375'],
                        21 => ['customer'=> 'Rustom Mamongay', 'invoice'=> '02924'],
                        22 => ['customer'=> 'Jermiah Heneral', 'invoice'=> '06482'],
                        23 => ['customer'=> 'Eugene Pol', 'invoice'=> '02846'],
                        24 => ['customer'=> 'Linux Doe', 'invoice'=> '09126'],
                        25 => ['customer'=> 'Mike Doe', 'invoice'=> '06375'],
                       ];

        $winner = array_rand($array, 3);
        $result = [0 => $array[$winner[0]],
                   1 => $array[$winner[1]],
                   2 => $array[$winner[2]]];
        return response()->json( $result );
    }
}
