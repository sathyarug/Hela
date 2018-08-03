(function(){

    var X_CSRF_TOKEN = '';
    var TABLE = null;

    $(function(){

       X_CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

        var validator = app_form_validator('#sub_category_form',{
            submitHandler: function() {
                try{
                    save_sub_category();
                    $("#sub_category_form :input").val('');
                    validator.resetForm();
                    $('#model_sub_category').modal('hide');
                }catch(e){return false;}
                return false;
            },
            rules: {
                subcategory_code: {
                    required : true,
                    minlength : 3,
                    remote: {
                       type: "get",
                       url: "sub-category-check-code"
                   }
                },
                subcategory_name: {
                    required : true
                },
                category_id: {
                    required : true
                }
            },
            messages: {
                custom: {
                    required: "This is a custom error message",
                }
            }
        });



        var dataSet = get_sub_category_list();

        TABLE = $('#tbl_sub_category').DataTable({
            autoWidth: false,
             columns: [
                 { data: "subcategory_id",
                render : function(data){
                   var str = '<i class="icon-pencil" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer;margin-right:3px" data-action="EDIT" data-id="'+data+'">\n\
</i>  <i class="icon-bin" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer" data-action="DELETE" data-id="'+data+'"></i>';
                    return str;
                }
                },
            { data: "subcategory_code" },
            { data: "subcategory_name" },
            { data: "category_name" },
            {
              data: "is_inspectiion_allowed",
              render : function(data){
                if(data == 1){
                    return '<span class="label label-success">Yes</span>';
                }
                else{
                  return '<span class="label label-default">No</span>';
                }
              }
            },
            {
              data: "is_display",
              render : function(data){
                if(data == 1){
                    return '<span class="label label-success">Yes</span>';
                }
                else{
                  return '<span class="label label-default">No</span>';
                }
              }
            },
            {
              data: "status",
              render : function(data){
                if(data == 1){
                    return '<span class="label label-success">Active</span>';
                }
                else{
                  return '<span class="label label-default">Inactive</span>';
                }

              }
           }
        ],
            columnDefs: [{
            orderable: false,
            width: '100px',
            targets: [ 0 ]
        }],
		data: dataSet,
			dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"pi>',
        });


      $('#tbl_sub_category').on('click','i',function(){
          var ele = $(this);
          if(ele.attr('data-action') === 'EDIT'){
              sub_category_edit(ele.attr('data-id'));
          }
          else if(ele.attr('data-action') === 'DELETE'){
              sub_category_change_status(ele.attr('data-id'));
          }
      });

      $('#btn_new_sub_category').click(function(){
		  $('#model_sub_category').modal('show');
          $("#sub_category_form :input").val('');
          $(':checkbox').prop('checked', false);
          $("#sub_category_code").prop('disabled', false);
          validator.resetForm();
          $('#btn_save').html('<b><i class="icon-floppy-disk"></i></b> Save');
      });


    });


    function save_sub_category()
	{
        var data = app_serialize_form_to_json('#sub_category_form');
        data['_token'] = X_CSRF_TOKEN;
        data['subcategory_code'] = $('#subcategory_code').val();
        data['is_display'] = $("#is_display").is(':checked') ? 1 : 0;
        data['is_inspectiion_allowed'] = $("#is_inspectiion_allowed").is(':checked') ? 1 : 0;

        $.ajax({
            url : 'sub-category-save',
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


	function get_sub_category_list(){
		var data = [];
		$.ajax({
			url : 'sub-category-get-list',
			async : false,
			type : 'get',
                        dataType:'JSON',
			data : {},
			success : function(res){
				//data = JSON.parse(res);
				data = JSON.parse(JSON.stringify(res));
			},
			error : function(){

			}
		});
		return data;
	}


	function reload_table()
	{
		var dataset = get_sub_category_list();
		  var tbl = $('#tbl_sub_category').dataTable();
		  tbl.fnClearTable();
		  tbl.fnDraw();
		  if(dataset != null && dataset.length != 0)
			  tbl.fnAddData(dataset);
	}


	function sub_category_edit(_id)
	{
			$.ajax({
				url : 'sub-category-get',
				type : 'get',
				data : {'subcategory_id' : _id},
				success : function(res){
						var data = JSON.parse(res);
						$('#subcategory_id').val(data['subcategory_id']);
						$('#subcategory_code').val(data['subcategory_code']).prop('disabled', true);
						$('#category_code').val(data['category_code']);
            $('#subcategory_name').val(data['subcategory_name']);
            $('#is_inspectiion_allowed').prop('checked', (data['is_inspectiion_allowed']== 1) ? true : false);
            $('#is_display').prop('checked', (data['is_display']== 1) ? true : false);
            $('#model_sub_category').modal('show');
				}
			});
	}


	function sub_category_change_status(_id){
    app_alert('warning','Do you want to deactivate selected sub category?',function(isConfirm){
			if (isConfirm) { // yes button
				$.ajax({
					url : 'sub-category-change-status',
					type : 'get',
					data : {'subcategory_id' : _id , 'status' : 0},
					success : function(res){
  						var data = JSON.parse(res);
  					  if(data['status'] == 'success'){
                reload_table();
              }
					}
				});
			}
		});
	}
        
        

})();

function LoadSubCategories(mainCatCode){
    
    $.ajax({
        url:'get-subcatby-maincat',
        type:'get',
        data:{'category_id': mainCatCode},
        dataType:'JSON',
        success:function(res){
            
            $.each(res, function(key, value){
               $("#subcategory").append(new Option(key, value)); 
            });
                
            
            
            //alert(JSON.parse(JSON.stringify(res)));
        }
    });
            
}