var X_CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
var supplier_tbl;

$(function () {


    var validator = app_form_validator('#frm_supplier', {

        submitHandler: function () {
            try {
                save_supplier();
                // $("#frm_supplier :input").val('');
                validator.resetForm();
            } catch (e) {
                console.log(e);
                return false;
            }
            return false;
        },

        rules: {

            // Supplier_code: {
            //  required: true,
            //  minlength: 4,
            //  },
            // supplier_name: {
            //  required: true,
            //  minlength: 4
            //  },

        },
        messages: {
             // source_code: {
             // remote: jQuery.validator.format('')
             // },

        }
    });


    $('select').select2();



    $('#add_data').click(function () {
        //
        $('#show_supplier').modal('show');
        $('#show_supplier')[0].reset();
        validator.resetForm();
        $('#btn-save').html('<b><i class="icon-floppy-disk"></i></b> Save');


        // $('#show_source .modal-title').html("Supplier");
        // $('#show_source .modal-body').html("Loading...");
        // $('#show_source .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
        // $('#show_source').modal();
        // $("#show_source .modal-content").load("supplier/getList", {id: id}, function () {
        //     $('.modal-backdrop').resize();
        // });
    });




    // Main Cluster Codes ====================================================================================


    supplier_tbl = $('#supplier_tbl').DataTable({
        autoWidth: false,
        "processing": true,
        "serverSide": true,
        "ajax": {
            url: "supplier/getList",
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
             {data: "supplier_name"},
             {data: "supplier_code"},
             {data: "supplier_city"},
             {data: "supplier_phone"},
             {data: "supplier_email"},
            {
                'data' : function(_data){
                    if (_data['status'] == '1'){
                        return '<td><span class="label label-success">Active</span></td>';
                    }else{
                        return '<td><span class="label label-default">Inactive</span></td>';
                    }
                }
            },
        ],
        columnDefs: [{
                orderable: false,
                width: '100px',
                targets: [0]
            }],
        dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
    });

});


function save_supplier() {

    var data = app_serialize_form_to_json('#frm_supplier');
    data['_token'] = X_CSRF_TOKEN;
    console.log(data);
    $.ajax({
        url: "/supplier/save",
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
                var tbl = $('#supplier_tbl').dataTable();
                tbl.fnClearTable();
                tbl.fnDraw();

                $('#frm_supplier')[0].reset();
                $('#show_supplier').modal('toggle');
                validator.resetForm();

            } else {
                app_alert('error', res.message);
            }


        }})

    function reload_table()
    {
        // // var dataSet2 = get_cluster_list();
        // var tbl = $('#cluster_tbl').dataTable();
        supplier_tbl.fnClearTable();
        supplier_tbl.fnDraw();
    }
}