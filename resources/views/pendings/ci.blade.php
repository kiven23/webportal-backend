@extends('layouts.app')

@section('title', 'Pending Invoices')

@section('content')
<style type="text/css">
	@media print {
		html, body {
			margin: 0;
			padding: 0;
			overflow-y: scroll;
		}

		.hidden-print {
			display: none;
		}

		@font-face {
		  font-family: 'Roboto';
		  font-style: normal;
		  font-weight: 300;
		  src: url('../../fonts/Roboto/roboto-v15-latin-regular.eot'); /* IE9 Compat Modes */
		  src: local('Roboto'), local('Roboto-Regular'),
		       url('../../fonts/Roboto/roboto-v15-latin-regular.eot?#iefix') format('embedded-opentype'), /* IE6-IE8 */
		       url('../../fonts/Roboto/roboto-v15-latin-regular.woff2') format('woff2'), /* Super Modern Browsers */
		       url('../../fonts/Roboto/roboto-v15-latin-regular.woff') format('woff'), /* Modern Browsers */
		       url('../../fonts/Roboto/roboto-v15-latin-regular.ttf') format('truetype'), /* Safari, Android, iOS */
		       url('../../fonts/Roboto/roboto-v15-latin-regular.svg#Roboto') format('svg'); /* Legacy iOS */
		}

		.table-actions {
		  margin-left: 10px;
		}

		/*
		 * styles for pending tables
		 */
		.table-noborder > thead > tr > th,
		.table-noborder > tbody > tr > th,
		.table-noborder > tfoot > tr > th,
		.table-noborder > thead > tr > td,
		.table-noborder > tbody > tr > td,
		.table-noborder > tfoot > tr > td {
		  padding: 0px;
		  line-height: 1.42857143;
		  vertical-align: top;
		  border-top: 0px solid #ddd;
		}

		.container {
			width: 100% !important;
		}

		/*table { page-break-inside:auto }*/

		/*Uncomment this if you want to pagebreak every TR tag*/
	    tr { page-break-inside: always; page-break-before: auto }
	    thead, tbody { page-break-inside: avoid; page-break-before: auto; } /*Page Break every Header*/

	    thead { display:table-header-group; } /*Print Header Every Page*/
	    tbody { display:table-header-group; } /*Print Header Every Page*/
	    tfoot { display:table-footer-group; } /*Print Footer Every Page*/
	}

	.table-extra-condensed > thead > tr > th,
	.table-extra-condensed > tbody > tr > th,
	.table-extra-condensed > tfoot > tr > th,
	.table-extra-condensed > thead > tr > td,
	.table-extra-condensed > tbody > tr > td,
	.table-extra-condensed > tfoot > tr > td {
	  padding: 0px;
	}

	th, td {
		text-align: left;
	}
	.modal {
		text-align: left;
	}
	#date-td {
		border-top: 1px solid transparent;
		border-bottom: 1px solid transparent;
		border-left: 1px solid transparent;
	}
	.custom-td {
		border-top: 1px solid #222 !important;
		border: 1px solid #222;
		font-size: 11px;
	}
	.custom-td.noborder {
		border-top: 1px solid transparent !important;
		border: 1px solid transparent;
	}
	.custom-td.noborder-xleft {
		border-top: 1px solid transparent !important;
		border-right: 1px solid #222 !important;
		border: 1px solid transparent;
	}
	.nowrap {
		whitespace: nowrap;
		width: 500px;
	}
