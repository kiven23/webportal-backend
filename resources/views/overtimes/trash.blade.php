@extends('layouts.app')

@section('content')
<div id="page-wrapper">
    <div class="row">
    	<div class="col-md-12">
            <h1 class="page-header">Delete Overtime</h1>
    	</div>

        <div class="col-md-12">
            @if (Session::has('overtime_delete_fail'))
                <div class="alert alert-danger">
                    <strong>{{ Session::get('overtime_delete_fail') }}</strong>
                </div>
            @endif
        </div>

        <div class="col-md-6">

            <p><strong>You are about to delete this Filed Overtime</strong></p>

            <form action="{{ route('overtime.delete', ['id' => $pending->id]) }}" method="post">
                {{ csrf_field() }}

                <ul class="list-group">
                    <li class="list-group-item">
                        <h4>Confirm Deletion</h4>
                    </li>
                    <li class="list-group-item">
                        <strong>Date:</strong>
                        {{ Carbon\Carbon::parse($pending->date_from)->format('F d, Y') }}
                        ({{ Carbon\Carbon::parse($pending->date_from)->format('h:i a') }} -
                        {{ Carbon\Carbon::parse($pending->date_to)->format('h:i a') }})
                    </li>
                    <li class="list-group-item">
                        <strong>Reason:</strong>
                        {{ $pending->reason }}
                    </li>
                    <li class="list-group-item">
                        <button type="submit" class="btn btn-danger btn-sm">Proceed</button>
                        <a href="{{ URL::previous() }}" class="btn btn-default btn-sm">Cancel</a>
                    </li>
                </ul>
            </form>
            
        </div>
    </div>
 </div>
@endsection