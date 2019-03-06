
var UOM_HID = 0;
var X_CSRF_TOKEN = '';
var TABLE = null;
var TABLE2 = null;
var TABLE3 = null;
$(document).ready(function () {
    X_CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var validator = app_form_validator('#uom_form', {

        submitHandler: function () {
            try {
                save_uom();
                $("#uom_form :input").val('');
                validator.resetForm();
            } catch (e) {
                return false;
            }
            return false;
        },

        rules: {

            uom_code: {
                required: true,
                minlength: 1,
                remote: {
                    type: "get",
                    url: "check_uom_code",
                    data: {

                        code: function () {
                            return $("#uom_code").val();
                        },
                        idcode: function () {
                            return $("#uom_hid").val();
                        }
                    }
                }
            },

            uom_description: {
                required: true,
                minlength: 4
            },

        },
        messages: {
            uom_code: {
                remote: jQuery.validator.format('')
            },

        }
    });


    $('#add_data').click(function () {
        $('#show_uom').modal('show');
        $('#uom_form')[0].reset();
<<<<<<< HEAD
=======
        $('#uom_code').prop('disabled', false);
>>>>>>> origin/master
        validator.resetForm();
        $('#btn-save').html('<b><i class="icon-floppy-disk"></i></b> save');
        //$('#button_action').val('insert');
        //$('#action').val('Add');

    });
    function get_all_uom() {
        var data = [];
        $.ajax({
            url: "get_all_uom",
            async: false,
            type: 'GET',
            data: {},
            success: function (res) {
                data = JSON.parse(res);
            },
            error: function () {

            }
        }
        );
        return data;
    }
    function save_uom() {

        var data = app_serialize_form_to_json('#uom_form');
        data['_token'] = X_CSRF_TOKEN;
<<<<<<< HEAD
=======
        data['uom_code'] =$('#uom_code').val();
>>>>>>> origin/master

        $.ajax({
            url: "save_uom",
            async: false,
            type: "post",
            data: data,
            data_type: "json",
            success: function (res)
            {
                var json_res = JSON.parse(res);
                //alert(json_res['message']) ;
                if (json_res['status'] === 'success')
                {
                    app_alert('success', json_res['message']);
                    reload_table();
                    $('#uom_form')[0].reset();
                    $('#show_uom').modal('toggle');
                    validator.resetForm();

                } else
                {
                    app_alert('error', json_res['message']);
                }


            }})


    }
    function uom_edit(_id) {
<<<<<<< HEAD

=======
>>>>>>> origin/master
        $('#show_uom').modal('show');
        $('#uom_form')[0].reset();
        validator.resetForm();

        $.ajax({
            url: 'edit_uom',
            type: 'get',
            data: {'uom_id': _id},
            success: function (res) {
                var data = JSON.parse(res);
                //alert(data);
                $('#uom_hid').val(data['uom_id']);
                $('#uom_code').val(data['uom_code']).prop('disabled', true);
<<<<<<< HEAD
=======
                //$('#uom_code').prop('disabled', true);
>>>>>>> origin/master
                $('#uom_description').val(data['uom_description']);
                $('#uom_factor').val(data['uom_factor']);
                $('#uom_base_unit').val(data['uom_base_unit']);
                $('#unit_type').val(data['unit_type']);
                $('#btn-save').html('<b><i class="icon-pencil"></i></b> Update');
            }
        });

    }
    function reload_table()
    {
        var dataset = get_uom_list();
        var tbl = $('#uom_tbl').dataTable();
        tbl.fnClearTable();
        tbl.fnDraw();
        if (dataset != null && dataset.length != 0)
            tbl.fnAddData(dataset);

    }

    function get_uom_list() {
        var data = [];
        $.ajax({
            url: "get_all_uom",
            async: false,
            type: 'get',
            data: {},
            success: function (res) {
                data = JSON.parse(res);
                //  alert(res);

            },
            error: function () {

            }
        });
        return data;
    }


    $('#uom_tbl').on('click', 'i', function () {
        var ele = $(this);
        if (ele.attr('data-action') === 'EDIT') {
            uom_edit(ele.attr('data-id'));
        } else if (ele.attr('data-action') === 'DELETE') {
            uom_delete(ele.attr('data-id'));
        }
    });

    function uom_delete(_id) {

        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this UOM file!",
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
                            url: 'delete_uom',
                            type: 'get',
                            data: {'uom_id': _id},
                            success: function (res) {
                                var data = JSON.parse(res);
                                swal({
                                    title: "Deleted!",
                                    text: "UOM has been deleted.",
                                    confirmButtonColor: "#66BB6A",
                                    type: "success"
                                });

                                reload_table();

                            }
                        });

                    } else {
                        swal({
                            title: "Cancelled",
                            text: "Your imaginary file is safe :)",
                            confirmButtonColor: "#2196F3",
                            type: "error"
                        });
                    }
                });

    }

    var dataSet = get_all_uom();
    TABLE = $('#uom_tbl').DataTable({
        autoWidth: false,
        columns: [
            {data: "uom_id",
                render: function (data) {
                    var str = '<i class="icon-pencil" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer;margin-right:3px" data-action="EDIT" data-id="' + data + '">\n\
       </i>  <i class="icon-bin" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer" data-action="DELETE" data-id="' + data + '"></i>';
                    return str;
                }
            },
            {data: "uom_code"},
            {data: "uom_description"},
            {data: "uom_factor"},
            {data: "uom_base_unit"},
            {data: "unit_type"},

            {
                'data': function (_data) {
                    if (_data['status'] == '1') {
                        return '<td><span class="label label-success">Active</span></td>';
                    } else {
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
        data: dataSet,
        dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
    });


});
