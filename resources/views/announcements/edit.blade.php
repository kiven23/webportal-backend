@extends('layouts.app')

@section('title', 'Edit Announcement')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
    	Announcements
    	<small>Manage announcement</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('announcements.index') }}">Announcements</a></li>
      <li class="active">Edit</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
			<div class="col-md-12">
				<div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Edit announcement</h3>
          </div>

        	<form method="post" action="{{ route('announcement.update', ['id' => $announcement->id]) }}">
						{{ csrf_field() }}
						<div class="box-body">
							<div class="row">
								<div class="col-md-5">
									@include ('errors.list')
									@include ('successes.list')

									<div class="form-group">
										<Label>Company</Label>
                    <select class="form-control select2" name="company">
                      @foreach ($companies as $company)
                        <option
													{{ $company->id === $announcement->company_id ? 'selected' : '' }}
													value="{{ $company->id }}">{{ $company->name }}</option>
                      @endforeach
                    </select>
                  </div>

									<div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
										<label>Title</label>
										<input class="form-control" type="text" name="title" value="{{ $announcement->title }}" placeholder="Name" autofocus>
										@if ($errors->has('title'))
											<span class="form-text text-danger">
												{{ $errors->first('title') }}
											</span>
										@endif
									</div>

                  <div class="form-group {{ $errors->has('body') ? 'has-error' : '' }}">
										<label>Body</label>
                    <textarea name="body" class="form-control" placeholder="Body">{{ $announcement->body }}</textarea>
										@if ($errors->has('body'))
											<span class="form-text text-danger">
												{{ $errors->first('body') }}
											</span>
										@endif
									</div>
								</div>
							</div>
						</div>

						<div class="box-footer">
							<div class="row">
								<div class="col-md-5">
									<button type="submit" class="btn btn-primary">Update</button>
									<a href="{{ route('announcements.index') }}" class="btn btn-default pull-right">Back</a>
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
</script>
@endpush

