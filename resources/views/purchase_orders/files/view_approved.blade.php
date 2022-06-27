@extends('layouts.app')

@section('title', 'Approved Purchase Order Files')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Purchase Order Files
      <small>Manage purchase order files</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Approved PO Files</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              Approved PO Files
            </h3>
          </div>
          <div class="box-body table-responsive">
            @include ('errors.list')
            @include ('successes.list')
            <table class="table table-bordered table-hover" id="file-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>File Name</th>
                  <th>File</th>
                  <th>From</th>
                  <th>To</th>
                  <th>To2</th>
                  <th>Progress</th>
                  <th>Status</th>
                  <th>Remarks</th>
                  <th>Seen By</th>
                  <th>Updated At</th>
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
                    <td>
                      @foreach ($file->seen_by_users as $key => $user)
                        <span>{{ $key + 1 }}.&nbsp;{{ $user->user->name }}</span><br>
                      @endforeach
                    </td>
                    <td>{{ $file->last_officer_approved->stamp }}</td>
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

    $('#file-table tbody').on('mouseover', 'tr', function () {
	    $('[data-toggle="tooltip"]').tooltip({
        trigger: 'hover',
        html: true,
	    });

	    $('[data-toggle="popover"]').popover({
        trigger: 'hover',
        html: true,
        placement: 'bottom',
	    });
		});

    $('#file-table').DataTable({
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'responsive'  : true,
      'scrollY'     : "300px",
      'columns': [
          { "data": "id" },
          { "data": "file", "visible": false },
          { "data": null,
            render: function ( data, type, row ) {
              var data_id = data.id;
              var data_to = data.to2;
              var data_file = data.file;
              var data_seen = data.seen_by_users;
              var data_datetime = data.updated_at;
                  file = '<a style="text-overflow:ellipsis;cursor:pointer;"' +
                          'data-toggle="popover" data-container="body"' +
                          'data-original-title="Seen By"' +
                          'data-content="'+data_seen+'"' +
                          'onclick="trigger(\'' + data_id + '\', \'' + data_to + '\', \'' + data_file + '\', \'' + data_datetime + '\')">'+
                          data.file+
                          '</a>';
              return file;
            }
          },
          { "data": "from" },
          { "data": "to" },
          { "data": "to2", "visible": false },
          { "data": "progress" },
          { "data": "status" },
          { "data": "remarks" },
          { "data": "seen_by_users", "visible": false },
          { "data": "updated_at", "visible": false },
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
  var currPage = 1; //Pages are 1-based not 0-based
  var numPages = 0;
  var thePDF = null;
  var watermark = null;
  var file_name = null;

  function trigger (data_id, data_to, data_file, data_datetime) {
    var to_splitted = data_to.split("-");
    var to_name = to_splitted[0];
    var to_id = to_splitted[1];
    var pre_file_path = null;
    file_name = data_file;

    var date = moment(data_datetime).format('MMDDYY');
    var time = moment(data_datetime).format('HHmm');
    var datetime = date + "-" + time;
    watermark = "sdc-" + datetime + "-" + data_id;

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
      doc.setTextColor(150);
      doc.setFontSize(21);
      doc.text(0, doc.internal.pageSize.height - 5, watermark.repeat(12));
      if (i != canvasCount) { doc.addPage(); }
    }
    window.open(doc.output('bloburl'));
    // doc.output('dataurlnewwindow');
    // doc.save(file_name);
  }
</script>
@endpush