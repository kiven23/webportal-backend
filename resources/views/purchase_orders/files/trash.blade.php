@extends('layouts.app')

@section('title', 'Delete Purchase Order File')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Purchase Order Files
      <small>Manage purchase order files</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('purchase_orders.files.index') }}">Purchase Order Files</a></li>
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
							<form action="{{ route('purchase_order.file.download', ['id' => $file->id]) }}" method="post">
								<li><strong>File: </strong>{{ csrf_field() }}
									<button type="submit" class='btn-to-link'>{{ $file->file }}</button>
								</li>
							</form>
							<li><strong>P.O #: </strong> {{ $file->po_number }}</li>
							<li><strong>Uploaded by: </strong> {{ $file->from_user->name }}</li>
							<li><strong>To: </strong> {{ $file->to_user ? $file->to_user->name : $file->to_company->name }}</li>
							<li><strong>Remarks: </strong> {{ $file->remarks }}</li>
						</ul>

						<span class="text-danger"><strong>Note: </strong> The action cannot be undo after you proceed.</span>
					</div>

					<div class="box-footer">
						<form method="post" action="{{ route('purchase_order.file.delete', ['id' => $file->id]) }}">
							{{ csrf_field() }}
							{{ method_field('delete') }}
							<button type="submit" class="btn btn-danger">Proceed</button>
							<a href="{{ route('purchase_orders.files.index') }}" class="btn btn-default">No, go back</a>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
