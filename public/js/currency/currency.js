(function(){
    
    var CUR_ID = 0;
    var X_CSRF_TOKEN = '';
    var TABLE = null;
    
    $(function(){        
       
       X_CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');      
    
        
        var validator = app_form_validator('#currency-form',{
            submitHandler: function() { 
                try{
                    save_currency();
                    $("#currency-form :input").val('');
                    validator.resetForm();
                }catch(e){return false;}
                return false; 
            },
            rules: {
                currency_code: {
                    required : true                
                },
                currency_description: {
                    required : true
                }         
            },
            messages: {
                custom: {
                    required: "This is a custom error message",
                },
                agree: "Please accept our policy"
            }
        });
        
        
        
        var dataSet = get_currency_list();
        
        TABLE = $('#tbl').DataTable({
            //autoWidth: false,
             columns: [
                 { data: "currency_id",
                render : function(data){
                   var str = '<i class="icon-pencil" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer;margin-right:3px" data-action="EDIT" data-id="'+data+'">\n\
</i>  <i class="icon-bin" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer" data-action="DELETE" data-id="'+data+'"></i>';
                    return str;
                }
                },
            { data: "currency_code" },
            { data: "currency_description" },
            
        ],
            columnDefs: [{ 
            orderable: false,
            width: '100px',
            targets: [ 0 ]
        }],
     data: dataSet,
        dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
        });
        
        
      $('#tbl').on('click','i',function(){
          var ele = $(this);
          if(ele.attr('data-action') === 'EDIT'){
              cur_edit(ele.attr('data-id'));
          }
          else if(ele.attr('data-action') === 'DELETE'){
              
          }
      });
      
      $('#btn-new').click(function(){          
          $("#currency-form :input").val('');
          validator.resetForm();
          $('#btn-save').html('<b><i class="icon-floppy-disk"></i></b> Save');
      });
        
        
    });
    

    
    
    
    function save_currency(){
        var data = app_serialize_form_to_json('#currency-form');
        data['_token'] = X_CSRF_TOKEN;
        //var cur_code = $('#cur-code').val();
        //var cur_description = $('#cur-description').val();
        $.ajax({
            url : 'currency.save',
            async : false,
            type : 'post',
            data : data,
            success : function(res){
                var json_res = JSON.parse(res);        
                if(json_res['status'] === 'success'){
                    app_alert('success',json_res['message'],function(){
                        reload_table();
                    });                    
                }
                else{
                    app_alert('error',json_res['message']);
                }            
            },
            error : function(){
                
            }
        });
    }
    
    
function get_currency_list(){
    var data = [];
    $.ajax({
        url : 'currency.get_currency_list',
        async : false,
        type : 'get',
        data : {},//{'cur_code' : cur_code,'cur_description' : cur_description},
        success : function(res){
            data = JSON.parse(res);
            //alert(res);
            //app_alert('Currency details saved successfully.');
        },
        error : function(){

        }
    });
    return data;
}
    
    
function reload_table()
{
	var dataset = get_currency_list();
	  var tbl = $('#tbl').dataTable();
	  tbl.fnClearTable();
	  tbl.fnDraw();
	  if(dataset != null && dataset.length != 0)
	      tbl.fnAddData(dataset);
	 
}


function cur_edit(_id){    
    
    app_alert('warning','Do you want to edit selected currency?',function(isConfirm){
        if (isConfirm) { // yes button
            $.ajax({
                url : 'currency.get',
                type : 'get',
                data : {'cur_id' : _id},
                success : function(res){
                    var data = JSON.parse(res);
                    $('#cur-id').val(data['currency_id']);
                    $('#cur-code').val(data['currency_code']);
                    $('#cur-description').val(data['currency_description']);
                    $('#btn-save').html('<b><i class="icon-floppy-disk"></i></b> Update');
                }
            });
        }           
    });  
    
}


function cur_delete(){
    
}
    
})();

