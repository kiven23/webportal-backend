@extends('layouts.app')

@section('title', 'Approve Purchase Order Files')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Purchase Order Files
      <small>Manage purchase order files</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route($previous_link) }}">Purchase Order Files</a></li>
      <li class="active">Confirm approve</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-success">
          <div class="box-header with-border">
            <h3 class="box-title">
              Confirm approve
            </h3>
          </div>
          <!-- Start of Download File -->
          <form id="download-form" action="{{ route('purchase_order.file.download', ['id' => $file->id]) }}" method="post">
            {{ csrf_field() }}
          </form>
          <!-- End of Download File -->
          <form method="post" action="{{ route('po.file.approval.proceed_approve', ['id' => $file->id]) }}">
            {{ csrf_field() }}
            <div class="box-body">

              <strong>You are about to approve this item with the following details:</strong>
              <ul>
                <li>
                  File: 
                  <a onclick="event.preventDefault();
                    document.getElementById('download-form').submit();" href="javascript:void(0);"
                  >{{ $file->file }}</a>
                </li>
                <li>
                  To:
                  {{ $file->to_user ? $file->to_user->name : '' }}
                  {{ $file->to_company ? $file->to_company->name : '' }}
                </li>
                <li>
                  Remarks: {{ $file->remarks }}
                </li>
              </ul>

              <span class="text-danger"><strong>Note: </strong> The action cannot be undo after you proceed.</span>
            </div>

            <div class="box-footer">
              <button type="submit" class="btn btn-success">Proceed</button>
              <a href="{{ route($previous_link) }}" class="btn btn-default">No, go back</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection