<!DOCTYPE html>
<html>

<head>
    <title>Summary of Revolving Fund</title>
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
            <div class="bold-letter" style="float:left">
                ADDESSA CORPORATION
            </div>
            <div style="float:right">
                <span class="bold-letter">DATE SUBMITTED:</span> {{ $submitted_date }}
            </div>
        </div>
        <div class="bold-letter">SUMMARY OF REVOLVING FUND</div>
        <div style="margin-top: 2px"><span class="bold-letter">BRANCH:</span> {{ strtoupper($branch) }}</div>
        <div style="margin-top: 2px"><span class="bold-letter">AS OF:</span> {{ date_format(date_create($as_of), "M. d, Y") }}</div>
    </div>
    <div style="margin-top: 15px">
        <table class="rv_fund">
            <tr>
                <td>REVOLVING FUND:</td>
                <td style="text-align: right; width:118px">{{ number_format($fund, 2) }}</td>
                <td style="width: 130px;"></td>
            </tr>
            <tr>
                <td>CASH ADVANCES: Marketing Activities:</td>
                <td style="text-align: right">{{ number_format($cash_advances, 2) }}</td>
                <td style="text-align: center">{{ number_format(($fund + $cash_advances), 2) }}</td>
            </tr>
        </table>
    </div>
    <div style="margin-top: 15px">
        <div class="bold-letter">CHECK VOUCHER VERIFICATION</div>
        <table>
            <tr>
                <th style="width:85px;">Date Transmitted</th>
                <th style="width: 150px;">CK#</th>
                <th style="width: 70px;">Status</th>
                <th style="width: 135px;">Amount</th>
                <th></th>
            </tr>
            @forelse ($check_voucher_verifications as $chk_voucher_veri)
            <tr>
                <td>{{ $chk_voucher_veri['date_transmitted'] }} </td>
                <td>{{ $chk_voucher_veri['ck_no'] }} </td>
                <td>{{ $chk_voucher_veri['status']}} </td>
                <td>{{ number_format($chk_voucher_veri['amount'], 2) }} </td>
                <td style="text-align: right">
                    @if($loop->last)
                    {{ number_format($check_voucher_verifications_total, 2) }}
                    @endif
                </td>
                <td style="width: 130px;"></td>
            </tr>
            @empty
            <tr>
                <td style="font-size:10px;" colspan="5">No Record Found.</td>
            </tr>
            @endforelse
        </table>
    </div>
    <div style="margin-top: 20px">
        <div class="bold-letter">CHECK VOUCHER FOR TRANSMITTAL</div>
        <table>
            <tr>
                <th style="width:85px;">Check Voucher Date</th>
                <th style="width: 222px;">CK#</th>
                <th style="width: 135px;">Amount</th>
                <th></th>
            </tr>
            @forelse ($check_voucher_for_transmittals as $chk_voucher_for_trans)
            <tr>
                <td>{{ $chk_voucher_for_trans['check_voucher_date'] }} </td>
                <td>{{ $chk_voucher_for_trans['ck_no'] }} </td>
                <td>{{ number_format($chk_voucher_for_trans['amount'], 2) }} </td>
                <td style="text-align: right">
                    @if($loop->last)
                    {{ number_format($check_voucher_for_transmittals_total, 2) }}
                    @endif
                </td>
                <td style="width: 130px;"></td>
            </tr>
            @empty
            <tr>
                <td style="font-size:10px;" colspan="4">No Record Found.</td>
            </tr>
            @endforelse
        </table>
    </div>
    <div style="margin-top: 20px">
        <div class="bold-letter">EXPENSES FOR CHECK PREPARATION</div>
        <table class="expenses_for_chk_prep_table">
            <tr>
                <th style="width:85px;">PCV Date</th>
                <th style="width: 222px;">Particulars</th>
                <th style="width: 135px;">Amount</th>
                <th></th>
                <th></th>
            </tr>
            @forelse ($expenses_for_check_preparations as $expenses_for_chk_prep)
            <tr>
                <td>{{ $expenses_for_chk_prep['pcv_date'] }} </td>
                <td>{{ $expenses_for_chk_prep['particulars'] }} </td>
                <td>{{ number_format($expenses_for_chk_prep['amount'], 2) }} </td>
                <td style="text-align:right">
                    @if($loop->last)
                    {{ number_format($expenses_for_check_preparations_total, 2) }}
                    @endif
                </td>
                <td style="width: 130px;"> @if($loop->last) {{ number_format(($check_voucher_verifications_total + $check_voucher_for_transmittals_total + $expenses_for_check_preparations_total), 2)}} @endif</td>
            </tr>
            @empty
            <tr>
                <td style="font-size:10px; padding-bottom: 10px" colspan="5">No Record Found.</td>
            </tr>
            @endforelse
        </table>
    </div>
    <div class="bold-letter">
        <table>
            <tr>
                <td style="text-align: left; padding-top: 12px;">AVAILABLE REVOLVING FUND ON HAND</td>
                <td style="width:130px; padding-top: 12px; border-bottom: double thick black">{{ number_format($avail_fund_on_hand, 2) }}</td>
            </tr>
        </table>
    </div>
</body>

</html>