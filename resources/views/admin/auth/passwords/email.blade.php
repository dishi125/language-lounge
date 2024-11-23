@extends('admin.layouts.auth')

@section('content')
<div class="card card-primary">
  
  <div class="card-header"><h4>{{ __('forms.reset.reset_password') }}</h4></div>

  <div class="card-body">
      <form method="POST" action="{{ route('password.email') }}">
          @csrf
          <div class="form-group">
              <label for="email">{{ __('forms.reset.email') }}</label>
              <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" tabindex="1" value="{{ old('email') }}" autofocus>
              <div class="invalid-feedback">
                {{ $errors->first('email') }}
              </div>
          </div>
          <div class="form-group">
              <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                {{ __('forms.reset.send_reset_link') }}
              </button>
          </div>
      </form>
  </div>
</div>
<div class="mt-5 text-muted text-center">
  {{ __('forms.reset.recall_login') }} <a href="{{ route('admin.auth.login') }}">{{ __('forms.reset.login') }}</a>
</div>
@endsection