</style>
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Pendings
      <small>Manage pending monitoring</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('pendings') }}">Pendings</a></li>
		  <li class="active">Invoices</li>
    </ol>
  </section>

  <section class="content">
  	<div class="box">
  		<div class="box-body">
  			<div class="row hidden-print">
					<div class="col-md-6">
						<form class="form" action="{{ route('pending.show_ci') }}" method="post">
							{{ csrf_field() }}
							<div class="form-group">
								<div class="row">
									<div class="col-md-12">
										<div class="columns columns-right pull-left">
											<input type="text" id="filter-date" name="filterdate" class="form-control">
											<script type="text/javascript">
						            $(function () {
						            	$('#filter-date').datetimepicker({
														timepicker:false,
						            		format:'Y-m-d'
						            	});
						            });
							        </script>
										</div>
										<div class="pull-left search">
							        <button class="btn btn-primary">
							        	<i class="fa fa-filter"></i>
							        	Filter
							        </button>
										</div>
										<a href="{{ route('charts.pendings.overall') }}" class="btn btn-danger" style="margin: 0 0 0 5px;">
											<i class="fa fa-pie-chart"></i>&nbsp;Chart
										</a>
									</div>
								</div>
							</div>
						</form>
					</div>
					<div class="col-md-6">
						<div class="btn-group pull-right">
							<a href="{{ route('pendings') }}" class="btn btn-default"><i class="fa fa-file-o"></i>&nbsp;Show All</a>
							<a href="javascript:print();" class="btn btn-default"><i class="fa fa-print"></i>&nbsp;Print</a>
						</div>
					</div>
				</div>

				<strong>
					<em>Users can send pending until
						<span class="text-danger">{{ \Carbon\Carbon::parse($grace_period)->format('g:i a') }}</span>
					</em>
				</strong>

				<p></p><br>
				<table id="pending-tbl" class="table table-noborder table-extra-condensed">
					<thead>
						<tr>
							<td class="custom-td noborder" colspan="19"><strong>ADDESSA CORPORATION</strong></td>
						</tr>
						<tr>
							<td class="custom-td noborder" colspan="19"><strong>PENDING TRANSACTION MONITORING</strong></td>
						</tr>
						<tr>
							<td class="custom-td noborder" colspan="19"><strong>AS OF
								{{ $filterdate2 or Carbon\Carbon::now()->format('F d, Y') }}
							</strong></td>
						</tr>
						<tr><td><br></td></tr>
				    <tr>
				    	<th class="custom-td noborder text-center"></th>
				        <th class="custom-td noborder text-center"></th>
				        <th class="custom-td noborder text-center" style="width: 100px;">CI</th>
				        <th class="custom-td noborder text-center" style="width: 100px;">CH</th>
				        <th class="custom-td noborder text-center" style="width: 675px;"></th>
				    </tr>
					</thead>
			    @foreach ($regions as $region)
      			<thead>
        			<tr>
        				<th colspan="19" class="custom-td noborder page-break"><u>{{ strtoupper($region->name) }}</u></th>
        			</tr>
      			</thead>
		        @foreach ($region->branches as $branch)
			        @if ($branch->machinenum <> 103)
			        <tbody class="custom-td noborder">
                <tr>
                	<!-- exclude Administrator -->
              		<th>
              			@if (in_array($branch->id, $conn_tickets_array) ||
              				 in_array($branch->id, $power_interruptions_array))
                			<a href="{{ route('pending.index_as', ['id' => $branch->id]) }}"
                			 style="color:grey;text-decoration:none;">{{ strtoupper($branch->name) }}</a>
                		@else
                			{{ strtoupper($branch->name) }}
                		@endif
              		</th>
                </tr>
                <?php $sum_ci = 0; ?>
                <?php $sum_ch = 0; ?>
                <?php $sum_reason = ''; ?>
                @forelse($branch->pendings as $pending)
              		<?php $sum_ci += $pending->ci; ?>
              		<?php $sum_ch += $pending->ch; ?>
              		<?php
              			$sum_reason .= strtoupper($pending->docdate->format('d') . "-".$pending->reason.", ");
              		?>
                  <tr>
                  	<td class="custom-td noborder"></td>
                      <td class="custom-td noborder-xleft text-right">
                      	{{ $pending->docdate->format('d') ? $pending->docdate->format('d') : '' }}
                      </td>
                      <td class="custom-td text-center">
                      	{{ $pending->ci ? $pending->ci : '' }}
                      </td>
                      <td class="custom-td text-center">
                      	{{ $pending->ch ? $pending->ch : '' }}
                      </td>
                  </tr>
                @empty
              		<tr>
              			<td class="custom-td noborder"></td>
              			<td class="custom-td noborder-xleft"></td>
              			<td class="custom-td text-center">0</td>
              			<td class="custom-td text-center">0</td>
              		</tr>
                @endforelse
	                <tr>
	                	<td class="custom-td noborder"></td>
	                	<th class="custom-td noborder text-center">TOTAL</th>
	                	<th class="custom-td noborder text-center">{{ $sum_ci }}</th>
	                	<th class="custom-td noborder text-center">{{ $sum_ch }}</th>
	                </tr>
	                <tr>
	                	<td class="custom-td noborder"></td>
	                	<th class="custom-td noborder text-center">REASON</th>
	                	<th colspan="17" class="custom-td noborder text-left">{{ $sum_reason }}</th>
	                </tr>
	            @endif
		        @endforeach
		        </tbody>
			    @endforeach
			    <tr>
			    	<th>Grand Total</th>
			    	<th></th>
			    	@if (count($gt) > 0)
				    	<th class="custom-td noborder text-center">
				    		{{ $gt->first()->ci ? $gt->first()->ci : '0' }}
				    	</th>
				    	<th class="custom-td noborder text-center">
				    		{{ $gt->first()->ch ? $gt->first()->ch : '0' }}
				    	</th>
				    @else
				    	<th class="custom-td noborder text-center">0</th>
				    	<th class="custom-td noborder text-center">0</th>
			    	@endif
			    </tr>
				</table>
			</div>
		</div>
  		</div>
  	</div>
  </section>
 </div>
@stop

@push ('scripts')
<!-- Script for printing report in new window tab -->
<script>
  function print() {
			var css = '{{ asset("css/pending.css") }}';
      var divText = document.getElementById("pending-tbl").outerHTML;
      var myWindow = window.open('', '', 'width=500,height=500');
      var doc = myWindow.document;
      doc.open();
			doc.write('<html><head><title>Pending Transaction Monitoring</title><link rel="stylesheet" type="text/css" href="'+css+'"></head><body>');
			doc.write(divText);
      doc.write('</body></html>');
      doc.close();
  }
</script>
@endpush
