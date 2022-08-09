<div class="customer_info">
    <p>{{ $letter["name"] }}</p>
    <p>{{ $letter["address"] }}</p>
    <p>{{ $letter["province"] }}</p>
</div>
@if($letter_type != "reminder_letter" && $letter_type != "dunning_letter" && $letter_type != "first_demand_letter")
    <p style="font-weight: bold">Subject: {{ ($letter_type == "final_demand_letter"? "FINAL" : "") . " REPOSSESION DEMAND" }} </p>
@endif
<p style="padding-bottom:0;margin-bottom:0">Dear Mr/Ms. {{  $letter["last_name"] }},</p>
<style>
    .customer_info > p{
        margin:0;
        padding:0;
    }
</style>