@extends('layouts.app')

@section('title', 'Interview Schedules')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Scheduling
      <small>Manage interview schedules</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Interview Schedules</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              Interview schedules
            </h3>
            <div class="box-toolbar pull-right">
	            @if (\Auth::user()->hasPermissionTo('Create Interview Schedules'))
	              <a href="{{ route('interview_sched.create') }}" class="btn btn-primary btn-xs">
	                <i class="fa fa-plus"></i>&nbsp;ADD
	              </a>
		          @elseif (\Auth::user()->hasPermissionTo('Add Interview Schedules'))
	              <a href="{{ route('interview_sched.add') }}" class="btn btn-success btn-xs">
	                <i class="fa fa-plus"></i>&nbsp;ADD
	              </a>
		          @else
	          		<!-- <button onclick="exportpdf()" class="btn btn-danger btn-xs"><i class="fa fa-file-pdf-o"></i>&nbsp;Export PDF</button> -->
            	@endif
            </div>
          </div>
          <div class="box-body table-responsive">
          	@include ('errors.list')
						@include ('successes.list')

						<table class="table table-bordered table-hover" id="interview-sched-table">
							<thead>
								<tr>
									@if (\Auth::user()->branch->machine_number === 103)
										<th>Branch</th>
									@endif
                  <th>Applicant</th>
			            <th>Date Requested</th>
			            <th>Date of Interview</th>
			            <th>Contact Number</th>
			            <th>Position Applying For</th>
									<th>Approval Number</th>
									<th>Status</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($ischeds as $index => $isched)
									<tr>
										@if (\Auth::user()->branch->machine_number === 103)
			              	<td>{{ $isched->branch->name }}</td>
										@endif
                    <td>{{ $isched->applicant_name }}</td>
			              <td>{{ \Carbon\Carbon::parse($isched->created_at)->format('m/d/y') }}</td>
			              <td>{{ $isched->interview_date != null ? \Carbon\Carbon::parse($isched->interview_date)->format('m/d/y') : '' }}</td>
			              <td>{{ $isched->contact_number }}</td>
			              <td>{{ $isched->position_applying }}</td>
										<td>{{ $isched->approval_number }}</td>
										<td>
                      @if ($isched->status == 0)
                        <span class="label label-default">Pending</span>
                      @elseif ($isched->status == 1)
                        <span class="label label-success">Passed</span>
                      @else
                        <span class="label label-danger">Failed</span>
                      @endif
                    </td>
										<td>
                      <div class="btn-group">
                        @if (($isched->added_by_admin ||
                             \Auth::user()->hasPermissionTo('Edit Interview Schedules')) &&
                             $isched->status === 0)
                          <a href="{{ route('interview_sched.edit', ['id' => $isched->id]) }}" class="btn btn-default btn-xs" title="Edit"><i class="fa fa-pencil"></i></a>
                        @endif
                        @if (($isched->added_by_admin ||
                             \Auth::user()->hasPermissionTo('Delete Interview Schedules')) &&
                             $isched->status === 0)
                          <a href="{{ route('interview_sched.trash', ['id' => $isched->id]) }}" class="btn btn-default btn-xs" title="Delete"><i class="fa fa-trash"></i></a>
                        @endif
                        @if ($isched->status == 0 &&
                             \Auth::user()->hasPermissionTo('Complete Interview Schedules'))
                          <a href="{{ route('interview_sched.complete', ['id' => $isched->id]) }}" class="btn btn-default btn-xs" title=""><i class="fa fa-check-circle"></i></a>
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
    $('#interview-sched-table').DataTable({
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'order'       : [],
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

  function exportpdf() {
    var doc = new jsPDF('l','px','letter');
    var elem = document.getElementById("interview-sched-table");
    var res = doc.autoTableHtmlToJson(elem);

    var header = "ADDESSA CORPORATION";

    var title = 'Interview Schedules';

    doc.setFontSize(8);
    doc.setFontType('bold');
    doc.text(header, 250, 16);

    doc.setFontSize(8);
    doc.setFontType('bold');
    doc.text(title, 30, 46);

    doc.setFontSize(8);
    doc.setFontType('bold');

    doc.autoTable(res.columns, res.data,
                  {
                    startY: 55,
                    theme: 'grid',
                    styles: {
                      overflow: 'linebreak',
                      fontSize: 7,
                      fillColor: [255, 255, 255],
                      textColor: [0, 0, 0],
                      lineColor: [0, 0, 0],
                      lineWidth: .1,
                    }
                  },
                  );
    doc.output("dataurlnewwindow");
  }
</script>
@endpush
