@extends('layouts.app')

@section('title', 'Unauthorized User')

@section('content')
<div class="content-wrapper">
  <section class="content">
    <div class="row mt-4 mb-4">
			<div class="col-md-12">
				<h2>
		      <center>403<br>
		        FORBIDDEN<br>
		        <small><a href="{{ URL::previous() }}" class="text-center">Back</a></small> ~
		        <small><a href="{{ route('home') }}" class="text-center">Home</a></small>
		      </center>
		    </h2>
			</div>
		</div>
	</section>
</div>
@endsection