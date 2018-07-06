var X_CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
var permission_tbl;

$(function () {

    permission_tbl = $('#permission_tbl').DataTable({
        autoWidth: false,
        "processing": true,
        "serverSide": true,
        "order": [[1, "asc"]],
        "ajax": {
            url: "/admin/permission/getList",
            data: {'_token': X_CSRF_TOKEN},
            type: 'POST'
        },
        columns: [
            {data: "id",
                render: function (data) {
                    var str = '<i class="icon-pencil" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer;margin-right:3px" onclick="addEditPermission(' + data + ')">\n\
 </i>  <i class="icon-bin" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer" onclick="delete_permission(' + data + ')"></i>';
                    return str;
                }
            },
            {data: "name"},
        ],
        columnDefs: [{
                orderable: false,
                width: '100px',
                targets: [0]
            }],
        dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
    });

});


function addEditPermission(id) {
    if (id == 0) {
        action = 'create';
    } else {
        action = id + '/edit';
    }
    $('#show_permission .modal-body').html("Loading...");
    $('#show_permission').modal();

    $("#show_permission .modal-content").load("/admin/permission/" + action, function (responseTxt, statusTxt, xhr) {
        if (statusTxt == "success") {
            $('.modal-backdrop').resize();
        }
    });
}

function add_edit_permission() {
    $.ajax({
        url: $("#permission_form").attr('action'),
        async: false,
        type: "POST",
        data: $("#permission_form").serialize(),
        dataType: "json",
        success: function (res)
        {
            if (res.status === 'success')
            {
                app_alert('success', res.message);
                $('#show_permission').modal('toggle');
                permission_tbl.ajax.reload(null, false); // reload datatabe

            } else {
                app_alert('error', res.message);
            }

        }
    });
}



function delete_permission(id) {
    swal({
        title: "Are you sure?",
        text: "You will not be able to recover this source file!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#EF5350",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "No, cancel pls!",
        closeOnConfirm: false,
        closeOnCancel: false
    },
            function (isConfirm) {
                if (isConfirm) {

                    $.ajax({
                        url: '/admin/permission/' + id,
                        type: 'delete',
                        data: {'_token': X_CSRF_TOKEN},
                        dataType: 'json',
                        success: function (res) {
                            if (res.status === 'success')
                            {
                                app_alert('success', res.message);
                                permission_tbl.ajax.reload(null, false);
                            } else {
                                app_alert('error', res.message);
                            }
                        }
                    });

                } else {
                    swal({
                        title: "Cancelled",
                        text: "No changes :)",
                        confirmButtonColor: "#2196F3",
                        type: "error"
                    });
                }
            });

}
