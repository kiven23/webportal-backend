@extends('layouts.app')

@section('title', 'Edit Concern')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
    	Concerns
    	<small>Manage concern</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('concerns.index') }}">Concerns</a></li>
      <li class="active">Edit</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
			<div class="col-md-12">
				<div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Edit concern</h3>
          </div>

        	<form method="post" action="{{ route('concern.update', ['id' => $concern->id]) }}">
						{{ csrf_field() }}
						<div class="box-body">
							<div class="row">
								<div class="col-md-5">
									@include ('errors.list')
									@include ('successes.list')

									<div class="row">
										<div class="col-md-6">
											<div class="form-group {{ $errors->has('branch') ? 'has-error' : '' }}">
												<label>Branch</label>
												<select name="branch" class="form-control select2">
													@foreach ($branches as $branch)
														<option
															{{ $concern->branch_id == $branch->id ? 'selected' : '' }}
															value="{{ $branch->id }}"
														>{{ $branch->name }}</option>
													@endforeach
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group {{ $errors->has('user') ? 'has-error' : '' }}">
												<label>Reported By</label>
												<select name="user" class="form-control select2 select2-new">
													@foreach ($users as $user)
														<option
															{{ $concern->reported_by == $user->name ? 'selected' : '' }}
															value="{{ $user->name }}">{{ $user->name }}</option>
													@endforeach
												</select>
												@if ($errors->has('user'))
													<span class="form-text text-danger">{{ $errors->first('user') }}</span>
												@endif
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label>Type</label>
												<select name="type" class="form-control select2 select2-new">
													@foreach ($types as $type)
														<option
															{{ $concern->concern_type_id == $type->id ? 'selected' : '' }}
															value="{{ $type->id }}">{{ $type->name }}</option>
													@endforeach
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>Category</label>
												<select name="category" class="form-control select2 select2-new">
													@foreach ($categories as $category)
														<option
															{{ $concern->concern_category_id == $category->id ? 'selected' : '' }}
															value="{{ $category->id }}"
														>{{ $category->name }}</option>
													@endforeach
												</select>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label>Database</label>
												<input type="text" class="form-control" name="database" value="{{ $concern->database }}">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>Cause</label>
												<input type="text" class="form-control" name="cause" value="{{ $concern->cause }}">
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-6">
											<div class="form-group {{ $errors->has('remarks') ? 'has-error' : '' }}">
												<label>Remarks *</label>
												<textarea type="text" class="form-control" name="remarks">{{ $concern->remarks }}</textarea>
											</div>
											@if ($errors->has('remarks'))
												<span class="form-text text-danger">{{ $errors->first('remarks') }}</span>
											@endif
										</div>
										<div class="col-md-6">
											<div class="form-group {{ $errors->has('remarks') ? 'has-error' : '' }}">
												<label>Resolution</label>
												<textarea type="text" class="form-control" name="resolution">{{ $concern->resolution }}</textarea>
											</div>
											@if ($errors->has('resolution'))
												<span class="form-text text-danger">{{ $errors->first('resolution') }}</span>
											@endif
										</div>
									</div>

									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label>Date Solved</label>
												<input type="text" class="form-control" id="date-solved" name="date_solved" value="{{ $concern->date_solved }}">

						            <script type="text/javascript">
							            $(function () {
							            	$('#date-solved').datetimepicker({
							            		format:'Y-m-d H:i'
							            	});
							            });
								        </script>

							        	@if ($errors->has('date_solved'))
					              	<span class="form-text text-danger">
					              		{{ $errors->first('date_solved') }}
					              	</span>
					              @endif
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>Status</label><br>
												@if ($concern->status == 0)
													<span class="label label-success">Open</span>
												@else
													<span class="label label-danger">Closed</span>
												@endif
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="box-footer">
							<div class="row">
								<div class="col-md-5">
									<button type="submit" class="btn btn-primary">Update</button>
									<a href="{{ route('regions') }}" class="btn btn-default pull-right">Back</a>
								</div>
							</div>
						</div>
					</form>
        </div>
      </div>
    </div>
  </section>
</div>
@stop

@push('scripts')
<script>
  $('.select2').select2();
  $('.select2-new').select2({
    tags: true,
    createTag: function (params) {
      var term = $.trim(params.term);
      var count = 0
      var existsVar = false;
      //check if there is any option already
      if($('#keywords option').length > 0){
        $('#keywords option').each(function(){
          if ($(this).text().toUpperCase() == term.toUpperCase()) {
            existsVar = true
            return false;
          }else{
            existsVar = false
          }
        });
        if(existsVar){
          return null;
        }
        return {
          id: params.term,
          text: params.term,
          newTag: true
        }
      }
      //since select has 0 options, add new without comparing
      else{
        return {
          id: params.term,
          text: params.term,
          newTag: true
        }
      }
    },
    maximumInputLength: 255,
    closeOnSelect: true
  });
</script>
@endpush