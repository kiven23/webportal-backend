@extends('layouts.app')

@section('title', 'Contact Lists')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Message Cast
      <small>Mange contact lists</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Contact Lists</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              Contact lists
            </h3>
            <div class="box-toolbar pull-right">
              <div class="btn-group">
                <a href="{{ route('contact_list.message_cast.create') }}" class="btn btn-primary btn-xs">
                  <i class="fa fa-plus"></i>&nbsp;ADD
                </a>
                <a href="{{ route('messages.message_casts') }}" class="btn btn-danger btn-xs">
                  <i class="fa fa-envelope"></i>&nbsp;COMPOSE MESSAGE
                </a>
              </div>
            </div>
          </div>
          <div class="box-body">
            @include ('errors.list')
            @include ('successes.list')

            <table class="table table-bordered table-hover" id="contactlist-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Contact Number</th>
                  <th>Location</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($contactlists as $index => $contactlist)
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $contactlist->name }}</td>
                    <td>{{ $contactlist->contact_number }}</td>
                    <td>{{ $contactlist->location ? $contactlist->location : 'None' }}</td>
                    <td>
                      <div class="btn-group">
                        <a href="{{ route('contact_list.message_cast.edit', ['id' => $contactlist->id]) }}" class="btn btn-default btn-xs" title="Edit"><i class="fa fa-pencil"></i></a>
                        @if (\Auth::user()->role !== 98)
                          <a href="{{ route('contact_list.message_cast.trash', ['id' => $contactlist->id]) }}" class="btn btn-default btn-xs" title="Delete"><i class="fa fa-trash"></i></a>
                        @endif
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@stop

@push('scripts')
<script>
  $(function () {
    $('#contactlist-table').DataTable({
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'select'      : true,
      'responsive'  : true,
      'scrollY'       : "300px",
      dom: "<'row'<'col-sm-3'l><'col-sm-6 text-center'B><'col-sm-3'f>>" +
           "<'row'<'col-sm-12'tr>>" +
           "<'row'<'col-sm-5'i><'col-sm-7'p>>",
      lengthMenu: [
          [ 10, 25, 50, -1 ],
          [ '10', '25', '50', '100', 'All' ]
      ],
      buttons: [
          {
              extend: 'excelHtml5',
              exportOptions: {
                  columns: ':visible'
              }
          },
          'colvis'
      ]
    })
  })
</script>
@endpush