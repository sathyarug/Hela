

var SOURCE_HID = 0;
var CLUSTER_HID = 0;
var LOCATION_HID = 0;
var SUB_LOCATION_HID = 0;
var X_CSRF_TOKEN = '';
var TABLE = null;
var TABLE2 = null;
var TABLE3 = null;
var TABLE4 = null;

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


    function save_source(){

     var data = app_serialize_form_to_json('#source_form');
     data['_token'] = X_CSRF_TOKEN;
     data['source_code'] = $('#source-code').val();

     $.ajax({
       url:"Mainsource.postdata",
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
                $('#source_form')[0].reset();
                $('#show_source').modal('toggle');
                validator.resetForm();

            }
            else
            {
                app_alert('error',json_res['message']);
            }      


        }})


 }




 var dataSet = get_source_list();

 TABLE = $('#source_tbl').DataTable({
    autoWidth: false,
    order: [[ 1, "asc" ]],
    columns: [
    { data: "source_id",
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
<<<<<<< HEAD
{ data: "source_code" },
{ data: "source_name" },

=======
>>>>>>> origin/master
{
    'data' : function(_data){
       if (_data['status'] == '1'){
           return '<td><span class="label label-success">Active</span></td>';
       }else{
           return '<td><span class="label label-default">Inactive</span></td>';   
       }
   }
},
<<<<<<< HEAD

<<<<<<< HEAD
=======
{ data: "source_code" },
{ data: "source_name" },


>>>>>>> origin/master
=======
{ data: "source_code" },
{ data: "source_name" }
>>>>>>> origin/master


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
<<<<<<< HEAD
            $('#source-code').val(data['source_code']);
=======
            $('#source-code').val(data['source_code']).prop('disabled', true);
>>>>>>> origin/master
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
//========================================================================================================


$('#add_cluster').click(function(){

    $('#show_cluster').modal('show');
    $('#cluster_form')[0].reset();
    validator1.resetForm();
    $('#btn-save-2').html('<b><i class="icon-floppy-disk"></i></b> save');
    $('#main_source').val(null).trigger('change');
        //$('#button_action').val('insert');
        //$('#action').val('Add');

    });



var validator1 = app_form_validator('#cluster_form',{

    submitHandler: function() { 
        try{
            save_cluster();
            $("#cluster_form :input").val('');
            validator1.resetForm();
        }catch(e){return false;}
        return false; 
    },

    rules: {



        group_code: {
            required: true,
            minlength: 4,
            remote: {
                type: "get",
                url: "Maincluster.check_code",               
                data: {

                    code    : function() { return $( "#cluster_code" ).val(); },
                    idcode  : function() { return $( "#cluster_hid" ).val(); }
                }
            }   

        },

        source_id: {
            required: true

        },

        group_name: {
          required: true,
          minlength: 4
      },

  },
  messages: {
     group_code:{
      remote: jQuery.validator.format('')
  },

}


});


function save_cluster(){

 var data = app_serialize_form_to_json('#cluster_form');
 data['_token'] = X_CSRF_TOKEN;
 data['group_code'] = $('#cluster_code').val();

 $.ajax({
   url:"Maincluster.postdata",
   async : false,
   type:"post",
   data:data,
   data_type:"json",
   success:function(res)
   {
    var json_res = JSON.parse(res); 
            //alert(json_res['status']) ;
            if(json_res['status'] === 'success')
            {
                app_alert('success',json_res['message']);
                reload_table2();
                $('#cluster_form')[0].reset();
                $('#show_cluster').modal('toggle');
                validator1.resetForm();

            }
            else
            {
                app_alert('error',json_res['message']);
            }      


        }})


}

var dataSet2 = get_cluster_list();

TABLE2 = $('#cluster_tbl').DataTable({
    autoWidth: false,
    order: [[ 1, "asc" ]],
    columns: [
    { data: "group_id",
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
<<<<<<< HEAD
{ data: "group_code" },
{ data: "source_code" },
{ data: "group_name" },
=======
>>>>>>> origin/master
{
    'data' : function(_data){
       if (_data['status'] == '1'){
           return '<td><span class="label label-success">Active</span></td>';
       }else{
           return '<td><span class="label label-default">Inactive</span></td>';   
       }
   }
},
<<<<<<< HEAD
=======
{ data: "group_code" },
{ data: "source_code" },
{ data: "group_name" },

>>>>>>> origin/master

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


$('#cluster_tbl').on('click','i',function(){
    var ele = $(this);
    if(ele.attr('data-action') === 'EDIT'){
        cluster_edit(ele.attr('data-id'));
    }
    else if(ele.attr('data-action') === 'DELETE'){
        cluster_delete(ele.attr('data-id'));
    }
});


function cluster_edit(_id){  

    $('#show_cluster').modal('show');
    $('#cluster_form')[0].reset();
    validator1.resetForm(); 

    $.ajax({
        url : 'Maincluster.edit',
        type : 'get',
        data : {'group_id' : _id},
        success : function(res){
            var data = JSON.parse(res);
            var loadsource = new Option(data[0].source_name, data[0].source_id, true, true);
            // Append it to the select
            $('#main_source').append(loadsource).trigger('change');

            $('#cluster_hid').val(data[0]['group_id']);
<<<<<<< HEAD
            $('#cluster_code').val(data[0]['group_code']);
=======
            $('#cluster_code').val(data[0]['group_code']).prop('disabled', true);
>>>>>>> origin/master
            $('#cluster_name').val(data[0]['group_name']);
            $('#btn-save-2').html('<b><i class="icon-pencil"></i></b> Update');
        }
    });
    
}

function cluster_delete(_id){ 

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
                url : 'Maincluster.delete',
                type : 'get',
                data : {'group_id' : _id},
                success : function(res){
                    var data = JSON.parse(res);
                    swal({
                        title: "Deleted!",
                        text: "Cluster file has been deleted.",
                        confirmButtonColor: "#66BB6A",
                        type: "success"
                    });

                    reload_table2();

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


$('#main_source').select2({

    placeholder: '[ Select and Search ]',
    //allowClear: true,
    ajax: {
        dataType: 'json',
        url: 'Mainsource.load_list',
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
                return {id:val.source_id, text:val.source_name};
            })
        };
    },
}


});








  // Main Location Codes ====================================================================================
  //=========================================================================================================

  $('#add_company').click(function(){

    $('#show_location').modal('show');
    $('#location_form')[0].reset();
    validator2.resetForm();
    $('#btn-save-3').html('<b><i class="icon-floppy-disk"></i></b> save');
    $('#main_cluster').val(null).trigger('change');
    $('#ctry_code').val(null).trigger('change');
    $('#def_curr').val(null).trigger('change');
    $('#fin_month').val(null).trigger('change');
    $('#sec_mulname').empty().trigger('change');
    $('#sel_depart').empty().trigger('change');

    
});


  var validator2 = app_form_validator('#location_form',{

    submitHandler: function() { 
        try{
            save_location();
            $("#location_form :input").val('');
            validator2.resetForm();
        }catch(e){return false;}
        return false; 
    },

    rules: {

        group_id: {
            required: true

        },

        company_code: {
            required: true,
            minlength: 4,
            remote: {
                type: "get",
                url: "Mainlocation.check_code",               
                data: {

                    code    : function() { return $( "#company_code" ).val(); },
                    idcode  : function() { return $( "#location_hid" ).val(); }
                }
            }   

        },

        company_name: {
            required: true,
            minlength: 4

        },

        company_address_1: {
          required: true,
          minlength: 4

      },

      city: {
        required: true,
        minlength: 4

    },

    country_code: {
        required: true


    },

    company_reg_no: {
        required: true,
        minlength: 4

    },

    company_contact_1: {
        required: true,
        number: true,
        minlength: 10

    },

    company_email: {
        email: true

    },

    vat_reg_no: {
        required: true,
        minlength: 4

    },

    tax_code: {
        required: true,
        minlength: 4

    },

    default_currency: {
        required: true

    },

    finance_month: {
        required: true


    },




},
messages: {
 company_code:{
  remote: jQuery.validator.format('')
},

}


});


  function save_location(){

     var data = app_serialize_form_to_json('#location_form');
     data['_token'] = X_CSRF_TOKEN;
     data['company_code'] = $('#company_code').val();

     $.ajax({
       url:"Mainlocation.postdata",
       async : false,
       type:"post",
       data:data,
       data_type:"json",
       success:function(res)
       {
        var json_res = JSON.parse(res); 
            //alert(json_res['status']) ;
            if(json_res['status'] === 'success')
            {
                app_alert('success',json_res['message']);
                reload_table3();
                $('#location_form')[0].reset();
                $('#show_location').modal('toggle');
                validator2.resetForm();

            }
            else
            {
                app_alert('error',json_res['message']);
            }      


        }})


 }


 var dataSet3 = get_location_list();

 TABLE3 = $('#location_tbl').DataTable({
    order: [[ 1, "asc" ]],
    scrollY:        "300px",
    scrollX:        true,
    scrollCollapse: true,

    fixedColumns:   { leftColumns: 5},

    columns: [
    { data: "company_id",
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
<<<<<<< HEAD

=======
{
    'data' : function(_data){
       if (_data['status'] == '1'){
           return '<td><span class="label label-success">Active</span></td>';
       }else{
           return '<td><span class="label label-default">Inactive</span></td>';   
       }
   }
},
// {
//     'data' : function(_data){
      
//            return '<td><i class="icon-plus22" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer" data-action="Sec_popup" data-id="'+_data['company_id']+'"></i></td>';
      
<<<<<<< HEAD
   }
},
>>>>>>> origin/master
=======
//    }
// },
>>>>>>> origin/master
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
{ data: "company_logo" },

<<<<<<< HEAD
{
    'data' : function(_data){
       if (_data['status'] == '1'){
           return '<td><span class="label label-success">Active</span></td>';
       }else{
           return '<td><span class="label label-default">Inactive</span></td>';   
       }
   }
},
=======

>>>>>>> origin/master

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





$('#main_cluster').select2({

    placeholder: '[ Select and Search ]',
    //allowClear: true,
    ajax: {
        dataType: 'json',
        url: 'Mainlocation.load_list',
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
                return {id:val.group_id, text:val.group_name};
            })
        };
    },
}


});


$('#ctry_code').select2({

    placeholder: '[ Select and Search ]',
    //allowClear: true,
    ajax: {
        dataType: 'json',
        url: 'Mainlocation.load_country',
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
                return {id:val.country_id, text:val.country_description};
            })
        };
    },
}


});



