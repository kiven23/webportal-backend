@extends('layouts.app')

@section('title', 'Inventory Reconciliation Discrepancy')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Reconciliation
      <small>Manage inventory reconciliation</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('inventories') }}">Inventory Recon</a></li>
      <li class="active">Discrepancy</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              {{ $branch->name }} discrepancy
            </h3>
            <div class="box-toolbar pull-right">
							<button onclick="generate()" class="btn btn-primary btn-xs">
								<i class="fa fa-file-pdf-o"></i>&nbsp;GENERATE PDF
							</button>
						</div>
          </div>
          <div class="box-body">
          	<table class="table table-bordered" id="invty-recon-table">
							<thead>
								<tr>
									<th>BRAND</th>
									<th>MODEL</th>
									<th>SAP</th>
									<th>BR</th>
									<th>DIFF</th>
									<th>SAP</th>
									<th>BRANCH</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($inventory_maps as $inventory_map)
									@if ($inventory_map['serial'] !== $inventory_map['serial_branch'] ||
												$inventory_map['quantity'] !== $inventory_map['quantity_branch'])
										<tr>
											<td><pre>{{ $inventory_map['brand'] }}</pre></td>
											<td><pre>{{ $inventory_map['model'] }}</pre></td>

											<td>
												<pre class="text-success">{{ $inventory_map['quantity'] !== null ? $inventory_map['quantity'] : 0 }}</pre>
											</td>
											<td>
												<pre class="text-danger">{{ $inventory_map['quantity_branch'] !== null ? $inventory_map['quantity_branch'] : 0 }}</pre>
											</td>
											<td>
												<pre class="{{ $inventory_map['quantity'] - $inventory_map['quantity_branch'] < 0 ? 'text-danger' : 'text-success' }}">{{ $inventory_map['quantity'] - $inventory_map['quantity_branch'] }}</pre>
											</td>

											<td>
												<?php

													$serials = explode(',', $inventory_map['serial']);
													$serials_branch = explode(',', $inventory_map['serial_branch']);
													$serials = array_diff($serials, $serials_branch);
													$numItems = count($serials);
													$i = 0;
													foreach ($serials as $key => $serial) {
														echo '<span class="text-success">' . $serial;
														if(++$i !== $numItems) {
													    echo ',</span><br>';
													  } else {
													  	echo '</span><br>';
													  }
													}

												?>
											</td>
											<td>
												<?php

													$serials = explode(',', $inventory_map['serial']);
													$serials_branch = explode(',', $inventory_map['serial_branch']);
													$serials_branch = array_diff($serials_branch, $serials);
													$numItems = count($serials_branch);
													$i = 0;
													foreach ($serials_branch as $key => $serial_branch) {
														echo '<span class="text-danger">'. $serial_branch;
														if(++$i !== $numItems) {
													    echo ',</span><br>';
													  } else {
													  	echo '</span><br>';
													  }
													}

												?>
											</td>
										</tr>
									@endif
								@endforeach
							</tbody>
						</table>
          </div>
				</div>
			</div>
		</div>
	</section>
</div>
@stop

@push('scripts')
<script>
  $(function () {
    $('#invty-recon-table').DataTable({
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : false,
      'info'        : true,
      'autoWidth'   : false,
      'scrollX'     : true,
      'aLengthMenu': [
          [5, 10, 25, 50, 100, 200, -1],
          [5, 10, 25, 50, 100, 200, "All"]
      ],
    })
  })
</script>

