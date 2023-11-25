<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ExpressWayDriver;
use DB;
class ExpressWayDriverController extends Controller
{
    public function drivers(){
      return ExpressWayDriver::all();
        $drivers =  array (
            0 => 
            array (
              'plateno' => 'TOB154',
              'driver' => 'FERNANDO VELASCO DELA CRUZ',
              'department' => '',
              'brand' => 'VELAR',
              'model' => '',
            ),
            1 => 
            array (
              'plateno' => 'NDM4976',
              'driver' => 'NO DRIVER',
              'department' => 'Logistics',
              'brand' => 'ISUZU',
              'model' => 'QKR77',
            ),
            2 => 
            array (
              'plateno' => 'NDO2274',
              'driver' => 'DON HENLEY TOLLO RAMIREZ',
              'department' => 'Delivery Panel',
              'brand' => 'ISUZU',
              'model' => 'TRAVIZ L',
            ),
            3 => 
            array (
              'plateno' => 'NID8405',
              'driver' => 'NO DRIVER',
              'department' => 'Delivery Panel',
              'brand' => 'ISUZU',
              'model' => 'NLR77 H TILT',
            ),
            4 => 
            array (
              'plateno' => 'NAM7649',
              'driver' => 'SEGUNDINO JR ARDEÑA OBRA',
              'department' => 'Logistics',
              'brand' => 'ISUZU',
              'model' => 'FRR TRUCK',
            ),
            5 => 
            array (
              'plateno' => 'NEB5249',
              'driver' => 'DON HENLEY TOLLO RAMIREZ',
              'department' => 'Logistics',
              'brand' => 'MITSUBISHI',
              'model' => 'CANTER',
            ),
            6 => 
            array (
              'plateno' => 'AAQ5765',
              'driver' => 'TEE JAY TOMAGOS FLORES',
              'department' => 'Officer',
              'brand' => 'TOYOTA',
              'model' => 'HI-LUX',
            ),
            7 => 
            array (
              'plateno' => 'AAY7258',
              'driver' => 'RAYMOND VARGAS ALAMBRA',
              'department' => 'Credit and Collection',
              'brand' => 'TOYOTA',
              'model' => 'HI-LUX',
            ),
            8 => 
            array (
              'plateno' => 'BAB7623',
              'driver' => 'MATEO URSANTE SANTIAGO',
              'department' => 'Officer',
              'brand' => 'HYUNDAI',
              'model' => 'H100 2.5 CRDI G',
            ),
            9 => 
            array (
              'plateno' => 'BAB7627',
              'driver' => 'MATEO URSANTE SANTIAGO',
              'department' => 'Delivery Panel',
              'brand' => 'HYUNDAI',
              'model' => 'H100 2.5 CRDI G',
            ),
            10 => 
            array (
              'plateno' => 'BAB8408',
              'driver' => 'ALEJANDRO BAUTISTA ELEGADO',
              'department' => 'Marketing',
              'brand' => 'HYUNDAI',
              'model' => 'H100 2.5 CRDI G',
            ),
            11 => 
            array (
              'plateno' => 'BAB8411',
              'driver' => 'VERDAD BERNARDINO LADERO',
              'department' => 'Delivery Panel',
              'brand' => 'HYUNDAI',
              'model' => 'H100 2.5 CRDI G',
            ),
            12 => 
            array (
              'plateno' => 'CBD9576',
              'driver' => 'RAYMUND DE LEON CAMERO',
              'department' => 'Delivery Panel',
              'brand' => 'MITSUBISHI',
              'model' => 'L300 C/C 2.2D M',
            ),
            13 => 
            array (
              'plateno' => 'NFL5941',
              'driver' => 'JOHN PAUL BERNARDO BAMBA',
              'department' => 'Delivery Panel',
              'brand' => 'ISUZU',
              'model' => 'TRAVIZ L',
            ),
            14 => 
            array (
              'plateno' => 'NGG1727',
              'driver' => 'MICHAEL MANZANO TABILIN',
              'department' => 'Officer',
              'brand' => 'ISUZU',
              'model' => 'D-MAX',
            ),
            15 => 
            array (
              'plateno' => 'NCI5386',
              'driver' => 'NO DRIVER',
              'department' => 'Officer',
              'brand' => 'TOYOTA',
              'model' => 'COASTER',
            ),
            16 => 
            array (
              'plateno' => 'NGB6252',
              'driver' => 'NO DRIVER',
              'department' => 'Officer',
              'brand' => 'FORD',
              'model' => 'TRANSIT MINIBUS',
            ),
            17 => 
            array (
              'plateno' => 'ABH3821',
              'driver' => 'NOVER MAMARADLO VELASCO',
              'department' => 'Officer',
              'brand' => 'TOYOTA',
              'model' => 'INNOVA',
            ),
            18 => 
            array (
              'plateno' => 'ADD925',
              'driver' => 'NO DRIVER',
              'department' => 'Officer',
              'brand' => 'TOYOTA',
              'model' => 'HI-ACE',
            ),
            19 => 
            array (
              'plateno' => 'ADX880',
              'driver' => 'CATALINO LOMIBAO CASTILLO',
              'department' => 'Officer',
              'brand' => 'MITSUBISHI',
              'model' => 'L-200',
            ),
            20 => 
            array (
              'plateno' => 'AHK402',
              'driver' => 'ISABELO JR MERTO VALDEZ',
              'department' => 'Officer',
              'brand' => 'TOYOTA',
              'model' => 'HI-LUX',
            ),
            21 => 
            array (
              'plateno' => 'CAH9287',
              'driver' => 'JOCELYN NARCILLA CORTEZ',
              'department' => 'Officer',
              'brand' => 'HONDA',
              'model' => 'MOBILIO',
            ),
            22 => 
            array (
              'plateno' => 'CRC741',
              'driver' => 'STEVE CALIX DELA CRUZ',
              'department' => 'Officer',
              'brand' => 'HONDA',
              'model' => 'CIVIC',
            ),
            23 => 
            array (
              'plateno' => 'NBD8265',
              'driver' => 'MARIEBETH IGAMA DELA CRUZ',
              'department' => 'Officer',
              'brand' => 'SUBARU',
              'model' => 'XV 2.0',
            ),
            24 => 
            array (
              'plateno' => 'NBH9972',
              'driver' => 'PAMELA FIGURACION MALLORCA',
              'department' => 'Officer',
              'brand' => 'CHEVROLET',
              'model' => 'TRAILBLAZER',
            ),
            25 => 
            array (
              'plateno' => 'NBX2206',
              'driver' => 'RODRIGO JR MAMARIN CABASOG',
              'department' => 'Officer',
              'brand' => 'TOYOTA',
              'model' => 'HI ACE',
            ),
            26 => 
            array (
              'plateno' => 'NCL7397',
              'driver' => 'FERNANDO VELASCO DELA CRUZ',
              'department' => 'Officer',
              'brand' => 'MERCEDES BENZ',
              'model' => 'GLC',
            ),
            27 => 
            array (
              'plateno' => 'NCZ5785',
              'driver' => 'FERNANDO VELASCO DELA CRUZ',
              'department' => 'Officer',
              'brand' => 'JEEP',
              'model' => 'GRAND CHEROKEE',
            ),
            28 => 
            array (
              'plateno' => 'NFU6061',
              'driver' => 'NO DRIVER',
              'department' => 'Officer',
              'brand' => 'TOYOTA',
              'model' => 'RUSH',
            ),
            29 => 
            array (
              'plateno' => 'PJO707',
              'driver' => 'JOSE ALLAN CALIX DELA CRUZ',
              'department' => 'Officer',
              'brand' => 'MITSUBISHI',
              'model' => 'STRADA',
            ),
            30 => 
            array (
              'plateno' => 'PJO836',
              'driver' => 'ARIEL ARIZO',
              'department' => 'Officer',
              'brand' => 'MITSUBISHI',
              'model' => 'STRADA',
            ),
            31 => 
            array (
              'plateno' => 'POQ808',
              'driver' => 'ARGIE NACION ONG',
              'department' => 'Officer',
              'brand' => 'MAZDA',
              'model' => 'CX-9',
            ),
            32 => 
            array (
              'plateno' => 'TTO697',
              'driver' => 'LOWEY PANGILINAN VELASCO',
              'department' => 'Officer',
              'brand' => 'MITSUBISHI',
              'model' => 'STRADA',
            ),
            33 => 
            array (
              'plateno' => 'UGQ288',
              'driver' => 'ELSIE DELA CRUZ NG',
              'department' => 'Officer',
              'brand' => 'TOYOTA',
              'model' => 'INNOVA',
            ),
            34 => 
            array (
              'plateno' => 'WIA561',
              'driver' => 'REY BERNAL MANUEL',
              'department' => 'Officer',
              'brand' => 'TOYOTA',
              'model' => 'INNOVA',
            ),
            35 => 
            array (
              'plateno' => 'WTO260',
              'driver' => 'ARIES CALIX DELA CRUZ',
              'department' => 'Officer',
              'brand' => 'TOYOTA',
              'model' => 'HI-LUX',
            ),
            36 => 
            array (
              'plateno' => 'XKC663',
              'driver' => 'RODRIGO JR MAMARIN CABASOG',
              'department' => 'Officer',
              'brand' => 'FORD',
              'model' => 'EXPEDITION',
            ),
            37 => 
            array (
              'plateno' => 'YAA8853',
              'driver' => 'RAFAEL VITERBO SORIANO',
              'department' => 'Officer',
              'brand' => 'HYUNDAI',
              'model' => 'Tucson',
            ),
            38 => 
            array (
              'plateno' => 'ZDA698',
              'driver' => 'FERNANDO VELASCO DELA CRUZ',
              'department' => 'Officer',
              'brand' => 'TOYOTA',
              'model' => 'RAV 4',
            ),
            39 => 
            array (
              'plateno' => 'ZPG898',
              'driver' => 'MARIEBETH IGAMA DELA CRUZ',
              'department' => 'Officer',
              'brand' => 'TOYOTA',
              'model' => 'INNOVA',
            ),
            40 => 
            array (
              'plateno' => 'ZRX268',
              'driver' => 'FERNANDO II CALIX DELA CRUZ',
              'department' => 'Officer',
              'brand' => 'TOYOTA',
              'model' => 'INNOVA',
            ),
            41 => 
            array (
              'plateno' => 'ZSU978',
              'driver' => 'ELSIE DELA CRUZ NG',
              'department' => 'Officer',
              'brand' => 'SUBARU',
              'model' => 'FORESTER',
            ),
            42 => 
            array (
              'plateno' => 'BAA2613',
              'driver' => 'JOMYR ECHANEZ SUAZO',
              'department' => 'Officer',
              'brand' => 'HYUNDAI',
              'model' => 'H-100 2.6 GL',
            ),
            43 => 
            array (
              'plateno' => 'NDO2271',
              'driver' => 'REY BERNAL MANUEL',
              'department' => 'Officer',
              'brand' => 'ISUZU',
              'model' => 'TRAVIZ L',
            ),
            44 => 
            array (
              'plateno' => 'CBS9419',
              'driver' => 'NO DRIVER',
              'department' => 'Delivery Panel',
              'brand' => 'MITSUBISHI',
              'model' => 'L300 C/C 2.2D M',
            ),
            45 => 
            array (
              'plateno' => 'CBT3142',
              'driver' => 'NO DRIVER',
              'department' => 'Delivery Panel',
              'brand' => 'MITSUBISHI',
              'model' => 'L300 C/C 2.2D M',
            ),
            46 => 
            array (
              'plateno' => 'CBT5734',
              'driver' => 'NO DRIVER',
              'department' => 'Delivery Panel',
              'brand' => 'MITSUBISHI',
              'model' => 'L300 C/C 2.2D M',
            ),
            47 => 
            array (
              'plateno' => 'ADD436',
              'driver' => 'NO DRIVER',
              'department' => 'Credit and Collection',
              'brand' => 'MITSUBISHI',
              'model' => 'L-300',
            ),
            48 => 
            array (
              'plateno' => 'ADD996',
              'driver' => 'NO DRIVER',
              'department' => 'Credit and Collection',
              'brand' => 'MITSUBISHI',
              'model' => 'L-300',
            ),
            49 => 
            array (
              'plateno' => 'ADX933',
              'driver' => 'NO DRIVER',
              'department' => 'Credit and Collection',
              'brand' => 'TOYOTA',
              'model' => 'TAMARAW FX',
            ),
            50 => 
            array (
              'plateno' => 'BAB7616',
              'driver' => 'CHRISTOPHER BIANAN SERNA',
              'department' => 'Delivery Panel',
              'brand' => 'HYUNDAI',
              'model' => 'H100 2.5 CRDI G',
            ),
            51 => 
            array (
              'plateno' => 'BAB7619',
              'driver' => 'RODRIGO JR MAMARIN CABASOG',
              'department' => 'Delivery Panel',
              'brand' => 'HYUNDAI',
              'model' => 'H100 2.5 CRDI G',
            ),
            52 => 
            array (
              'plateno' => 'NDE3431',
              'driver' => 'MICHAEL ANGELO MALONG MILLIET',
              'department' => 'Delivery Panel',
              'brand' => 'MITSUBISHI',
              'model' => 'L300 EXCEED',
            ),
            53 => 
            array (
              'plateno' => 'NDU1632',
              'driver' => 'NO DRIVER',
              'department' => 'Marketing',
              'brand' => 'MITSUBISHI',
              'model' => 'L-300',
            ),
            54 => 
            array (
              'plateno' => 'TGQ920',
              'driver' => 'NO DRIVER',
              'department' => 'Credit and Collection',
              'brand' => 'MITSUBISHI',
              'model' => 'L-300',
            ),
            55 => 
            array (
              'plateno' => 'XAD 585',
              'driver' => 'NO DRIVER',
              'department' => 'Credit and Collection',
              'brand' => 'TOYOTA',
              'model' => 'TAMARAW FX',
            ),
            56 => 
            array (
              'plateno' => 'NCE4515',
              'driver' => 'XERXES GABRIEL AGPOON',
              'department' => 'Logistics',
              'brand' => 'MITSUBISHI',
              'model' => 'CANTER',
            ),
            57 => 
            array (
              'plateno' => 'RNJ122',
              'driver' => 'DAVID JOHN SERRANO MAMARIL',
              'department' => 'Delivery Panel',
              'brand' => 'ISUZU',
              'model' => 'H-SIDE RAILINGS',
            ),
            58 => 
            array (
              'plateno' => 'AWA7238',
              'driver' => 'REY BERNAL MANUEL',
              'department' => 'Tactical Vehicle',
              'brand' => 'ISUZU',
              'model' => '6SD1',
            ),
            59 => 
            array (
              'plateno' => 'BAB7961',
              'driver' => 'JERWIN BAYLON',
              'department' => 'Tactical Vehicle',
              'brand' => 'HYUNDAI',
              'model' => 'H100 2.5 CRDI G',
            ),
            60 => 
            array (
              'plateno' => 'BAB7963',
              'driver' => 'RAYMUND DE LEON CAMERO',
              'department' => 'Tactical Vehicle',
              'brand' => 'HYUNDAI',
              'model' => 'H100 2.5 CRDI G',
            ),
            61 => 
            array (
              'plateno' => 'BAB7964',
              'driver' => 'MANOLITO, JR. QUERO SANTOS',
              'department' => 'Tactical Vehicle',
              'brand' => 'HYUNDAI',
              'model' => 'H100 2.5 CRDI G',
            ),
            62 => 
            array (
              'plateno' => 'CAB2046',
              'driver' => 'ALEJANDRO BAUTISTA ELEGADO',
              'department' => 'Tactical Vehicle',
              'brand' => 'FUSO',
              'model' => 'SUPER GREAT',
            ),
            63 => 
            array (
              'plateno' => 'CAL4664',
              'driver' => 'VERDAD BERNARDINO LADERO',
              'department' => 'Tactical Vehicle',
              'brand' => 'ISUZU',
              'model' => 'GIGA',
            ),
            64 => 
            array (
              'plateno' => 'NAN4434',
              'driver' => 'NO DRIVER',
              'department' => 'Tactical Vehicle',
              'brand' => 'ISUZU',
              'model' => 'QKR77',
            ),
            65 => 
            array (
              'plateno' => 'RAZ328',
              'driver' => 'RODRIGO JR MAMARIN CABASOG',
              'department' => 'Tactical Vehicle',
              'brand' => 'ISUZU',
              'model' => '6SD1',
            ),
            66 => 
            array (
              'plateno' => 'RFD440',
              'driver' => 'MENANDRO ALLAS',
              'department' => 'Tactical Vehicle',
              'brand' => 'ISUZU',
              'model' => '10PEI',
            ),
        ); 
    foreach($drivers as $dr){
 
        $new = new ExpressWayDriver;
        $new->driver = $dr['driver'];
        $new->department = $dr['department'];
        $new->brand = $dr['brand'];
        $new->model = $dr['model'];
        $new->plate = $dr['plateno'];
        $new->save();
    }
     
return 'ok';
    }
}