$('#def_curr').select2({

    placeholder: '[ Select and Search ]',
    //allowClear: true,
    ajax: {
        dataType: 'json',
        url: 'Mainlocation.load_currency',
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
                return {id:val.currency_id, text:val.currency_description};
            })
        };
    },
}


});



$('#location_tbl').on('click','i',function(){
    var ele = $(this);
    if(ele.attr('data-action') === 'EDIT'){
        location_edit(ele.attr('data-id'));
    }
    else if(ele.attr('data-action') === 'DELETE'){
        location_delete(ele.attr('data-id'));
    }
<<<<<<< HEAD
<<<<<<< HEAD
});


=======
    else if(ele.attr('data-action') === 'Sec_popup'){
        location_load_sec(ele.attr('data-id'));
        //alert(ele.attr('data-id'));
    }
=======
    
>>>>>>> origin/master
});



$('#sec_mulname').select2({

    placeholder: '[ Select and Search ]',
    //allowClear: true,
    ajax: {
        dataType: 'json',
        url: 'Mainlocation.load_section_list',
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
                return {id:val.section_id, text:val.section_name};
            })
        };
    },
}


});

$('#sel_depart').select2({

    placeholder: '[ Select and Search ]',
    //allowClear: true,
    ajax: {
        dataType: 'json',
        url: 'Mainlocation.load_depat_list',
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
                return {id:val.dep_id, text:val.dep_name};
            })
        };
    },
}


});





