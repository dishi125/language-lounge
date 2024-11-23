@extends('admin.layouts.app')
@section('title')
<title>{{ $formTitle }} &mdash; {{ config('app.name', 'Laravel') }}</title>
@endsection
@section('header-content')
<h1>{{ $formTitle }}</h1>
@include('admin.layouts.partials.breadcrumb-section')
@endsection
@section('content')
<div class="card">
    <div class="card-body">
        <form method="post" name="user-form" id="user-form" action="{{ route('admin.users.save') }}">
            @csrf
            <div class="row">
                <div class="col-12 col-md-4 col-lg-4">
                    <div class="form-group mb-35">
                        <label>{{ __('forms.user.email') }} <span class="required">*</span></label>
                        <input class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" type="text" name="email">
                        @if ($errors->has('email'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-12 col-md-4 col-lg-4">
                    <div class="form-group mb-35">
                        <label>{{ __('forms.user.name') }} <span class="required">*</span></label>
                        <input class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" type="text" name="name">
                        @if ($errors->has('name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-12 col-md-4 col-lg-4">
                    <div class="form-group mb-35">
                        <label>{{ __('forms.user.password') }} <span class="required">*</span></label>
                        <input class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" type="password" name="password">
                        @if ($errors->has('password'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-12 col-md-4 col-lg-4">
                    <div class="form-group mb-35">
                        <label>{{ __('forms.user.conf_password') }} <span class="required">*</span></label>
                        <input class="form-control {{ $errors->has('conf_password') ? ' is-invalid' : '' }}" name="conf_password" type="password">
                        @if ($errors->has('conf_password'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('conf_password') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-12 col-md-4 col-lg-4">
                    <div class="form-group mb-35">
                        <label>{{ __('forms.user.ticket_level') }} <span class="required">*</span></label>
                        <select name="ticket_level" class="form-control {{ $errors->has('ticket_level') ? ' is-invalid' : '' }}">
                            <option value="no_ticket">No ticket</option>
                            <option value="language_lounge_gold" selected>Language lounge gold</option>
                            <option value="language_lounge_platinum">Language lounge platinum</option>
                        </select>
                        @if ($errors->has('ticket_level'))
                            <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('ticket_level') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>


                <div class="col-12 col-lg-12">
                    <button id="submit_form" type="submit" class="btn btn-primary btn-submit">{{ __('general.submit') }}</button>
                    <button type="reset" class="btn btn-outline-secondary mr-1">{{ __('general.reset') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('page-script')
<script src="{!! asset('admin/js/jquery-validation/jsvalidation.min.js') !!}"></script>
<script type="text/javascript">
    $('#user-form').validate({
        rules: {
            'email': {
                required: true,
            },
            'name': {
                required: true
            },
            'password': {
                required: true
            },
            'conf_password': {
                required: true
            }
        }, messages: {
            email: {
                required: "{{ __('forms.user.validate_email') }}"
            },
            name: {
                required: "{{ __('forms.user.validate_name') }}"
            },
            password: {
                required: "{{ __('forms.user.validate_password') }}"
            },
            conf_password: {
                required: "{{ __('forms.user.validate_conf_password') }}"
            },
        },
        submitHandler: function(form) { // <- pass 'form' argument in
            $(".btn-submit").attr("disabled", true);
            form.submit(); // <- use 'form' argument here.
        }
    });
</script>
@endsection
