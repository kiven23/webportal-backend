@extends('layouts.app')

@section('title', 'Message Cast Settings')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Message Cast
      <small>Mange settings</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Settings</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">
              Settings
            </h3>
          </div>
          <form action="{{ route('setting.message_cast.update') }}" method="post">
						{{ csrf_field() }}
						<div class="box-body">
							<div class="row">
								<div class="col-md-5">
									@include ('errors.list')
									@include ('successes.list')

									<div class="row">
										<div class="col-md-6">
									    <div class="form-group {{ $errors->has('user') ? 'has-error' : '' }}">
									      <label for="user">user
													<i class="fa fa-asterisk text-danger" title="Required" style="cursor: pointer;"></i>
												</label>
												<input type="text" class="form-control" name="user" value="{{ $setting->user }}" placeholder="user">
												@if ($errors->has('user'))
													<span class="form-text- text-danger">
														{{ $errors->first('user') }}
													</span>
												@endif
									    </div>
									  </div>

									  <div class="col-md-6">
									    <div class="form-group {{ $errors->has('pass') ? 'has-error' : '' }}">
									      <label for="pass">pass
													<i class="fa fa-asterisk text-danger" title="Required" style="cursor: pointer;"></i>
												</label>
												<input type="text" class="form-control" name="pass" value="{{ $setting->pass }}" placeholder="pass">
												@if ($errors->has('pass'))
													<span class="form-text- text-danger">
														{{ $errors->first('pass') }}
													</span>
												@endif
									    </div>
									  </div>
									</div>

									<div class="row">
										<div class="col-md-6">
									    <div class="form-group {{ $errors->has('from') ? 'has-error' : '' }}">
									    	<label for="from">from
													<i class="fa fa-asterisk text-danger" title="Required" style="cursor: pointer;"></i>
												</label>
												<input type="text" class="form-control" name="from" value="{{ $setting->from }}" placeholder="from">
												@if ($errors->has('from'))
													<span class="form-text- text-danger">
														{{ $errors->first('from') }}
													</span>
												@endif
									    </div>
									  </div>
									  
									  <div class="col-md-6">
									    <div class="form-group {{ $errors->has('send_url') ? 'has-error' : '' }}">
									    	<label for="send_url">send url
													<i class="fa fa-asterisk text-danger" title="Required" style="cursor: pointer;"></i>
												</label>
												<input type="url" class="form-control" name="send_url" value="{{ $setting->send_url }}" placeholder="send_url">
												@if ($errors->has('send_url'))
													<span class="form-text- text-danger">
														{{ $errors->first('send_url') }}
													</span>
												@endif
									    </div>
									  </div>
									</div>
								</div>
							</div>
						</div>

						<div class="box-footer">
							<div class="row">
								<div class="col-md-5">
									<button type="submit" class="btn btn-danger"><i class="fa fa-save"></i>&nbsp;Save</button>
									<a href="{{ route('messages.message_casts') }}" class="btn btn-default"><i class="fa fa-envelope"></i>&nbsp;Compose Message</a>
								</div>
							</div>
						</div>
					</form>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
