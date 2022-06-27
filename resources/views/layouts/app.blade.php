<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Webportal | @yield('title')</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  
  <!-- jQuery 3 -->
  <script src="{{ asset('adminlte/bower_components/jquery/dist/jquery.min.js') }}"></script>

  <!-- PDFJS -->
  <script src="{{ asset('plugins/pdf/pdf.js') }}"></script>
  <script src="{{ asset('plugins/pdf/pdf.worker.js') }}"></script>

  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="{{ asset('adminlte/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
  <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('adminlte/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/bower_components/datatables.net-buttons-bs/css/buttons.bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/bower_components/datatables.net-fixedcolumns-bs/css/fixedColumns.bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/bower_components/datatables.net-responsive-bs/css/responsive.bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/bower_components/datatables.net-select-bs/css/select.bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/bower_components/datatables.net-editor-bs/css/editor.bootstrap.min.css') }}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('adminlte/bower_components/font-awesome/css/font-awesome.min.css') }}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="{{ asset('adminlte/bower_components/Ionicons/css/ionicons.min.css') }}">
  <!-- daterange picker -->
  <link rel="stylesheet" href="{{ asset('adminlte/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
  <!-- bootstrap datepicker -->
  <link rel="stylesheet" href="{{ asset('adminlte/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/iCheck/all.css') }}">
  <!-- Bootstrap Color Picker -->
  <link rel="stylesheet" href="{{ asset('adminlte/bower_components/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css') }}">
  <!-- Bootstrap time Picker -->
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/timepicker/bootstrap-timepicker.min.css') }}">
  <!-- Select2 -->
  <link rel="stylesheet" href="{{ asset('adminlte/bower_components/select2/dist/css/select2.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('adminlte/dist/css/AdminLTE.min.css') }}">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="{{ asset('adminlte/dist/css/skins/_all-skins.min.css') }}">

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

  <!-- Custom CSS -->
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">

  <!-- Selectize CSS - For Message Cast -->
  <link href="{{ asset('selectize.js-master/dist/css/selectize.legacy.css') }}" rel="stylesheet">

  <!-- Popper JS -->
  <script src="{{ asset('bootswatch.v4.0.0-beta.2/js/popper.min.js') }}"></script>

  <!-- Croppie -->
  <script src="{{ asset('bower_components/exif-js/exif.js') }}"></script>
  <script src="{{ asset('bower_components/Croppie/croppie.min.js') }}"></script>
  <link href="{{ asset('bower_components/Croppie/croppie.css') }}" rel="stylesheet">

  <!-- Lightbox -->
  <link href="{{ asset('bower_components/lightbox2/dist/css/lightbox.min.css') }}" rel="stylesheet">

  <!-- Bootstrap star rating -->
  <link href="{{ asset('bower_components/bootstrap-star-rating/css/star-rating.css') }}" rel="stylesheet">
  <link href="{{ asset('bower_components/bootstrap-star-rating/themes/krajee-uni/theme.css') }}" media="all" rel="stylesheet" type="text/css" />

  <!-- ON-OFF-Toggle-Switches-Switcher -->
  <link href="{{ asset('plugins/ON-OFF-Toggle-Switches-Switcher/css/switcher.css') }}" rel="stylesheet">

  <!-- SweetAlert -->
  <link href="{{ asset('plugins/sweetalert2-8.6.0/dist/sweetalert2.min.css') }}" rel="stylesheet">

  <!-- DataTables Editor -->
  <!-- <link rel="stylesheet" href="{{ asset('plugins/editor/css/dataTables.editor.css') }}"> -->
  <!-- <link rel="stylesheet" href="{{ asset('plugins/editor/css/editor.bootstrap.css') }}"> -->

  <!-- WebCam -->
  <script type="text/javascript" src="{{ asset('WebCam/webcam.min.js') }}"></script>

  <!-- Bootstrap Table JS -->
  <script src="{{ asset('bower_components/bootstrap-table/dist/bootstrap-table-custom.js') }}"></script>
  <script src="{{ asset('bower_components/bootstrap-table/dist/extensions/export/bootstrap-table-export-custom.js') }}"></script>
  <script src="{{ asset('bower_components/bootstrap-table/dist/extensions/export/tableexport.js') }}"></script>
  <script src="{{ asset('bower_components/bootstrap-table/dist/extensions/export/FileSaver.min.js') }}"></script>

  <script src="{{ asset('bower_components/jspdf/dist/jspdf.min.js') }}"></script>
  <script src="{{ asset('bower_components/jspdf-autotable/dist/jspdf.plugin.autotable.min.js') }}"></script>

  <!-- jQuery Datetime Picker -->
  <link rel="stylesheet" type="text/css" href="{{ asset('bower_components/datetimepicker-2.5.20/build/jquery.datetimepicker.min.css') }}">
  <script src="{{ asset('bower_components/datetimepicker-2.5.20/build/jquery.datetimepicker.full.min.js') }}"></script>

</head>
<body class="hold-transition
            {{ \Auth::check() ?
              (\Auth::user()->theme ? \Auth::user()->theme->skin . ' ' .
              (\Auth::user()->theme->sidebar_mini ? 'sidebar-mini' : '') . ' ' .
              (\Auth::user()->theme->sidebar_collapse ? 'sidebar-collapse' : '') . ' ' .
              (\Auth::user()->theme->fixed ? 'fixed' : '') : 'skin-black-light sidebar-mini fixed') :
              'login-page'
            }}">

<div id="app" class="wrapper">

  @if (\Auth::check())
    @include('includes.nav')
    @include('includes.sidenav')
  @endif

  @yield('content')

  @if (\Auth::check())
    @include('includes.footer')
  @endif
 
