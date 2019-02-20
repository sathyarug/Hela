var X_CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
var role_tbl;

$(function () {
<<<<<<< HEAD
    /*var validator = app_form_validator('#role_form', {
     
     submitHandler: function () {
     try {
     save_role();
     $("#role_form :input").val('');
     validator.resetForm();
     } catch (e) {
     console.log(e);
     return false;
     }
     return false;
     },
     
     rules: {
     
     "name": {
     required: true,
     remote: {
     type: "get",
     url: "/admin/role/checkName",
     data: {
     name: function () {
     return $("#role-name").val();
     },
     id: function () {
     return $("#role_id").val();
     }
     },
     dataFilter: function (data) {
     if (data == 'true') {
     return "\"" + "This role name already exists." + "\"";
     ;
     } else {
     return 'true';
     }
     }
     },
     
     },
     
     }
     });
     
     
     $('#add_data').click(function () {
     
     $('#show_role').modal('show');
     $('#role_form')[0].reset();
     validator.resetForm();
     $("#permission-field").val(null).trigger("change");
     $('#btn-save').html('<b><i class="icon-floppy-disk"></i></b> Save');
     });
     */



// Main Cluster Codes ====================================================================================

=======
>>>>>>> origin/master

    role_tbl = $('#role_tbl').DataTable({
        autoWidth: false,
        "processing": true,
        "serverSide": true,
        "order": [[1, "asc"]],
        "ajax": {
            url: "/admin/role/getList",
            data: {'_token': X_CSRF_TOKEN},
            type: 'POST'
        },
        columns: [
            {data: "id",
                render: function (data) {
                    var str = '<i class="icon-pencil" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer;margin-right:3px" onclick="addEditRole(' + data + ')">\n\
 </i>  <i class="icon-bin" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer" onclick="delete_role(' + data + ')"></i>';
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
<<<<<<< HEAD
/*
 function edit_role(id) {
 $('#show_role').modal('show');
 $('#role_form')[0].reset();
 validator.resetForm();

 $('#role_id').val(id);

 $.ajax({
 url: '/admin/role/edit',
 type: 'get',
 data: {'id': id},
 success: function (res) {
 $('#role-name').val(data.name);
 $('#source-name').val(data['source_name']);
 $('#btn-save').html('<b><i class="icon-pencil"></i></b> Update');
 }
 });
 }*/
/**/
=======

>>>>>>> origin/master

function addEditRole(id) {
    if (id == 0) {
        action = 'create';
    } else {
        action = id + '/edit';
    }
    $('#show_role .modal-body').html("Loading...");
    $('#show_role').modal();

    $("#show_role .modal-content").load("/admin/role/" + action, function (responseTxt, statusTxt, xhr) {
        if (statusTxt == "success") {
            $('.modal-backdrop').resize();
        }
    });
}

<<<<<<< HEAD

=======
function add_edit_role() {
    $.ajax({
        url: $("#role_form").attr('action'),
        async: false,
        type: "POST",
        data: $("#role_form").serialize(),
        dataType: "json",
        success: function (res)
        {
            if (res.status === 'success')
            {
                app_alert('success', res.message);
                $('#show_role').modal('toggle');
                role_tbl.ajax.reload(null, false); // reload datatabe

            } else {
                app_alert('error', res.message);
            }

        }
    });
}
>>>>>>> origin/master

function delete_role(id) {
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
                        url: '/admin/role/' + id,
                        type: 'delete',
                        data: {'_token': X_CSRF_TOKEN},
                        dataType: 'json',
                        success: function (res) {
                            if (res.status === 'success')
                            {
                                app_alert('success', res.message);
<<<<<<< HEAD
                                role_tbl.ajax.reload();
=======
                                role_tbl.ajax.reload(null, false);
>>>>>>> origin/master
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
<<<<<<< HEAD
/*
 function delete_role() {
 $.ajax({
 url: $("#role_form").attr('action'),
 async: false,
 type: "DELETE",
 data: $("#role_form").serialize(),
 dataType: "json",
 success: function (res)
 {
 if (res.status === 'success')
 {
 app_alert('success', res.message);
 $('#show_role').modal('toggle');
 role_tbl.ajax.reload(); // reload datatabe
 
 } else {
 app_alert('error', res.message);
 }
 }
 });
 }*/
=======
>>>>>>> origin/master
