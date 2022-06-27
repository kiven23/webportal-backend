<div class="row">
  <div class="col-md-6">
    <h2 style="margin: 0 0 15px 0;">Generate Reports</h2>

    @if (\Request::route()->getName() === 'report.overtime')
      <form id="form" name="form" action="{{ route('report.overtime') }}" method="get">
    @elseif (\Request::route()->getName() === 'breport.overtime')
      <form id="form" name="form" action="{{ route('breport.overtime') }}" method="get">
    @elseif (\Request::route()->getName() === 'report.dtr')
      <form id="form" name="form" action="{{ route('report.dtr') }}" method="get">
    @elseif (\Request::route()->getName() === 'breport.dtr')
      <form id="form" name="form" action="{{ route('breport.dtr') }}" method="get">
    @elseif (\Request::route()->getName() === 'report.biometric')
      <form id="form" name="form" action="{{ route('report.biometric') }}" method="get">
    @elseif (\Request::route()->getName() === 'breport.biometric')
      <form id="form" name="form" action="{{ route('breport.biometric') }}" method="get">
    @endif

      @if (\Auth::user()->branch->machine_number === 103)
        <div class="form-group">
          <label for="branch">Branch</label>
          <select name="branch" class="form-control select2">
            @foreach ($branches as $branch)
              <option
                value="{{ $branch->id }},{{ $branch->machine_number }}"
                {{ Illuminate\Support\Facades\Input::get('branch') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
            @endforeach
          </select>
        </div>
      @endif
      <div class="form-group">
        <label>Date range</label>
        <div class="input-group">
          <div class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </div>
          <input type="text" name="date_range" class="form-control" value="{{ $start_date }} - {{ $end_date }}" id="daterange">
        </div>
      </div>

      @if (\Request::route()->getName() === 'report.dtr' || \Request::route()->getName() === 'breport.dtr')
        <div class="form-group">
          <label>Payroll</label>
          <select class="form-control select2" name="payroll">
            <option {{ Illuminate\Support\Facades\Input::get('payroll') == 0 ? 'selected' : '' }} value="0">Cash</option>
            <option {{ Illuminate\Support\Facades\Input::get('payroll') == 1 ? 'selected' : '' }} value="1">ATM</option>
          </select>
        </div>
      @endif
      <button type="submit" class="btn btn-primary">
        <span class="fa fa-refresh"></span>
        Generate
      </button>
  	</form>
  </div>

  <div class="col-md-6">
    @if (\Auth::user()->branch->machine_number === 103)
      <h2 style="margin: 0 0 15px 0;">Import CSV File</h2>

      @if (Session::has('import_fail'))
        <div class="alert alert-danger">
          <em>{{ Session::get('import_fail') }}</em>
        </div>
      @elseif (Session::has('import_success'))
        <div class="alert alert-success">
          <em>{{ Session::get('import_success') }}</em>
        </div>
      @elseif (Session::has('import_require'))
        <div class="alert alert-danger">
          <em>{{ Session::get('import_require') }}</em>
        </div>
      @elseif (Session::has('import_invalid'))
        <div class="alert alert-danger">
          <em>{{ Session::get('import_invalid') }}</em>
        </div>
      @endif

      <form action="{{ route('report.import') }}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="form-group {{ $errors->has('file') ? 'has-danger' : '' }}">
          <label for="file">File</label>
          <input type="file" class="form-control {{ $errors->has('file') ? 'is-invalid' : '' }}" name="file">
          @if ($errors->has('file'))
            <p class="form-text text-danger">
              {{ $errors->first('file') }}
            </span>
          @endif
        </div>
        <button id="importbtn" class="btn btn-default">
          <span class="fa fa-upload"></span>
          Import
        </button>

        <span id="processing"><i class="fa fa-spinner fa-pulse fa-fw"></i> Please wait...</span>
      </form>
      <br>
    @endif
    <h2 style="margin: 0 0 15px 0;" class="mt-4">Biometrics</h2>
    <p><em>{{ $biometric_mindate }} - {{ $biometric_maxdate }}</em> was uploaded into our database.</p>
  </div>
</div>