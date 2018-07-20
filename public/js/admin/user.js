/**
 * Created by sankap on 7/4/2018.
 */
$(document).ready(function(){

    // User regisration form validation
    X_CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

    var validator = app_form_validator('#signup-form',{
        submitHandler: function() {
            alert('ss');
            /*try{

               $( "#signup-form" ).submit();
            }catch(e){return false;}
            return false;*/
        },
        rules: {

            first_name: {
                required: true,
                minlength: 1
            },

            last_name: {
                required: true,
                minlength: 1
            },

            email: {
                required: true,
                email: true,
                maxlength: 100
            },

            emp_number: {
                required: true,
                remote: "validate-empno"
            },

            loc_id: {
                required: true,
            },

            dept_id: {
                required: true,
            },

            cost_center_id: {
                required: true,
            },

            desig_id: {
                required: true,
            },

            password: {
                required: true,
            },

            user_name: {
                required: true,
                remote: "validate-username"
            }

        },
        messages: {
            user_name:{
                remote: "This Username is already avilable."
            },

            emp_number:{
                remote: "This Employee Number is already avilable."
            }
        }
    });

    // User regisration Report Level dropdowns
    $('#immediate').select2({
        ajax: {
            url: "admin/load-report-levels",
            dataType: 'json',
            delay: 250,
            dropdownParent: $('#alternative'),
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function(obj) {
                        return { id: obj.user_id, text: obj.first_name+' '+obj.last_name };
                    })
                };
            },
            cache: true
        },
    });


    // Initialize
    $("#alternative").select2({
        ajax: {
            url: "admin/load-report-levels",
            dataType: 'json',
            delay: 250,
            dropdownParent: $('#alternative'),
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function(obj) {
                        return { id: obj.user_id, text: obj.first_name+' '+obj.last_name };
                    })
                };
            },
            cache: true
        },

    });

    function formatRepoSelection (repo) {
        //console.log(repo);
        return repo.user_id || repo.emp_number;
    }



    supplier_tbl = $('#user-tbl').DataTable({
        autoWidth: false,
        "processing": true,
        "serverSide": true,
        "ajax": {
            url: "get-user-list",
            data: {'_token': X_CSRF_TOKEN},
            type: 'POST'
        },
        "pageLength": 25,
        columns: [
            {data: "user_id",
                    render: function (data) {
                        var str = '<i class="icon-pencil" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer;margin-right:3px" data-action="EDIT" data-id="' + data + '">\n\
                        </i>  <i class="icon-bin" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer" data-action="DELETE" data-id="' + data + '"></i>';
                        return str;
                    }
                },
            {data: "first_name"},
            {data: "last_name"},
            {data: "emp_number"},
            {data: "email"},
            {data: "loc_name"},
            {data: "dept_name"},
            {data: "desig_name"}
           /* {
                'data' : function(_data){
                    if (_data['status'] == '1'){
                        return '<td><span class="label label-success">Active</span></td>';
                    }else{
                        return '<td><span class="label label-default">Inactive</span></td>';
                    }
                }
            },*/
        ],
        columnDefs: [{
            orderable: false,
            width: '100px',
            targets: [0]
        }],
        dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
    });

    $('#date_of_birth').pickadate({
        max: true,
        selectMonths: true,
        selectYears: true
    });
});
