

var SOURCE_HID = 0;
var X_CSRF_TOKEN = '';
var TABLE = null;
var TABLE2 = null;
var TABLE3 = null;

$(document).ready(function(){


	X_CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

	var validator = app_form_validator('#source_form',{

        submitHandler: function() { 
            try{
                save_source();
                $("#source_form :input").val('');
                validator.resetForm();
            }catch(e){return false;}
            return false; 
        },

        rules: {

        	source_code: {
        		required: true,
        		minlength: 4,
        		remote: {
        			type: "get",
        			url: "Mainsource.check_code",        		
        			data: {

                        code    : function() { return $( "#source-code" ).val(); },
                        idcode  : function() { return $( "#source_hid" ).val(); }
                    }
                }       	
            },

            source_name: {
              required: true,
              minlength: 4
          },

      },
      messages: {
         source_code:{
          remote: jQuery.validator.format('')
      },

  }
});



    $('#add_data').click(function(){

    	$('#show_source').modal('show');
    	$('#source_form')[0].reset();
    	validator.resetForm();
    	$('#btn-save').html('<b><i class="icon-floppy-disk"></i></b> save');
    	//$('#button_action').val('insert');
    	//$('#action').val('Add');

    });

    $('#add_cluster').click(function(){

    	$('#show_cluster').modal('show');
    	$('#cluster_form')[0].reset();
        //get_main_source_list();
    	//$('#button_action').val('insert');
    	//$('#action').val('Add');

    });

    $('#add_company').click(function(){

        $('#show_location').modal('show');
        $('#location_form')[0].reset();
        //get_main_source_list();
        //$('#button_action').val('insert');
        //$('#action').val('Add');

    });
    





    function save_source(){

     var data = app_serialize_form_to_json('#source_form');
     data['_token'] = X_CSRF_TOKEN;

     $.ajax({
       url:"Mainsource.postdata",
       async : false,
       type:"post",
       data:data,
       data_type:"json",
       success:function(res)
       {

        if(res.message == true)
        {

         app_alert('Source details saved successfully.');
         reload_table();

         $('#source_form')[0].reset();
         $('#show_source').modal('toggle');
         validator.resetForm();

     }else{

     }	

 }})


 }




 var dataSet = get_source_list();

 TABLE = $('#source_tbl').DataTable({
    autoWidth: false,
    columns: [
    { data: "source_id",
    render : function(data){
     var str = '<i class="icon-pencil" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer;margin-right:3px" data-action="EDIT" data-id="'+data+'">\n\
     </i>  <i class="icon-bin" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer" data-action="DELETE" data-id="'+data+'"></i>';
     return str;
 }
},
{ data: "source_code" },
{ data: "source_name" },

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
   targets: [ 0 ]
}],
data: dataSet,
dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
});



 function get_source_list(){
     var data = [];
     $.ajax({
      url : "Mainsource.loaddata",
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
     var dataset = get_source_list();
     var tbl = $('#source_tbl').dataTable();
     tbl.fnClearTable();
     tbl.fnDraw();
     if(dataset != null && dataset.length != 0)
      tbl.fnAddData(dataset);

}


$('#source_tbl').on('click','i',function(){
    var ele = $(this);
    if(ele.attr('data-action') === 'EDIT'){
        source_edit(ele.attr('data-id'));
    }
    else if(ele.attr('data-action') === 'DELETE'){
        source_delete(ele.attr('data-id'));
    }
});


function source_edit(_id){  

    $('#show_source').modal('show');
    $('#source_form')[0].reset();
    validator.resetForm(); 

    $.ajax({
        url : 'Mainsource.edit',
        type : 'get',
        data : {'source_id' : _id},
        success : function(res){
            var data = JSON.parse(res);
            //alert(data);
            $('#source_hid').val(data['source_id']);
            $('#source-code').val(data['source_code']);
            $('#source-name').val(data['source_name']);
            $('#btn-save').html('<b><i class="icon-pencil"></i></b> Update');
        }
    });
    
}

function source_delete(_id){ 

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
                url : 'Mainsource.delete',
                type : 'get',
                data : {'source_id' : _id},
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


// Main Cluster Codes ====================================================================================


var dataSet2 = get_cluster_list();

TABLE2 = $('#cluster_tbl').DataTable({
    autoWidth: false,
    columns: [
    { data: "group_id",
    render : function(data){
        var str = '<i class="icon-pencil" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer;margin-right:3px" data-action="EDIT" data-id="'+data+'">\n\
        </i>  <i class="icon-bin" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer" data-action="DELETE" data-id="'+data+'"></i>';
        return str;
    }
},
{ data: "group_code" },
{ data: "source_code" },
{ data: "group_name" },
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
   targets: [ 0 ]
}],
data: dataSet2,
dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
});



function get_cluster_list(){
 var data = [];
 $.ajax({
  url : "Maincluster.loaddata",
  async : false,
  type : 'get',
  data : {},
  success : function(res){
   data = JSON.parse(res);
         //alert(res);

     },
     error : function(){

     }
 });
 return data;
}



function reload_table2()
{
 var dataSet2 = get_cluster_list();
 var tbl = $('#cluster_tbl').dataTable();
 tbl.fnClearTable();
 tbl.fnDraw();
 if(dataSet2 != null && dataSet2.length != 0)
  tbl.fnAddData(dataSet2);

}




 $('#main_source').select2({
            
    placeholder: 'Select and Search',
            ajax: {
                dataType: 'json',
                url: 'Mainsource.load_list',
                type:'GET',
                delay: 250,
                data: function(params) {
                    return {
                        s_source_lists: params.term
                    }
                },
                processResults: function (data) {
                  return {
                    results: $.map(data.items,function(val,i){

                        return {id:val, text:val};
                    })
                  };
                },
            }


     });








  // Main Location Codes ====================================================================================


  var dataSet3 = get_location_list();

  TABLE3 = $('#location_tbl').DataTable({
    scrollY:        "300px",
    scrollX:        true,
    scrollCollapse: true,

    fixedColumns:   { leftColumns: 5},

    columns: [
    { data: "group_id",
    render : function(data){
        var str = '<i class="icon-pencil" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer;margin-right:3px" data-action="EDIT" data-id="'+data+'">\n\
        </i>  <i class="icon-bin" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer" data-action="DELETE" data-id="'+data+'"></i>';
        return str;
    }
},
{ data: "company_logo" },
{ data: "group_code" },
{ data: "company_code" },
{ data: "company_name" },
{ data: "company_address_1" },
{ data: "company_address_2" },
{ data: "city" },
{ data: "country_code" },
{ data: "company_reg_no" },
{ data: "company_contact_1" },
{ data: "company_contact_2" },
{ data: "company_fax" },
{ data: "company_email" },
{ data: "company_web" },
{ data: "company_remarks" },
{ data: "default_currency" },
{ data: "finance_month" },
{ data: "vat_reg_no" },
{ data: "tax_code" },

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
   targets: [ 0 ]
}],
data: dataSet3,
dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
});



  function get_location_list(){
     var data = [];
     $.ajax({
      url : "Mainlocation.loaddata",
      async : false,
      type : 'get',
      data : {},
      success : function(res){
       data = JSON.parse(res);
        // alert(res);

    },
    error : function(){

    }
});
     return data;
 }



 function reload_table3()
 {
     var dataSet3 = get_location_list();
     var tbl = $('#location_tbl').dataTable();
     tbl.fnClearTable();
     tbl.fnDraw();
     if(dataSet3 != null && dataSet3.length != 0)
      tbl.fnAddData(dataSet3);

}






});





