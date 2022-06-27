@if (\Session::has('create_success'))
  <div class="alert alert-dismissible alert-{{ \Session::get('create_success.status') }}">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>{{ \Session::get('create_success.title') }}</strong> {{ \Session::get('create_success.message') }}
  </div>
@elseif (\Session::has('update_success'))
  <div class="alert alert-dismissible alert-{{ \Session::get('update_success.status') }}">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>{{ \Session::get('update_success.title') }}</strong> {{ \Session::get('update_success.message') }}
  </div>
@elseif (\Session::has('delete_success'))
  <div class="alert alert-dismissible alert-{{ \Session::get('delete_success.status') }}">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>{{ \Session::get('delete_success.title') }}</strong> {{ \Session::get('delete_success.message') }}
  </div>
@endif
