@extends('admin.layouts.auth')

@section('content')
<div class="card card-primary">
  <div class="card-header"><h4>{{ __('forms.reset.email') }}</h4></div>

  <div class="card-body">
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
      <div class="form-group">
        <label for="email">{{ __('forms.reset.email') }}</label>
        <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" tabindex="1" value="{{ $email }}" readonly>
        <div class="invalid-feedback">
          {{ $errors->first('email') }}
        </div>
      </div>
      <div class="form-group">
        <label for="password" class="control-label">{{ __('forms.reset.password') }}</label>
        <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid': '' }}" name="password" tabindex="2">
        <div class="invalid-feedback">
          {{ $errors->first('password') }}
        </div>
      </div>
      <div class="form-group">
        <label for="password_confirmation" class="control-label">{{ __('forms.reset.confirm_password') }}</label>
        <input id="password_confirmation" type="password" class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid': '' }}" name="password_confirmation" tabindex="2">
        <div class="invalid-feedback">
          {{ $errors->first('password_confirmation') }}
        </div>
      </div>
      <div class="form-group">
        <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
          {{ __('forms.reset.set_new_password') }}
        </button>
      </div>
    </form>
  </div>
</div>
<div class="mt-5 text-muted text-center">
  {{ __('forms.reset.recall_login') }} <a href="{{ route('admin.auth.login') }}">{{ __('forms.reset.login') }}</a>
</div>
@endsection


