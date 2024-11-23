@extends('admin.layouts.auth')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="text-center error-template">
                <h1>
                    @lang('pages.500.oops')</h1>
                <h2>@lang('pages.500.500_msg')</h2>
            </div>
        </div>
    </div>
    <div class="row error-actions">
        <div class=" col-md-6">
            <a href="{{ redirect()->back()->getTargetUrl() }}" class="mt-3 btn-block btn btn-primary btn-lg">
                <i class="fas fa-arrow-left"></i>
                @lang('pages.500.go_back') </a>
        </div>
        <div class=" col-md-6">

            <a href="{{ route('admin.dashboard') }}" class="mt-3 btn-block btn btn-primary btn-lg">
                <span class="color-white fa fa-home"></span>
                @lang('pages.500.take_me_home') </a>
        </div>
    </div>
@endsection
