<!DOCTYPE html>
<html>
<head>
	<title>{{ $letter_title }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style>
        html, body{
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
        }

        .letter {
            margin-left: 1.8rem;
        }

        ol.number-order-with-double-parenthesis,
        ol.alpha-order-with-double-parenthesis
         {
            counter-reset: list;
        }

        ol.number-order-with-double-parenthesis > li,
        ol.alpha-order-with-double-parenthesis > li {
            list-style: none;
            position: relative;
        }

        ol.number-order-with-double-parenthesis > li:before {
            content: "(" counter(list) ") ";
        }

        ol.alpha-order-with-double-parenthesis > li:before {
            content: "(" counter(list, upper-alpha) ") ";
        }

        ol.number-order-with-double-parenthesis > li:before,
        ol.alpha-order-with-double-parenthesis > li:before{
            counter-increment: list;
            position: absolute;
            left: -2.4em;
        }

        .page-break {
            page-break-after: always;
        }
      
    </style>
</head>
<body>
    @foreach($letters as $letter)
        <div class="letter">
            @include("credit_dung_letters.layouts.letter_header")
            <p style="margin-top:2.8em; margin-bottom:2.5em">{{ \Carbon\Carbon::now()->format("F d, Y")}}</p>
            @include("credit_dung_letters.layouts.customer_info")
            <p style="margin-bottom:0px">Greetings!</p>
            <div class="content">
                @include("credit_dung_letters." . $letter_type)
            </div>
            @include("credit_dung_letters.layouts.truly_yours")
            @include("credit_dung_letters.layouts.note")
        </div>
        @if(! $loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>