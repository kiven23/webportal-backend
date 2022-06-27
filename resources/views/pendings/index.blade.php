@extends('layouts.app')

@section('title', 'Pendings')

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
      <li class="active">Pendings</li>
    </ol>
  </section>

  <section class="content">
  	<div class="box">
  		<div class="box-body">
  			<div class="row hidden-print">
					<div class="col-md-8">
						<form class="form" action="{{ route('pending.show') }}" method="post">
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
										<div class="btn-group" style="margin-left: 5px;">
											<a href="{{ route('pending.add_as') }}" class="btn btn-warning">
												<i class="fa fa-plus"></i>&nbsp;Add As
											</a>
											<a href="{{ route('charts.pendings.overall') }}" class="btn btn-danger">
												<i class="fa fa-pie-chart"></i>&nbsp;Chart
											</a>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
					<div class="col-md-4">
						<div class="btn-group pull-right">
							<a href="{{ route('pending.ci') }}" class="btn btn-default"><i class="fa fa-file-o"></i>&nbsp;Show Invoice</a>
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
				<div id="pending-tbl">
					<table class="table table-noborder table-extra-condensed">
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
						        <th class="custom-td noborder text-center">LSU(OR)</th>
						        <th class="custom-td noborder text-center">OR</th>
						        <th class="custom-td noborder text-center">LSU(CI)</th>
						        <th class="custom-td noborder text-center">CI</th>
						        <th class="custom-td noborder text-center">LSU(CH)</th>
						        <th class="custom-td noborder text-center">CH</th>
						        <th class="custom-td noborder text-center">DEP</th>
						        <th class="custom-td noborder text-center">CLA</th>
						        <th class="custom-td noborder text-center">GRPO</th>
						        <th class="custom-td noborder text-center">SI</th>
						        <th class="custom-td noborder text-center">SO</th>
						        <th class="custom-td noborder text-center">STS</th>
						        <th class="custom-td noborder text-center">DISB</th>
						        <th class="custom-td noborder text-center">ARCM</th>
						        <th class="custom-td noborder text-center">APCM</th>
						        <th class="custom-td noborder text-center">INT</th>
						        <th class="custom-td noborder text-center">RC</th>
						        <th class="custom-td noborder text-center">SC</th>
						    </tr>
						</thead>
				    @foreach ($regions as $region)
        			<thead>
          			<tr>
          				<th colspan="19" class="custom-td noborder page-break"><u>{{ strtoupper($region->name) }}</u></th>
          			</tr>
        			</thead>
			        @foreach ($region->branches as $branch)
				        @if ($branch->machine_number <> 103)
				        <tbody class="custom-td noborder">
			                <tr>
			                	<!-- exclude Administrator -->
			                	<th>
			                		@if (in_array($branch->id, $conn_tickets_array) ||
			                				 in_array($branch->id, $power_interruptions_array))
			                			<a href="{{ route('pending.index_as', ['id' => $branch->id]) }}"
			                			 style="color:red;text-decoration:none;">{{ strtoupper($branch->name) }}</a>
			                		@else
			                			{{ strtoupper($branch->name) }}
			                		@endif
			                	</th>
			                </tr>
			                <?php $sum_or = 0; ?>
			                <?php $sum_ci = 0; ?>
			                <?php $sum_ch = 0; ?>
			                <?php $sum_dep = 0; ?>
			                <?php $sum_cla = 0; ?>
			                <?php $sum_grpo = 0; ?>
			                <?php $sum_si = 0; ?>
			                <?php $sum_so = 0; ?>
			                <?php $sum_sts = 0; ?>
			                <?php $sum_disb = 0; ?>
			                <?php $sum_arcm = 0; ?>
			                <?php $sum_apcm = 0; ?>
			                <?php $sum_int = 0; ?>
			                <?php $sum_rc = 0; ?>
			                <?php $sum_sc = 0; ?>
			                <?php $sum_reason = ''; ?>
											@forelse($branch->pendings as $pending)
		                		<?php $sum_or += $pending->por; ?>
		                		<?php $sum_ci += $pending->ci; ?>
		                		<?php $sum_ch += $pending->ch; ?>
		                		<?php $sum_dep += $pending->dep; ?>
		                		<?php $sum_cla += $pending->cla; ?>
		                		<?php $sum_grpo += $pending->grpo; ?>
		                		<?php $sum_si += $pending->si; ?>
		                		<?php $sum_so += $pending->so; ?>
		                		<?php $sum_sts += $pending->sts; ?>
		                		<?php $sum_disb += $pending->disb; ?>
		                		<?php $sum_arcm += $pending->arcm; ?>
		                		<?php $sum_apcm += $pending->apcm; ?>
		                		<?php $sum_int += $pending->pint; ?>
		                		<?php $sum_rc += $pending->rc_cash; ?>
		                		<?php $sum_sc += $pending->sc; ?>
		                		<?php
		                			$sum_reason .= strtoupper($pending->docdate->format('d') . "-".$pending->reason.", ");
		                		?>
		                        <tr>
		                        	<td class="custom-td noborder"></td>
		                            <td class="custom-td noborder-xleft text-right">
		                            	{{ $pending->docdate->format('d') ? $pending->docdate->format('d') : '' }}
		                            </td>
		                            <td class="custom-td text-center">
		                            	{{ $pending->ls_or ? $pending->ls_or : '' }}
		                            </td>
		                            <td class="custom-td text-center">
		                            	{{ $pending->por ? $pending->por : '' }}
		                            </td>
		                            <td class="custom-td text-center">
		                            	{{ $pending->ls_ci ? $pending->ls_ci : '' }}
		                            </td>
		                            <td class="custom-td text-center">
		                            	{{ $pending->ci ? $pending->ci : '' }}
		                            </td>
		                            <td class="custom-td text-center">
		                            	{{ $pending->ls_ch ? $pending->ls_ch : '' }}
		                            </td>
		                            <td class="custom-td text-center">
		                            	{{ $pending->ch ? $pending->ch : '' }}
		                            </td>
		                            <td class="custom-td text-center">
		                            	{{ $pending->dep ? $pending->dep : '' }}
		                            </td>
		                            <td class="custom-td text-center">
		                            	{{ $pending->cla ? $pending->cla : '' }}
		                            </td>
		                            <td class="custom-td text-center">
		                            	{{ $pending->grpo ? $pending->grpo : '' }}
		                            </td>
		                            <td class="custom-td text-center">
		                            	{{ $pending->si ? $pending->si : '' }}
		                            </td>
		                            <td class="custom-td text-center">
		                            	{{ $pending->so ? $pending->so : '' }}
		                            </td>
		                            <td class="custom-td text-center">
		                            	{{ $pending->sts ? $pending->sts : '' }}
		                            </td>
		                            <td class="custom-td text-center">
		                            	{{ $pending->disb ? $pending->disb : '' }}
		                            </td>
		                            <td class="custom-td text-center">
		                            	{{ $pending->arcm ? $pending->arcm : '' }}
		                            </td>
		                            <td class="custom-td text-center">
		                            	{{ $pending->apcm ? $pending->apcm : '' }}
		                            </td>
		                            <td class="custom-td text-center">
		                            	{{ $pending->pint ? $pending->pint : '' }}
		                            </td>
		                            <td class="custom-td text-center">
		                            	{{ $pending->rc_cash ? $pending->rc_cash : '' }}
		                            </td>
		                            <td class="custom-td text-center">
		                            	{{ $pending->sc ? $pending->sc : '' }}
		                            </td>
		                        </tr>
			                @empty
		                		<tr>
		                			<td class="custom-td noborder"></td>
		                			<td class="custom-td noborder-xleft"></td>
		                			<td class="custom-td text-center">-</td>
		                			<td class="custom-td text-center">0</td>
		                			<td class="custom-td text-center">-</td>
		                			<td class="custom-td text-center">0</td>
		                			<td class="custom-td text-center">-</td>
		                			<td class="custom-td text-center">0</td>
		                			<td class="custom-td text-center">0</td>
		                			<td class="custom-td text-center">0</td>
		                			<td class="custom-td text-center">0</td>
		                			<td class="custom-td text-center">0</td>
		                			<td class="custom-td text-center">0</td>
		                			<td class="custom-td text-center">0</td>
		                			<td class="custom-td text-center">0</td>
		                			<td class="custom-td text-center">0</td>
		                			<td class="custom-td text-center">0</td>
		                			<td class="custom-td text-center">0</td>
		                			<td class="custom-td text-center">0</td>
		                			<td class="custom-td text-center">0</td>
		                		</tr>
			                @endforelse
			                <tr>
			                	<td class="custom-td noborder"></td>
			                	<th class="custom-td noborder text-center">TOTAL</th>
			                	<th class="custom-td noborder text-center">-</th>
			                	<th class="custom-td noborder text-center">{{ $sum_or }}</th>
			                	<th class="custom-td noborder text-center">-</th>
			                	<th class="custom-td noborder text-center">{{ $sum_ci }}</th>
			                	<th class="custom-td noborder text-center">-</th>
			                	<th class="custom-td noborder text-center">{{ $sum_ch }}</th>
			                	<th class="custom-td noborder text-center">{{ $sum_dep }}</th>
			                	<th class="custom-td noborder text-center">{{ $sum_cla }}</th>
			                	<th class="custom-td noborder text-center">{{ $sum_grpo }}</th>
			                	<th class="custom-td noborder text-center">{{ $sum_si }}</th>
			                	<th class="custom-td noborder text-center">{{ $sum_so }}</th>
			                	<th class="custom-td noborder text-center">{{ $sum_sts }}</th>
			                	<th class="custom-td noborder text-center">{{ $sum_disb }}</th>
			                	<th class="custom-td noborder text-center">{{ $sum_arcm }}</th>
			                	<th class="custom-td noborder text-center">{{ $sum_apcm }}</th>
			                	<th class="custom-td noborder text-center">{{ $sum_int }}</th>
			                	<th class="custom-td noborder text-center">{{ $sum_rc }}</th>
			                	<th class="custom-td noborder text-center">{{ $sum_sc }}</th>
			                </tr>
			                <tr>
			                	<td class="custom-td noborder"></td>
			                	<th class="custom-td noborder text-center">REASON:</th>
			                	<th colspan="17" class="custom-td noborder text-left nowrap">{{ $sum_reason }}</th>
			                </tr>
			            @endif
			        @endforeach
			        </tbody>
				    @endforeach
				    <tr>
			    		<th class="custom-td noborder-xleft" colspan="2">GRAND TOTAL</th>
			        <th class="custom-td text-center">LSU(OR)</th>
			        <th class="custom-td text-center">OR</th>
			        <th class="custom-td text-center">LSU(CI)</th>
			        <th class="custom-td text-center">CI</th>
			        <th class="custom-td text-center">LSU(CH)</th>
			        <th class="custom-td text-center">CH</th>
			        <th class="custom-td text-center">DEP</th>
			        <th class="custom-td text-center">CLA</th>
			        <th class="custom-td text-center">GRPO</th>
			        <th class="custom-td text-center">SI</th>
			        <th class="custom-td text-center">SO</th>
			        <th class="custom-td text-center">STS</th>
			        <th class="custom-td text-center">DISB</th>
			        <th class="custom-td text-center">ARCM</th>
			        <th class="custom-td text-center">APCM</th>
			        <th class="custom-td text-center">INT</th>
			        <th class="custom-td text-center">RC</th>
			        <th class="custom-td text-center">SC</th>
				    </tr>
				    <tr>
				    	<th></th>
				    	<th></th>
				    	@if (count($gt) > 0)
				    		<th class="custom-td text-center">-</th>
				    		<th class="custom-td text-center">
				    			{{ $gt->first()->por ? $gt->first()->por : '0' }}
				    		</th>
					    	<th class="custom-td text-center">-</th>
					    	<th class="custom-td text-center">
					    		{{ $gt->first()->ci ? $gt->first()->ci : '0' }}
					    	</th>
					    	<th class="custom-td text-center">-</th>
					    	<th class="custom-td text-center">
					    		{{ $gt->first()->ch ? $gt->first()->ch : '0' }}
					    	</th>
					    	<th class="custom-td text-center">
					    		{{ $gt->first()->dep ? $gt->first()->dep : '0' }}
					    	</th>
					    	<th class="custom-td text-center">
					    		{{ $gt->first()->cla ? $gt->first()->cla : '0' }}
					    	</th>
					    	<th class="custom-td text-center">
					    		{{ $gt->first()->grpo ? $gt->first()->grpo : '0' }}
					    	</th>
					    	<th class="custom-td text-center">
					    		{{ $gt->first()->si ? $gt->first()->si : '0' }}
					    	</th>
					    	<th class="custom-td text-center">
					    		{{ $gt->first()->so ? $gt->first()->so : '0' }}
					    	</th>
					    	<th class="custom-td text-center">
					    		{{ $gt->first()->sts ? $gt->first()->sts : '0' }}
					    	</th>
					    	<th class="custom-td text-center">
					    		{{ $gt->first()->disb ? $gt->first()->disb : '0' }}
					    	</th>
					    	<th class="custom-td text-center">
					    		{{ $gt->first()->arcm ? $gt->first()->arcm : '0' }}
					    	</th>
					    	<th class="custom-td text-center">
					    		{{ $gt->first()->apcm ? $gt->first()->apcm : '0' }}
					    	</th>
					    	<th class="custom-td text-center">
					    		{{ $gt->first()->pint ? $gt->first()->pint : '0' }}
					    	</th>
					    	<th class="custom-td text-center">
					    		{{ $gt->first()->rc_cash ? $gt->first()->rc_cash : '0' }}
					    	</th>
					    	<th class="custom-td text-center">
					    		{{ $gt->first()->sc ? $gt->first()->sc : '0' }}
					    	</th>
					    @else
					    	<th class="custom-td text-center">-</th>
					    	<th class="custom-td text-center">0</th>
					    	<th class="custom-td text-center">-</th>
					    	<th class="custom-td text-center">0</th>
					    	<th class="custom-td text-center">-</th>
					    	<th class="custom-td text-center">0</th>
					    	<th class="custom-td text-center">0</th>
					    	<th class="custom-td text-center">0</th>
					    	<th class="custom-td text-center">0</th>
					    	<th class="custom-td text-center">0</th>
					    	<th class="custom-td text-center">0</th>
					    	<th class="custom-td text-center">0</th>
					    	<th class="custom-td text-center">0</th>
					    	<th class="custom-td text-center">0</th>
					    	<th class="custom-td text-center">0</th>
					    	<th class="custom-td text-center">0</th>
					    	<th class="custom-td text-center">0</th>
					    	<th class="custom-td text-center">0</th>
				    	@endif
				    </tr>
					</table>
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