</div>

  <!-- Bootstrap 3.3.7 -->
  <script src="{{ asset('adminlte/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
  <!-- iCheck -->
  <script src="{{ asset('adminlte/plugins/iCheck/icheck.min.js') }}"></script>
  <!-- DataTables -->
  <script src="{{ asset('adminlte/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('adminlte/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
  <script src="{{ asset('adminlte/bower_components/datatables.net-buttons-bs/js/buttons.bootstrap.min.js') }}"></script>
  <script src="{{ asset('adminlte/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
  <script src="{{ asset('adminlte/bower_components/jszip/dist/jszip.min.js') }}"></script>
  <script src="{{ asset('adminlte/bower_components/pdfmake/build/pdfmake.min.js') }}"></script>
  <script src="{{ asset('adminlte/bower_components/pdfmake/build/vfs_fonts.js') }}"></script>
  <script src="{{ asset('adminlte/bower_components/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
  <script src="{{ asset('adminlte/bower_components/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
  <script src="{{ asset('adminlte/bower_components/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>
  <script src="{{ asset('adminlte/bower_components/datatables.net-fixedcolumns/js/dataTables.fixedColumns.min.js') }}"></script>
  <script src="{{ asset('adminlte/bower_components/datatables.net-fixedcolumns-bs/js/fixedColumns.bootstrap.min.js') }}"></script>
  <script src="{{ asset('adminlte/bower_components/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
  <script src="{{ asset('adminlte/bower_components/datatables.net-responsive-bs/js/responsive.bootstrap.min.js') }}"></script>
  <script src="{{ asset('adminlte/bower_components/datatables.net-select/js/dataTables.select.min.js') }}"></script>
  <script src="{{ asset('adminlte/bower_components/datatables.net-select-bs/js/select.bootstrap.min.js') }}"></script>
  <script src="{{ asset('adminlte/bower_components/datatables.net/plugins/1.10.19/dataRender/ellipsis.js') }}"></script>
  <!-- date-range-picker -->
  <script src="{{ asset('adminlte/bower_components/moment/min/moment.min.js') }}"></script>
  <script src="{{ asset('adminlte/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
  <!-- bootstrap datepicker -->
  <script src="{{ asset('adminlte/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
  <!-- bootstrap color picker -->
  <script src="{{ asset('adminlte/bower_components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}"></script>
  <!-- bootstrap time picker -->
  <script src="{{ asset('adminlte/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
  <!-- SlimScroll -->
  <script src="{{ asset('adminlte/bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
  <!-- FastClick -->
  <script src="{{ asset('adminlte/bower_components/fastclick/lib/fastclick.js') }}"></script>
  <!-- Select2 -->
  <script src="{{ asset('adminlte/bower_components/select2/dist/js/select2.min.js') }}"></script>
  <script src="{{ asset('adminlte/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
  <script src="{{ asset('adminlte/bower_components/select2/dist/js/customAdapter.js') }}"></script>
  <!-- AdminLTE App -->
  <script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>

  <!-- DataTables Editor -->
  <!-- <script src="{{ asset('plugins/editor/js/dataTables.editor.js')}}"></script> -->
  <!-- <script src="{{ asset('plugins/editor/js/editor.bootstrap.min.js')}} "></script> -->

  <script>
    $(document).ready(function () {
      $('.sidebar-menu').tree()
    })
  </script>

  <!-- Selectize JS - For Message Cast -->
  <script src="{{ asset('selectize.js-master/dist/js/standalone/selectize.js') }}"></script>
  <script src="{{ asset('selectize.js-master/examples/js/index.js') }}"></script>

  <!-- HighCharts -->
  <script src="{{ asset('bower_components/highcharts/highcharts.js') }}" type="text/javascript"></script>
  <script src="{{ asset('bower_components/highcharts/highcharts-3d.js') }}" type="text/javascript"></script>
  <script src="{{ asset('bower_components/highcharts/highmaps.js') }}" type="text/javascript"></script>
  <script src="{{ asset('bower_components/highcharts/highstock.js') }}" type="text/javascript"></script>
  <script src="{{ asset('bower_components/highcharts/modules/drilldown.js') }}" type="text/javascript"></script>
  <!-- End of HighCharts -->

  <!-- Lightbox -->
  <script src="{{ asset('bower_components/lightbox2/dist/js/lightbox.min.js') }}"></script>

  <!-- Bootstrap star rating -->
  <script src="{{ asset('bower_components/bootstrap-star-rating/js/star-rating.js') }}" type="text/javascript"></script>
  <script src="{{ asset('bower_components/bootstrap-star-rating/themes/krajee-uni/theme.js') }}" type="text/javascript"></script>

  <!-- Custom DataTables AJAX CRUD -->
  <script src="{{ asset('plugins/dataTables.jaa.ajaxcrud/dataTables.jaa.ajaxcrud.js') }}"></script>

  <!-- Bug Fixes -->
  <script src="{{ asset('js/fixes/fixes.min.js') }}"></script>

  <!-- ON-OFF-Toggle-Switches-Switcher -->
  <script src="{{ asset('plugins/ON-OFF-Toggle-Switches-Switcher/js/jquery.switcher.js') }}"></script>

  <!-- SweetAlert -->
  <script src="{{ asset('plugins/sweetalert2-8.6.0/dist/sweetalert2.all.min.js') }}"></script>
  <!-- Include this after the sweet alert js file -->
  @include('sweet::alert')

  <script>
    // Clickable Table
    $('table').on('click', '.clickable-row', function(event) {
      $(this).addClass('active').siblings().removeClass('active');
    });
  </script>

  <script>
    lightbox.option({
      'imageFadeDuration': 0
    });
  </script>

  @stack('scripts')
</body>
</html>