<style type="text/css">
	@page {
		size: auto;
		margin: 0mm;
	}
	@media print {
		body {
			margin: 1px;
		}
		.print-hidden {
			display: none;
		}
		.print_item_name {
			text-indent: 10px;
			text-transform: uppercase;
		}
		.print_item {
			position: absolute;
			float: right;
			top: 7mm;
			right: 10mm;
		}
	}
</style>

<div class="print-hidden">
	<h2>Application Form</h2>

	<a href="{{ route('customer.printimage', ['id' => $customer->id]) }}">Print 2 x 2</a> |
	<a href="{{ route('customer.printimage2', ['id' => $customer->id]) }}">Print Ledger</a>

	<hr>
</div>

<img src="{{ $customer->picture }}" style="width: 2in; height: 2in;" class="print_item">

@if (\Auth::user()->role !== 24)
	<br>
	<a href="{{ route('customer.basic') }}" class="print-hidden">Take another</a> |
	<a href="{{ route('customer.files', ['customer_id' => $customer->id]) }}" class="print-hidden">Files</a>
@endif
