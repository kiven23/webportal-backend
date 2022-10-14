<!DOCTYPE html>
<html>

<head>
    <title>Available Revolving Fund On Hand Reports</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style>
        html,
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
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
            font-size: 14px;
        }

        table tr th {
            text-align: left;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div>
        <div style="text-align: right; font-size: 15px">
            Date: {{ date("M. d, Y") }}
        </div>
        <div class="bold-letter" style="font-size: 20px">
            Available Revolving Funds On Hand
        </div>
        <div style="margin-top: 10px">
            <table>
                <tr>
                    <th>BRANCH</th>
                    <th>REVOLVING FUND</th>
                    <th>CASH ADVANCES</th>
                    <th>AVAILABLE REVOLVING FUND ON HAND</th>
                </tr>
                @foreach($items as $item)
                <tr class="list-item">
                    <td>{{ $item["branch"] }}</td>
                    <td>{{ "P" . number_format($item["revolving_fund"], 2) }}</td>
                    <td>{{ "P" . number_format($item["cash_advances"], 2) }}</td>
                    <td>{{ "P" . number_format($item["avail_fund_on_hand"], 2) }}</td>
                </tr>
                @endforeach
                <tr class="bold-letter" style="border-top: solid thin black; font-size:16px">
                    <td style="padding-top:5px">Total</td>
                    <td style="padding-top:5px">{{ "P" . number_format($rf_total, 2) }}</td>
                    <td style="padding-top:5px">{{ "P" . number_format($ca_total, 2) }}</td>
                    <td style="padding-top:5px">{{ "P" . number_format($avail_rf_total, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>