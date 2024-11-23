@extends('admin.layouts.app')
@section('title')
<title>{{ __('pages.change_password.title') }} &mdash; {{ config('app.name', 'Laravel') }}</title>
@endsection
@section('header-content')
<h1>{{ __('pages.change_password.title') }}</h1>
@endsection
@section('content')    

<div class="card">
    <div class="card-body">
        <form method="post" name="change-password-form" id="change-password-form" action="{{ route('admin.submit.change.password') }}">
            @csrf
            
            <div class="row">
                <div class="col-lg-2">
                    <div class="form-group">
                        <label>{{ __('forms.change_password.old_password') }}</label>
                    </div>
                </div>
                <div class="col-lg-8">
                    <input class="form-control" type="password"  name="old_password" id="old_password" value="" placeholder="{{ __('forms.change_password.old_password') }}">
                    <span class="text-danger">{{ ($errors->has('old_password'))?$errors->first('old_password'):"" }}</span>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-2">
                    <div class="form-group">
                        <label>{{ __('forms.change_password.new_password') }}</label>
                    </div>
                </div>
                <div class="col-lg-8">
                    <input class="form-control" type="password" name="new_password" id="new_password" value="" placeholder="{{ __('forms.change_password.new_password') }}">
                    <span class="text-danger">{{ ($errors->has('new_password'))?$errors->first('new_password'):"" }}</span>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-2">
                    <div class="form-group">
                        <label>{{ __('forms.change_password.confirm_password') }}</label>
                    </div>
                </div>
                <div class="col-lg-8">
                    <input class="form-control" type="password" id="confirm_password" name="confirm_password"value="" placeholder="{{ __('forms.change_password.confirm_password') }}">
                    <span class="text-danger">{{ ($errors->has('confirm_password'))?$errors->first('confirm_password'):"" }}</span>
                </div>
            </div>
            

            <div class="row">
                <div class="col-lg-2"></div>
                <div class="col-lg-6">
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
    jQuery.validator.addMethod("notEqual", function(value, element, param) {
        console.log("result",(value == $(param).val()));
        return (value != $(param).val());
    }, "New password should not same as old password");
    
    $('#change-password-form').validate({
        rules: {
            'old_password': {
                required: true,
            },
            'new_password': {
                required: true,
                notEqual : "#old_password"
            },
            'confirm_password': {
                required: true,
                equalTo : "#new_password"
            },
        }, messages: {
            old_password: {
                required: "{{ __('admin_message.old_pwd_field_require') }}"
            },
            new_password: {
                required: "{{ __('admin_message.new_pwd_field_require') }}",
                notEqual: "{{ __('admin_message.new_pwd_not_same_old_pwd') }}",
            },
            confirm_password: {
                required: "{{ __('admin_message.confirm_pwd_field_require') }}",
                equalTo: "{{ __('admin_message.confirm_pwst_same_new_pwd') }}",
            }
        },
        submitHandler: function(form) { // <- pass 'form' argument in
            $(".btn-submit").attr("disabled", true);
            form.submit(); // <- use 'form' argument here.
        }
    });

</script>
@endsection
