

var SOURCE_HID = 0;
var X_CSRF_TOKEN = '';
var TABLE = null;
var TABLE2 = null;
var TABLE3 = null;

$(document).ready(function(){


	X_CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

	var validator = app_form_validator('#stores_form',{

        submitHandler: function() { 
            try{
                save_stores();
                $("#stores_form :input").val('');
                validator.resetForm();
            }catch(e){return false;}
            return false; 
        },

        rules: {

        	store_name: {
        		required: true,
        		minlength: 4,
        		remote: {
        			type: "get",
        			url: "OrgStores.check_Store_Name",        		
        			data: {

                        code    : function() { return $( "#store_name" ).val(); },
                        idcode  : function() { return $( "#stores_hid" ).val(); }
                    }
                }       	
            },

            store_address: {
              required: true,
              minlength: 4
          },
          
          
            store_email: {
             email : true
                
            },
            
            loc_id: {
              required: true
                
            }

      },
      messages: {
         store_name:{
          remote: jQuery.validator.format('')
      },

  }
});



    $('#add_data').click(function(){

    	$('#show_stores').modal('show');
    	$('#stores_form')[0].reset();
    	validator.resetForm();
    	$('#btn-save').html('<b><i class="icon-floppy-disk"></i></b> save');
    	//$('#button_action').val('insert');
    	//$('#action').val('Add');

    });
    
    $('#loc_id').select2({

    placeholder: '[ Select and Search ]',
    //allowClear: true,
    ajax: {
        dataType: 'json',
        url: 'OrgStores.load_fac_locations',
        type:'GET',
        delay: 250,
        data: function(params) {
            return {
                search: params.term,
                type: 'public'
            }
        },
        processResults: function (data) {
          return {
            results: $.map(data.items,function(val,i){
                return {id:val.loc_id, text:val.company_code};
            })
        };
    },
}


});


 $('#fac_section').select2({

    placeholder: '[ Select and Search ]',
    //allowClear: true,
    ajax: {
        dataType: 'json',
        url: 'OrgStores.load_fac_section',
        type:'GET',
        delay: 250,
        data: function(params) {
            return {
                search: params.term,
                type: 'public'
            }
        },
        processResults: function (data) {
          return {
            results: $.map(data.items,function(val,i){
                return {id:val.loc_id, text:val.company_code};
            })
        };
    },
}


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
    





    function save_stores(){

     var data = app_serialize_form_to_json('#stores_form');

     data['_token'] = X_CSRF_TOKEN;
     $.ajax({
       url:"OrgStores.postdata",
       async : false,
       type:"post",
       data:data,
       data_type:"json",
       success:function(res)
       {

        if(res.message == true)
        {

         app_alert('success', 'Stores details saved successfully.');
         reload_table();

         $('#stores_form')[0].reset();
         $('#show_stores').modal('toggle');
         validator.resetForm();

     }else{

     }	

 }})


 }




 var dataSet = get_stores_list();

 TABLE = $('#stores_tbl').DataTable({
    autoWidth: false,
    columns: [
    { data: "store_id",
    render : function(data){
     var str = '<i class="icon-pencil" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer;margin-right:3px" data-action="EDIT" data-id="'+data+'">\n\
     </i>  <i class="icon-bin" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer" data-action="DELETE" data-id="'+data+'"></i>';
     return str;
 }
},
{ data: "loc_code" },
{ data: "store_name" },
{ data: "store_address" },
{ data: "store_phone" },
{ data: "store_fax" },
{ data: "store_email" },


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



 function get_stores_list(){
  
     var data = [];
     $.ajax({
      url : "OrgStores.loaddata",
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



 function reload_table()
 {
     var dataset = get_stores_list();
     var tbl = $('#stores_tbl').dataTable();
     tbl.fnClearTable();
     tbl.fnDraw();
     if(dataset != null && dataset.length != 0)
      tbl.fnAddData(dataset);

}


$('#stores_tbl').on('click','i',function(){
    var ele = $(this);
    if(ele.attr('data-action') === 'EDIT'){
        stores_edit(ele.attr('data-id'));
    }
    else if(ele.attr('data-action') === 'DELETE'){
        stores_delete(ele.attr('data-id'));
    }
});


function stores_edit(_id){  
// mig need to check update
    $('#show_stores').modal('show');
    $('#stores_form')[0].reset();
    validator.resetForm(); 

    $.ajax({
        url:"OrgStores.edit",
        type : 'get',
        data : {'store_id' : _id},
        success : function(res){
            var data = JSON.parse(res);
            var load_loc = new Option(data[0].company_code, data[0].loc_id, true, true);



           // alert(data['store_address']);
            $('#stores_hid').val(data[0]['store_id']);
            $('#loc_id').append(load_loc).trigger('change');
            $('#store_address').val(data[0]['store_address']);
            $('#store_name').val(data[0]['store_name']);
            $('#store_phone').val(data[0]['store_phone']);
            $('#store_fax').val(data[0]['store_fax']);
            $('#store_email').val(data[0]['store_email']) 
             
            $('#btn-save').html('<b><i class="icon-pencil"></i></b> Update');
        }
    });
    
}

function stores_delete(_id){ 

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
                url : 'OrgStores.delete',
                type : 'get',
                data : {'store_id' : _id},
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





