@extends('admin.layouts.app')
@section('title')
<title>{{ __('pages.user.title') }} &mdash; {{ config('app.name', 'Laravel') }}</title>
@endsection
@section('header-content')
<h1>{{ __('pages.user.title') }}</h1>
@include('admin.layouts.partials.breadcrumb-section')
@endsection
@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="contenttopbar">
            <ul class="d-flex align-content-center float-right assigned-order">
                <a href="{{ route('admin.users.add') }}"  class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ __('pages.user.add_user') }}
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
                                <th>{{ __('datatable.signup_date') }}</th>
                                <th>{{ __('datatable.email') }}</th>
                                <th>{{ __('datatable.ticket') }}</th>
                                <th>{{ __('datatable.action') }}</th>
                                <th></th>
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

<!-- Modal -->
<div class="modal fade" id="UserDeleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
</div>

<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>

<div class="modal fade" id="editPasswordModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" style="max-width: 550px;">
        <div class="modal-content">
            <div class="modal-header justify-content-center">
                <h5>Edit Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body justify-content-center">
                <form id="editPasswordForm" style="width: 100%;" method="POST" action="" accept-charset="UTF-8">
                    @csrf
                    <input type="hidden" name="user_id" value="">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="new_password">New Password</label>
                                <input type="password" class="form-control" name="new_password" required id="new_password" />
                            </div>
                        </div>
                    </div>

                    <button type="submit" id="form_submit" class="btn btn-primary" style="float: right">Save</button>
                </form>
            </div>
            <div class="modal-footer">
                {{--            <button type="button" class="btn btn-dark" data-dismiss="modal">{!! __(Lang::get('general.close')) !!}</button>--}}
            </div>
        </div>
    </div>
</div>

