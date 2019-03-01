var X_CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
var supplier_tbl;


function addEditSupplier(id) {
    if (id == 0) {
        action = '';
    } else {
        action = '?id='+id ;
    }

    $('#show_supplier .modal-body').html("Loading...");
    $('#show_supplier').modal();

<<<<<<< HEAD
    $("#show_supplier .modal-content").load("/supplier/loadAddOrEdit"+action, function (responseTxt, statusTxt, xhr) {
        if (statusTxt == "success") {
            $('.modal-backdrop').resize();
        }
    });
=======
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
>>>>>>> origin/master

}
$(function () {





    $('select').select2();





<<<<<<< HEAD
=======

    });
>>>>>>> origin/master


    $('#supplier_tbl').on('click','i',function(){
        var ele = $(this);
        if(ele.attr('data-action') === 'EDIT'){
            supplier_edit(ele.attr('data-id'));
        }
        else if(ele.attr('data-action') === 'DELETE'){
            supplier_delete(ele.attr('data-id'));
        }
    });


<<<<<<< HEAD
    function supplier_delete(_id){

        swal({
                title: "Are you sure?",
                text: "You will not be able to recover this supplier information!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#EF5350",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel pls!",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function(isConfirm){
                if (isConfirm) {

                    $.ajax({
                        url : 'supplier/delete',
                        type : 'get',
                        data : {'id' : _id},
                        success : function(res){
                            var data = JSON.parse(res);
                            swal({
                                title: "Deleted!",
                                text: "supplier has been deleted.",
                                confirmButtonColor: "#66BB6A",
                                type: "success"
                            });
                            var tbl = $('#supplier_tbl').dataTable();
                            tbl.fnClearTable();
                            tbl.fnDraw();

                        }
                    });

                }
                else {
                    swal({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        confirmButtonColor: "#2196F3",
                        type: "error"
                    });
                }
            });

=======
    function supplier_edit(_id){

        $('#show_supplier').modal('show');
        $('#frm_supplier')[0].reset();
        validator.resetForm();

        $.ajax({
            url : 'supplier/edit',
            type : 'get',
            data : {'id' : _id},
            success : function(res){
                var data = JSON.parse(res);
                //alert(data);
                $('#supplier_hid').val(data['supplier_id']);
                $("input[name~='supplier_code']").val(data['supplier_code']);
                $("input[name~='supplier_name']").val(data['supplier_name']);
                $("input[name~='supplier_city']").val(data['supplier_city']);
                $("input[name~='supplier_address1']").val(data['supplier_address1']);
                $("input[name~='supplier_address2']").val(data['supplier_address2']);
                $("input[name~='supplier_phone']").val(data['supplier_phone']);
                $("input[name~='supplier_fax']").val(data['supplier_fax']);
                $("input[name~='supplier_email']").val(data['supplier_email']);

                // $("select[name='supplier_country_id']").select2("val", data['supplier_country_id']);
                // $('#source-name').val(data['source_name']);
                $('#btn-save').html('<b><i class="icon-pencil"></i></b> Update');
            }
        });

    }

    function supplier_delete(_id){

        swal({
                title: "Are you sure?",
                text: "You will not be able to recover this supplier information!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#EF5350",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel pls!",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function(isConfirm){
                if (isConfirm) {

                    $.ajax({
                        url : 'supplier/delete',
                        type : 'get',
                        data : {'id' : _id},
                        success : function(res){
                            var data = JSON.parse(res);
                            swal({
                                title: "Deleted!",
                                text: "supplier has been deleted.",
                                confirmButtonColor: "#66BB6A",
                                type: "success"
                            });
                            var tbl = $('#supplier_tbl').dataTable();
                            tbl.fnClearTable();
                            tbl.fnDraw();

                        }
                    });

                }
                else {
                    swal({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        confirmButtonColor: "#2196F3",
                        type: "error"
                    });
                }
            });

>>>>>>> origin/master
    }

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
            {data: "supplier_id",
                render: function (data) {
                    var str = '<i class="icon-pencil" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer;margin-right:3px" data-action="EDIT" onclick="addEditSupplier((' + data + '))" data-id="' + data + '">\n\
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

<<<<<<< HEAD

=======
    function reload_table()
    {
        // // var dataSet2 = get_cluster_list();
        // var tbl = $('#cluster_tbl').dataTable();
        supplier_tbl.fnClearTable();
        supplier_tbl.fnDraw();
    }
>>>>>>> origin/master
}