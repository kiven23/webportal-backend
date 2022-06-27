<style type="text/css">
	@page {
		size: auto;
		margin: 0mm;
	}
	@media print {
		@page { margin: 0mm; }
		body {
			margin: 1px;
		}
		.print-hidden {
			display: none;
		}
	}
</style>

<div class="print-hidden">
	<h2>2 x 2</h2>

	<a href="{{ route('customer.printimage2', ['id' => $customer->id]) }}">Print Ledger</a> |
	<a href="{{ route('customer.printimage2', ['id' => $customer->id]) }}">Print Application Form</a>

	<hr>
</div>

<img src="{{ $customer->picture }}" style="width: 2in; height: 2in;">
<img src="{{ $customer->picture }}" style="width: 2in; height: 2in;">

@if (\Auth::user()->role !== 24)
	<br>
	<a href="{{ route('customer.basic') }}" class="print-hidden">Take another</a> |
	<a href="{{ route('customer.files', ['customer_id' => $customer->id]) }}" class="print-hidden">Files</a>
@endif
