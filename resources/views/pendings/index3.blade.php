@extends('layouts.app')

@section('title', 'Pendings')

@section('content')
<style type="text/css">
	th, td {
		text-align: center;
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
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title">
              {{ strtoupper(\Auth::user()->branch->name) }} pendings as of {{ Carbon\Carbon::now()->addDay(-1)->format('F d, Y') }}
            </h3>
            <div class="box-toolbar pull-right">
	            <div class="btn-group">
	            	<a href="{{ route('pending.createprev') }}" class="btn btn-primary btn-xs">
									<i class="fa fa-plus"></i> &nbsp;Add new to previous
								</a>
								<a href="{{ route('pending.branch.chart', ['id' => \Auth::user()->branch->id]) }}" class="btn btn-primary btn-xs">
									<i class="fa fa-pie-chart"></i> Add from Charts
								</a>
	            </div>
            </div>
          </div>
          <div class="box-body">
          	@include ('errors.list')
						@include ('successes.list')

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
								@foreach ($prevPendings as $index => $pending)
									<tr>
										<td>
											<div class="btn-group">
                        <a href="javascript:void(0);" data-toggle="dropdown">
                          <i class="fa fa-ellipsis-v"></i>
                        </a>
                        <ul class="dropdown-menu">
                          <li>
                          	<a class="dropdown-item" href="{{ route('pending.edit', ['id' => $pending->id]) }}">Edit</a>
                          </li>
                          <li>
                          	<a class="dropdown-item" href="javascript:void(0);" data-toggle="modal" name="{{ $pending->id }}" id="delbtn" data-target="#myModal">Delete</a>
                          </li>
                        </ul>
                      </div>
										</td>
										<td>
											{{ $pending->docdate->format('d') }}
										</td>
										<td>{{ $pending->ls_or }}</td>
										<td>{{ $pending->por }}</td>
										<td>{{ $pending->ls_ci }}</td>
										<td>{{ $pending->ci }}</td>
										<td>{{ $pending->ls_ch }}</td>
										<td>{{ $pending->ch }}</td>
										<td>{{ $pending->dep }}</td>
										<td>{{ $pending->cla }}</td>
										<td>{{ $pending->grpo }}</td>
										<td>{{ $pending->si }}</td>
										<td>{{ $pending->so }}</td>
										<td>{{ $pending->sts }}</td>
										<td>{{ $pending->disb }}</td>
										<td>{{ $pending->arcm }}</td>
										<td>{{ $pending->apcm }}</td>
										<td>{{ $pending->pint }}</td>
										<td>{{ $pending->rc_cash }}</td>
										<td>{{ $pending->sc }}</td>
									</tr>
									<!-- SUM -->
									<?php $sums_or+= $pending->por ?>
									<?php $sums_ci+= $pending->ci ?>
									<?php $sums_ch+= $pending->ch ?>
									<?php $sums_dep+= $pending->dep ?>
									<?php $sums_cla+= $pending->cla ?>
									<?php $sums_grpo+= $pending->grpo ?>
									<?php $sums_si+= $pending->si ?>
									<?php $sums_so+= $pending->so ?>
									<?php $sums_sts+= $pending->sts ?>
									<?php $sums_disb+= $pending->disb ?>
									<?php $sums_arcm+= $pending->arcm ?>
									<?php $sums_apcm+= $pending->apcm ?>
									<?php $sums_int+= $pending->pint ?>
									<?php $sums_rc+= $pending->rc_cash ?>
									<?php $sums_sc+= $pending->sc ?>
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
						<p><strong>Note: </strong>Because the Administrator will giving you a grace period for submitting pending, you can still update your previous pendings <strong>dated yesterday</strong>({{ Carbon\Carbon::now()->addDay(-1)->format('F d, Y') }}) until <strong class="text-danger">{{ \Carbon\Carbon::parse($grace_period)->format('h:i a') }}</strong> today and/or even submitting new pendings.</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<!-- Modal Core -->
<div class="modal fade" id="myModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title text-danger" id="myModalLabel"><i class="fa fa-warning"></i> Confirm your action!</h4>
      </div>
      <div class="modal-body"></div>
			<div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@stop

@push('scripts')
<script type="text/javascript">
	$(document).ready(function() {
		$('.table').delegate('#delbtn','click', function() {
			var branch_id = <?php echo isset($pending) ? $pending->branch_id : 1; ?>;
			var pending_id = $(this).attr('name');
			var url = '/webportal/public/pendings/' + branch_id + '/' + pending_id + '/delete';
			$('.el').remove();
			$('#myModal .modal-body').prepend('<p class="el">You are about to delete your pending with Document ID #' + pending_id + '.</p>');
			$('#myModal .modal-footer').prepend('<a href="'+ url +'" class="el btn btn-danger">Confirm</a>');
		});
	});
</script>
@endpush
