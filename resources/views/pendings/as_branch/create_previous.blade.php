@extends('layouts.app')

@section('title', 'Add Pending to Previous')

@section('content')
<style type="text/css">
	th {
		text-align: center;
	}
	.form-control-pending {
		width: 100%;
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
      <li class="active">Create on previous date As</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title">
              Add Pending to previous date ({{ Carbon\Carbon::now()->addDay(-1)->format('F d, Y') }})
            </h3>
          </div>
          <div class="box-body">
          	@include ('errors.list')
						@include ('successes.list')

						<form class="form" action="{{ route('pending.store') }}" method="post">
							{{ csrf_field() }}
							<input type="hidden" name="isCreatePrev" value="1">
							<div class="row">
								<div class="col-md-3">
									<div class="form-group {{ $errors->has('docdate') ? 'has-error' : '' }}">
										<label class="control-label" for="docdate">Document Date</label>
										<input readonly="true" type="text" id="document-date" name="docdate" class="form-control datepicker" data-date-format="yyyy-mm-dd" value="{{ old('docdate') }}" >
							        @if ($errors->has('docdate'))
						        		<span class="form-text text-danger">{{ $errors->first('docdate') }}</span>
							        @endif
									</div>
									<script type="text/javascript">
										$(function () {
											$('#document-date').datetimepicker({
												timepicker:false,
												format:'Y-m-d'
											});
										});
									</script>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>Add As</label>
										<select class="form-control" name="branch">
											@foreach ($branches as $branch)
												<option {{ old('branch') == $branch->id ? 'selected' : '' }} value="{{ $branch->id }}">{{ $branch->name }}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>

							<div class="table-responsive">
								<table class="table table-condensed table-bordered">
									<thead>
										<tr>
											<th class="text-danger">LSU(OR)</th>
											<th>OR</th>
											<th class="text-danger">LSU(CI)</th>
											<th>CI&nbsp;&nbsp;</th>
											<th class="text-danger">LSU(CH)</th>
											<th>CH</th>
											<th>DEP</th>
											<th>CLA</th>
											<th>GRPO</th>
											<th>SI&nbsp;&nbsp;</th>
											<th>SO</th>
											<th>STS</th>
											<th>DISB</th>
											<th>ARCM</th>
											<th>APCM</th>
											<th>INT</th>
											<th>RC&nbsp;</th>
											<th>SC&nbsp;</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>
												<input type="text" name="ls_or" class="form-control-pending" value="{{ old('ls_or') }}">
												@if ($errors->has('ls_or'))
													<span class="form-text text-danger">{{ $errors->first('ls_or') }}</span>
												@endif
											</td>
											<td>
												<input type="text" name="or" class="form-control-pending" value="{{ old('or') }}">
												@if ($errors->has('or'))
													<span class="form-text text-danger">{{ $errors->first('or') }}</span>
												@endif
											</td>
											<td>
												<input type="text" name="ls_ci" class="form-control-pending" value="{{ old('ls_ci') }}">
												@if ($errors->has('ls_ci'))
													<span class="form-text text-danger">{{ $errors->first('ls_ci') }}</span>
												@endif
											</td>
											<td>
												<input type="text" name="ci" class="form-control-pending" value="{{ old('ci') }}">
												@if ($errors->has('ci'))
													<span class="form-text text-danger">{{ $errors->first('ci') }}</span>
												@endif
											</td>
											<td>
												<input type="text" name="ls_ch" class="form-control-pending" value="{{ old('ls_ch') }}">
												@if ($errors->has('ls_ch'))
													<span class="form-text text-danger">{{ $errors->first('ls_ch') }}</span>
												@endif
											</td>
											<td>
												<input type="text" name="ch" class="form-control-pending" value="{{ old('ch') }}">
												@if ($errors->has('ch'))
													<span class="form-text text-danger">{{ $errors->first('ch') }}</span>
												@endif
											</td>
											<td>
												<input type="text" name="dep" class="form-control-pending" value="{{ old('dep') }}">
												@if ($errors->has('dep'))
													<span class="form-text text-danger">{{ $errors->first('dep') }}</span>
												@endif
											</td>
											<td>
												<input type="text" name="cla" class="form-control-pending" value="{{ old('cla') }}">
												@if ($errors->has('cla'))
													<span class="form-text text-danger">{{ $errors->first('cla') }}</span>
												@endif
											</td>
											<td>
												<input type="text" name="grpo" class="form-control-pending" value="{{ old('grpo') }}">
												@if ($errors->has('grpo'))
													<span class="form-text text-danger">{{ $errors->first('grpo') }}</span>
												@endif
											</td>
											<td>
												<input type="text" name="si" class="form-control-pending" value="{{ old('si') }}">
												@if ($errors->has('si'))
													<span class="form-text text-danger">{{ $errors->first('si') }}</span>
												@endif
											</td>
											<td>
												<input type="text" name="so" class="form-control-pending" value="{{ old('so') }}">
												@if ($errors->has('so'))
													<span class="form-text text-danger">{{ $errors->first('so') }}</span>
												@endif
											</td>
											<td>
												<input type="text" name="sts" class="form-control-pending" value="{{ old('sts') }}">
												@if ($errors->has('sts'))
													<span class="form-text text-danger">{{ $errors->first('sts') }}</span>
												@endif
											</td>
											<td>
												<input type="text" name="disb" class="form-control-pending" value="{{ old('disb') }}">
												@if ($errors->has('disb'))
													<span class="form-text text-danger">{{ $errors->first('disb') }}</span>
												@endif
											</td>
											<td>
												<input type="text" name="arcm" class="form-control-pending" value="{{ old('arcm') }}">
												@if ($errors->has('arcm'))
													<span class="form-text text-danger">{{ $errors->first('arcm') }}</span>
												@endif
											</td>
											<td>
												<input type="text" name="apcm" class="form-control-pending" value="{{ old('apcm') }}">
												@if ($errors->has('apcm'))
													<span class="form-text text-danger">{{ $errors->first('apcm') }}</span>
												@endif
											</td>
											<td>
												<input type="text" name="int" class="form-control-pending" value="{{ old('int') }}">
												@if ($errors->has('int'))
													<span class="form-text text-danger">{{ $errors->first('int') }}</span>
												@endif
											</td>
											<td>
												<input type="text" name="rc_cash" class="form-control-pending" value="{{ old('rc_cash') }}">
												@if ($errors->has('rc_cash'))
													<span class="form-text text-danger">{{ $errors->first('rc_cash') }}</span>
												@endif
											</td>
											<td>
												<input type="text" name="sc" class="form-control-pending" value="{{ old('sc') }}">
												@if ($errors->has('sc'))
													<span class="form-text text-danger">{{ $errors->first('sc') }}</span>
												@endif
											</td>
										</tr>
									</tbody>
									<tfoot>
										<tr>
											<th colspan="18">Reason</th>
										</tr>
										<tr>
											<td colspan="18">
												<textarea name="reason" class="form-control">{{ old('reason') }}</textarea>
												@if ($errors->has('reason'))
													<span class="form-text text-danger">{{ $errors->first('reason') }}</span>
												@endif
											</td>
										</tr>
									</tfoot>
								</table>
							</div>

							<div class="form-group">
								<button class="btn btn-primary">Submit</button>
								<a href="{{ route('pendings') }}" class="btn btn-raised btn-default">Back</a>
								<div class="pull-right">
									<p class="text-info"><strong>Legend:</strong></p>
									<strong class="text-danger">LSU - </strong><small>Last Series Used</small>
								</div>
							</div>
						</form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
