
var DEPARTMENT_HID = 0;
var X_CSRF_TOKEN = '';
var TABLE = null;
$(document).ready(function () {
    X_CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var validator = app_form_validator('#department_form', {

        submitHandler: function () {
            try {
                save_department();
                $("#department_form :input").val('');
                validator.resetForm();
            } catch (e) {
                return false;
            }
            return false;
        },

        rules: {

            dep_code: {
                required: true,
                minlength: 2,
                remote: {
                    type: "get",
                    url: "Department.check_department",
                    data: {

                        code: function () {
                            return $("#dep_code").val();
                        },
                        idcode: function () {
                            return $("#dep_name").val();
                        }
                    }
                }
            },

            dep_name: {
                required: true,
                minlength: 2
            },

        },
        messages: {
            dep_code: {
                remote: jQuery.validator.format('')
            },

        }
    });


    $('#add_data').click(function () {
        $('#show_department').modal('show');
        $('#department_form')[0].reset();
        $('#dep_code').prop('disabled', false);
        validator.resetForm();
        $('#dep-save').html('<b><i class="icon-floppy-disk"></i></b> save');
        //$('#button_action').val('insert');
        //$('#action').val('Add');

    });

    

function save_department(){

     var data = app_serialize_form_to_json('#department_form');
     data['_token'] = X_CSRF_TOKEN;
     data['dep_code'] = $('#dep_code').val();

     $.ajax({
       url:"Department.save",
       async : false,
       type:"post",
       data:data,
       data_type:"json",
       success:function(res)
       {
        var json_res = JSON.parse(res); 
            //alert(json_res['message']) ;
            if(json_res['status'] === 'success')
            {
                app_alert('success',json_res['message']);
                reload_table();
                $('#department_form')[0].reset();
                $('#show_department').modal('toggle');
                validator.resetForm();

            }
            else
            {
                app_alert('error',json_res['message']);
            }      


        }})


 }




 var dataSet = get_dep_list();

 TABLE = $('#department_tbl').DataTable({
    autoWidth: false,
    order: [[ 1, "asc" ]],
    columns: [
    { data: "dep_id",
    render : function(data, type, row){
     if ( row.status == '1') {
     var str = '<i class="icon-pencil" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer;margin-right:3px" data-action="EDIT" data-id="'+data+'">\n\
     </i>  <i class="icon-bin" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer" data-action="DELETE" data-id="'+data+'"></i>';
     return str;
     }else if(row.status == '0'){
     var str = '<i class="icon-pencil" style="border-style:solid; border-width: 1px;padding:2px;margin-right:3px;background-color:#999999;" >\n\
     </i>  <i class="icon-bin" style="border-style:solid; border-width: 1px;padding:2px;background-color:#999999;"></i>';
     return str;
     }
   
 }
},
{
    'data' : function(_data){
       if (_data['status'] == '1'){
           return '<td><span class="label label-success">Active</span></td>';
       }else{
           return '<td><span class="label label-default">Inactive</span></td>';   
       }
   }
},
{ data: "dep_code" },
{ data: "dep_name" }


],
columnDefs: [{ 
   orderable: false,
   width: '100px',
   targets: [ 0 ]
}],
data: dataSet,
dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
});



 function get_dep_list(){
     var data = [];
     $.ajax({
      url : "Department.loaddata",
      async : false,
      type : 'get',
      data : {},
      success : function(res){
       data = JSON.parse(res);
          //  alert(res);

      },
      error : function(){

      }
  });
     return data;
 }



 function reload_table()
 {
     var dataset = get_dep_list();
     var tbl = $('#department_tbl').dataTable();
     tbl.fnClearTable();
     tbl.fnDraw();
     if(dataset != null && dataset.length != 0)
      tbl.fnAddData(dataset);

}


$('#department_tbl').on('click','i',function(){
    var ele = $(this);
    if(ele.attr('data-action') === 'EDIT'){
        department_edit(ele.attr('data-id'));
    }
    else if(ele.attr('data-action') === 'DELETE'){
        department_delete(ele.attr('data-id'));
    }
});


function department_edit(_id){  

    $('#show_department').modal('show');
    $('#department_form')[0].reset();
    validator.resetForm(); 

    $.ajax({
        url : 'Department.edit',
        type : 'get',
        data : {'dep_id' : _id},
        success : function(res){
            var data = JSON.parse(res);
            //alert(data);
            $('#department_hid').val(data['dep_id']);
            $('#dep_code').val(data['dep_code']).prop('disabled', true);
            $('#dep_name').val(data['dep_name']);
            $('#dep-save').html('<b><i class="icon-pencil"></i></b> Update');
        }
    });
    
}

function department_delete(_id){ 

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
    function(isConfirm){
        if (isConfirm) {

            $.ajax({
                url : 'Department.delete',
                type : 'get',
                data : {'dep_id' : _id},
                success : function(res){
                    var data = JSON.parse(res);
                    swal({
                        title: "Deleted!",
                        text: "Source file has been deleted.",
                        confirmButtonColor: "#66BB6A",
                        type: "success"
                    });

                    reload_table();

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

}




});
