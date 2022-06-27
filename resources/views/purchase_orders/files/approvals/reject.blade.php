@extends('layouts.app')

@section('title', 'Reject Purchase Order Files')

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
      <li class="active">Confirm reject</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">
              Confirm reject
            </h3>
          </div>
          <!-- Start of Download File -->
          <form id="download-form" action="{{ route('purchase_order.file.download', ['id' => $file->id]) }}" method="post">
            {{ csrf_field() }}
          </form>
          <!-- End of Download File -->
          <form method="post" action="{{ route('po.file.approval.proceed_reject', ['id' => $file->id]) }}">
            {{ csrf_field() }}
            <div class="box-body">
              <div class="row">
                <div class="col-md-5">
                  @include ('errors.list')
                  <strong>You are about to reject this item with the following details:</strong>
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
              
                  <div class="form-group {{ $errors->has('remarks2') ? 'has-error' : '' }}">
                    <label>Status Remarks:</label>
                    <textarea type="text" name="remarks2" class="form-control"></textarea>
                    @if ($errors->has('remarks2'))
                      <span class="form-text text-danger">
                        {{ $errors->first('remarks2') }}
                      </span>
                    @endif
                  </div>

                  <span class="text-danger"><strong>Note: </strong> The action cannot be undo after you proceed.</span>
                </div>
              </div>
            </div>

            <div class="box-footer">
              <button type="submit" class="btn btn-danger">Proceed</button>
              <a href="{{ route($previous_link) }}" class="btn btn-default">No, go back</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection