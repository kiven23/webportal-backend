@extends('layouts.app')

@section('title', 'Purchase Order Files')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Purchase Order Files
      <small>Manage purchase order files</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Purchase Order Files</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              Files
            </h3>
            <div class="box-toolbar pull-right">
              <a href="{{ route('purchase_order.file.create') }}" class="btn btn-primary btn-xs">
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
                  <th>File</th>
                  <th>P.O #</th>
                  <th>From</th>
                  <th>To</th>
                  <th>To User/Company</th>
                  <th>Progress</th>
                  <th>Status</th>
                  <th>Remarks</th>
                  <th>Created At</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($files as $index => $file)
                  <tr>
                    <td>{{ $file->id }}</td>
                    <td>{{ $file->file }}</td>
                    <td>
                      <!-- Files -->
                    </td>
                    <td>{{ $file->po_number }}</td>
                    <td>{{ $file->from_user ? $file->from_user->name : '' }}</td>
                    <td>
                      {{ $file->to_user ? $file->to_user->name : '' }}
                      {{ $file->company_id ? $file->to_company->name : '' }}
                    </td>
                    <td>{{ $file->to_user ? "user-" . $file->to_user->id : "company-" . $file->to_company->id }}</td>
                    <td>
                      @if ($file->status !== 2)
												@foreach ($approvers as $index => $approver)
													<i
														class="icon-pending fa
															{{ $file->waiting_for > $approver['level'] ? 'fa-check-circle' : 'fa-circle-o' }}
														" data-toggle="tooltip" data-container="body"
														title="
															@foreach ($approver['user'] as $user)
																{{ isset($file->officers_approved[$index]) ? ($user == $file->officers_approved[$index]['approver'] ? '*' . $user . '*' : $user) : $user }}
																@if ($user != end($approver['user']))
																	/
																@endif
															@endforeach
													"></i>
												@endforeach
											@else
												@foreach ($file->officers_approved as $officer_approved)
													<i
														class="icon-approved fa fa-check-circle" data-toggle="tooltip"
														data-container="body"
														title="
															{{ $officer_approved->approver }}
														"></i>
												@endforeach
											@endif
										</td>
										<td class="align-top">
											@if ($file->status == 0)
                        <span class="label label-default">Pending</span>
											@elseif ($file->status == 1)
												<abbr title=""
															data-container="body"
															data-toggle="popover"
															data-placement="left"
															data-content="{{ $file->remarks_user->first_name }} {{ $file->remarks_user->last_name }} - {{ $file->remarks2 }}"
															data-original-title="File Rejected">
                          <span class="label label-danger">Rejected</span>
                        </abbr>
											@elseif ($file->status == 2)
                        <span class="label label-success">Approved</span>
											@endif
										</td>
                    <td>{{ $file->remarks }}</td>
                    <td>{{ $file->created_at }}</td>
                    <td>
                      @if ($file->status === 0)
                        <div class="btn-group">
                          <a class="btn btn-default btn-xs" href="{{ route('purchase_order.file.edit', ['' => $file->id]) }}"><i class="fa fa-pencil"></i></a>
                          <a class="btn btn-default btn-xs" href="{{ route('purchase_order.file.trash', ['' => $file->id]) }}"><i class="fa fa-trash"></i></a>
                        </div>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>

            <!-- Canvas for pdf -->
            <div id="pdf-canvas"></div>
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
              { "data": null,
                render: function ( data, type, row ) {
                  // var url = '{{ route("purchase_order.file.download", ["id" => ":id"]) }}',
                  //     _token = '{{ csrf_field() }}',
                  //     url = url.replace(':id', data.id),
                  //     file = '<form method="post" action="'+url+'">'+
                  //            _token+
                  //            '<button style="text-overflow:ellipsis;" stype="submit" class="btn-to-link text-to-white">'+data.file+'</button>'+
                  //            '</form>';
                  var data_id = data.id;
                  var data_file = data.file;
                  var data_to = data.to_data;
                      file = '<a id="file-'+data_id+'" style="text-overflow:ellipsis;cursor:pointer;"' +
                              'data-toggle="popover" data-container="body"' +
                              'onclick="trigger(\'' + data_id + '\', \'' + data_to + '\', \'' + data_file + '\')">'+
                              data.file+
                              '</a>';
                  return file;
                }
              },
              { "data": "po_number" },
              { "data": "from", "visible": false },
              { "data": "to" },
              { "data": "to_data", "visible": false },
              { "data": "progress" },
              { "data": "status" },
              { "data": "remarks" },
              { "data": "created_at", "visible": false },
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

<script>
  var currPage = 1; // Pages are 1-based not 0-based
  var numPages = 0;
  var thePDF = null;
  var watermark = null;
  var file_name = null;

  function trigger (data_id, data_to, data_file) {
    var to_splitted = data_to.split("-");
    var to_name = to_splitted[0];
    var to_id = to_splitted[1];
    var pre_file_path = null;
    file_name = data_file;

    if (to_name == "user") {
      pre_file_path = "files/user/" + to_id + "/";
    } else { pre_file_path = "files/company/" + to_id + "/"; }

    $("#pdf-canvas").html('');
    currPage = 1;
    numPages = 0;
    var url = pre_file_path + data_file;
    var url2 = '{!! url(\Storage::url("'+url+'")) !!}';
    showPDF(url2);

    Swal.fire({
      title: 'Please wait...',
      html: 'Downloading '+ data_file +'.',
      allowOutsideClick: false,
      onBeforeOpen: () => {
        Swal.showLoading();
      }
    });

    var data = {
                "_token": '{{ csrf_token() }}',
                "id": data_id
                };

    var url = '{{ route("purchase_order.file.download", ["id" => ":id"]) }}',
        _token = '{{ csrf_field() }}',
        url = url.replace(':id', data_id);
    
    $.ajax({
      url: url,
      type: 'post',
      data: data,
      success: function (response) {
        console.log(response);
      },
      error: function (err) {
        var err = err.responseJSON;
        console.log(err);
      }
    });
  }

  function showPDF (url) {
    //This is where you start
    PDFJS.getDocument(url).then(function(pdf) {
      //Set PDFJS global object (so we can easily access in our page functions
      thePDF = pdf;

      //How many pages it has
      numPages = pdf.numPages;

      //Start with first page
      pdf.getPage( 1 ).then( handlePages );
    });
  }

  function handlePages(page)
  {
      //This gives us the page's dimensions at full scale
      var viewport = page.getViewport(3);

      //We'll create a canvas for each page to draw it on
      var canvas = document.createElement( "canvas" );
      canvas.className = "pdf-canvas"+currPage;
      canvas.style.display = "none";
      var context = canvas.getContext('2d');
      canvas.height = viewport.height;
      canvas.width = viewport.width;

      //Draw it on the canvas
      page.render({canvasContext: context, viewport: viewport});

      //Add it to the web page
      document.getElementById("pdf-canvas").append( canvas );

      //Move to next page
      currPage++;

      if ( thePDF !== null && currPage <= numPages )
      {
          thePDF.getPage( currPage ).then( handlePages );
      }

      if (currPage > numPages) {
        // give 1 sec to append elements in div#pdf-canvas
        setTimeout(function(){ downloadPDF(); Swal.close(); }, 1000);
      }
  }

  // Download pdf
  function downloadPDF () {
    var canvasCount = $("#pdf-canvas > canvas").length;
    var doc = new jsPDF("p", "mm", "letter", true);

    for (i = 1; i <= canvasCount; i++) {
      var imgData = $('.pdf-canvas'+i).get(0).toDataURL();
      doc.addImage(imgData, 'JPEG', 0, 0, 215.9, 279.4,null,'FAST');
      if (i != canvasCount) { doc.addPage(); }
    }
    window.open(doc.output('bloburl'));
    // doc.output('dataurlnewwindow');
    // doc.save(file_name);
  }
</script>
@endpush
