@extends('admin.layouts.app')
@section('title')
<title>{{ __('pages.banner.title') }} &mdash; {{ config('app.name', 'Laravel') }}</title>
@endsection
@section('header-content')
<h1>{{ __('pages.banner.title') }}</h1>
@include('admin.layouts.partials.breadcrumb-section')
@endsection
@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="contenttopbar">
            <ul class="d-flex align-content-center float-right assigned-order">
                <a href="{{ route('admin.users.add') }}"  class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ __('pages.user.add_banner') }}
                </a>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="user_list" style="width:100%;">
                        <thead>
                            <tr>
                                <th>{{ __('datatable.name') }}</th>
                                <th>{{ __('datatable.email') }}</th>
                                <th>{{ __('datatable.ticket') }}</th>
                                <th>{{ __('datatable.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>

@section('page-script')
<script type="text/javascript">
    $(document).ready(function () {
        //load user data into table
        // userList();
    });

    function userList() {
        // $('#user_listing').DataTable().ajax.reload();
        $('#user_list').DataTable({
            // "order": [0, "desc"],
            "responsive": true,
            "searching": true,
            "processing": true,
            "serverSide": true,
            "deferRender": true,
            "lengthChange": true,
            "initComplete": function (settings, json) {},
            "ajax": {
                "url": "{{ route('admin.users.list') }}",
                "dataType": "json",
                "type": "POST",
                "data": {
                    "_token": "{{ csrf_token() }}",
                },
            },
            "columns": [
                {"data": "name",orderable: true},
                {"data": "email",orderable: true},
                {"data": "ticket",orderable: true},
                {"data": "action",orderable: false}
            ]
        });
    }
</script>
@endsection