>>>>>>> origin/master
function location_edit(_id){ 

 $('#show_location').modal('show');
 $('#location_form')[0].reset();
 validator2.resetForm(); 
 $('#sec_mulname').empty().trigger('change');
 $('#sel_depart').empty().trigger('change');

 $.ajax({
    url : 'Mainlocation.edit',
    type : 'get',
    data : {'loc_id' : _id},
    success : function(res){
        var data = JSON.parse(res);
        //alert(data.com_hed[0]['company_code']);
        var loadclust = new Option(data.com_hed[0].group_name, data.com_hed[0].group_id, true, true);
        var loadfinmonth = new Option(data.com_hed[0].finance_month, data.com_hed[0].finance_month, true, true);
        var loadcountry = new Option(data.com_hed[0].country_description, data.com_hed[0].country_code, true, true);
        var loadcurrency = new Option(data.com_hed[0].currency_description, data.com_hed[0].default_currency, true, true);
        
        for(var i=0;i<data.multi.length;i++){
            var loadmulti= new Option(data.multi[i].section_name, data.multi[i].section_id, true, true);
            $('#sec_mulname').append(loadmulti).trigger('change');
            }

        for(var i=0;i<data.dep.length;i++){
            var loaddep= new Option(data.dep[i].dep_name, data.dep[i].com_dep_name, true, true);
            $('#sel_depart').append(loaddep).trigger('change');
            }

        $('#main_cluster').append(loadclust).trigger('change');
        $('#fin_month').append(loadfinmonth).trigger('change');
        $('#ctry_code').append(loadcountry).trigger('change');
        $('#def_curr').append(loadcurrency).trigger('change');
<<<<<<< HEAD

        $('#location_hid').val(data[0]['company_id']);
<<<<<<< HEAD
        $('#company_code').val(data[0]['company_code']);
=======
        $('#company_code').val(data[0]['company_code']).prop('disabled', true);
>>>>>>> origin/master
        $('#company_name').val(data[0]['company_name']);
        $('#company_ad1').val(data[0]['company_address_1']);
        $('#company_ad2').val(data[0]['company_address_2']);
        $('#company_city').val(data[0]['city']);
        $('#com_regnum').val(data[0]['company_reg_no']);
        $('#contact_1').val(data[0]['company_contact_1']);
        $('#contact_2').val(data[0]['company_contact_2']);
        $('#con_fax').val(data[0]['company_fax']);
        $('#com_email').val(data[0]['company_email']);
        $('#com_web').val(data[0]['company_web']);
        $('#com_remak').val(data[0]['company_remarks']);
        $('#vat_regnum').val(data[0]['vat_reg_no']);
        $('#tax_code').val(data[0]['tax_code']);
=======
        
        $('#location_hid').val(data.com_hed[0]['company_id']);
        $('#company_code').val(data.com_hed[0]['company_code']).prop('disabled', true);
        $('#company_name').val(data.com_hed[0]['company_name']);
        $('#company_ad1').val(data.com_hed[0]['company_address_1']);
        $('#company_ad2').val(data.com_hed[0]['company_address_2']);
        $('#company_city').val(data.com_hed[0]['city']);
        $('#com_regnum').val(data.com_hed[0]['company_reg_no']);
        $('#contact_1').val(data.com_hed[0]['company_contact_1']);
        $('#contact_2').val(data.com_hed[0]['company_contact_2']);
        $('#con_fax').val(data.com_hed[0]['company_fax']);
        $('#com_email').val(data.com_hed[0]['company_email']);
        $('#com_web').val(data.com_hed[0]['company_web']);
        $('#com_remak').val(data.com_hed[0]['company_remarks']);
        $('#vat_regnum').val(data.com_hed[0]['vat_reg_no']);
        $('#tax_code').val(data.com_hed[0]['tax_code']);
>>>>>>> origin/master

        $('#btn-save-3').html('<b><i class="icon-pencil"></i></b> Update');
    }
});

}

