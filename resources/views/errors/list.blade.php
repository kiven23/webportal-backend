@if (\Session::has('create_fail'))
  <div class="alert alert-dismissible alert-{{ \Session::get('create_fail.status') }}">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>{{ \Session::get('create_fail.title') }}</strong> {{ \Session::get('create_fail.message') }}
  </div>
@elseif (\Session::has('update_fail'))
  <div class="alert alert-dismissible alert-{{ \Session::get('update_fail.status') }}">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>{{ \Session::get('update_fail.title') }}</strong> {{ \Session::get('update_fail.message') }}
  </div>
@elseif (\Session::has('delete_fail'))
  <div class="alert alert-dismissible alert-{{ \Session::get('delete_fail.status') }}">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>{{ \Session::get('delete_fail.title') }}</strong> {{ \Session::get('delete_fail.message') }}
  </div>
@elseif (isset($_GET['err']) && $_GET['err'] == 1)
  <div class="alert alert-dismissible alert-danger">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>Delete failed! </strong> Another record depend to this item.
  </div>
@endif

@if (\Session::has('expired_password'))
  <div class="alert alert-dismissible alert-{{ \Session::get('expired_password.status') }}">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>{{ \Session::get('expired_password.title') }}</strong> {{ \Session::get('expired_password.message') }}
  </div>
@endif