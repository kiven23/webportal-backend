@extends('layouts.app')

@section('title', 'Inventory Reconciliations')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Reconciliation
      <small>Manage inventory reconciliation</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('inventories') }}">Inventory Recon</a></li>
      <li class="active">View</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              View inventory recon
            </h3>
          </div>
          <div class="box-body table-responsive">
            <table class="table table-bordered table-hover" id="invty-recon-table">
              <thead>
                <tr>
                  <th>BRAND</th>
                  <th>MODEL</th>
                  <th>SERIAL</th>
                  <th>QUANTITY</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($inventory_maps as $inventory_map)
                  <tr class="clickable-row">
                    <td><pre>{{ $inventory_map['brand'] }}</pre></td>
                    <td><pre>{{ $inventory_map['model'] }}</pre></td>
                    <td>
                      @if ($inventory_map['serial_branch'])
                      <?php

                        $serials_branch = explode(',', $inventory_map['serial_branch']);
                        $numItems = count($serials_branch);
                        if ($inventory_map['serial_branch']) {
                          $i = 0;
                          foreach ($serials_branch as $key => $serial_branch) {
                            echo '<span class="text-danger">'. $serial_branch;
                            if(++$i !== $numItems) {
                              echo ',</span><br>';
                            } else {
                              echo '</span><br>';
                            }
                          }
                        } else {
                          echo '<span class="text-danger">NONE</span>';
                        }

                      ?>
                      @endif
                    </td>
                    <td>
                      <span class="text-danger">{{ $inventory_map['quantity_branch'] }}</span>
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
    $('#invty-recon-table').DataTable({
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : true,
      'columnDefs': [
          { 'width': 220, 'targets': 0 },
          { 'width': 200, 'targets': 1 },
          { 'width': 200, 'targets': 2 },
          { 'width': 100, 'targets': 3 },
      ],
      'scrollY'       : "300px",
      'scrollX'       : true,
      'scrollCollapse': true,
      'fixedHeader'   : true,
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
