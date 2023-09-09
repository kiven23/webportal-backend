<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ExpressWayDriver;
use App\ExpressWayToll;
use App\ExpressWayUpload;
use DB;

class ExpressWayUploadController extends Controller
{

    public function upload(Request $req){
        
        // $json = '[  {
        //     "key": "NEB5249",
        //     "row": [
        //         {
        //             "Posted": "2023-08-04",
        //             "Date": "13:08:03",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-04 13:08:03 Toll MEXICO-X03 NLEX 469.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "469.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-04 13:13:41 2023-08-04 13:08:03 Toll MEXICO-X03 NLEX 469.00 0.00",
        //             "Total": "4,582.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-04",
        //             "Date": "14:23:27",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-04 14:23:27 Toll CONCEPCION-X01 CAVITEX 302.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "302.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-04 14:29:11 2023-08-04 14:23:27 Toll CONCEPCION-X01 NLEX 302.00 0.00",
        //             "Total": "4,582.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-04",
        //             "Date": "16:05:00",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-04 16:05:00 Toll TARLAC-X03 CALAX 167.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "167.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-04 16:13:43 2023-08-04 16:05:00 Toll TARLAC-X03 NLEX 167.00 0.00",
        //             "Total": "4,582.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-09",
        //             "Date": "13:19:52",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-09 13:19:52 Toll CONCEPCION-X01 SLEX 167.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "167.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-09 13:25:01 2023-08-09 13:19:52 Toll CONCEPCION-X01 NLEX 167.00 0.00",
        //             "Total": "4,582.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-09",
        //             "Date": "15:07:04",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-09 15:07:04 Toll TARLAC-X05 SKYWAY 167.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "167.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-09 15:19:02 2023-08-09 15:07:04 Toll TARLAC-X05 NLEX 167.00 0.00",
        //             "Total": "4,582.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-12",
        //             "Date": "15:14:00",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-12 15:14:00 Toll ANGELES-X03 NAIAX 383.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "383.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-12 15:22:32 2023-08-12 15:14:00 Toll ANGELES-X03 NLEX 383.00 0.00",
        //             "Total": "4,582.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-12",
        //             "Date": "17:38:23",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-12 17:38:23 Toll TARLAC-X04 STAR 309.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "309.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-12 17:57:32 2023-08-12 17:38:23 Toll TARLAC-X04 NLEX 309.00 0.00",
        //             "Total": "4,582.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-14",
        //             "Date": "10:37:05",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-14 10:37:05 Toll MEXICO-X01 TPLEX 469.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "469.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-14 10:40:21 2023-08-14 10:37:05 Toll MEXICO-X01 NLEX 469.00 0.00",
        //             "Total": "4,582.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-14",
        //             "Date": "13:47:43",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-14 13:47:43 Toll TARLAC-X03 CLLEX 469.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "469.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-14 13:54:42 2023-08-14 13:47:43 Toll TARLAC-X04 NLEX 469.00 0.00",
        //             "Total": "4,582.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-15",
        //             "Date": "07:39:15",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-15 07:39:15 Toll SAN FERNANDO SB-X02 NLEX 541.00 0.00",
        //             "Tollway": "FERNANDO",
        //             "Debit": "SB-X02",
        //             "Credit": "NLEX",
        //             "datas": "2023-08-15 07:43:42 2023-08-15 07:39:15 Toll SAN FERNANDO SB-X02 NLEX 541.00 0.00",
        //             "Total": "4,582.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-15",
        //             "Date": "08:46:52",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-15 08:46:52 Toll TIPO-X01 NLEX 192.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "192.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-15 08:52:02 2023-08-15 08:46:52 Toll TIPO-X01 NLEX 192.00 0.00",
        //             "Total": "4,582.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-15",
        //             "Date": "14:59:50",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-15 14:59:50 Toll PORAC-X01 NLEX 478.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "478.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-15 15:04:12 2023-08-15 14:59:50 Toll PORAC-X01 NLEX 478.00 0.00",
        //             "Total": "4,582.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-15",
        //             "Date": "16:36:11",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-15 16:36:11 Toll TARLAC-X05 NLEX 469.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "469.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-15 16:54:52 2023-08-15 16:36:11 Toll TARLAC-X05 NLEX 469.00 0.00",
        //             "Total": "4,582.00 0.00"
        //         },
        //         {
        //             "Posted": "Total",
        //             "Date": "0.00",
        //             "Transaction": "",
        //             "Description": "4,582.00 0.00",
        //             "Tollway": "",
        //             "Debit": "",
        //             "Credit": "",
        //             "datas": "Total : 4,582.00 0.00",
        //             "Total": "4,582.00 0.00"
        //         }
        //     ],
        //     "info": {
        //         "driver": "DON HENLEY TOLLO RAMIREZ",
        //         "department": "Logistics",
        //         "brand": "MITSUBISHI",
        //         "model": "CANTER",
        //         "plateno": "NEB5249"
        //     }
        // },
        // {
        //     "key": "NGG1727",
        //     "row": [
        //         {
        //             "Posted": "2023-08-11",
        //             "Date": "05:30:16",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-11 05:30:16 Toll SAN SIMON SB-X01 NLEX 284.00 0.00",
        //             "Tollway": "SIMON",
        //             "Debit": "SB-X01",
        //             "Credit": "NLEX",
        //             "datas": "2023-08-11 05:36:31 2023-08-11 05:30:16 Toll SAN SIMON SB-X01 NLEX 284.00 0.00",
        //             "Total": "1,593.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-11",
        //             "Date": "16:14:50",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-11 16:14:50 Toll MEXICO-X01 NLEX 180.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "180.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-11 16:19:12 2023-08-11 16:14:50 Toll MEXICO-X01 NLEX 180.00 0.00",
        //             "Total": "1,593.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-12",
        //             "Date": "09:40:43",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-12 09:40:43 Toll TARLAC-X04 NLEX 155.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "155.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-12 10:03:43 2023-08-12 09:40:43 Toll TARLAC-X04 NLEX 155.00 0.00",
        //             "Total": "1,593.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-14",
        //             "Date": "08:43:04",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-14 08:43:04 Toll BOCAUE BARRIER CS-X16 NLEX 487.00 0.00",
        //             "Tollway": "BARRIER",
        //             "Debit": "CS-X16",
        //             "Credit": "NLEX",
        //             "datas": "2023-08-14 08:52:02 2023-08-14 08:43:04 Toll BOCAUE BARRIER CS-X16 NLEX 487.00 0.00",
        //             "Total": "1,593.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-14",
        //             "Date": "11:13:50",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-14 11:13:50 Toll MARILAO NB-N01 NLEX 69.00 0.00",
        //             "Tollway": "NB-N01",
        //             "Debit": "NLEX",
        //             "Credit": "69.00",
        //             "datas": "2023-08-14 11:19:22 2023-08-14 11:13:50 Toll MARILAO NB-N01 NLEX 69.00 0.00",
        //             "Total": "1,593.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-14",
        //             "Date": "13:17:20",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-14 13:17:20 Toll TARLAC-X03 NLEX 418.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "418.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-14 13:27:42 2023-08-14 13:17:20 Toll TARLAC-X03 NLEX 418.00 0.00",
        //             "Total": "1,593.00 0.00"
        //         },
        //         {
        //             "Posted": "Total",
        //             "Date": "0.00",
        //             "Transaction": "",
        //             "Description": "1,593.00 0.00",
        //             "Tollway": "",
        //             "Debit": "",
        //             "Credit": "",
        //             "datas": "Total : 1,593.00 0.00",
        //             "Total": "1,593.00 0.00"
        //         }
        //     ],
        //     "info": {
        //         "driver": "MICHAEL MANZANO TABILIN",
        //         "department": "Officer",
        //         "brand": "ISUZU",
        //         "model": "D-MAX",
        //         "plateno": "NGG1727"
        //     }
        // },
        // {
        //     "key": "XKC663",
        //     "row": [
        //         {
        //             "Posted": "2023-08-11",
        //             "Date": "21:54:55",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-11 21:54:55 Toll BOCAUE BARRIER CS-X06 NLEX 487.00 0.00",
        //             "Tollway": "BARRIER",
        //             "Debit": "CS-X06",
        //             "Credit": "NLEX",
        //             "datas": "2023-08-11 22:07:01 2023-08-11 21:54:55 Toll BOCAUE BARRIER CS-X06 NLEX 487.00 0.00",
        //             "Total": "2,922.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-12",
        //             "Date": "09:50:46",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-12 09:50:46 Toll BALINTAWAK-N07 NLEX 69.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "69.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-12 09:58:11 2023-08-12 09:50:46 Toll BALINTAWAK-N07 NLEX 69.00 0.00",
        //             "Total": "2,922.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-12",
        //             "Date": "11:24:41",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-12 11:24:41 Toll TARLAC-X05 NLEX 418.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "418.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-12 11:34:11 2023-08-12 11:24:41 Toll TARLAC-X05 NLEX 418.00 0.00",
        //             "Total": "2,922.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-13",
        //             "Date": "00:38:29",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-13 00:38:29 Toll BOCAUE BARRIER CS-X06 NLEX 487.00 0.00",
        //             "Tollway": "BARRIER",
        //             "Debit": "CS-X06",
        //             "Credit": "NLEX",
        //             "datas": "2023-08-13 00:42:52 2023-08-13 00:38:29 Toll BOCAUE BARRIER CS-X06 NLEX 487.00 0.00",
        //             "Total": "2,922.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-13",
        //             "Date": "07:58:35",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-13 07:58:35 Toll MINDANAO-N03 NLEX 69.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "69.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-13 08:03:31 2023-08-13 07:58:35 Toll MINDANAO-N03 NLEX 69.00 0.00",
        //             "Total": "2,922.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-13",
        //             "Date": "09:30:57",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-13 09:30:57 Toll TARLAC-X03 NLEX 418.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "418.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-13 09:45:42 2023-08-13 09:30:57 Toll TARLAC-X03 NLEX 418.00 0.00",
        //             "Total": "2,922.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-14",
        //             "Date": "16:51:17",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-14 16:51:17 Toll BOCAUE BARRIER CS-X29 NLEX 487.00 0.00",
        //             "Tollway": "BARRIER",
        //             "Debit": "CS-X29",
        //             "Credit": "NLEX",
        //             "datas": "2023-08-14 16:55:31 2023-08-14 16:51:17 Toll BOCAUE BARRIER CS-X29 NLEX 487.00 0.00",
        //             "Total": "2,922.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-15",
        //             "Date": "05:36:12",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-15 05:36:12 Toll MINDANAO-N03 NLEX 69.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "69.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-15 05:40:01 2023-08-15 05:36:12 Toll MINDANAO-N03 NLEX 69.00 0.00",
        //             "Total": "2,922.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-15",
        //             "Date": "07:51:35",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-15 07:51:35 Toll TARLAC-X04 NLEX 418.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "418.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-15 07:57:31 2023-08-15 07:51:35 Toll TARLAC-X04 NLEX 418.00 0.00",
        //             "Total": "2,922.00 0.00"
        //         },
        //         {
        //             "Posted": "Total",
        //             "Date": "0.00",
        //             "Transaction": "",
        //             "Description": "2,922.00 0.00",
        //             "Tollway": "",
        //             "Debit": "",
        //             "Credit": "",
        //             "datas": "Total : 2,922.00 0.00",
        //             "Total": "2,922.00 0.00"
        //         }
        //     ],
        //     "info": {
        //         "driver": "RODRIGO JR MAMARIN CABASOG",
        //         "department": "Officer",
        //         "brand": "FORD",
        //         "model": "EXPEDITION",
        //         "plateno": "XKC663"
        //     }
        // },
        // {
        //     "key": "NDO2271",
        //     "row": [
        //         {
        //             "Posted": "2023-08-03",
        //             "Date": "11:17:39",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-03 11:17:39 Toll SAN FERNANDO SB-X01 NLEX 248.00 0.00",
        //             "Tollway": "FERNANDO",
        //             "Debit": "SB-X01",
        //             "Credit": "NLEX",
        //             "datas": "2023-08-03 11:26:02 2023-08-03 11:17:39 Toll SAN FERNANDO SB-X01 NLEX 248.00 0.00",
        //             "Total": "3,825.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-03",
        //             "Date": "12:40:12",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-03 12:40:12 Toll TIPO-X01 NLEX 88.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "88.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-03 12:47:01 2023-08-03 12:40:12 Toll TIPO-X01 NLEX 88.00 0.00",
        //             "Total": "3,825.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-03",
        //             "Date": "15:37:44",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-03 15:37:44 Toll DINALUPIHAN-X02 NLEX 88.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "88.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-03 15:47:02 2023-08-03 15:37:44 Toll DINALUPIHAN-X02 NLEX 88.00 0.00",
        //             "Total": "3,825.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-03",
        //             "Date": "17:17:59",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-03 17:17:59 Toll TARLAC-X04 NLEX 248.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "248.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-03 17:23:21 2023-08-03 17:17:59 Toll TARLAC-X04 NLEX 248.00 0.00",
        //             "Total": "3,825.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-04",
        //             "Date": "10:25:39",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-04 10:25:39 Toll CONCEPCION-X01 NLEX 84.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "84.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-04 10:31:52 2023-08-04 10:25:39 Toll CONCEPCION-X01 NLEX 84.00 0.00",
        //             "Total": "3,825.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-04",
        //             "Date": "14:27:07",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-04 14:27:07 Toll SAN SIMON SB-X01 NLEX 200.00 0.00",
        //             "Tollway": "SIMON",
        //             "Debit": "SB-X01",
        //             "Credit": "NLEX",
        //             "datas": "2023-08-04 14:32:32 2023-08-04 14:27:07 Toll SAN SIMON SB-X01 NLEX 200.00 0.00",
        //             "Total": "3,825.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-04",
        //             "Date": "15:13:06",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-04 15:13:06 Toll BOCAUE IC SB-X03 NLEX 119.00 0.00",
        //             "Tollway": "IC",
        //             "Debit": "SB-X03",
        //             "Credit": "NLEX",
        //             "datas": "2023-08-04 15:20:22 2023-08-04 15:13:06 Toll BOCAUE IC SB-X03 NLEX 119.00 0.00",
        //             "Total": "3,825.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-04",
        //             "Date": "20:01:48",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-04 20:01:48 Toll TARLAC-X05 NLEX 399.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "399.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-04 20:08:02 2023-08-04 20:01:48 Toll TARLAC-X05 NLEX 399.00 0.00",
        //             "Total": "3,825.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-07",
        //             "Date": "07:43:03",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-07 07:43:03 Toll BOCAUE BARRIER CS-X09 NLEX 487.00 0.00",
        //             "Tollway": "BARRIER",
        //             "Debit": "CS-X09",
        //             "Credit": "NLEX",
        //             "datas": "2023-08-07 07:52:22 2023-08-07 07:43:03 Toll BOCAUE BARRIER CS-X09 NLEX 487.00 0.00",
        //             "Total": "3,825.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-08",
        //             "Date": "13:50:25",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-08 13:50:25 Toll TARLAC-X05 NLEX 418.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "418.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-08 14:06:34 2023-08-08 13:50:25 Toll TARLAC-X05 NLEX 418.00 0.00",
        //             "Total": "3,825.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-11",
        //             "Date": "08:00:59",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-11 08:00:59 Toll TIPO-X06 NLEX 439.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "439.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-11 08:06:52 2023-08-11 08:00:59 Toll TIPO-X06 NLEX 439.00 0.00",
        //             "Total": "3,825.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-11",
        //             "Date": "11:17:42",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-11 11:17:42 Toll TARLAC-X05 NLEX 439.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "439.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-11 11:25:02 2023-08-11 11:17:42 Toll TARLAC-X05 NLEX 439.00 0.00",
        //             "Total": "3,825.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-15",
        //             "Date": "07:36:27",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-15 07:36:27 Toll CONCEPCION-X01 NLEX 84.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "84.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-15 07:43:02 2023-08-15 07:36:27 Toll CONCEPCION-X01 NLEX 84.00 0.00",
        //             "Total": "3,825.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-15",
        //             "Date": "08:27:56",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-15 08:27:56 Toll SAN SIMON SB-X01 NLEX 200.00 0.00",
        //             "Tollway": "SIMON",
        //             "Debit": "SB-X01",
        //             "Credit": "NLEX",
        //             "datas": "2023-08-15 08:32:32 2023-08-15 08:27:56 Toll SAN SIMON SB-X01 NLEX 200.00 0.00",
        //             "Total": "3,825.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-15",
        //             "Date": "09:29:30",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-15 09:29:30 Toll MEXICO-X01 NLEX 65.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "65.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-15 09:33:42 2023-08-15 09:29:30 Toll MEXICO-X01 NLEX 65.00 0.00",
        //             "Total": "3,825.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-15",
        //             "Date": "16:54:26",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-15 16:54:26 Toll TARLAC-X04 NLEX 219.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "219.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-15 17:16:42 2023-08-15 16:54:26 Toll TARLAC-X04 NLEX 219.00 0.00",
        //             "Total": "3,825.00 0.00"
        //         },
        //         {
        //             "Posted": "Total",
        //             "Date": "0.00",
        //             "Transaction": "",
        //             "Description": "3,825.00 0.00",
        //             "Tollway": "",
        //             "Debit": "",
        //             "Credit": "",
        //             "datas": "Total : 3,825.00 0.00",
        //             "Total": "3,825.00 0.00"
        //         }
        //     ],
        //     "info": {
        //         "driver": "REY BERNAL MANUEL",
        //         "department": "Officer",
        //         "brand": "ISUZU",
        //         "model": "TRAVIZ L",
        //         "plateno": "NDO2271"
        //     }
        // },
        // {
        //     "key": "PJO836",
        //     "row": [
        //         {
        //             "Posted": "2023-08-09",
        //             "Date": "13:02:55",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-09 13:02:55 Toll MABIGA SB-X01 NLEX 155.00 0.00",
        //             "Tollway": "SB-X01",
        //             "Debit": "NLEX",
        //             "Credit": "155.00",
        //             "datas": "2023-08-09 13:07:42 2023-08-09 13:02:55 Toll MABIGA SB-X01 NLEX 155.00 0.00",
        //             "Total": "3,048.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-09",
        //             "Date": "14:55:21",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-09 14:55:21 Toll TARLAC-X03 NLEX 155.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "155.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-09 15:01:03 2023-08-09 14:55:21 Toll TARLAC-X03 NLEX 155.00 0.00",
        //             "Total": "3,048.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-11",
        //             "Date": "07:54:04",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-11 07:54:04 Toll BOCAUE BARRIER CS-X09 NLEX 487.00 0.00",
        //             "Tollway": "BARRIER",
        //             "Debit": "CS-X09",
        //             "Credit": "NLEX",
        //             "datas": "2023-08-11 08:03:32 2023-08-11 07:54:04 Toll BOCAUE BARRIER CS-X09 NLEX 487.00 0.00",
        //             "Total": "3,048.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-11",
        //             "Date": "20:28:38",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-11 20:28:38 Toll MINDANAO-N07 NLEX 69.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "69.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-11 20:33:42 2023-08-11 20:28:38 Toll MINDANAO-N07 NLEX 69.00 0.00",
        //             "Total": "3,048.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-11",
        //             "Date": "21:52:19",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-11 21:52:19 Toll TARLAC-X05 NLEX 418.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "418.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-11 21:58:12 2023-08-11 21:52:19 Toll TARLAC-X05 NLEX 418.00 0.00",
        //             "Total": "3,048.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-12",
        //             "Date": "11:34:23",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-12 11:34:23 Toll TIPO-X06 NLEX 439.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "439.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-12 11:40:02 2023-08-12 11:34:23 Toll TIPO-X06 NLEX 439.00 0.00",
        //             "Total": "3,048.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-12",
        //             "Date": "14:02:29",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-12 14:02:29 Toll TARLAC-X04 NLEX 439.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "439.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-12 14:41:02 2023-08-12 14:02:29 Toll TARLAC-X04 NLEX 439.00 0.00",
        //             "Total": "3,048.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-16",
        //             "Date": "09:05:25",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-16 09:05:25 Toll BOCAUE BARRIER CS-X10 NLEX 487.00 0.00",
        //             "Tollway": "BARRIER",
        //             "Debit": "CS-X10",
        //             "Credit": "NLEX",
        //             "datas": "2023-08-16 09:16:42 2023-08-16 09:05:25 Toll BOCAUE BARRIER CS-X10 NAIAX 487.00 0.00",
        //             "Total": "3,048.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-16",
        //             "Date": "22:07:00",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-16 22:07:00 Toll TARLAC-X03 NLEX 399.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "399.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-16 22:15:45 2023-08-16 22:07:00 Toll TARLAC-X03 STAR 399.00 0.00",
        //             "Total": "3,048.00 0.00"
        //         },
        //         {
        //             "Posted": "Total",
        //             "Date": "0.00",
        //             "Transaction": "",
        //             "Description": "3,048.00 0.00",
        //             "Tollway": "",
        //             "Debit": "",
        //             "Credit": "",
        //             "datas": "Total : 3,048.00 0.00",
        //             "Total": "3,048.00 0.00"
        //         }
        //     ],
        //     "info": {
        //         "driver": "ARIEL ARIZO",
        //         "department": "Officer",
        //         "brand": "MITSUBISHI",
        //         "model": "STRADA",
        //         "plateno": "PJO836"
        //     }
        // },
        // {
        //     "key": "NDM4976",
        //     "row": [
        //         {
        //             "Posted": "2023-08-07",
        //             "Date": "07:45:11",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-07 07:45:11 Toll BOCAUE BARRIER CS-X17 STAR 1,138.00 0.00",
        //             "Tollway": "BARRIER",
        //             "Debit": "CS-X17",
        //             "Credit": "NLEX",
        //             "datas": "2023-08-07 07:55:42 2023-08-07 07:45:11 Toll BOCAUE BARRIER CS-X17 STAR 1,138.00 0.00",
        //             "Total": "2,276.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-08",
        //             "Date": "11:32:39",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-08 11:32:39 Toll BALINTAWAK-N13 STAR 172.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "172.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-08 11:40:02 2023-08-08 11:32:39 Toll BALINTAWAK-N13 NLEX 172.00 0.00",
        //             "Total": "2,276.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-08",
        //             "Date": "13:50:47",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-08 13:50:47 Toll TARLAC-X03 NLEX 966.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "966.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-08 13:58:32 2023-08-08 13:50:47 Toll TARLAC-X03 NLEX 966.00 0.00",
        //             "Total": "2,276.00 0.00"
        //         },
        //         {
        //             "Posted": "Total",
        //             "Date": "0.00",
        //             "Transaction": "",
        //             "Description": "2,276.00 0.00",
        //             "Tollway": "",
        //             "Debit": "",
        //             "Credit": "",
        //             "datas": "Total : 2,276.00 0.00",
        //             "Total": "2,276.00 0.00"
        //         }
        //     ],
        //     "info": {
        //         "driver": "NO DRIVER",
        //         "department": "Logistics",
        //         "brand": "ISUZU",
        //         "model": "QKR77",
        //         "plateno": "NDM4976"
        //     }
        // },
        // {
        //     "key": "BAA2613",
        //     "row": [
        //         {
        //             "Posted": "2023-08-02",
        //             "Date": "08:29:19",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-02 08:29:19 Toll SAN FERNANDO SB-X01 CLLEX 248.00 0.00",
        //             "Tollway": "FERNANDO",
        //             "Debit": "SB-X01",
        //             "Credit": "NLEX",
        //             "datas": "2023-08-02 08:33:42 2023-08-02 08:29:19 Toll SAN FERNANDO SB-X01 NLEX 248.00 0.00",
        //             "Total": "1,917.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-02",
        //             "Date": "14:37:00",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-02 14:37:00 Toll TARLAC-X03 NLEX 219.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "219.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-02 14:52:03 2023-08-02 14:37:00 Toll TARLAC-X03 CLLEX 219.00 0.00",
        //             "Total": "1,917.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-04",
        //             "Date": "11:55:08",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-04 11:55:08 Toll MABIGA SB-X01 NLEX 155.00 0.00",
        //             "Tollway": "SB-X01",
        //             "Debit": "NLEX",
        //             "Credit": "155.00",
        //             "datas": "2023-08-04 12:01:41 2023-08-04 11:55:08 Toll MABIGA SB-X01 NLEX 155.00 0.00",
        //             "Total": "1,917.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-04",
        //             "Date": "12:30:34",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-04 12:30:34 Toll TARLAC-X04 NLEX 155.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "155.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-04 12:38:22 2023-08-04 12:30:34 Toll TARLAC-X04 NLEX 155.00 0.00",
        //             "Total": "1,917.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-05",
        //             "Date": "09:05:16",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-05 09:05:16 Toll PULILAN SB-X02 NLEX 331.00 0.00",
        //             "Tollway": "SB-X02",
        //             "Debit": "NLEX",
        //             "Credit": "331.00",
        //             "datas": "2023-08-05 09:11:02 2023-08-05 09:05:16 Toll PULILAN SB-X02 NLEX 331.00 0.00",
        //             "Total": "1,917.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-05",
        //             "Date": "20:40:03",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-05 20:40:03 Toll TARLAC-X04 NLEX 331.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "331.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-05 20:49:41 2023-08-05 20:40:03 Toll TARLAC-X04 NLEX 331.00 0.00",
        //             "Total": "1,917.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-07",
        //             "Date": "08:14:10",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-07 08:14:10 Toll MABIGA SB-X01 NLEX 155.00 0.00",
        //             "Tollway": "SB-X01",
        //             "Debit": "NLEX",
        //             "Credit": "155.00",
        //             "datas": "2023-08-07 08:19:32 2023-08-07 08:14:10 Toll MABIGA SB-X01 NLEX 155.00 0.00",
        //             "Total": "1,917.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-07",
        //             "Date": "10:36:52",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-07 10:36:52 Toll TARLAC-X05 NLEX 155.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "155.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-07 10:44:31 2023-08-07 10:36:52 Toll TARLAC-X05 CLLEX 155.00 0.00",
        //             "Total": "1,917.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-16",
        //             "Date": "14:00:47",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-16 14:00:47 Toll CONCEPCION-X01 NLEX 84.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "84.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-16 14:09:02 2023-08-16 14:00:47 Toll CONCEPCION-X01 NLEX 84.00 0.00",
        //             "Total": "1,917.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-16",
        //             "Date": "14:35:04",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-16 14:35:04 Toll TARLAC-X04 NLEX 84.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "84.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-16 14:51:52 2023-08-16 14:35:04 Toll TARLAC-X04 TPLEX 84.00 0.00",
        //             "Total": "1,917.00 0.00"
        //         },
        //         {
        //             "Posted": "Total",
        //             "Date": "0.00",
        //             "Transaction": "",
        //             "Description": "1,917.00 0.00",
        //             "Tollway": "",
        //             "Debit": "",
        //             "Credit": "",
        //             "datas": "Total : 1,917.00 0.00",
        //             "Total": "1,917.00 0.00"
        //         }
        //     ],
        //     "info": {
        //         "driver": "JOMYR ECHANEZ SUAZO",
        //         "department": "Officer",
        //         "brand": "HYUNDAI",
        //         "model": "H-100 2.6 GL",
        //         "plateno": "BAA2613"
        //     }
        // },
        // {
        //     "key": "WIA561",
        //     "row": [
        //         {
        //             "Posted": "2023-08-02",
        //             "Date": "08:57:35",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-02 08:57:35 Toll SAN FERNANDO SB-X01 NLEX 248.00 0.00",
        //             "Tollway": "FERNANDO",
        //             "Debit": "SB-X01",
        //             "Credit": "NLEX",
        //             "datas": "2023-08-02 09:12:33 2023-08-02 08:57:35 Toll SAN FERNANDO SB-X01 NLEX 248.00 0.00",
        //             "Total": "672.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-02",
        //             "Date": "09:59:11",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-02 09:59:11 Toll TIPO-X02 NLEX 88.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "88.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-02 10:03:32 2023-08-02 09:59:11 Toll TIPO-X02 STAR 88.00 0.00",
        //             "Total": "672.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-02",
        //             "Date": "11:25:02",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-02 11:25:02 Toll DINALUPIHAN-X01 NLEX 88.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "88.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-02 11:33:31 2023-08-02 11:25:02 Toll DINALUPIHAN-X01 NLEX 88.00 0.00",
        //             "Total": "672.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-02",
        //             "Date": "14:04:01",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-02 14:04:01 Toll MABIGA NB-X01 NLEX 93.00 0.00",
        //             "Tollway": "NB-X01",
        //             "Debit": "NLEX",
        //             "Credit": "93.00",
        //             "datas": "2023-08-02 14:07:01 2023-08-02 14:04:01 Toll MABIGA NB-X01 NLEX 93.00 0.00",
        //             "Total": "672.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-02",
        //             "Date": "15:41:57",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-02 15:41:57 Toll TARLAC-X05 NLEX 155.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "155.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-02 15:52:02 2023-08-02 15:41:57 Toll TARLAC-X05 NLEX 155.00 0.00",
        //             "Total": "672.00 0.00"
        //         },
        //         {
        //             "Posted": "Total",
        //             "Date": "0.00",
        //             "Transaction": "",
        //             "Description": "672.00 0.00",
        //             "Tollway": "",
        //             "Debit": "",
        //             "Credit": "",
        //             "datas": "Total : 672.00 0.00",
        //             "Total": "672.00 0.00"
        //         }
        //     ],
        //     "info": {
        //         "driver": "REY BERNAL MANUEL",
        //         "department": "Officer",
        //         "brand": "TOYOTA",
        //         "model": "INNOVA",
        //         "plateno": "WIA561"
        //     }
        // },
        // {
        //     "key": "NCL7397",
        //     "row": [
        //         {
        //             "Posted": "2023-08-09",
        //             "Date": "14:47:20",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-09 14:47:20 Toll BALINTAWAK-N04 NLEX 69.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "69.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-09 15:01:03 2023-08-09 14:47:20 Toll BALINTAWAK-N04 NLEX 69.00 0.00",
        //             "Total": "487.00 0.00"
        //         },
        //         {
        //             "Posted": "2023-08-09",
        //             "Date": "16:17:12",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-09 16:17:12 Toll TARLAC-X04 NLEX 418.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "418.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-09 16:44:53 2023-08-09 16:17:12 Toll TARLAC-X04 NLEX 418.00 0.00",
        //             "Total": "487.00 0.00"
        //         },
        //         {
        //             "Posted": "Total",
        //             "Date": "0.00",
        //             "Transaction": "",
        //             "Description": "487.00 0.00",
        //             "Tollway": "",
        //             "Debit": "",
        //             "Credit": "",
        //             "datas": "Total : 487.00 0.00",
        //             "Total": "487.00 0.00"
        //         }
        //     ],
        //     "info": {
        //         "driver": "FERNANDO VELASCO DELA CRUZ",
        //         "department": "Officer",
        //         "brand": "MERCEDES BENZ",
        //         "model": "GLC",
        //         "plateno": "NCL7397"
        //     }
        // },
        // {
        //     "key": "NBX2206",
        //     "row": [
        //         {
        //             "Posted": "2023-08-06",
        //             "Date": "06:52:46",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-06 06:52:46 Toll BOCAUE BARRIER CS-X07 NLEX 487.00 0.00",
        //             "Tollway": "BARRIER",
        //             "Debit": "CS-X07",
        //             "Credit": "NLEX",
        //             "datas": "2023-08-06 06:58:22 2023-08-06 06:52:46 Toll BOCAUE BARRIER CS-X07 SLEX 487.00 0.00",
        //             "Total": "Finally, please be informed of the cut-off date which varies from one reloading facility to another."
        //         },
        //         {
        //             "Posted": "2023-08-10",
        //             "Date": "08:53:02",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-10 08:53:02 Toll BALINTAWAK-N04 NLEX 69.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "69.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-10 09:01:11 2023-08-10 08:53:02 Toll BALINTAWAK-N04 CAVITEX 69.00 0.00",
        //             "Total": "Finally, please be informed of the cut-off date which varies from one reloading facility to another."
        //         },
        //         {
        //             "Posted": "2023-08-10",
        //             "Date": "11:02:54",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-10 11:02:54 Toll TARLAC-X05 NLEX 418.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "418.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-10 11:25:22 2023-08-10 11:02:54 Toll TARLAC-X05 NLEX 418.00 0.00",
        //             "Total": "Finally, please be informed of the cut-off date which varies from one reloading facility to another."
        //         },
        //         {
        //             "Posted": "2023-08-16",
        //             "Date": "07:52:51",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-16 07:52:51 Toll CLARK SOUTH A-X04 NLEX 162.00 0.00",
        //             "Tollway": "SOUTH",
        //             "Debit": "A-X04",
        //             "Credit": "NLEX",
        //             "datas": "2023-08-16 07:57:42 2023-08-16 07:52:51 Toll CLARK SOUTH A-X04 NLEX 162.00 0.00",
        //             "Total": "Finally, please be informed of the cut-off date which varies from one reloading facility to another."
        //         },
        //         {
        //             "Posted": "2023-08-16",
        //             "Date": "08:42:14",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-16 08:42:14 Toll PULILAN SB-X01 NLEX 155.00 0.00",
        //             "Tollway": "SB-X01",
        //             "Debit": "NLEX",
        //             "Credit": "155.00",
        //             "datas": "2023-08-16 08:46:15 2023-08-16 08:42:14 Toll PULILAN SB-X01 NLEX 155.00 0.00",
        //             "Total": "Finally, please be informed of the cut-off date which varies from one reloading facility to another."
        //         },
        //         {
        //             "Posted": "2023-08-16",
        //             "Date": "12:21:46",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-16 12:21:46 Toll SAN FERNANDO NB-X05 SLEX 111.00 0.00",
        //             "Tollway": "FERNANDO",
        //             "Debit": "NB-X05",
        //             "Credit": "NLEX",
        //             "datas": "2023-08-16 12:30:42 2023-08-16 12:21:46 Toll SAN FERNANDO NB-X05 NLEX 111.00 0.00",
        //             "Total": "Finally, please be informed of the cut-off date which varies from one reloading facility to another."
        //         },
        //         {
        //             "Posted": "2023-08-16",
        //             "Date": "14:32:20",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-16 14:32:20 Toll DAU IC NB-X01 NAIAX 71.00 0.00",
        //             "Tollway": "IC",
        //             "Debit": "NB-X01",
        //             "Credit": "NLEX",
        //             "datas": "2023-08-16 14:40:33 2023-08-16 14:32:20 Toll DAU IC NB-X01 NLEX 71.00 0.00",
        //             "Total": "Finally, please be informed of the cut-off date which varies from one reloading facility to another."
        //         },
        //         {
        //             "Posted": "2023-08-16",
        //             "Date": "16:00:09",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-16 16:00:09 Toll ANGELES-X03 NLEX 8.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "8.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-16 16:06:52 2023-08-16 16:00:09 Toll ANGELES-X03 NLEX 8.00 0.00",
        //             "Total": "Finally, please be informed of the cut-off date which varies from one reloading facility to another."
        //         },
        //         {
        //             "Posted": "2023-08-16",
        //             "Date": "18:12:35",
        //             "Transaction": "Toll",
        //             "Description": "2023-08-16 18:12:35 Toll TARLAC-X05 SKYWAY 185.00 0.00",
        //             "Tollway": "NLEX",
        //             "Debit": "185.00",
        //             "Credit": "0.00",
        //             "datas": "2023-08-16 18:28:12 2023-08-16 18:12:35 Toll TARLAC-X05 NLEX 185.00 0.00",
        //             "Total": "Finally, please be informed of the cut-off date which varies from one reloading facility to another."
        //         },
        //         {
        //             "Posted": "Total",
        //             "Date": "0.00",
        //             "Transaction": "",
        //             "Description": "1,666.00 0.00",
        //             "Tollway": "",
        //             "Debit": "",
        //             "Credit": "",
        //             "datas": "Total : 1,666.00 0.00",
        //             "Total": "Finally, please be informed of the cut-off date which varies from one reloading facility to another."
        //         },
        //         {
        //             "Posted": "This",
        //             "Date": "confidential",
        //             "Transaction": "information",
        //             "Description": "contains confidential information and is intended only for the owner of the Easytrip RFID account stated herein. If",
        //             "Tollway": "is",
        //             "Debit": "intended",
        //             "Credit": "only",
        //             "datas": "This document contains confidential information and is intended only for the owner of the Easytrip RFID account stated herein. If",
        //             "Total": "Finally, please be informed of the cut-off date which varies from one reloading facility to another."
        //         },
        //         {
        //             "Posted": "you",
        //             "Date": "the",
        //             "Transaction": "addressee,",
        //             "Description": "not the addressee, you should not disseminate, distribute or copy the information. Please be informed that this statement of",
        //             "Tollway": "should",
        //             "Debit": "not",
        //             "Credit": "disseminate,",
        //             "datas": "you are not the addressee, you should not disseminate, distribute or copy the information. Please be informed that this statement of",
        //             "Total": "Finally, please be informed of the cut-off date which varies from one reloading facility to another."
        //         },
        //         {
        //             "Posted": "account",
        //             "Date": "posting",
        //             "Transaction": "date",
        //             "Description": "the posting date and NOT the transaction date.",
        //             "Tollway": "NOT",
        //             "Debit": "the",
        //             "Credit": "transaction",
        //             "datas": "account reflects the posting date and NOT the transaction date.",
        //             "Total": "Finally, please be informed of the cut-off date which varies from one reloading facility to another."
        //         },
        //         {
        //             "Posted": "This",
        //             "Date": "not",
        //             "Transaction": "valid",
        //             "Description": "is not valid for claiming input tax.",
        //             "Tollway": "claiming",
        //             "Debit": "input",
        //             "Credit": "tax.",
        //             "datas": "This document is not valid for claiming input tax.",
        //             "Total": "Finally, please be informed of the cut-off date which varies from one reloading facility to another."
        //         },
        //         {
        //             "Posted": "Finally,",
        //             "Date": "informed",
        //             "Transaction": "of",
        //             "Description": "be informed of the cut-off date which varies from one reloading facility to another.",
        //             "Tollway": "cut-off",
        //             "Debit": "date",
        //             "Credit": "which",
        //             "datas": "Finally, please be informed of the cut-off date which varies from one reloading facility to another.",
        //             "Total": "Finally, please be informed of the cut-off date which varies from one reloading facility to another."
        //         }
        //     ],
        //     "info": {
        //         "driver": "RODRIGO JR MAMARIN CABASOG",
        //         "department": "Officer",
        //         "brand": "TOYOTA",
        //         "model": "HI ACE",
        //         "plateno": "NBX2206"
        //     }
        // }
        // ] ';
        
        //$json[1]['additional']['path'];
        $json = $req;
        function compute($data){
            foreach($data as $tt){
                   $total[] = $tt['pay'];
            }
            return array_sum($total);
        }
        $pdfname = $json[1]['additional']['filename'];
        $path = $json[1]['additional']['path'];
        $idkeymap = md5($json);
       
        foreach($json[0]['all']  as $data){
           if(isset($data['info'] )){
             $uid = md5($data['info']['driver'].$data['info']['plateno'].date('Y-m-d H:i:s').$path.$pdfname);
           }
           if(isset($data['row'])){
                $arr = [];
                foreach($data['row'] as $tollway){
                    if (isset($tollway['Posted']) && strtotime($tollway['Posted']) !== false) {
                          $asof[] = $tollway['Posted'];
                          if (preg_match('/\b(\d{1,3}(?:,\d{3})*\.\d{2})\b/', $tollway['Description'], $matches)) {
                            $pay = str_replace(',', '', $matches[1]); 
                          } 
                            $parts = explode(' ', $tollway['Description']);
                            foreach ($parts as $part) {
                                if ($part === "NLEX") {
                                    $nlex = $part;
                                    break; 
                                }
                                if ($part === "CAVITEX") {
                                    $nlex = $part;
                                    break; 
                                }
                                if ($part === "CALAX") {
                                    $nlex = $part;
                                    break; 
                                }
                                if ($part === "SLEX") {
                                    $nlex = $part;
                                    break; 
                                }
                                if ($part === "SKYWAY") {
                                    $nlex = $part;
                                    break; 
                                }
                                if ($part === "NAIAX") {
                                    $nlex = $part;
                                    break; 
                                }
                                if ($part === "STAR") {
                                    $nlex = $part;
                                    break; 
                                }
                                if ($part === "TPLEX") {
                                    $nlex = $part;
                                    break; 
                                }
                                if ($part === "CLLEX") {
                                    $nlex = $part;
                                    break; 
                                }
                            }
                            if (isset($nlex)) {
                                $toll[] =  $nlex;
                            } 
                            $arr[] = ["uid"=> $uid,
                                      "posted"=> $tollway['Posted'], 
                                      "pay"=> $pay,
                                      "tollway"=> $nlex,
                                      "data"=> $tollway['Description'],
                                    ];
                     } 
                }
           }
           if(isset($data['info'])){
           $das[] = ["uid"=> $uid,
                     "map"=> $idkeymap,
                     "driver"=> $data['info']['driver'],
                     "department"=>$data['info']['department'],
                     "brand"=>$data['info']['brand'],
                     "model"=>$data['info']['model'],
                     "plateno"=>$data['info']['plateno'],
                     "total"=>compute($arr),
                     "expressData"=> $arr];  
           }
        }
 
        sort($asof);
        $lowestDate = $asof[0];
        $highestDate = end($asof);
        $newDate = $lowestDate.' -> '.$highestDate;
       
        $checked = ExpressWayUpload::where('uid', $idkeymap)->pluck('uid')->first();
        if(!$checked){
            $execute = new ExpressWayUpload();
            $execute->uid = $idkeymap;
            $execute->pdfname = $pdfname;
            $execute->path = $path;
            $execute->asof = $newDate;
            $execute->save();
            foreach($das as $all){
             
                $driver = new ExpressWayDriver();
                $driver->map = $all['map'];
                $driver->uid = $all['uid'];
                $driver->driver = $all['driver'];
                $driver->department = $all['department'];
                $driver->brand = $all['brand'];
                $driver->model = $all['model'];
                $driver->total = $all['total'];
                $driver->plate = $all['plateno'];
                $driver->save();
                foreach($all['expressData'] as $express){
                    $toll = new ExpressWayToll();
                    $toll->uid = $express['uid'];
                    $toll->posted = $express['posted'];
                    $toll->pay = $express['pay'];
                    $toll->data = $express['data'];
                    $toll->toll1 = $express['tollway'] == 'NLEX'? $express['pay'] : NULL;
                    $toll->toll2 = $express['tollway'] == 'CAVITEX'? $express['pay'] : NULL;
                    $toll->toll3 = $express['tollway'] == 'CALAX'? $express['pay'] : NULL;
                    $toll->toll4 = $express['tollway'] == 'SLEX'? $express['pay'] : NULL;
                    $toll->toll5 = $express['tollway'] == 'SKYWAY'? $express['pay'] : NULL;
                    $toll->toll6 = $express['tollway'] == 'NAIAX'? $express['pay'] : NULL;
                    $toll->toll7 = $express['tollway'] == 'STAR'? $express['pay'] : NULL;
                    $toll->toll8 = $express['tollway'] == 'TPLEX'? $express['pay'] : NULL;
                    $toll->toll9 = $express['tollway'] == 'CLLEX'? $express['pay'] : NULL;
                    $toll->save();
                }
            }
            return 0;
        }else{
            return 1;
        }
        
        
         
    }
}
