

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
        <div class="bold-letter">Expenses For Check Preparation History</div>
        <div style="margin-top: 2px"><span class="bold-letter">BRANCH:</span>  Agoo</div>
        <div style="margin-top: 2px"><span class="bold-letter">AS OF:</span> {{$asof }}</div>
    </div>
 
    
    <div style="margin-top: 20px">
        <div class="bold-letter">EXPENSES FOR CHECK PREPARATION</div>
        <table class="expenses_for_chk_prep_table">
            <tr>
                   <th>PCV DATE</th>
                    <th>PAYEE</th>
                    <th>AMOUNT</th>
                    <th>BIR</th>
                    <th>GL ACCOUNT</th>
                    <th>STATUS</th>
            </tr>
                  @foreach($history as $data)        
                <tr class="list-item">
                    <td> {{$data->pcv_date}} </td>
                    <td> {{$data->payee}}</td>
                    <td> {{$data->amount}}</td>
                    <td> {{$data->tin ==1? 'Yes': 'No'}}</td>
                    <td> {{$data->glaccounts}}</td>
                    <td> {{$data->status}}</td>
                </tr>
                  @endforeach
                  
    </table>
    </div>
 

    
</body>

</html>