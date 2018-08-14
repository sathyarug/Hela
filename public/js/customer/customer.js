$(document).ready(function () {
    var CUSTOMER_HID = 0;
    var X_CSRF_TOKEN = '';
    var TABLE = null;
    var TABLE2 = null;
    var TABLE3 = null;
    /* TABLE3 = $('#customer_listing_tbl').DataTable({
     order: [[1, "asc"]],
     scrollY: "300px",
     scrollX: true,
     scrollCollapse: true,
     });*/
    X_CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var validator = app_form_validator('#customer_form', {

        submitHandler: function () {
            try {
                save_customer();
                $("#customer_form :input").val('');
                validator.resetForm();
            } catch (e) {
                return false;
            }
            return false;
        },
        rules: {

            customer_code: {
                required: true,
                minlength: 2,
                remote: {
                    type: "get",
                    url: "check_customer_code",
                    data: {

                        code: function () {
                            return $("#customer_code").val();
                        },
                        idcode: function () {
                            return $("#customer_hid").val();
                        }
                    }
                }
            },
            customer_name: {
                required: true,
                minlength: 4
            },
        },
        messages: {
            customer_code: {
                remote: jQuery.validator.format('')
            },
        }
    });

    $('#btn-new').click(function () {
        $("#customer_form :input").val('');
        validator.resetForm();
        $('#btn-save').html('<b><i class="icon-floppy-disk"></i></b> Save');
    });
    /* $('#add_data').click(function () {
     $('#season_form')[0].reset();
     $('#season_code').prop('disabled', false);
     validator.resetForm();
     $('#btn-save').html('<b><i class="icon-floppy-disk"></i></b> save');
     });*/
    function get_all_season() {
        var data = [];
        $.ajax({
            url: "get_all_customers",
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
    function save_customer() {

        var data = app_serialize_form_to_json('#customer_form');
        data['_token'] = X_CSRF_TOKEN;
        data['customer_code'] = $('#customer_code').val();
        $.ajax({
            url: "save_customer",
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
                    $('#customer_form')[0].reset();
                    $('#show_customer').modal('toggle');
                    validator.resetForm();
                } else
                {
                    app_alert('error', json_res['message']);
                }


            }})


    }
    function season_edit(_id) {

        $('#show_customer').modal('show');
        $('#customer_form')[0].reset();
        validator.resetForm();
        $.ajax({
            url: 'edit_customer',
            type: 'get',
            data: {'customer_id': _id},
            success: function (res) {
                var data = JSON.parse(res);
                //alert(data);
                $('#customer_hid').val(data['customer_id']);
                $('#customer_code').val(data['customer_code']).prop('disabled', true);
                $('#customer_name').val(data['customer_name']);
                $('#btn-save').html('<b><i class="icon-pencil"></i></b> Update');
            }
        });
    }

    function reload_table()
    {

        var dataset = get_customer_list();
        var tbl = $('#customer_listing_tbl').dataTable();
        tbl.fnClearTable();
        tbl.fnDraw();
        if (dataset != null && dataset.length != 0)
            tbl.fnAddData(dataset);
    }
    /* TABLE3 = $('#customer_listing_tbl').DataTable({
     order: [[1, "asc"]],
     scrollY: "300px",
     scrollX: true,
     scrollCollapse: true,
     });*/

    function get_customer_list() {
        var data = [];
        $.ajax({
            url: "get_all_customers",
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


    $('#customer_listing_tbl').on('click', 'i', function () {
        var ele = $(this);
        if (ele.attr('data-action') === 'EDIT') {
            season_edit(ele.attr('data-id'));
        } else if (ele.attr('data-action') === 'DELETE') {
            customer_delete(ele.attr('data-id'));
        }
    });
    function customer_delete(_id) {

        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this season file!",
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
                            url: 'delete_customer',
                            type: 'get',
                            data: {'customer_id': _id},
                            success: function (res) {
                                var data = JSON.parse(res);
                                swal({
                                    title: "Deleted!",
                                    text: "Season has been deleted.",
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

    var dataSet = get_all_season();
    TABLE = $('#customer_listing_tbl').DataTable({
        autoWidth: false,
        order: [[1, "asc"]],
        scrollY: "300px",
        scrollX: true,
        scrollCollapse: true,
        columns: [
            {data: "customer_id",
                render: function (data) {
                    var str = '<i class="icon-pencil" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer;margin-right:3px" data-action="EDIT" data-id="' + data + '">\n\
       </i>  <i class="icon-bin" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer" data-action="DELETE" data-id="' + data + '"></i>';
                    return str;
                }
            },
            {data: "customer_code"},
            {data: "customer_name"},
            {data: "customer_short_name"},
            {data: "type_of_service"},
            {data: "business_reg_no"},
            {data: "business_reg_date"},
            {data: "customer_address1"},
            {data: "customer_address2"},
            {data: "customer_city"},
            {data: "customer_postal_code"},
            {data: "customer_state"},
            {data: "customer_county"},
            {data: "customer_contact1"},
            {data: "customer_contact2"},
            {data: "customer_contact3"},
            {data: "customer_email"},
            {data: "customer_map_location"},
            {data: "customer_website"},
            {data: "company_code"},
            {data: "operation_start_date"},
            {data: "order_destination"},
            {data: "currency"},
            {data: "boi_reg_no"},
            {data: "boi_reg_date"},
            {data: "vat_reg_no"},
            {data: "svat_no"},
            {data: "managing_director_name"},
            {data: "managing_director_email"},
            {data: "finance_director_name"},
            {data: "finance_director_email"},
            {data: "finance_director_contact"},
            {data: "additional_comments"},
            {data: "ship_terms_agreed"},
            {data: "payemnt_terms"},
            {data: "payment_mode"},
            {data: "bank_acc_no"},
            {data: "bank_name"},
            {data: "bank_branch"},
            {data: "bank_code"},
            {data: "bank_swift"},
            {data: "bank_iban"},
            {data: "bank_contact"},
            {data: "intermediary_bank_name"},
            {data: "intermediary_bank_address"},
            {data: "intermediary_bank_contact"},
            {data: "buyer_posting_group"},
            {data: "business_posting_group"},
            {data: "approved_by"},
            {data: "system_updated_by"},
            {data: "customer_creation_form"},
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
//Load currency to drop down
    $('#currency').select2({

        placeholder: '[ Select and Search ]',
        //allowClear: true,
        ajax: {
            dataType: 'json',
            url: 'load_currency',
            type: 'GET',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                    type: 'public'
                }
            },
            processResults: function (data) {
                return {
                    results: $.map(data.items, function (val, i) {
                        return {id: val.currency_id, text: val.currency_code};
                    })
                };
            },
        }


    });
//Load Payment terms to drop down
    $('#payemnt_terms').select2({

        placeholder: '[ Select and Search ]',
        //allowClear: true,
        ajax: {
            dataType: 'json',
            url: 'load_payemnt_terms',
            type: 'GET',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                    type: 'public'
                }
            },
            processResults: function (data) {
                return {
                    results: $.map(data.items, function (val, i) {
                        return {id: val.payment_term_id, text: val.payment_code};
                    })
                };
            },
        }


    });

});


