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
    public function reuploadBranchCode(){
        $branches = array(
            array('sapcode' => 'ADMI-PN','id' => '1','machine_number' => '103','bsched_id' => '1','region_id' => '2','name' => 'Admin','whscode' => 'ADMN,ADM2,ANON','bm_oic' => 'Mariel Quitaleg','companies' => NULL,'created_at' => NULL,'updated_at' => '2020-11-23 16:10:28','sap_segment' => '001','grades' => NULL),
            array('sapcode' => 'AGOO-LU','id' => '2','machine_number' => '141','bsched_id' => '1','region_id' => '3','name' => 'Agoo','whscode' => 'AGOO','bm_oic' => 'Albert Quilondrino','companies' => '4','created_at' => NULL,'updated_at' => '2020-06-19 11:22:31','sap_segment' => '034','grades' => NULL),
            array('sapcode' => 'ALAM-PN','id' => '3','machine_number' => '108','bsched_id' => NULL,'region_id' => '1','name' => 'Alaminos','whscode' => 'ALAM','bm_oic' => 'Richard Mariano','companies' => '3','created_at' => NULL,'updated_at' => '2020-06-29 08:33:51','sap_segment' => '032','grades' => NULL),
            array('sapcode' => 'APAL-PM','id' => '4','machine_number' => '114','bsched_id' => '0','region_id' => '1','name' => 'Apalit','whscode' => 'APLT','bm_oic' => 'Donard Dale Tabinas','companies' => '8','created_at' => NULL,'updated_at' => '2018-10-21 14:25:05','sap_segment' => '042','grades' => NULL),
            array('sapcode' => 'APPL-PN','id' => '5','machine_number' => '148','bsched_id' => '0','region_id' => '2','name' => 'Appletronics Main Office','whscode' => 'APPL2','bm_oic' => 'Arnel Salagubang','companies' => '1','created_at' => NULL,'updated_at' => '2019-10-30 15:27:17','sap_segment' => '059','grades' => NULL),
            array('sapcode' => NULL,'id' => '6','machine_number' => '999','bsched_id' => '0','region_id' => '2','name' => 'Appletronics Santiago','whscode' => 'APLS','bm_oic' => 'John Doe','companies' => NULL,'created_at' => NULL,'updated_at' => '2019-10-30 15:27:41','sap_segment' => '059','grades' => NULL),
            array('sapcode' => 'BAGU-BN','id' => '7','machine_number' => '115','bsched_id' => '0','region_id' => '3','name' => 'Baguio','whscode' => 'BAGU','bm_oic' => 'Elenita Parazo','companies' => '4','created_at' => NULL,'updated_at' => '2018-10-21 14:25:56','sap_segment' => '013','grades' => NULL),
            array('sapcode' => 'BALA-BT','id' => '8','machine_number' => '144','bsched_id' => '0','region_id' => '1','name' => 'Balanga','whscode' => 'BALA','bm_oic' => 'Christian Paul','companies' => '9','created_at' => NULL,'updated_at' => '2018-10-21 14:26:08','sap_segment' => '051','grades' => NULL),
            array('sapcode' => 'BALI-BL','id' => '9','machine_number' => '137','bsched_id' => '0','region_id' => '1','name' => 'Baliuag','whscode' => 'BALI','bm_oic' => 'Philip Baylon','companies' => '8','created_at' => NULL,'updated_at' => '2018-10-21 14:26:18','sap_segment' => '044','grades' => NULL),
            array('sapcode' => 'BANT-SR','id' => '10','machine_number' => '116','bsched_id' => '0','region_id' => '3','name' => 'Bantay','whscode' => 'BANT','bm_oic' => 'Etelyn Tagayuna','companies' => '6','created_at' => NULL,'updated_at' => '2018-10-21 14:29:45','sap_segment' => '016','grades' => NULL),
            array('sapcode' => 'BATA-NR','id' => '11','machine_number' => '117','bsched_id' => '0','region_id' => '3','name' => 'Batac','whscode' => 'BATC','bm_oic' => 'Mark Leo Rabbon','companies' => '5','created_at' => NULL,'updated_at' => '2018-10-21 14:29:59','sap_segment' => '028','grades' => NULL),
            array('sapcode' => 'BAYA-PN','id' => '12','machine_number' => '109','bsched_id' => NULL,'region_id' => '1','name' => 'Bayambang','whscode' => 'BAYA','bm_oic' => 'Fedema Rivera','companies' => '3','created_at' => NULL,'updated_at' => '2020-06-29 08:34:23','sap_segment' => '033','grades' => NULL),
            array('sapcode' => 'CAMI-TR','id' => '13','machine_number' => '118','bsched_id' => '0','region_id' => '1','name' => 'Camiling','whscode' => 'CMLG','bm_oic' => 'Ma. Catherine Valdez','companies' => '10','created_at' => NULL,'updated_at' => '2018-10-21 14:30:23','sap_segment' => '024','grades' => NULL),
            array('sapcode' => 'CAND-SR','id' => '14','machine_number' => '119','bsched_id' => '0','region_id' => '3','name' => 'Candon','whscode' => 'CAND','bm_oic' => 'Leo Baliton','companies' => '4','created_at' => NULL,'updated_at' => '2018-10-21 14:30:35','sap_segment' => '014','grades' => NULL),
            array('sapcode' => 'CAPA-TR','id' => '15','machine_number' => '120','bsched_id' => '0','region_id' => '1','name' => 'Capas','whscode' => 'CAPA','bm_oic' => 'Alma Joy Casoy','companies' => '10','created_at' => NULL,'updated_at' => '2018-10-21 14:30:44','sap_segment' => '029','grades' => NULL),
            array('sapcode' => 'CAUA-IA','id' => '16','machine_number' => '121','bsched_id' => '0','region_id' => '2','name' => 'Cauayan','whscode' => 'CAUA','bm_oic' => 'Michael Iris Grande','companies' => '7','created_at' => NULL,'updated_at' => '2018-10-21 14:30:54','sap_segment' => '040','grades' => NULL),
            array('sapcode' => 'DAGU-PN','id' => '17','machine_number' => '112','bsched_id' => '0','region_id' => '3','name' => 'Dagupan','whscode' => 'DAGU','bm_oic' => 'Rhodora Pedro','companies' => '3','created_at' => NULL,'updated_at' => '2018-10-21 14:31:05','sap_segment' => '006','grades' => NULL),
            array('sapcode' => 'IBAZ-ZM','id' => '18','machine_number' => '143','bsched_id' => '0','region_id' => '1','name' => 'Iba','whscode' => 'IBAZ','bm_oic' => 'Armando Jr. Ochua','companies' => '9','created_at' => NULL,'updated_at' => '2018-10-21 14:31:14','sap_segment' => '050','grades' => NULL),
            array('sapcode' => 'ILAG-IA','id' => '19','machine_number' => '145','bsched_id' => NULL,'region_id' => '2','name' => 'Ilagan','whscode' => 'ILAG','bm_oic' => 'Kevin Jasper Magno','companies' => '5','created_at' => NULL,'updated_at' => '2021-01-07 10:15:19','sap_segment' => '052','grades' => NULL),
            array('sapcode' => 'LAOA-NR','id' => '20','machine_number' => '122','bsched_id' => '0','region_id' => '3','name' => 'Laoag','whscode' => 'LAOA','bm_oic' => 'Osborne Dela Cruz','companies' => '5','created_at' => NULL,'updated_at' => '2018-10-21 14:31:26','sap_segment' => '015','grades' => NULL),
            array('sapcode' => 'MABA-PM','id' => '21','machine_number' => '123','bsched_id' => '0','region_id' => '1','name' => 'Mabalacat','whscode' => 'MABA,MABI','bm_oic' => 'Regina Rabang','companies' => '8','created_at' => NULL,'updated_at' => '2019-09-05 13:04:21','sap_segment' => '031','grades' => NULL),
            array('sapcode' => 'MANA-PN','id' => '22','machine_number' => '111','bsched_id' => '0','region_id' => '3','name' => 'Manaoag','whscode' => 'MANA','bm_oic' => 'Caroline Operaña','companies' => '3','created_at' => NULL,'updated_at' => '2018-10-21 14:31:47','sap_segment' => '008','grades' => NULL),
            array('sapcode' => 'MANG-PN','id' => '23','machine_number' => '110','bsched_id' => '0','region_id' => '3','name' => 'Mangaldan','whscode' => 'MANG','bm_oic' => 'Roselida Manaois','companies' => '3','created_at' => NULL,'updated_at' => '2018-10-21 14:31:57','sap_segment' => '007','grades' => NULL),
            array('sapcode' => 'MONC-TR','id' => '24','machine_number' => '124','bsched_id' => '0','region_id' => '1','name' => 'Moncada','whscode' => 'MONC','bm_oic' => 'Marilou Mauricio','companies' => '10','created_at' => NULL,'updated_at' => '2018-10-21 14:32:11','sap_segment' => '019','grades' => NULL),
            array('sapcode' => 'NAGU-LU','id' => '25','machine_number' => '136','bsched_id' => '0','region_id' => '3','name' => 'Naguilian','whscode' => 'NAGU','bm_oic' => 'Lanilyn Edna Florendo','companies' => '4','created_at' => NULL,'updated_at' => '2018-10-21 14:32:23','sap_segment' => '012','grades' => NULL),
            array('sapcode' => 'NANC-PN','id' => '26','machine_number' => '105','bsched_id' => '0','region_id' => '2','name' => 'Nancayasan','whscode' => 'NANC','bm_oic' => 'Rhodora Cancino','companies' => '1','created_at' => NULL,'updated_at' => '2018-10-21 14:32:37','sap_segment' => '027','grades' => NULL),
            array('sapcode' => NULL,'id' => '27','machine_number' => '155','bsched_id' => '0','region_id' => '2','name' => 'Appletronics-Addessa','whscode' => 'APPL','bm_oic' => 'Arnel Salagubang','companies' => '1','created_at' => NULL,'updated_at' => '2019-10-30 15:28:48','sap_segment' => '059','grades' => NULL),
            array('sapcode' => 'PANI-TR','id' => '28','machine_number' => '125','bsched_id' => '0','region_id' => '1','name' => 'Paniqui','whscode' => 'PANI','bm_oic' => 'Leslie Soliman','companies' => '10','created_at' => NULL,'updated_at' => '2018-10-21 14:32:48','sap_segment' => '017','grades' => NULL),
            array('sapcode' => 'POZO-PN','id' => '29','machine_number' => '126','bsched_id' => '0','region_id' => '3','name' => 'Pozorrubio','whscode' => 'POZO','bm_oic' => 'Lowella Perez','companies' => '1','created_at' => NULL,'updated_at' => '2018-10-21 14:32:58','sap_segment' => '009','grades' => NULL),
            array('sapcode' => 'ROSA-PN','id' => '30','machine_number' => '107','bsched_id' => '0','region_id' => '2','name' => 'Rosales','whscode' => 'ROSS','bm_oic' => 'Imelda Mones','companies' => '1','created_at' => NULL,'updated_at' => '2018-10-21 14:33:10','sap_segment' => '004','grades' => NULL),
            array('sapcode' => 'ROSA-LU','id' => '31','machine_number' => '127','bsched_id' => '0','region_id' => '3','name' => 'Rosario','whscode' => 'ROSO','bm_oic' => 'Ailen Jucar','companies' => '4','created_at' => NULL,'updated_at' => '2018-10-21 14:33:20','sap_segment' => '010','grades' => NULL),
            array('sapcode' => 'ROXA-IA','id' => '32','machine_number' => '140','bsched_id' => '0','region_id' => '2','name' => 'Roxas','whscode' => 'ROXA','bm_oic' => 'Charisse Sales','companies' => '7','created_at' => NULL,'updated_at' => '2018-10-21 14:33:30','sap_segment' => '048','grades' => NULL),
            array('sapcode' => 'SANC-PN','id' => '33','machine_number' => '129','bsched_id' => NULL,'region_id' => '1','name' => 'San Carlos','whscode' => 'SNCA','bm_oic' => 'Edward Gallarin','companies' => '3','created_at' => NULL,'updated_at' => '2020-06-29 08:35:52','sap_segment' => '043','grades' => NULL),
            array('sapcode' => 'SANF-LU','id' => '34','machine_number' => '128','bsched_id' => '0','region_id' => '3','name' => 'San Fernando','whscode' => 'SFLU','bm_oic' => 'Clarisa Ugma','companies' => '4','created_at' => NULL,'updated_at' => '2018-10-21 14:33:56','sap_segment' => '011','grades' => NULL),
            array('sapcode' => 'SANJ-NE','id' => '35','machine_number' => '130','bsched_id' => '0','region_id' => '2','name' => 'San Jose','whscode' => 'SNJO,EASY','bm_oic' => 'Rose Marie Espiritu','companies' => '7','created_at' => NULL,'updated_at' => '2021-11-10 16:39:05','sap_segment' => '002','grades' => NULL),
            array('sapcode' => 'SANC-CG','id' => '36','machine_number' => '142','bsched_id' => '0','region_id' => '2','name' => 'Sanchez Mira','whscode' => 'SANC','bm_oic' => 'Manolito Bonifacio Leonador','companies' => '5','created_at' => NULL,'updated_at' => '2018-10-21 14:34:21','sap_segment' => '049','grades' => NULL),
            array('sapcode' => 'SANT-SR','id' => '37','machine_number' => '134','bsched_id' => '0','region_id' => '3','name' => 'Santa Cruz','whscode' => 'STCR','bm_oic' => 'Irene Lolie Abajo','companies' => '4','created_at' => NULL,'updated_at' => '2018-10-21 14:34:33','sap_segment' => NULL,'grades' => NULL),
            array('sapcode' => 'STAM-BL','id' => '38','machine_number' => '149','bsched_id' => '0','region_id' => '1','name' => 'Santa Maria','whscode' => 'STAM','bm_oic' => 'Frankie Viernes','companies' => '8','created_at' => NULL,'updated_at' => '2018-10-21 14:34:47','sap_segment' => NULL,'grades' => NULL),
            array('sapcode' => 'SANT-IA','id' => '39','machine_number' => '131','bsched_id' => '0','region_id' => '2','name' => 'Santiago','whscode' => 'STGO','bm_oic' => 'Dikela Managad','companies' => '7','created_at' => NULL,'updated_at' => '2018-10-21 14:34:59','sap_segment' => '041','grades' => NULL),
            array('sapcode' => 'SIND-PM','id' => '40','machine_number' => '132','bsched_id' => '0','region_id' => '1','name' => 'Sindalan','whscode' => 'SIND','bm_oic' => 'Frederick Taylan','companies' => '9','created_at' => NULL,'updated_at' => '2018-10-21 14:35:09','sap_segment' => '035','grades' => NULL),
            array('sapcode' => 'SOLA-NV','id' => '41','machine_number' => '135','bsched_id' => '0','region_id' => '2','name' => 'Solano','whscode' => 'SOLA','bm_oic' => 'Josen Bautista','companies' => '7','created_at' => NULL,'updated_at' => '2018-10-21 14:35:21','sap_segment' => '046','grades' => NULL),
            array('sapcode' => 'TARL-TR','id' => '42','machine_number' => '133','bsched_id' => '0','region_id' => '1','name' => 'Tarlac','whscode' => 'TARL','bm_oic' => 'Raquel Sarguet','companies' => '10','created_at' => NULL,'updated_at' => '2018-10-21 14:35:32','sap_segment' => '018','grades' => NULL),
            array('sapcode' => 'TAYU-PN','id' => '43','machine_number' => '113','bsched_id' => '0','region_id' => '2','name' => 'Tayug','whscode' => 'TAYU','bm_oic' => 'Rose Ann Bugarin','companies' => '1','created_at' => NULL,'updated_at' => '2018-10-21 14:35:44','sap_segment' => '005','grades' => NULL),
            array('sapcode' => 'TUGU-CG','id' => '44','machine_number' => '139','bsched_id' => '0','region_id' => '2','name' => 'Tuguegarao','whscode' => 'TUGU','bm_oic' => 'Charince Parajas','companies' => '5','created_at' => NULL,'updated_at' => '2018-10-21 14:35:54','sap_segment' => '047','grades' => NULL),
            array('sapcode' => 'TUMA-IA','id' => '45','machine_number' => '147','bsched_id' => '0','region_id' => '2','name' => 'Tumauini','whscode' => 'TUMA','bm_oic' => 'Marcelino Lazo Jr.','companies' => '5','created_at' => NULL,'updated_at' => '2018-10-23 06:27:59','sap_segment' => '054','grades' => NULL),
            array('sapcode' => 'UMIN-PN','id' => '46','machine_number' => '138','bsched_id' => '0','region_id' => '2','name' => 'Umingan','whscode' => 'UMIN','bm_oic' => 'Mary Ann Tolentino','companies' => '1','created_at' => NULL,'updated_at' => '2018-10-21 14:37:07','sap_segment' => '037','grades' => NULL),
            array('sapcode' => 'URDA-PN','id' => '47','machine_number' => '104','bsched_id' => '0','region_id' => '2','name' => 'Urdaneta','whscode' => 'URDA,URD2','bm_oic' => 'Joan Laulita','companies' => '1','created_at' => NULL,'updated_at' => '2019-05-24 13:07:57','sap_segment' => '002','grades' => NULL),
            array('sapcode' => 'VILL-PN','id' => '48','machine_number' => '106','bsched_id' => '0','region_id' => '2','name' => 'Villasis','whscode' => 'VILL','bm_oic' => 'Mariles Dela Cruz','companies' => '1','created_at' => NULL,'updated_at' => '2018-10-21 14:37:28','sap_segment' => '003','grades' => NULL),
            array('sapcode' => NULL,'id' => '49','machine_number' => '255','bsched_id' => '0','region_id' => '2','name' => 'ADMIN PAN BACKHAUL','whscode' => 'PANW,MEXI','bm_oic' => 'Jay Jay','companies' => NULL,'created_at' => '2018-10-21 14:24:01','updated_at' => '2022-04-25 18:46:14','sap_segment' => '001','grades' => NULL),
            array('sapcode' => 'BAMB-NV','id' => '50','machine_number' => '146','bsched_id' => NULL,'region_id' => '2','name' => 'Bambang','whscode' => 'BAMB','bm_oic' => 'Verylie Ann Martinez','companies' => '7','created_at' => '2018-10-21 14:29:12','updated_at' => '2018-10-21 14:29:12','sap_segment' => '053','grades' => NULL),
            array('sapcode' => 'BUNT-CG','id' => '51','machine_number' => '151','bsched_id' => '0','region_id' => '2','name' => 'Buntun','whscode' => 'BUNT','bm_oic' => 'Jessica Amor','companies' => '5','created_at' => '2018-10-31 08:06:43','updated_at' => '2019-07-16 15:10:50','sap_segment' => '057','grades' => NULL),
            array('sapcode' => 'MAGA-PM','id' => '52','machine_number' => '152','bsched_id' => '0','region_id' => '1','name' => 'Magalang','whscode' => 'MAGA','bm_oic' => 'Mariel Baoanin','companies' => '8','created_at' => '2018-10-31 08:07:53','updated_at' => '2019-07-16 15:13:23','sap_segment' => '056','grades' => NULL),
            array('sapcode' => 'MALO-BL','id' => '53','machine_number' => '150','bsched_id' => '0','region_id' => '1','name' => 'Malolos','whscode' => 'MALO','bm_oic' => 'Alexander Cunanan','companies' => '8','created_at' => '2018-10-31 08:08:21','updated_at' => '2019-07-16 15:10:35','sap_segment' => '062','grades' => NULL),
            array('sapcode' => 'SUBI-ZM','id' => '54','machine_number' => '153','bsched_id' => '0','region_id' => '1','name' => 'Subic','whscode' => 'SUBI','bm_oic' => 'Mc Jeron Caronongan','companies' => '9','created_at' => '2018-12-14 08:46:07','updated_at' => '2019-07-16 15:11:31','sap_segment' => '061','grades' => NULL),
            array('sapcode' => NULL,'id' => '55','machine_number' => NULL,'bsched_id' => NULL,'region_id' => NULL,'name' => 'MIA Backhaul','whscode' => NULL,'bm_oic' => NULL,'companies' => NULL,'created_at' => '2019-03-01 13:32:57','updated_at' => '2019-03-01 13:32:57','sap_segment' => '02','grades' => NULL),
            array('sapcode' => NULL,'id' => '56','machine_number' => NULL,'bsched_id' => NULL,'region_id' => NULL,'name' => 'PAN Backhaul','whscode' => NULL,'bm_oic' => NULL,'companies' => NULL,'created_at' => '2019-03-01 13:38:07','updated_at' => '2019-03-01 13:38:07','sap_segment' => '01','grades' => NULL),
            array('sapcode' => NULL,'id' => '57','machine_number' => NULL,'bsched_id' => NULL,'region_id' => NULL,'name' => 'Main Office','whscode' => NULL,'bm_oic' => NULL,'companies' => NULL,'created_at' => '2019-03-12 15:38:41','updated_at' => '2019-03-12 15:38:41','sap_segment' => '001','grades' => NULL),
            array('sapcode' => 'MANM-PN','id' => '58','machine_number' => '900','bsched_id' => NULL,'region_id' => '1','name' => 'Mangatarem','whscode' => 'MANM','bm_oic' => 'Eliseo John Buenaventura','companies' => '3','created_at' => '2019-11-11 13:47:30','updated_at' => '2020-06-29 08:35:25','sap_segment' => '063','grades' => NULL),
            array('sapcode' => 'GAPA-NE','id' => '59','machine_number' => '9999','bsched_id' => NULL,'region_id' => '1','name' => 'Gapan','whscode' => 'GAPA','bm_oic' => 'Johnlyn Agpoon','companies' => '7','created_at' => '2020-02-13 09:45:46','updated_at' => '2020-06-29 08:34:51','sap_segment' => '064','grades' => NULL),
            array('sapcode' => 'PAND-PM','id' => '61','machine_number' => '158','bsched_id' => '0','region_id' => '1','name' => 'Pandan','whscode' => 'PAND','bm_oic' => 'Emerson Villar','companies' => '9','created_at' => '2020-11-05 09:37:35','updated_at' => '2021-02-12 09:55:59','sap_segment' => '066','grades' => NULL),
            array('sapcode' => 'CONC-TR','id' => '62','machine_number' => '157','bsched_id' => NULL,'region_id' => '1','name' => 'Concepcion','whscode' => 'CONC','bm_oic' => 'Jesthony Bryan Leysa','companies' => '10','created_at' => '2020-11-05 10:12:04','updated_at' => '2020-11-05 10:12:04','sap_segment' => '065','grades' => NULL),
            array('sapcode' => 'MARI-BT','id' => '63','machine_number' => '581','bsched_id' => '0','region_id' => '1','name' => 'Mariveles','whscode' => 'MARI','bm_oic' => 'Jerome Salivio','companies' => '9','created_at' => '2020-11-26 08:47:13','updated_at' => '2020-11-27 09:22:36','sap_segment' => '067','grades' => NULL),
            array('sapcode' => 'SANF-PN','id' => '65','machine_number' => '3452','bsched_id' => '1','region_id' => '3','name' => 'San Fabian','whscode' => 'SANF','bm_oic' => 'John Doe','companies' => '3','created_at' => '2021-03-25 11:06:40','updated_at' => '2021-03-25 11:06:40','sap_segment' => NULL,'grades' => NULL),
            array('sapcode' => 'LING-PN','id' => '66','machine_number' => '2345','bsched_id' => '0','region_id' => '2','name' => 'Lingayen','whscode' => 'LING','bm_oic' => 'Nelson G. Serrano','companies' => '3','created_at' => '2021-05-21 14:03:07','updated_at' => '2021-07-02 15:00:53','sap_segment' => NULL,'grades' => NULL),
            array('sapcode' => 'BINA-PN','id' => '67','machine_number' => '3423','bsched_id' => '0','region_id' => '2','name' => 'Binalonan','whscode' => 'BINA','bm_oic' => 'Danny Quero','companies' => '1','created_at' => '2021-05-31 09:02:35','updated_at' => '2021-07-06 14:05:45','sap_segment' => NULL,'grades' => NULL),
            array('sapcode' => 'GUIM-NE','id' => '68','machine_number' => '23423','bsched_id' => '0','region_id' => '2','name' => 'Guimba','whscode' => 'GUIM','bm_oic' => 'John Doe','companies' => '7','created_at' => '2021-05-31 09:03:24','updated_at' => '2021-07-06 14:14:08','sap_segment' => '003','grades' => NULL),
            array('sapcode' => NULL,'id' => '69','machine_number' => '345','bsched_id' => '0','region_id' => '1','name' => 'ADMIN MIA BACKHAUL','whscode' => 'MIAW','bm_oic' => 'JOHNDOE','companies' => NULL,'created_at' => '2021-07-05 09:27:07','updated_at' => '2022-04-25 18:45:57','sap_segment' => '001','grades' => NULL),
            array('sapcode' => 'ZARA-NE','id' => '70','machine_number' => '567','bsched_id' => '0','region_id' => '2','name' => 'Zaragoza','whscode' => 'ZARA','bm_oic' => 'Jeffrey Gorospe Pascual','companies' => '7','created_at' => '2022-03-22 14:20:18','updated_at' => '2022-03-22 14:23:11','sap_segment' => '004','grades' => NULL),
            array('sapcode' => NULL,'id' => '71','machine_number' => '160','bsched_id' => '0','region_id' => '2','name' => 'ADMIN ADDESSA BACKHAUL','whscode' => 'ADDE','bm_oic' => 'Main Office','companies' => NULL,'created_at' => '2022-03-26 11:46:21','updated_at' => '2022-04-25 18:45:40','sap_segment' => '001','grades' => NULL)
          );
          foreach($branches as $in){
           $dd[] = $in['id'].'-'.$in['sapcode'].'-'.$in['sap_segment'].'';   

            DB::table('branches')->where('id', $in['id'])->update([
                'sapcode'=> $in['sapcode'],
                'sap_segment'=> $in['sap_segment']
            ]);
          }
          return $dd;

    }
    public function branches(){
        $branches = Branch::orderBy('name', 'asc')->get();
        foreach($branches as $branch){
            // if($branch->sapcode == 'SANJ-NE' || $branch->sapcode == 'GUIM-NE'){
                $b[] = ['name'=> $branch->name,
                'value'=> $branch->id, 
                'sapcode' => $branch->sapcode,
                'segment'=> $branch->sap_segment];
            // }
           
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
