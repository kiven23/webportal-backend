@if($letter_type == "reminder_letter" || $letter_type == "dunning_letter")
    <p  style="margin-top: 2rem">Sincerely yours, </p>
    <p>Credit & Collection Supervisor</p>
@else
    <p  style="margin-top: 2rem">Very truly yours, </p>
    <p>
        <div style="text-transform: uppercase">{{ $letter["attorney"] }}</div>
        <div>Legal Counsel For</div>
        <div style="text-transform: capitalize">{{ strtolower($letter["branch_company_name"]) }} (Addessa)</div>
    </p>
@endif