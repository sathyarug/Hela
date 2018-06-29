var X_CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
var role_tbl;

$(function () {
    // alert('sdada');

    

    var validator = app_form_validator('#role_form', {

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

            /*  source_code: {
             required: true,
             minlength: 4,
             remote: {
             type: "get",
             url: "Mainsource.check_code",
             data: {
             
             code: function () {
             return $("#source-code").val();
             },
             idcode: function () {
             return $("#source_hid").val();
             }
             }
             }
             },*/

            /*source_name: {
             required: true,
             minlength: 4
             },*/

        },
        messages: {
            /* source_code: {
             remote: jQuery.validator.format('')
             },*/

        }
    });


    $('select').select2();



    $('#add_data').click(function () {

        $('#show_role').modal('show');
        $('#role_form')[0].reset();
        validator.resetForm();
        $('#btn-save').html('<b><i class="icon-floppy-disk"></i></b> Save');
    });




    // Main Cluster Codes ====================================================================================

    
    role_tbl = $('#role_tbl').DataTable({
        autoWidth: false,
        "processing": true,
        "serverSide": true,
        "ajax": {
            url: "/admin/role/getList",
            data: {'_token': X_CSRF_TOKEN},
            type: 'POST'
        },
        columns: [
            {data: "id",
                render: function (data) {
                    var str = '<i class="icon-pencil" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer;margin-right:3px" data-action="EDIT" data-id="' + data + '">\n\
        </i>  <i class="icon-bin" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer" data-action="DELETE" data-id="' + data + '"></i>';
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


function save_role() {

    var data = app_serialize_form_to_json('#role_form');
    data['_token'] = X_CSRF_TOKEN;
    console.log(data);
    $.ajax({
        url: "/admin/role",
        async: false,
        type: "POST",
        data: data,
        dataType: "json",
        success: function (res)
        {
            //var json_res = JSON.parse(res);
            if (res.status === 'success')
            {
                app_alert('success', res.message);
                //reload_table();
                $('#role_form')[0].reset();
                $('#show_role').modal('toggle');
                validator.resetForm();

            } else {
                app_alert('error', res.message);
            }


        }})
}