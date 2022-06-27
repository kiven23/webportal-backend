@extends('layouts.app')

@section('title', 'Delete Announcement')

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
      <li class="active">Trash</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
			<div class="col-md-12">
				<div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">Confirm delete</h3>
          </div>

					<div class="box-body">
						You are about to delete a record with the following details:
						<ul>
							<li><strong>To: </strong> {{ $announcement->company->name }}</li>
							<li><strong>Title: </strong> {{ $announcement->title }}</li>
							<li><strong>Body: </strong> {{ $announcement->body }}</li>
						</ul>

						<span class="text-danger"><strong>Note: </strong> The action cannot be undo after you proceed.</span>
					</div>

					<div class="box-footer">
						<form method="post" action="{{ route('announcement.delete', ['id' => $announcement->id]) }}">
							{{ csrf_field() }}
							<button type="submit" class="btn btn-danger">Proceed</button>
							<a href="{{ route('announcements.index') }}" class="btn btn-default">No, go back</a>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
