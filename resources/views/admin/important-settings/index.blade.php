@extends('admin.layouts.app')
@section('title')
<title>{{ __('pages.important_settings.title') }} &mdash; {{ config('app.name', 'Laravel') }}</title>
@endsection

@section('styles')
<link rel="stylesheet" href="{!! asset('plugins/bootstrap-toggle/bootstrap4-toggle.min.css') !!}">
@endsection

@section('header-content')
<h1>{{ __('pages.important_settings.title') }}</h1>
@include('admin.layouts.partials.breadcrumb-section')
@endsection

@section('content')
{{--<div class="row">
    <div class="col-xl-12">
        <div class="contenttopbar">
            <ul class="d-flex align-content-center float-right assigned-order">
                <a href="{{ route('admin.users.add') }}"  class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ __('pages.user.add_banner') }}
                </a>
            </ul>
        </div>
    </div>
</div>--}}

<div class="row">
    <div class="col-lg-12 col-md-12 col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="settings_list" style="width:100%;">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script src="{!! asset('plugins/bootstrap-toggle/bootstrap4-toggle.min.js') !!}"></script>
<script src="{!! asset('plugins/jquery-ui/jquery-ui.js') !!}"></script>
<script type="text/javascript">
    $(document).ready(function () {
        settingsList();
    });

    function settingsList() {
        $('#settings_list').DataTable({
            // "order": [1, "desc"],
            "responsive": true,
            "searching": true,
            "processing": true,
            "serverSide": true,
            "deferRender": true,
            "lengthChange": true,
            "initComplete": function (settings, json) {},
            "ajax": {
                "url": "{{ route('admin.important-settings.list') }}",
                "dataType": "json",
                "type": "POST",
                "data": {
                    "_token": "{{ csrf_token() }}",
                },
                dataSrc: function ( json ) {
                    setTimeout(function() {
                        $('.toggle-btn').bootstrapToggle();
                    }, 300);
                    return json.data;
                }
            },
            createdRow: function(row, data, dataIndex) {
                $('.toggle-btn').bootstrapToggle();
            },
            "columns": [
                { data: "field", orderable: true },
                { data: "on_off", orderable: false }
            ]
        });
    }

    $(document).on('change','.onoff-toggle-btn',function(e){
        var dataID = $(this).attr('data-id');
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{!! route('admin.important-settings.update.on-off') !!}",
            data: {
                data_id: dataID,
                checked: e.target.checked,
                _token: "{{ csrf_token() }}",
            },
            success: function (response) {
                $('#settings_list').DataTable().ajax.reload();
            },
        });
    });
</script>
@endsection
