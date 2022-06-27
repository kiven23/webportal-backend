@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Customers
      <small>Manage customers</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Customers</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              Customer lists
            </h3>
          </div>
          <div class="box-body">
            @include ('errors.list')
            @include ('successes.list')

            @if (\Session::has('duplicates'))
              <h3>Duplicate entries found:</h3>
              <ol>
                @foreach(\Session::get('duplicates') as $i => $duplicate)
                  <li>{{ $duplicate->name }} - {{ $duplicate->contact_number }}</li>
                @endforeach
              </ol>
            @endif

            <div class="row">
              <div class="col-md-6">
                <a href="{{ route('customer.basic') }}" class="btn btn-primary">
                  <span class="fa fa-camera"></span>
                  Take a photo
                </a>

                @if (\Auth::user()->hasPermissionTo('Import Customers'))
                  <a href="{{ route('customer.import') }}" class="btn btn-default">
                    <span class="fa fa-upload"></span>
                    Import
                  </a>
                @endif
              </div>

              <div class="col-md-6">
                <div class="pull-right search">
                  <button onclick="event.preventDefault();
                          document.getElementById('search-form').submit();" class="btn btn-default" type="button">
                  Go!</button>
                </div>
                <div class="columns columns-right pull-right">
                  <form id="search-form" action="{{ route('customers') }}" method="post">
                    {{ csrf_field() }}
                    <input name="search_field"
                           type="text"
                           class="form-control"
                           value="{{ $search_field ? $search_field : '' }}"
                           placeholder="Search for..." />
                  </form>
                </div>
              </div>
            </div>
            <br>

            <div class="table-responsive">
              <table class="table table-bordered" id="camera-table">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Picture</th>
                    <th>Name</th>
                    <th>Birthday</th>
                    <th>Contact #</th>
                    <th>Address</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($customers as $index => $customer)
                    <tr>
                      <td>{{ $index + 1 }}</td>
                      <td>
                        <a href="{{ $customer->picture ? $customer->picture : asset('images/placeholders/customer-placeholder.png') }}" data-lightbox="image-1" data-title="{{ $customer->first_name }} {{ $customer->last_name }}">
                          <img src="{{ $customer->picture ? $customer->picture : asset('images/placeholders/customer-placeholder.png') }}" class="img-thumbnail" style="width: auto; height: .5in;">
                        </a>
                      </td>
                      <td>{{ $customer->title }} {{ $customer->first_name }} {{ $customer->last_name }} {{ $customer->suffix == 0 ? '' : $customer->suffix }}</td>
                      <td>{{ $customer->birth_date }}</td>
                      <td>{{ $customer->contact_number }}</td>
                      <td>{{ $customer->address }}</td>
                      <td>
                        <div class="btn-group">
                          <a href="{{ route('customer.edit', ['id' => $customer->id]) }}" class="btn btn-default btn-sm">
                            <span class="fa fa-pencil"></span>
                          </a>
                          <a href="{{ route('customer.files', ['id' => $customer->id]) }}" class="btn btn-default btn-sm"><span class="fa fa-file-text-o"></span></a>

                          <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                              <i class="fa fa-print"></i>&nbsp;&nbsp;<span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                              <li>
                                <a class="dropdown-item" href="{{ route('customer.printimage', ['id' => $customer->id]) }}" >2 x 2</a>
                              </li>
                              <li>
                                <a class="dropdown-item" href="{{ route('customer.printimage2', ['id' => $customer->id]) }}" >Ledger</a>
                              </li>
                              <li>
                                <a class="dropdown-item" href="{{ route('customer.printimage3', ['id' => $customer->id]) }}" >Application Form</a>
                              </li>
                            </ul>
                          </div>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>

              {{-- @if (!empty($search_customers) || $search_field == '')
                @if (Request::has('page') && Request::get('page') > 1)
                  <a class="btn btn-default" href="{{ route('customers', ['page' => \Request::get('page') - 1]) }}"><< Previous Page</a>
                @endif

                @if (Request::has('page'))
                  <a class="btn btn-default" href="{{ route('customers', ['page' => \Request::get('page') + 1]) }}">Next Page >></a>
                @else
                  <a class="btn btn-default" href="{{ route('customers', ['page' => 2]) }}">Next Page >></a>
                @endif
              @endif --}}

              {{ $customers->links() }}

            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<div class="row mb-4 mt-4">
  <div class="col-md-12">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
      <li class="breadcrumb-item active">Droplist</li>
    </ol>

    
  </div>
</div>
@endsection
