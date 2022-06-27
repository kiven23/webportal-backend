@extends('layouts.app')

@section('title', 'Pending Chart Breakdown')

@section('content')
<style type="text/css">
	th, td {
		text-align: center;
	}
	.modal {
		text-align: left;
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
      @if (\Auth::user()->branch->id != $branch_id)
				<li><a href="{{ route('pending.index_as', ['id' => $branch_id]) }}">{{ $branch_name }}</a></li>
			@endif
			<li><a href="{{ route('pending.branch.chart', ['id' => $branch_id]) }}">Charts</a></li>
		  <li class="active">Breakdown</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">
              Pendings from Breakdown <em>({{ $display_date }})</em>
            </h3>
            <div class="box-toolbar pull-right">
							<a href="{{ route('pending.add_all', ['id' => $branch_id, 'date' => $date]) }}" class="btn btn-primary btn-xs">
								<i class="fa fa-plus"></i> Add all
							</a>
            	@if (\Auth::user()->branch->id != $branch_id)
								<a href="{{ route('pending.index_as', ['id' => $branch_id]) }}" class="btn btn-primary btn-xs">Go to Current Pending</a>
							@else
								<a href="{{ route('pendings') }}" class="btn btn-primary btn-xs">Go to My Current Pending</a>
							@endif
            </div>
          </div>
          <div class="box-body">
          	@include ('errors.list')
						@include ('successes.list')

						<div class="table-responsive">
							<table class="table table-condensed">
								<thead>
									<tr>
										<th></th>
										<th>Date</th>
										<th class="text-danger">LSU(OR)</th>
										<th>OR</th>
										<th class="text-danger">LSU(CI)</th>
										<th>CI</th>
										<th class="text-danger">LSU(CH)</th>
										<th>CH</th>
										<th>DEP</th>
										<th>CLA</th>
										<th>GRPO</th>
										<th>SI</th>
										<th>SO</th>
										<th>STS</th>
										<th>DISB</th>
										<th>ARCM</th>
										<th>APCM</th>
										<th>INT</th>
										<th>RC</th>
										<th>SC</th>
									</tr>
								</thead>
								<tbody>
									<?php $sums_or = 0 ?>
									<?php $sums_ci = 0 ?>
									<?php $sums_ch = 0 ?>
									<?php $sums_dep = 0 ?>
									<?php $sums_cla = 0 ?>
									<?php $sums_grpo = 0 ?>
									<?php $sums_si = 0 ?>
									<?php $sums_so = 0 ?>
									<?php $sums_sts = 0 ?>
									<?php $sums_disb = 0 ?>
									<?php $sums_arcm = 0 ?>
									<?php $sums_apcm = 0 ?>
									<?php $sums_int = 0 ?>
									<?php $sums_rc = 0 ?>
									<?php $sums_sc = 0 ?>
									@foreach ($breakdown_pendings as $index => $breakdown_pending)
										<tr>
											<td>
												<div class="btn-group">
	                        <a href="javascript:void(0);" data-toggle="dropdown">
	                          <i class="fa fa-ellipsis-v"></i>
	                        </a>
	                        <ul class="dropdown-menu">
	                          <li>
	                          	<a class="dropdown-item" href="{{ route('pending.readd_breakdown', ['id' => $breakdown_pending->branch_id, 'pending_id' => $breakdown_pending->id]) }}">Add</a>
	                          </li>
	                        </ul>
	                      </div>
											</td>
											<td>
												{{ $breakdown_pending->docdate->format('d') }}
											</td>
											<td>{{ $breakdown_pending->ls_or }}</td>
											<td>{{ $breakdown_pending->por }}</td>
											<td>{{ $breakdown_pending->ls_ci }}</td>
											<td>{{ $breakdown_pending->ci }}</td>
											<td>{{ $breakdown_pending->ls_ch }}</td>
											<td>{{ $breakdown_pending->ch }}</td>
											<td>{{ $breakdown_pending->dep }}</td>
											<td>{{ $breakdown_pending->cla }}</td>
											<td>{{ $breakdown_pending->grpo }}</td>
											<td>{{ $breakdown_pending->si }}</td>
											<td>{{ $breakdown_pending->so }}</td>
											<td>{{ $breakdown_pending->sts }}</td>
											<td>{{ $breakdown_pending->disb }}</td>
											<td>{{ $breakdown_pending->arcm }}</td>
											<td>{{ $breakdown_pending->apcm }}</td>
											<td>{{ $breakdown_pending->pint }}</td>
											<td>{{ $breakdown_pending->rc_cash }}</td>
											<td>{{ $breakdown_pending->sc }}</td>
										</tr>
										<!-- SUM -->
										<?php $sums_or+= $breakdown_pending->por ?>
										<?php $sums_ci+= $breakdown_pending->ci ?>
										<?php $sums_ch+= $breakdown_pending->ch ?>
										<?php $sums_dep+= $breakdown_pending->dep ?>
										<?php $sums_cla+= $breakdown_pending->cla ?>
										<?php $sums_grpo+= $breakdown_pending->grpo ?>
										<?php $sums_si+= $breakdown_pending->si ?>
										<?php $sums_so+= $breakdown_pending->so ?>
										<?php $sums_sts+= $breakdown_pending->sts ?>
										<?php $sums_disb+= $breakdown_pending->disb ?>
										<?php $sums_arcm+= $breakdown_pending->arcm ?>
										<?php $sums_apcm+= $breakdown_pending->apcm ?>
										<?php $sums_int+= $breakdown_pending->pint ?>
										<?php $sums_rc+= $breakdown_pending->rc_cash ?>
										<?php $sums_sc+= $breakdown_pending->sc ?>
									@endforeach
									<tr class="text-danger">
										<th></th>
										<th>Total</th>
										<th>-</th>
										<th>{{ $sums_or }}</th>
										<th>-</th>
										<th>{{ $sums_ci }}</th>
										<th>-</th>
										<th>{{ $sums_ch }}</th>
										<th>{{ $sums_dep }}</th>
										<th>{{ $sums_cla }}</th>
										<th>{{ $sums_grpo }}</th>
										<th>{{ $sums_si }}</th>
										<th>{{ $sums_so }}</th>
										<th>{{ $sums_sts }}</th>
										<th>{{ $sums_disb }}</th>
										<th>{{ $sums_arcm }}</th>
										<th>{{ $sums_apcm }}</th>
										<th>{{ $sums_int }}</th>
										<th>{{ $sums_rc }}</th>
										<th>{{ $sums_sc }}</th>
									</tr>
								</tbody>
							</table>
							<p><strong>Note: </strong>You can add this breakdown to your pending for less inconvenience. Just click on the &nbsp;<i class="fa fa-ellipsis-v fa-2x"></i>&nbsp; icon on the left and click "Add" link to add.</p>
						</div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<!-- Modal Core -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel"><i class="fa fa-warning"></i> Confirm your action!</h4>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-simple" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@stop

@push('scripts')
<script type="text/javascript">
	$(document).ready(function() {
		$('.table').delegate('#delbtn','click', function() {
			var id = $(this).attr('class');
			var url = 'pendings/'+ id +'/delete';
			$('.el').remove();
			$('#myModal .modal-body').prepend('<p class="el">You are about to delete your pending with Document ID #' + id + '.</p>');
			$('#myModal .modal-footer').prepend('<a href="'+ url +'" class="el btn btn-simple btn-sm btn-danger">Confirm</a>');
		});
	});
</script>
@endpush
