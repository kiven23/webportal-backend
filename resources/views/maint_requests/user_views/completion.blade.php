@extends('layouts.app')

@section('title', 'Rate Maintenance Request')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Maintenance Requests
      <small>Manage maintenance requests</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('maint_requests') }}">Maintenance Request</a></li>
      <li class="active">Completion</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title">
              Rate activity
            </h3>
          </div>
          <form method="post" action="{{ route('maint_request.completion_proceed', ['id' => $maint_request->id]) }}">
            {{ csrf_field() }}
            <div class="box-body">
              You are about to rate activity with the following request details:
              <ul>
                <li><strong>ID #: </strong>{{ $maint_request->id }}</li>
                <li><strong>Submitted By: </strong>{{ $maint_request->user->name }}</li>
                <li><strong>Received By: </strong>{{ $maint_request->received_by_user->name }}</li>
                <li>
                  <strong>Date Logged: </strong>
                  {{ \Carbon\Carbon::parse($maint_request->created_at)->format('F d, Y (h:i a)') }}
                </li>
                <li>
                  @if (count($maint_request->files) > 0)
                    <div class='view-files'>
                      @foreach ($maint_request->files as $index => $file)
                        @if ($index === 0)
                          <strong>Attached Files: </strong>
                          <em>
                            <a href="{{ asset('storage/'.$file->file_path) }}"
                              data-lightbox="{{ $file->maint_request_id }}"
                              data-title="{{ $file->file_name }}"
                            >
                              {{ count($maint_request->files) }}
                              attachment{{ count($maint_request->files) > 1 ? 's' : '' }}
                              <span class="fa fa-paperclip fa-flip-vertical"></span>
                            </a>
                          </em>
                        @else
                          <a href="{{ asset('storage/'.$file->file_path) }}" data-lightbox="{{ $file->maint_request_id }}" data-title="{{ $file->file_name }}"></a>
                        @endif
                      @endforeach
                    </div>
                  @else
                    <span>No Attachments</span>
                  @endif
                </li>
                <li>
                  <strong>Remarks: </strong>
                  {{ $maint_request->remarks }}
                </li>
                <li>
                  <strong>Status: </strong>
                  @if ($maint_request->status === 0)
                    <span class="label bg-default">Pending</span>
                  @elseif ($maint_request->status === 1)
                    <span class="label bg-blue">Received</span>
                  @elseif ($maint_request->status === 2)
                    <span class="label bg-orange">Cancelled</span>
                  @else
                    <span class="label bg-green">Approved</span>
                  @endif
                </li>
              </ul>

              <hr>

              <div class="form-group {{ $errors->has('rate') ? 'has-error' : '' }}">
                <label for="rate">Rate:</label>
                <input
                  name="rate"
                  class="kv-ltr-theme-uni-alt rating"
                  dir="ltr"
                  value="{{ $maint_request->survey ? $maint_request->survey->rate : old('rate') }}"
                  data-min="0"
                  data-max="5"
                  data-step="1"
                  data-size="md"
                >
                @if ($errors->has('rate'))
                  <span class="form-text text-danger">
                    {{ $errors->first('rate') }}
                  </span>
                @endif
              </div>
              <div class="form-group {{ $errors->has('remarks') ? 'has-error' : '' }}">
                <label for="remarks">Remarks:</label>
                <textarea name="remarks" class="form-control">{{ $maint_request->survey ? $maint_request->survey->remarks : old('remarks') }}</textarea>
                @if ($errors->has('remarks'))
                  <span class="form-text text-danger">
                    {{ $errors->first('remarks') }}
                  </span>
                @endif
              </div>

              <span class="help-block">
                <strong>NOTE:&nbsp;</strong>
                Once proceed status will be marked as <span class="label bg-red">Completed</span> and cannot be undo.
              </span>
            </div>
          
            <div class="box-footer">
              <button type="submit" class="btn btn-warning">Submit Rating</button>
              <a href="{{ route('maint_requests') }}" class="btn btn-default">Cancel</a>
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
  $('.rating').rating({
    hoverOnClear: false,
    theme: 'krajee-uni',
    starCaptions: function (rating) {
      return rating == 1 ? 'Poor' :
        (rating == 2 ? 'Needs Improvement' :
          (rating == 3 ? 'Satisfactory' :
            (rating == 4 ? 'Very Good' : 'Excellent')
          )
        );
    },
  });
</script>
@endpush
