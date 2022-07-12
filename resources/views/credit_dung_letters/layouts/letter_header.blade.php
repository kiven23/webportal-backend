<style>
    .header > .img_logo {
        width: 100%;
        height: 80px;
        display:inline-block;
    }

    .header > div {
        text-transform: uppercase;
        font-weight: bold;
        text-align:center;
    }
</style>

<div class="header">
    @if($letter_type == "reminder_letter" || $letter_type == "dunning_letter")
        <img class="img_logo" src="{{ asset('images/addessa_letterhead.jpg') }}" alt="addesa_logo" />
    @else
        <div>{{ $letter["attorney"] }}</div>
        <div>ATTORNEY-AT-LAW</div>
        <div>{{ $letter["branch_company_name"] }}</div>
        <div>{{ $letter["branch_company_address"] }}</div> 
    @endif
</div>