<script>
    function generate() {
        var doc = new jsPDF('p','px','letter');

        var d = new Date();
        var months = ['January',
        							'February',
        							'March',
        							'April',
        							'May',
        							'June',
        							'July',
        							'August',
        							'September',
        							'October',
        							'November',
        							'December'];
        var thisMonth = months[d.getMonth()];
        var lastMonth = months[d.getMonth() - 1];
				var gMonth = d.getMonth() + 1;
				var gDate = d.getDate();
				var gFYear = d.getFullYear();

        var header = 'ADDESSA CORPORATION';
        var invtymemo = 'INVTY MEMO#';
        var date = 'Date:';
        var to = 'To:';
        var from = 'From:';

        var invtymemo_value = '{{ $branch->whscode }}-' + gFYear + '-' + gMonth;
        var date_value = thisMonth + ' ' + gDate + ',' + gFYear;
        var to_value = '{{ $branch->bm_oic ? strtoupper($branch->bm_oic) : 'NONE' }}';
        var to_position = 'BM/OIC';
        var from_value = 'Admin-Inventory Department';

        var beneath_table = 'Please verify, reconcile and coordinate to admin for the reconciliation of the discrepancies within Two (2) days upon the receipt of this Memo.';
        var beneath_from = 'Physical Inventory Report for the month of';
        var beneath_from_value = lastMonth + ' ' + gFYear;
        var date_submitted = 'Date Submitted:';
        var date_submitted_value = '{{ $upload_date }}';

        var before_table = 'We have reconciled your Physical Inventory Count Report versus SAP Report and we found out the following unreconciled items:';


		    doc.setFontSize(7);
		    doc.text(invtymemo_value, 80, 30);

		    doc.setFontSize(7);
		    doc.text(date_value, 80, 40);

		    doc.setFontSize(7);
		    doc.text(to_value, 80, 55);
		    doc.text(to_position, 80, 60);

		    doc.setFontSize(7);
		    doc.text(from_value, 80, 75);

		    doc.setFontSize(7);
		    doc.text(before_table, 80, 118);

		    doc.setFontSize(8);
		    doc.setFontType('bold');
		    doc.text(header, 200, 16);

		    doc.setFontSize(7);
		    doc.setFontType('bold');
		    doc.text(invtymemo, 30, 30);

		    doc.setFontSize(7);
		    doc.setFontType('bold');
		    doc.text(date, 30, 40);

		    doc.setFontSize(7);
		    doc.setFontType('bold');
		    doc.text(to, 30, 55);

		    doc.setFontSize(7);
		    doc.setFontType('bold');
		    doc.text(from, 30, 75);

		    doc.line(30, 80, 425, 80); // horizontal line

		    doc.setFontSize(7);
		    doc.setFontType('bold');
		    doc.text(beneath_from, 30, 88);

		    doc.setFontSize(7);
		    doc.setFontType('bold');
		    doc.text(beneath_from_value, 380, 88);

		    doc.setFontSize(7);
		    doc.setFontType('bold');
		    doc.text(date_submitted, 30, 97);

		    doc.setFontSize(7);
		    doc.setFontType('bold');
		    doc.text(date_submitted_value, 380, 97);

		    var elem = document.getElementById("invty-recon-table");

		    // var tbl = $('#invty-recon-table').clone();
		    // tbl.find('thead tr:nth-child(1)').remove();
		    // var res = doc.autoTableHtmlToJson(tbl.get(0));

		    var res = doc.autoTableHtmlToJson(elem);

		    doc.autoTable(res.columns, res.data,
		    							{
		    								startY: 130,
		    								theme: 'grid',
		    								styles: {
		    									overflow: 'linebreak',
		    									fontSize: 7,
		    									fillColor: [255, 255, 255],
		    									textColor: [0, 0, 0],
		    									lineColor: [0, 0, 0],
		    									lineWidth: .1,
		    								}
		    							},
		    							);

		    doc.setFontSize(7);
		    doc.setFontType('normal');
		    doc.text(beneath_table, 80, doc.autoTableEndPosY() + 15);

		    var prepared_by = 'Prepared by:';
		    var prepared_by_value = '{{ strtoupper(\Auth::user()->first_name) }} {{ strtoupper(\Auth::user()->last_name) }}';
		    var prepared_by_position = '{{ \Auth::user()->position }} ';

		    var verified_by = 'Verified by:';
		    var verified_by_value = 'GERALD SUNIGA';
		    var verified_by_position = 'Inventory Section Head';
		    var verified_by_value_2 = 'MARIEL QUITALEG';
		    var verified_by_position_2 = 'Inventory & Purchasing Manager';

		    var noted_by = 'Noted by:';
		    var noted_by_value = 'DORIE P. MONES';
		    var noted_by_position = 'General Manager';
		    var noted_by_value_2 = 'MS. SONIA DELA CRUZ';
		    var noted_by_position_2 = 'Vice President';

		    // PREPARED BY
		    doc.setFontSize(7);
		    doc.setFontType('normal');
		    doc.text(prepared_by, 30, doc.autoTableEndPosY() + 40);

		    doc.setFontType('bold');
		    doc.text(prepared_by_value, 80, doc.autoTableEndPosY() + 40);

		    doc.setFontType('normal');
		    doc.text(prepared_by_position, 80, doc.autoTableEndPosY() + 45);

		    // VERIFIED BY
		    doc.setFontSize(7);
		    doc.setFontType('normal');
		    doc.text(verified_by, 30, doc.autoTableEndPosY() + 60);

		    doc.setFontType('bold');
		    doc.text(verified_by_value, 80, doc.autoTableEndPosY() + 60);

		    doc.setFontType('normal');
		    doc.text(verified_by_position, 80, doc.autoTableEndPosY() + 65);

		    doc.setFontType('bold');
		    doc.text(verified_by_value_2, 180, doc.autoTableEndPosY() + 60);

		    doc.setFontType('normal');
		    doc.text(verified_by_position_2, 180, doc.autoTableEndPosY() + 65);

		    // VERIFIED BY
		    doc.setFontSize(7);
		    doc.setFontType('normal');
		    doc.text(noted_by, 30, doc.autoTableEndPosY() + 80);

		    doc.setFontType('bold');
		    doc.text(noted_by_value, 80, doc.autoTableEndPosY() + 80);

		    doc.setFontType('normal');
		    doc.text(noted_by_position, 80, doc.autoTableEndPosY() + 85);

		    doc.setFontType('bold');
		    doc.text(noted_by_value_2, 180, doc.autoTableEndPosY() + 80);

		    doc.setFontType('normal');
		    doc.text(noted_by_position_2, 180, doc.autoTableEndPosY() + 85);

		    doc.output("dataurlnewwindow");


    }
</script>
@endpush
