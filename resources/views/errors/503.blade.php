@extends('errors.layout')
@section('message')
    <div class="row">
        <div class="col-md-12">
            <div class="text-center error-template">
                <img src="{{ asset('admin/img/stisla-fill.svg') }}" alt="logo" width="100" class="shadow-light rounded-circle">
                <h3> @lang('pages.503.sorry')</h3>
                <h4> @lang('pages.503.503_msg')</h4>
            </div>
        </div>
    </div>
@endsection
