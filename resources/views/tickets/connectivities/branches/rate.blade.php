@extends('layouts.app')

@section('title', 'Rate Connectivity Ticket')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Service Call Tickets
      <small>Manage connectivity tickets</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('ticket.branch.connectivities') }}">Connectivity Tickets</a></li>
      <li class="active">Rate</li>
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
          <form method="post" action="{{ route('ticket.connectivity.rate_proceed', ['id' => $connectivity->id]) }}">
            {{ csrf_field() }}
            <div class="box-body">
              You are about to rate activity with the following connectivity details:
              <ul>
                <li><strong>Ticket #: </strong>{{ $connectivity->id }}</li>
                <li>
                  <strong>Service Provider: </strong> {{ $connectivity->service_provider->name }}
                </li>
                <li>
                  <strong>Service Type: </strong> {{ $connectivity->service_type->name }}
                </li>
                <li>
                  <strong>Service Category: </strong> {{ $connectivity->service_category->name }}
                </li>
                <li><strong>Problem: </strong>{{ $connectivity->problem }}</li>
                <li>
                  <strong>Reported By: </strong>
                  {{ $connectivity->reported_by_name }}
                  ({{ $connectivity->reported_by_position }})
                </li>
                <li><strong>Logged By: </strong>{{ $connectivity->user->first_name }} {{ $connectivity->user->last_name }}</li>
                <li>
                  <strong>Date Logged: </strong>
                  {{ \Carbon\Carbon::parse($connectivity->created_at)->format('F d, Y (h:i a)') }}
                </li>
                <li>
                  <strong>Status: </strong>
                  @if ($connectivity->status === 1)
                    <span class="label bg-green">Open</span>
                  @elseif ($connectivity->status === 2)
                    <span class="label bg-orange">Pending</span>
                  @else
                    <span class="label bg-red">Closed</span>
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
                  value="{{ $connectivity->survey ? $connectivity->survey->rate : old('rate') }}"
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
                <textarea name="remarks" class="form-control">{{ $connectivity->survey ? $connectivity->survey->remarks : old('remarks') }}</textarea>
                @if ($errors->has('remarks'))
                  <span class="form-text text-danger">
                    {{ $errors->first('remarks') }}
                  </span>
                @endif
              </div>
            </div>
          
            <div class="box-footer">
              <button type="submit" class="btn btn-warning">Submit Rating</button>
              <a href="{{ route('ticket.branch.connectivities') }}" class="btn btn-default">Cancel</a>
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
