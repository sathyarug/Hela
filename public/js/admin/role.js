$(function () {
    // alert('sdada');

    $('select').select2();

    /*var roleTbl;
     roleTbl = $('#role_table').dataTable({
     "aaSorting": [[2, "desc"]],
     "bAutoWidth": false,
     "bStateSave": false,
     
     });*/





    // Main Cluster Codes ====================================================================================

    var role_tbl;
    var roleDataSet = get_role_list();

    role_tbl = $('#role_tbl').DataTable({
        autoWidth: false,
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
        data: roleDataSet,
        dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
    });

});

function get_role_list() {
    var data = [];
    $.ajax({
        url: "/admin/role/getList",
        async: false,
        type: 'get',
        data: {},
        success: function (res) {
            data = JSON.parse(res);
        },
        error: function () {

        }
    });
    return data;
}

