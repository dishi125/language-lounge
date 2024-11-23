<div class="modal-dialog" style="max-width: 550px;">
    <div class="modal-content">
        <div class="modal-header justify-content-center">
            <h5>Edit User</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        </div>
        <div class="modal-body justify-content-center">
            <input name="user-id" id="user-id" type="hidden" value="{{ $user->id }}">
            <div class="align-items-xl-center mb-3">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="custom-checkbox custom-control">
                            <input type="checkbox" data-checkboxes="mygroup" data-checkbox-role="dad" class="custom-control-input check-admin-access" id="checkbox-admin-access" @if(isset($user->is_admin_access) && $user->is_admin_access==1) checked @endif>
                            <label for="checkbox-admin-access" class="custom-control-label">Is Admin Access</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="custom-checkbox custom-control d-flex">
                            <input type="checkbox" data-checkboxes="mygroup" data-checkbox-role="dad" class="custom-control-input check-tutor" id="checkbox-tutor" @if(isset($user->is_tutor) && $user->is_tutor==1) checked @endif>
                            <label for="checkbox-tutor" class="custom-control-label">Is Tutor</label>
                        </div>
                    </div>
                </div>
                @if($user->visit_status == "non_visit")
                <button type="button" class="btn btn-dark" onclick="activateUser({{$user->id}})" id="activate_btn">Activate</button>
                @else
                <button type="button" class="btn btn-dark" id="activate_btn" user-id="{{ $user->id }}" last-visited-at="{{ date("Y-m-d H:i:s", strtotime($user->last_visited_at)) }}"></button>
                @endif
            </div>
        </div>
        <div class="modal-footer">
{{--            <button type="button" class="btn btn-dark" data-dismiss="modal">{!! __(Lang::get('general.close')) !!}</button>--}}
        </div>
    </div>
</div>

<script>
    var remaining_Time = {{ $user->remaining_time ?? 0 }};
    var timer;
    var timer_Display = document.getElementById('activate_btn');
    var start_TimerButton = document.getElementById('activate_btn');
    // Start the timer automatically if remainingTime is greater than 0.
    if (remaining_Time > 0) {
        startRemainTimer();
    }

    function startRemainTimer() {
        // Disable the button after starting the timer to prevent multiple timers running simultaneously.
        start_TimerButton.disabled = true;

        const targetTime = new Date().getTime() + remaining_Time;

        // Update the timer every second.
        timer = setInterval(() => {
            const now = new Date().getTime();
            const remainingTime = targetTime - now;

            // Check if the timer is completed.
            if (remainingTime <= 0) {
                clearInterval(timer);
                timer_Display.innerText = 'Activate';
                start_TimerButton.disabled = false;
                $("#activate_btn").removeAttr('user-id');
                $("#activate_btn").removeAttr('last-visited-at');
                $("#activate_btn").attr('onclick','activateUser({{$user->id}})');
                $.ajax({
                    url: "{{ route('admin.user.change-visit-status') }}",
                    method: 'POST',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        user_id : "{{ $user->id }}",
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
                timer_Display.innerText = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }
        }, 1000);
    }
</script>
