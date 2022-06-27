@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Dashboard
    </h1>
  </section>

  <section class="content">

    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Theme</h3>
      </div>
      <form action="{{ route('themes.update', ['id' => \Auth::user()->id]) }}" method="post">
        {{ csrf_field() }}
        <div class="box-body">
          <div class="row">
            <div class="col-md-6">
              <!-- START OF SKINS -->
              <div class="form-group">
                <label>Skins:</label>
                <div class="row">
                  <div class="col-md-6">
                    <!-- START OF DARK THEMES -->
                    <label><em><small>Dark Themes:</small></em></label>
                    <div class="checkbox">
                      <label>
                        <input type="radio"
                               value="skin-blue"
                               name="skin"
                               class="minimal"
                               {{ $theme ? ($theme->skin == "skin-blue" ? 'checked' : '') : '' }}>
                        Blue Black
                      </label>
                    </div>

                    <div class="checkbox">
                      <label>
                        <input type="radio"
                               value="skin-black"
                               name="skin"
                               class="minimal"
                               {{ $theme ? ($theme->skin == "skin-black" ? 'checked' : '') : '' }}>
                        Black
                      </label>
                    </div>

                    <div class="checkbox">
                      <label>
                        <input type="radio"
                               value="skin-purple"
                               name="skin"
                               class="minimal"
                               {{ $theme ? ($theme->skin == "skin-purple" ? 'checked' : '') : '' }}>
                        Purple Black
                      </label>
                    </div>

                    <div class="checkbox">
                      <label>
                        <input type="radio"
                               value="skin-green"
                               name="skin"
                               class="minimal"
                               {{ $theme ? ($theme->skin == "skin-green" ? 'checked' : '') : '' }}>
                        Green Black
                      </label>
                    </div>

                    <div class="checkbox">
                      <label>
                        <input type="radio"
                               value="skin-red"
                               name="skin"
                               class="minimal"
                               {{ $theme ? ($theme->skin == "skin-red" ? 'checked' : '') : '' }}>
                        Red Black
                      </label>
                    </div>

                    <div class="checkbox">
                      <label>
                        <input type="radio"
                               value="skin-yellow"
                               name="skin"
                               class="minimal"
                               {{ $theme ? ($theme->skin == "skin-yellow" ? 'checked' : '') : '' }}>
                        Yellow Black
                      </label>
                    </div>
                    <!-- END OF DARK THEMES -->
                  </div>

                  <div class="col-md-6">
                    <!-- START LIGHT THEMES -->
                    <label><em><small>Light Themes:</small></em></label>
                    <div class="checkbox">
                      <label>
                        <input type="radio"
                               value="skin-blue-light"
                               name="skin"
                               class="minimal"
                               {{ $theme ? ($theme->skin == "skin-blue-light" ? 'checked' : '') : '' }}>
                        Blue Light
                      </label>
                    </div>

                    <div class="checkbox">
                      <label>
                        <input type="radio"
                               value="skin-black-light"
                               name="skin"
                               class="minimal"
                               {{ $theme ? ($theme->skin == "skin-black-light" ? 'checked' : '') : 'checked' }}>
                        Light
                      </label>
                    </div>

                    <div class="checkbox">
                      <label>
                        <input type="radio"
                               value="skin-purple-light"
                               name="skin"
                               class="minimal"
                               {{ $theme ? ($theme->skin == "skin-purple-light" ? 'checked' : '') : '' }}>
                        Purple Light
                      </label>
                    </div>

                    <div class="checkbox">
                      <label>
                        <input type="radio"
                               value="skin-green-light"
                               name="skin"
                               class="minimal"
                               {{ $theme ? ($theme->skin == "skin-green-light" ? 'checked' : '') : '' }}>
                        Green Light
                      </label>
                    </div>

                    <div class="checkbox">
                      <label>
                        <input type="radio"
                               value="skin-red-light"
                               name="skin"
                               class="minimal"
                               {{ $theme ? ($theme->skin == "skin-red-light" ? 'checked' : '') : '' }}>
                        Red Light
                      </label>
                    </div>

                    <div class="checkbox">
                      <label>
                        <input type="radio"
                               value="skin-yellow-light"
                               name="skin"
                               class="minimal"
                               {{ $theme ? ($theme->skin == "skin-yellow-light" ? 'checked' : '') : '' }}>
                        Yellow Light
                      </label>
                    </div>
                    <!-- END OF LIGHT THEMES -->
                  </div>
                </div>
              </div>
              <!-- END OF SKINS -->

              <div class="form-group">
                <label>Sidebar Settings:</label>
                <div class="checkbox">
                  <label>
                    <input type="checkbox"
                           name="sidebar_mini"
                           class="minimal"
                           {{ $theme ? ($theme->sidebar_mini ? 'checked' : '') : 'checked' }}>
                    Sidebar Mini
                  </label>
                </div>
                <div class="checkbox">
                  <label>
                    <input type="checkbox"
                           name="sidebar_collapse"
                           class="minimal"
                           {{ $theme ? ($theme->sidebar_collapse ? 'checked' : '') : '' }}>
                    Sidebar Collapse
                  </label>
                </div>
                <div class="checkbox">
                  <label>
                    <input type="checkbox"
                           name="fixed"
                           class="minimal"
                           {{ $theme ? ($theme->fixed ? 'checked' : '') : 'checked' }}>
                    Fixed
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="box-footer">
          <div class="row">
            <div class="col-md-5">
              <button type="submit" class="btn btn-primary">Apply</button>
            </div>
          </div>
        </div>
      </form>
    </div>

    <input type="text" id="name" name="name" class="form-control hidden">
    <button id="btn" type="submit" class="btn btn-primary hidden">Submit</button>

    <script type="text/javascript">
      $(document).ready (function () {
        $('#btn').click(function () {
          $.ajax({
              type:'POST',
              url:'{{ route("test") }}',
              data:{_token: "{{ csrf_token() }}",
                    name: $('#name').val(),
              },
              success: function( msg ) {
                console.log(msg);
              }
          });
        });
      })
    </script>

  </section>
</div>
@stop

@push('scripts')
<script>
  //iCheck for checkbox and radio inputs
  $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
    checkboxClass: 'icheckbox_minimal-blue',
    radioClass   : 'iradio_minimal-blue'
  })
</script>
@endpush