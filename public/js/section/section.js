
var SECTION_HID = 0;
var X_CSRF_TOKEN = '';
var TABLE = null;
var TABLE2 = null;
var TABLE3 = null;
$(document).ready(function () {
    X_CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var validator = app_form_validator('#section_form', {

        submitHandler: function () {
            try {
                save_section();
                $("#section_form :input").val('');
                validator.resetForm();
            } catch (e) {
                return false;
            }
            return false;
        },

        rules: {

            section_code: {
                required: true,
                minlength: 2,
                remote: {
                    type: "get",
                    url: "check_section_code",
                    data: {

                        code: function () {
                            return $("#section_code").val();
                        },
                        idcode: function () {
                            return $("#section_hid").val();
                        }
                    }
                }
            },

            section_name: {
                required: true,
                minlength: 4
            },

        },
        messages: {
            section_code: {
                remote: jQuery.validator.format('')
            },

        }
    });


    $('#add_data').click(function () {
        $('#show_section').modal('show');
        $('#section_form')[0].reset();
<<<<<<< HEAD
=======
        $('#section_code').prop('disabled', false);
>>>>>>> origin/master
        validator.resetForm();
        $('#btn-save').html('<b><i class="icon-floppy-disk"></i></b> save');
        //$('#button_action').val('insert');
        //$('#action').val('Add');

    });
    function get_all_section() {
        var data = [];
        $.ajax({
            url: "get_all_section",
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
    function save_section() {

        var data = app_serialize_form_to_json('#section_form');
        data['_token'] = X_CSRF_TOKEN;
<<<<<<< HEAD
=======
        data['section_code'] = $('#section_code').val();
>>>>>>> origin/master

        $.ajax({
            url: "save_section",
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
                    $('#section_form')[0].reset();
                    $('#show_section').modal('toggle');
                    validator.resetForm();

                } else
                {
                    app_alert('error', json_res['message']);
                }


            }})


    }
    function section_edit(_id) {

        $('#show_section').modal('show');
        $('#section_form')[0].reset();
        validator.resetForm();

        $.ajax({
            url: 'edit_section',
            type: 'get',
            data: {'section_id': _id},
            success: function (res) {
                var data = JSON.parse(res);
                //alert(data);
                $('#section_hid').val(data['section_id']);
                $('#section_code').val(data['section_code']).prop('disabled', true);
                $('#section_name').val(data['section_name']);
                $('#btn-save').html('<b><i class="icon-pencil"></i></b> Update');
            }
        });

    }
    function reload_table()
    {
        var dataset = get_section_list();
        var tbl = $('#section_tbl').dataTable();
        tbl.fnClearTable();
        tbl.fnDraw();
        if (dataset != null && dataset.length != 0)
            tbl.fnAddData(dataset);

    }

    function get_section_list() {
        var data = [];
        $.ajax({
            url: "get_all_section",
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


    $('#section_tbl').on('click', 'i', function () {
        var ele = $(this);
        if (ele.attr('data-action') === 'EDIT') {
            section_edit(ele.attr('data-id'));
        } else if (ele.attr('data-action') === 'DELETE') {
            section_delete(ele.attr('data-id'));
        }
    });

    function section_delete(_id) {

        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this section file!",
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
                            url: 'delete_section',
                            type: 'get',
                            data: {'section_id': _id},
                            success: function (res) {
                                var data = JSON.parse(res);
                                swal({
                                    title: "Deleted!",
                                    text: "Section has been deleted.",
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

    var dataSet = get_all_section();
    TABLE = $('#section_tbl').DataTable({
        autoWidth: false,
        columns: [
            {data: "section_id",
                render: function (data) {
                    var str = '<i class="icon-pencil" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer;margin-right:3px" data-action="EDIT" data-id="' + data + '">\n\
       </i>  <i class="icon-bin" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer" data-action="DELETE" data-id="' + data + '"></i>';
                    return str;
                }
            },
            {data: "section_code"},
            {data: "section_name"},

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
