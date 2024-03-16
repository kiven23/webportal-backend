

<!DOCTYPE html>
<html>

<head>
    <title>HISTORY</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <style>
        html,
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
        }

        .letter {
            margin-left: 1.8rem;
        }

        .page-break {
            page-break-after: always;
        }

        .bold-letter {
            font-weight: bold;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table tr th {
            font-weight: normal;
            font-size: 11px;
            vertical-align: middle;
            padding-top: 10px;
            padding-bottom: 10px;
        }

        table tr td {
            text-align: center;
            /* border: solid thin black */
        }

        table.rv_fund tr td {
            text-align: left;
        }

        /* table.rv_fund tr:last-child td, */
        table.expenses_for_chk_prep_table tr:last-child td {
            border-bottom: solid thin black;
        }
    </style>
</head>

<body>
    <div>
        <div class="clearfix" style="width:100%">
 
            <div style="float:right">
                <span class="bold-letter">DATE:</span> {{ date("M. d, Y") }}
            </div>
        </div>
        <div class="bold-letter">TRANSMITTAL CONTROL FORM</div>
        <div style="margin-top: 2px"><span class="bold-letter">BRANCH:</span> {{$branch}} </div>
        </div>
    </div>
 
    
    <div style="margin-top: 20px">
        <div class="bold-letter">TRANSMITTED CK #</div>
        <table class="expenses_for_chk_prep_table">
            <tr>
                   <th>DATE_CONTROLLED</th>
                    <th>CK#</th>
                    <th>STATUS</th>
                    <th>AMOUNT</th>
                    <th>RECEIVED BY</th>
            </tr>
                  @foreach($data as $d)
                <tr class="list-item">
                    <td>{{$d->date_transmitted}}</td>
                    <td>{{$d->ck_no}}</td>
                    <td>{{$d->status}}</td>
                    <td>{{$d->amount}}</td>
                    <td> </td>
                </tr>
                 @endforeach
                  
    </table>
    </div>
 

    
</body>

</html>