function location_delete(_id){ 

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
                url : 'Mainlocation.delete',
                type : 'get',
                data : {'loc_id' : _id},
                success : function(res){
                    var data = JSON.parse(res);
                    swal({
                        title: "Deleted!",
                        text: "Location file has been deleted.",
                        confirmButtonColor: "#66BB6A",
                        type: "success"
                    });

                    reload_table3();

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




// Main Sub Location Codes ====================================================================================
  //=========================================================================================================

  $('#add_sublocation').click(function(){

    $('#show_sub_location').modal('show');
    $('#sub_location_form')[0].reset();
    validator4.resetForm();
    $('#btn-save-4').html('<b><i class="icon-floppy-disk"></i></b> save');
    $('#company_id').val(null).trigger('change');
    $('#loc_type').val(null).trigger('change');
    $('#country_code').val(null).trigger('change');
    $('#currency_code').val(null).trigger('change');
    $('#type_of_loc').val(null).trigger('change');
    $('#type_center').val(null).trigger('change');
    $('#type_property').val(null).trigger('change');
    
    
});

 var validator4 = app_form_validator('#sub_location_form',{

    submitHandler: function() { 
        try{
            save_sub_location();
            $("#sub_location_form :input").val('');
            validator4.resetForm();
        }catch(e){return false;}
        return false; 
    },

    rules: {

        company_id: {
            required: true

        },

        loc_code: {
            required: true,
            minlength: 4,
            remote: {
                type: "get",
                url: "MainSubLocation.check_code",               
                data: {

                    code    : function() { return $( "#loc_code" ).val(); },
                    idcode  : function() { return $( "#sub_location_hid" ).val(); }
                }
            }   

        },

        loc_name: {
            required: true,
            minlength: 4

        },

        loc_type: {
        required: true


         },

        loc_address_1: {
          required: true,
          minlength: 4

      },

      city: {
        required: true,
        minlength: 4

    },

    country_code: {
        required: true

    },

    loc_phone: {
        required: true,
        number: true,
        minlength: 10

    },

    loc_email: {
        email: true

    },

    time_zone: {
        required: true

    },

    currency_code: {
        required: true

    },

    opr_start_date: {
        required: true

    },


},
messages: {
 loc_code:{
  remote: jQuery.validator.format('')
},

}


});


function save_sub_location(){

     var data = app_serialize_form_to_json('#sub_location_form');
         data['_token'] = X_CSRF_TOKEN;
         data['loc_code'] = $('#loc_code').val();

         //alert(data);

     $.ajax({
       url:"MainSubLocation.postdata",
       async : false,
       type:"post",
       data:data,
       data_type:"json",
       success:function(res)
       {
        var json_res = JSON.parse(res); 
            //alert(json_res['status']) ;
            if(json_res['status'] === 'success')
            {
                app_alert('success',json_res['message']);
                reload_table4();
                $('#sub_location_form')[0].reset();
                $('#show_sub_location').modal('toggle');
                validator4.resetForm();

            }
            else
            {
                app_alert('error',json_res['message']);
            }      


        }})


 }


var dataSet4 = get_sub_location_list();
//alert(dataSet4);

TABLE4 = $('#sub_location_tbl').DataTable({
    order: [[ 1, "asc" ]],
    autoWidth: false,
    scrollY: "300px",
    scrollX: true,
    scrollCollapse: true,
    columns: [
    { data: "loc_id",
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
<<<<<<< HEAD
=======
{
    'data' : function(_data){
       if (_data['status'] == '1'){
           return '<td><span class="label label-success">Active</span></td>';
       }else{
           return '<td><span class="label label-default">Inactive</span></td>';   
       }
   }
},
>>>>>>> origin/master
{ data: "loc_code" },
{ data: "company_name" },
{ data: "loc_name" },
{ data: "loc_type" },
{ data: "loc_address_1" },
{ data: "loc_address_2" },
{ data: "city" },
{ data: "country_code" },
{ data: "loc_phone" },
{ data: "loc_fax" },
{ data: "loc_email" },
{ data: "loc_web" },
{ data: "time_zone" },
{ data: "currency_code" },
<<<<<<< HEAD
{
    'data' : function(_data){
       if (_data['status'] == '1'){
           return '<td><span class="label label-success">Active</span></td>';
       }else{
           return '<td><span class="label label-default">Inactive</span></td>';   
       }
   }
},
=======

>>>>>>> origin/master

],
columnDefs: [{ 
   orderable: false,
   width: '100px',
   targets: [ 0 ]
}],
data: dataSet4,
dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
});



function get_sub_location_list(){
 var data = [];
 $.ajax({
  url : "MainSubLocation.loaddata",
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



function reload_table4()
{
 var dataSet4 = get_sub_location_list();
 var tbl = $('#sub_location_tbl').dataTable();
 tbl.fnClearTable();
 tbl.fnDraw();
 if(dataSet4 != null && dataSet4.length != 0)
  tbl.fnAddData(dataSet4);

}


$('#sub_location_tbl').on('click','i',function(){
    var ele = $(this);
    if(ele.attr('data-action') === 'EDIT'){
        sub_location_edit(ele.attr('data-id'));
    }
    else if(ele.attr('data-action') === 'DELETE'){
        sub_location_delete(ele.attr('data-id'));
    }
});


function sub_location_edit(_id){ 
 //$('#type_center').val(null).trigger('change');
$("#type_center").empty().trigger('change')

 $('#show_sub_location').modal('show');
 $('#sub_location_form')[0].reset();
 validator4.resetForm(); 

 $.ajax({
    url : 'MainSubLocation.edit',
    type : 'get',
    data : {'company_id' : _id},
    success : function(res){
        var data = JSON.parse(res);
       
        var loadcompany = new Option(data.sl_hd[0].company_name, data.sl_hd[0].company_id, true, true);
        var loadismanu = new Option(data.sl_hd[0].loc_type, data.sl_hd[0].loc_type, true, true);
        var loadcountry = new Option(data.sl_hd[0].country_description, data.sl_hd[0].country_code, true, true);
        var loadcurrency = new Option(data.sl_hd[0].currency_description, data.sl_hd[0].currency_code, true, true);
        var loadloctype = new Option(data.sl_hd[0].type_location, data.sl_hd[0].type_of_loc, true, true);
        var loadproperty = new Option(data.sl_hd[0].type_property, data.sl_hd[0].type_prop_id, true, true);

        for(var i=0;i<data.sl_mul.length;i++){
            var loadmulti= new Option(data.sl_mul[i].cost_center_name, data.sl_mul[i].cost_name, true, true);
            $('#type_center').append(loadmulti).trigger('change');
            }

        $('#company_id').append(loadcompany).trigger('change');
        $('#loc_type').append(loadismanu).trigger('change');
        $('#country_code').append(loadcountry).trigger('change');
        $('#currency_code').append(loadcurrency).trigger('change');
<<<<<<< HEAD

        $('#sub_location_hid').val(data[0]['loc_id']);
<<<<<<< HEAD
        $('#loc_code').val(data[0]['loc_code']);
=======
        $('#loc_code').val(data[0]['loc_code']).prop('disabled', true);
>>>>>>> origin/master
        $('#loc_name').val(data[0]['loc_name']);
        $('#loc_address_1').val(data[0]['loc_address_1']);
        $('#loc_address_2').val(data[0]['loc_address_2']);
        $('#city').val(data[0]['city']);
        $('#loc_phone').val(data[0]['loc_phone']);
        $('#loc_fax').val(data[0]['loc_fax']);
        $('#loc_email').val(data[0]['loc_email']);
        $('#loc_web').val(data[0]['loc_web']);
        $('#time_zone').val(data[0]['time_zone']);
=======
        $('#type_of_loc').append(loadloctype).trigger('change');
        $('#type_property').append(loadproperty).trigger('change');
 
        $('#sub_location_hid').val(data.sl_hd[0]['loc_id']);
        $('#loc_code').val(data.sl_hd[0]['loc_code']).prop('disabled', true);
        $('#loc_name').val(data.sl_hd[0]['loc_name']);
        $('#loc_address_1').val(data.sl_hd[0]['loc_address_1']);
        $('#loc_address_2').val(data.sl_hd[0]['loc_address_2']);
        $('#city').val(data.sl_hd[0]['city']);
        $('#loc_phone').val(data.sl_hd[0]['loc_phone']);
        $('#loc_fax').val(data.sl_hd[0]['loc_fax']);
        $('#loc_email').val(data.sl_hd[0]['loc_email']);
        $('#loc_web').val(data.sl_hd[0]['loc_web']);
        $('#time_zone').val(data.sl_hd[0]['time_zone']);
        $('#postal_code').val(data.sl_hd[0]['postal_code']);
        $('#state_Territory').val(data.sl_hd[0]['state_Territory']);
        $('#loc_google').val(data.sl_hd[0]['loc_google']);
        $('#land_acres').val(data.sl_hd[0]['land_acres']);
        $('#latitude').val(data.sl_hd[0]['latitude']);
        $('#longitude').val(data.sl_hd[0]['longitude']);
        $('#opr_start_date').val(data.sl_hd[0]['opr_start_date']);
>>>>>>> origin/master
        
        $('#btn-save-4').html('<b><i class="icon-pencil"></i></b> Update');
    }
});

}

function sub_location_delete(_id){ 

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
                url : 'MainSubLocation.delete',
                type : 'get',
                data : {'company_id' : _id},
                success : function(res){
                    var data = JSON.parse(res);
                    swal({
                        title: "Deleted!",
                        text: "Location file has been deleted.",
                        confirmButtonColor: "#66BB6A",
                        type: "success"
                    });

                reload_table4();

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



$('#company_id').select2({

    placeholder: '[ Select and Search ]',
    //allowClear: true,
    ajax: {
        dataType: 'json',
        url: 'MainSubLocation.load_list',
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
                return {id:val.company_id, text:val.company_name};
            })
        };
    },
}


});


$('#country_code').select2({

    placeholder: '[ Select and Search ]',
    //allowClear: true,
    ajax: {
        dataType: 'json',
        url: 'Mainlocation.load_country',
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
                return {id:val.country_id, text:val.country_description};
            })
        };
    },
}


});



$('#currency_code').select2({

    placeholder: '[ Select and Search ]',
    //allowClear: true,
    ajax: {
        dataType: 'json',
        url: 'Mainlocation.load_currency',
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
                return {id:val.currency_id, text:val.currency_description};
            })
        };
    },
}


});


$('#type_of_loc').select2({

    placeholder: '[ Select and Search ]',
    //allowClear: true,
    ajax: {
        dataType: 'json',
        url: 'MainSubLocation.type_of_loc',
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
                return {id:val.type_loc_id, text:val.type_location};
            })
        };
    },
}


});


$('#type_center').select2({

    placeholder: '[ Select and Search ]',
    //allowClear: true,
    ajax: {
        dataType: 'json',
        url: 'MainSubLocation.load_cost_center',
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
                return {id:val.cost_center_id, text:val.cost_center_name};
            })
        };
    },
}


});


$('#type_property').select2({

    placeholder: '[ Select and Search ]',
    //allowClear: true,
    ajax: {
        dataType: 'json',
        url: 'MainSubLocation.load_property',
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
                return {id:val.type_prop_id, text:val.type_property};
            })
        };
    },
}


});





});





