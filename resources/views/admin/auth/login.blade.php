@extends('admin.layouts.auth')
@section('title')
<title>{{ __('pages.login.title') }} &mdash; {{ config('app.name', 'Laravel') }}</title>
@endsection
@section('content')
<div class="card card-primary">
  <div class="card-header"><h4>{{ __(Lang::get('pages.login.title')) }}</h4></div>

  <div class="card-body">
    <form method="POST" action="{{ route('admin.auth.postlogin') }}">
        @csrf
      <div class="form-group">
        <label for="email">{{ __(Lang::get('forms.login.email')) }}</label>
        <input aria-describedby="emailHelpBlock" id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" placeholder="{{ __(Lang::get('forms.login.email')) }}" tabindex="1" value="{{ old('email') }}" autofocus>
        <div class="invalid-feedback">
          {{ $errors->first('email') }}
        </div>
      </div>

      <div class="form-group">
        <div class="d-block">
            <label for="password" class="control-label">{{ __(Lang::get('forms.login.password')) }}</label>
          {{--<div class="float-right">
              @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-small">
                    {{ __(Lang::get('forms.login.forgot')) }}
                    </a>
              @endif
          </div>--}}
        </div>
        <input aria-describedby="passwordHelpBlock" id="password" type="password" placeholder="{{ __(Lang::get('forms.login.password')) }}" class="form-control{{ $errors->has('password') ? ' is-invalid': '' }}" name="password" tabindex="2">
        <div class="invalid-feedback">
          {{ $errors->first('password') }}
        </div>
      </div>

      <div class="form-group">
        <div class="custom-control custom-checkbox">
          <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember"{{ old('remember') ? ' checked': '' }}>
          <label class="custom-control-label" for="remember">{{ __(Lang::get('forms.login.remember')) }}</label>
        </div>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
          {{ __(Lang::get('forms.login.submit-btn')) }}
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
