@extends('admin.layouts.app')
@section('title')
    <title>Coupon &mdash; {{ config('app.name', 'Laravel') }}</title>
@endsection
@section('header-content')
    <h1>Coupon</h1>
    @include('admin.layouts.partials.breadcrumb-section')
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="contenttopbar">
                <ul class="d-flex align-content-center float-right assigned-order">
                    <button id="add_coupon" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Coupon
                    </button>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-12">
        <div class="card">
                <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="Coupon-table">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Image</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                </div>
            </div>
        </div>
    </div>
    <div class="cover-spin"></div>
@endsection

<div class="modal fade" id="addCouponModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addCouponForm" method="post">
                {{ csrf_field() }}
                <div class="modal-header justify-content-center">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body justify-content-center">
                    <div class="align-items-xl-center mb-3">
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <label>Title</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" name="title" id="title" class="form-control" required/>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <label>Image</label>
                            </div>
                            <div class="col-md-8">
                                <input type="file" name="image" id="image" class="form-control" required accept="image/*"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{!! __(Lang::get('general.close')) !!}</button>
                    <button type="submit" class="btn btn-primary" id="save_btn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('page-script')
    <script>
        var csrfToken = "{{ csrf_token() }}";
        var allTable = "{!! route('admin.coupon.table') !!}";
    </script>
    <script src="{!! asset('plugins/datatables/datatables.min.js') !!}"></script>
    <script>
        $(function() {
            var all = $("#Coupon-table").DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                deferRender: true,
                // "order": [[ 4, "desc" ]],
                ajax: {
                    url: allTable,
                    dataType: "json",
                    type: "POST",
                    data: { _token: csrfToken }
                },
                columns: [
                    { data: "title", orderable: false },
                    { data: "image", orderable: false },
                    { data: "copy_link", orderable: false },
                ]
            });
        });

        $(document).on('click', '#add_coupon', function (){
            $("#addCouponModal").modal('show');
        })

        $(document).on('submit', '#addCouponForm', function (e){
            e.preventDefault();
            var formData = new FormData($("#addCouponForm")[0]);

            $.ajax({
                url: "{{ url('admin/coupon/add') }}",
                processData: false,
                contentType: false,
                type: 'POST',
                data: formData,
                success:function(response){
                    $(".cover-spin").hide();
                    if(response.success == true){
                        $("#addCouponModal").modal('hide');
                        showToastMessage('Coupon added successfully.',true);
                        $('#Coupon-table').DataTable().ajax.reload();
                    }
                    else {
                        showToastMessage('Something went wrong!!',false);
                    }
                },
                beforeSend:function (){
                    $(".cover-spin").show();
                },
                error: function(response) {
                    $(".cover-spin").hide();
                    showToastMessage('Something went wrong!!',false);
                },
            });
        })

        $('#addCouponModal').on('hidden.bs.modal', function () {
            $("#addCouponForm")[0].reset();
        })
    </script>
@endsection