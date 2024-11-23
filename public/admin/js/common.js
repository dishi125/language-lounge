function deleteFire(route = "",data = {}, tableId = "",confirmMsg = ""){

    data._token = $('meta[name="csrf-token"]').attr('content');
    
    Swal.fire({
        title: commonMessages.areYouSure,
        text: confirmMsg,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: commonMessages.cancel,
        confirmButtonText: commonMessages.deleteYes
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                method: "POST",
                url: route,
                data: data,
                beforeSend:function(res) {
                    laravel.startLoader();
                },
                success: function (res) {
                    if(res.status) {
                        $('#'+tableId).DataTable().ajax.reload();
                        Swal.fire(commonMessages.deleted,res.message,'success');
                    } else {
                        Swal.fire(commonMessages.error,res.message,'error');
                    }
                },
                error: function(res){
                    laravel.error(res.message);
                },
                complete: function(res) {
                    laravel.stopLoader();
                }
            });
        }
    })
}