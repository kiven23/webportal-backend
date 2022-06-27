@extends('layouts.app')

@section('title', 'Delete Company')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Companies
      <small>Manage company</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('companies.index') }}">Companies</a></li>
      <li class="active">Trash</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">
              Confirm delete
            </h3>
          </div>
          <div class="box-body">
						You are about to delete a record with the following details:
						<ul>
							<li><strong>Company Name: </strong> {{ $company->name }}</li>
							<li><strong>Address: </strong> {{ $company->address }}</li>
							<li><strong>Contact: </strong> {{ $company->contact }}</li>
							<li><strong>Email: </strong> {{ $company->email }}</li>
						</ul>
						<span>User(s) under this record:</span>
						<ol>
							@forelse ($company->users as $user)
								<li><strong>{{ $user->first_name }} {{ $user->last_name }}</strong></li>
							@empty
								<li>None</li>
							@endforelse
						</ol>

						<span class="text-danger"><strong>Note: </strong> The action cannot be undo after you proceed.</span>
					</div>

					<div class="box-footer">
						<form method="post" action="{{ route('company.delete', ['id' => $company->id]) }}">
							{{ csrf_field() }}
							{{ method_field('delete') }}
							<button type="submit" class="btn btn-danger">Proceed</button>
							<a href="{{ route('companies.index') }}" class="btn btn-default">No, go back</a>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
