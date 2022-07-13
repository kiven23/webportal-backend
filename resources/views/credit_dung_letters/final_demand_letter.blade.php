<div class="final-demand">
    <p>
        Because of your persistent refusal to pay the balance of your account with our client, {{  $letter["branch_company_name"] }}, we
        are constrained to proceed with the REPOSSESSION/SEIZURE  of your Unit/Units.
    </p>
    <p style="line-height: 1.4;">
        REPOSSESSION of your Unit shall either be done with your <span class="bold-with-underline">consent or approval</span> or effected with the sanction of the Court of
        Law, should you refuse to surrender the Unit/Units voluntarily.
    </p>
    <p style="line-height: 1.4;">
        If you <span class="bold-with-underline">VOLUNTARILY AGREE TO SURRENDER</span> your Unit/Units to us, we shall consider your accounts closed and you will be
        <span class="bold-with-underline">completely relieved of your liability/liabilities</span> with our client. Your previous payments will be totally forfeited in favor of
        our client.
    </p>
    <p style="line-height: 1.4;">
        If you <span class="bold-with-underline">REFUSE TO SURRENDER</span> your Unit/Units, Sheriff of other Officer, duly appointed by the Court will undertake or effect
        said seizure by virtue of the Writ of Replevin and you will unavoidably face the following undesirable consequences to wit;
        <ol class="alpha-order-with-double-parenthesis">
            <li>
                DAMAGES, COSTS OF SUIT, REPLEVIN BOND PREMIUM, SHERIFF'S FEES, ATTORNEY'S FEES and OTHER EXPENSES OF
                SEIZURE WILL BE COLLECTED FROM YOU.
            </li>
            <li>
                CONTEMPT PROCEEDINGS WILL BE INSTITUTED AGAINST YOU AND COURT MAY IMPOSE IMPRISONMENT OR OTHER PENALTY AGAINST YOU.
            </li>
            <li>
                EXPOSURE TO PUBLIC EMBARASSMENT AND UNNECESSARY INCONVENIENCES WILL ENSURE THERETO.
            </li>
        </ol>
    </p>
    <p>
        We, therefore, give you three (3) days upon receipt of this letter to settle the outstanding balance of your account to our
        client in the sum of {{ $letter['principal'] }} and its interest charges in the sum of {{ $letter['penalty'] }} or surrender the Unit/Units.
        Otherwise, we will proceed with the contemplated legal actions against you without further reluctance and delay.
    </p>
    <p>THIS IS OUR <span style="text-decoration:underline">FINAL</span> REPOSSESSION DEMAND</p>
</div>


<style>
    .bold-with-underline {
        font-weight:bold; 
        text-decoration:underline;
    }

    .final-demand .alpha-order-with-double-parenthesis {
        margin-top:1.5em;
    }

    .final-demand .alpha-order-with-double-parenthesis > li {
        margin-bottom: 1.5em;
    }
</style>