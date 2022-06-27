@extends('layouts.app')

@section('title', 'Purchasing Reports')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Purchasing Reports
      <small>Manage purchasing reports</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      @if (isset($file_type))
        <li><a href="{{ route('report.purchasing.index') }}">Purchasing Reports</a></li>
        <li class="active">{{ $file_type }}</li>
      @else
        <li class="active">Purchasing Reports</li>
      @endif
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              Purchasing Reports ({{ isset($file_type) ? $file_type : 'All' }})
            </h3>
            <div class="box-toolbar pull-right">
              <a href="{{ route('report.purchasing.create') }}" class="btn btn-primary btn-xs">
                <i class="fa fa-plus"></i>&nbsp;
                  ADD
              </a>
            </div>
          </div>
          <div class="box-body table-responsive">
            @include ('errors.list')
            @include ('successes.list')
            <table class="table table-bordered table-hover"
                   id="file-table"
                   data-operators='[{"url":"{{route('purchase_order.file.edit',['id'=>':id'])}}","text":"Edit","icon":"fa fa-pencil"},{"url":"{{route('purchase_order.file.trash',['id'=>':id'])}}","text":"Delete","icon":"fa fa-trash"}]'>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>File Name</th>
                  <th>File(s)</th>
                  <th>Type</th>
                  <th>From</th>
                  <th>To</th>
                  <th>Remarks</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($files as $index => $file)
                  <tr>
                    <td>{{ $file->id }}</td>
                    <td>{{ $file->file }}</td>
                    <td>
                      <a
                        href="javascript:void(0);"
                        class="btn btn-default btn-xs"
                        title="View"
                        id="expandModal"
                        data-toggle="modal"
                        data-target="#exampleModal"
                        data-id="{{ $file->id }}"
                        data-to_user="{{ $file->to_user ? $file->to_user->name : '' }}"
                        data-to_company="{{ $file->company_id ? $file->to_company->name : '' }}"
                        data-files="{{ $file->file }}"
                      >
                        View
                      </a>
                    </td>
                    <td>{{ $file->type->name }}</td>
                    <td>{{ $file->from_user ? $file->from_user->name : '' }}</td>
                    <td>
                      {{ $file->to_user ? $file->to_user->name : '' }}
                      {{ $file->company_id ? $file->to_company->name : '' }}
                    </td>
                    <td>{{ $file->remarks }}</td>
                    <td>
                      <div class="btn-group">
                        <a class="btn btn-default btn-xs" href="{{ route('report.purchasing.edit', ['' => $file->id]) }}"><i class="fa fa-pencil"></i></a>
                        <a class="btn btn-default btn-xs" href="{{ route('report.purchasing.trash', ['' => $file->id]) }}"><i class="fa fa-trash"></i></a>
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

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"></h5>
      </div>
      <div class="modal-body" style="overflow-wrap: break-word;"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@stop

@push('scripts')
<script>
  $(function () {

    $('table').on('click', '#expandModal', function () {
      $('.modal-title').html('');
      var id = $(this).data('id');
      var to_user = $(this).data('to_user');
      var to_company = $(this).data('to_company');
      var to;
      if (to_user) {
        to = to_user;
      } else { to = to_company; }
      $('.modal-title').append("To: "+to);

      $('.modal-body').html('');
      var files = $(this).data('files');
      var files = files.split(",");

      var file_text;
      files.length > 1 ? file_text = "Files" : file_text = "File";
      var header = '<strong>'+file_text+'&nbsp;('+files.length+')</strong><br>';
      $('.modal-body').append(header);
      for (var c = 0; c < files.length; c++) {
        var url = '{{ route("report.purchasing.download", ["id" => ":id","file" => ":file"]) }}',
            _token = '{{ csrf_field() }}',
            url = url.replace(':id', id),
            url = url.replace(':file', files[c]),
            file = '<form method="post" action="'+url+'">'+
                    _token+
                    (c + 1) + '.&nbsp;<button style="text-overflow:ellipsis;" stype="submit" class="btn-to-link text-to-white">'+files[c]+'</button>'+
                    '</form>';
        $('.modal-body').append(file);
      }
    });

    $('label').on('click', '#to-user', function() {
      var div = $($(this).parent()).parent(),
          span = $(this).parent().find('span');

      $(span).html('To User');
      $(this).attr('id', 'to-company').html('or To Company:');
      
      $(div).find('select').attr('name', '').hide();
      $(div).find('select').next().attr('name', 'user').show();
    });

    $('label').on('click', '#to-company', function() {
      var div = $($(this).parent()).parent(),
          span = $(this).parent().find('span');

      $(span).html('To Company');
      $(this).attr('id', 'to-user').html('or To User:');
      
      $(div).find('select').attr('name', 'company').show();
      $(div).find('select').next().attr('name', '').hide();
    });

    $('#file-table tbody').on('mouseover', 'tr', function () {
	    $('[data-toggle="tooltip"]').tooltip({
        trigger: 'hover',
        html: true,
	    });

	    $('[data-toggle="popover"]').popover({
        trigger: 'hover',
        html: true,
        placement: 'left',
	    });
		});

    // --------------------------------------------------------------------

    var fileTable = $('#file-table');
    var table = $(fileTable).DataTable({
          'paging'      : true,
          'lengthChange': true,
          'searching'   : true,
          'ordering'    : true,
			    'order'		    : [],
          'info'        : true,
          'responsive'  : true,
          'scrollY'     : "300px",
          'columns': [
              { "data": "id" },
              { "data": "file", "visible": false },
              { "data": "file",
                // render: function ( data, type, row ) {
                //   var url = '{{ route("report.purchasing.download", ["id" => ":id", "file" => ":file"]) }}',
                //       _token = '{{ csrf_field() }}',
                //       url = url.replace(':id', data.id),
                //       file = '<form method="post" action="'+url+'">'+
                //              _token+
                //              '<button style="text-overflow:ellipsis;" stype="submit" class="btn-to-link text-to-white">View</button>'+
                //              '</form>';
                //   return file;
                // }
              },
              { "data": "type" },
              { "data": "from", "visible": false },
              { "data": "to" },
              { "data": "remarks" },
              { "data": "actions"},
          ],
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
        });
  });
</script>
@endpush
