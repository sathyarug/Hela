
var SAMPLE_HID = 0;
var X_CSRF_TOKEN = '';
var TABLE = null;
var TABLE2 = null;
var TABLE3 = null;
$(document).ready(function () {
    X_CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var validator = app_form_validator('#sample_form', {

        submitHandler: function () {
            try {
                save_sample();
                $("#sample_form :input").val('');
                validator.resetForm();
            } catch (e) {
                return false;
            }
            return false;
        },

        rules: {

            sample_code: {
                required: true,
                minlength: 2,
                remote: {
                    type: "get",
                    url: "check_sample_stage_code",
                    data: {

                        code: function () {
                            return $("#sample_code").val();
                        },
                        idcode: function () {
                            return $("#sample_hid").val();
                        }
                    }
                }
            },

            sample_description: {
                required: true,
                minlength: 4
            },

        },
        messages: {
            sample_code: {
                remote: jQuery.validator.format('')
            },

        }
    });


    $('#add_data').click(function () {
        $('#show_sample').modal('show');
        $('#sample_form')[0].reset();
        $('#sample_code').prop('disabled', false);
        validator.resetForm();
        $('#btn-save').html('<b><i class="icon-floppy-disk"></i></b> save');
        //$('#button_action').val('insert');
        //$('#action').val('Add');

    });
    function get_all_sample() {
        var data = [];
        $.ajax({
            url: "get_all_sample_stage",
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
    function save_sample() {

        var data = app_serialize_form_to_json('#sample_form');
        data['_token'] = X_CSRF_TOKEN;
        data['sample_code'] = $('#sample_code').val();

        $.ajax({
            url: "save_sample_stage",
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
                    $('#sample_form')[0].reset();
                    $('#show_sample').modal('toggle');
                    validator.resetForm();

                } else
                {
                    app_alert('error', json_res['message']);
                }


            }})


    }
    function sample_edit(_id) {

        $('#show_sample').modal('show');
        $('#sample_form')[0].reset();
        validator.resetForm();

        $.ajax({
            url: 'edit_sample_stage',
            type: 'get',
            data: {'sample_id': _id},
            success: function (res) {
                var data = JSON.parse(res);
                //alert(data);
                $('#sample_hid').val(data['sample_id']);
                $('#sample_code').val(data['sample_code']).prop('disabled', true);
                $('#sample_description').val(data['sample_description']);
                $('#btn-save').html('<b><i class="icon-pencil"></i></b> Update');
            }
        });

    }
    function reload_table()
    {
        var dataset = get_sample_list();
        var tbl = $('#sample_tbl').dataTable();
        tbl.fnClearTable();
        tbl.fnDraw();
        if (dataset != null && dataset.length != 0)
            tbl.fnAddData(dataset);

    }

    function get_sample_list() {
        var data = [];
        $.ajax({
            url: "get_all_sample_stage",
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


    $('#sample_tbl').on('click', 'i', function () {
        var ele = $(this);
        if (ele.attr('data-action') === 'EDIT') {
            sample_edit(ele.attr('data-id'));
        } else if (ele.attr('data-action') === 'DELETE') {
            sample_delete(ele.attr('data-id'));
        }
    });

    function sample_delete(_id) {

        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this sample file!",
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
                            url: 'delete_sample_stage',
                            type: 'get',
                            data: {'sample_id': _id},
                            success: function (res) {
                                var data = JSON.parse(res);
                                swal({
                                    title: "Deleted!",
                                    text: "Sample stage has been deleted.",
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

    var dataSet = get_all_sample();
    TABLE = $('#sample_tbl').DataTable({
        autoWidth: false,
        columns: [
            {data: "sample_id",
                render: function (data) {
                    var str = '<i class="icon-pencil" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer;margin-right:3px" data-action="EDIT" data-id="' + data + '">\n\
       </i>  <i class="icon-bin" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer" data-action="DELETE" data-id="' + data + '"></i>';
                    return str;
                }
            },
            {data: "sample_code"},
            {data: "sample_description"},

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