@section('page-script')
<script type="text/javascript">
    let user_status_timer;
    $(document).ready(function () {
        //load user data into table
        userList();
    });

    function userList() {
        // $('#user_listing').DataTable().ajax.reload();
        $('#user_list').DataTable({
            "order": [1, "desc"],
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
                {"data": "signup_date",orderable: true},
                {"data": "email",orderable: true},
                {"data": "ticket",orderable: true},
                {"data": "action",orderable: false},
                {"data": "edit_password",orderable: false}
            ]
        });
    }

    function editUser(user_id) {
        $.get(baseUrl + '/admin/user/edit/' + user_id, function (data, status) {
            $("#editUserModal").html('');
            $("#editUserModal").html(data);
            $("#editUserModal").modal('show');
        });
    }

    function editPassword(user_id) {
        $("#editPasswordForm")[0].reset();
        $("#editPasswordModal").find('input[name="user_id"]').val(user_id);
        $("#editPasswordModal").modal('show');
    }

    $(document).on("submit","#editPasswordForm",function(e){
        e.preventDefault();
        var ajaxurl = "{{ url('admin/user/edit/password') }}";

        $.ajax({
            method: 'POST',
            cache: false,
            data: $(this).serialize(),
            url: ajaxurl,
            success: function(results) {
                $(".cover-spin").hide();

                if(results.success == true) {
                    iziToast.success({
                        title: '',
                        message: results.message,
                        position: 'topRight',
                        progressBar: false,
                        timeout: 1000,
                    });

                    $("#editPasswordModal").modal('hide');
                }
                else {
                    iziToast.error({
                        title: '',
                        message: results.message,
                        position: 'topRight',
                        progressBar: false,
                        timeout: 2000,
                    });
                }
            },
            beforeSend: function(){ $(".cover-spin").show(); },
            error: function(response) {
                $(".cover-spin").hide();
                if( response.responseJSON.success === false ) {
                    var errors = response.responseJSON.errors;

                    $.each(errors, function (key, val) {
                        console.log(val);
                        var errorHtml = '<label class="error">'+val+'</label>';
                        $('#'+key).parent().append(errorHtml);
                    });
                }
            }
        });
    });

    $(document).on('click', '#checkbox-admin-access', function(event) {
        var access;
        if (this.checked) {
            this.checked = true;
            access = 1;
        } else {
            this.checked = false;
            access = 0;
        }

        // console.log(access);
        $.ajax({
            url: "{{ route('admin.user.edit-admin-access') }}",
            method: 'POST',
            data: {
                '_token': "{{ csrf_token() }}",
                user_id : $("#user-id").val(),
                is_admin_access : access,
            },
            beforeSend: function() {
            },
            success: function(res) {
                showToastMessage(res.message, res.success);
            },
            error: function(response) {
                showToastMessage("Failed to update admin access.",false);
            }
        });
    });

    $(document).on('click', '#checkbox-tutor', function(event) {
        var access;
        if (this.checked) {
            this.checked = true;
            access = 1;
        } else {
            this.checked = false;
            access = 0;
        }

        // console.log(access);
        $.ajax({
            url: "{{ route('admin.user.edit-is-tutor') }}",
            method: 'POST',
            data: {
                '_token': "{{ csrf_token() }}",
                user_id : $("#user-id").val(),
                is_tutor : access,
            },
            beforeSend: function() {
            },
            success: function(res) {
                showToastMessage(res.message, res.success);
            },
            error: function(response) {
                showToastMessage("Failed to update tutor access.",false);
            }
        });
    });

    function removeUser(id) {
        var pageModel = $("#UserDeleteModal");

        $.get("{{ url('admin/user/delete') }}" + "/" + id, function(data, status) {
            pageModel.html('');
            pageModel.html(data);
            pageModel.modal('show');
        });
    }

    function activateUser(user_id){
        startTimer(user_id);
        $.ajax({
            url: "{{ route('admin.user.change-visit-status') }}",
            method: 'POST',
            data: {
                '_token': "{{ csrf_token() }}",
                user_id : user_id,
                type: "activate"
            },
            beforeSend: function() {
            },
            success: function(res) {
                showToastMessage(res.message, res.success);
                // $("#editUserModal").modal('hide');
            },
            error: function(response) {
                showToastMessage("Failed to activate user!!",false);
            }
        });
    }

    $('#editUserModal').on('hidden.bs.modal', function () {
        clearInterval(timer);
        clearInterval(user_status_timer);
    });

    function startTimer(user_id){
        const startTimerButton = document.getElementById('activate_btn');
        const timerDisplay = document.getElementById('activate_btn');
        // Disable the button after starting the timer to prevent multiple timers running simultaneously.
        startTimerButton.disabled = true;

        // Set the target time to 6 hours from now.
        const targetTime = new Date().getTime() + 6 * 60 * 60 * 1000;
        // const targetTime = new Date().getTime() + 5 * 60 * 1000;

        // Update the timer every second.
        user_status_timer = setInterval(() => {
            const now = new Date().getTime();
            const remainingTime = targetTime - now;

            // Check if the timer is completed.
            if (remainingTime <= 0) {
                clearInterval(user_status_timer);
                timerDisplay.innerText = 'Activate';
                startTimerButton.disabled = false;
                $.ajax({
                    url: "{{ route('admin.user.change-visit-status') }}",
                    method: 'POST',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        user_id : user_id,
                        type: "non_visit"
                    },
                    beforeSend: function() {
                    },
                    success: function(res) {
                        // showToastMessage(res.message, res.success);
                    },
                    error: function(response) {
                        showToastMessage("Failed to de-activate user!!",false);
                    }
                });
            } else {
                // Calculate hours, minutes, and seconds from remaining milliseconds.
                const hours = Math.floor(remainingTime / (60 * 60 * 1000));
                const minutes = Math.floor((remainingTime % (60 * 60 * 1000)) / (60 * 1000));
                const seconds = Math.floor((remainingTime % (60 * 1000)) / 1000);

                // Display the remaining time in HH:MM:SS format.
                timerDisplay.innerText = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }
        }, 1000);
    }
</script>
@endsection
