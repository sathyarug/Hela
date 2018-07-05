(function(){

    var OT = 0;
    var X_CSRF_TOKEN = '';
    var TABLE = null;

    $(function(){

       X_CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

        var validator = app_form_validator('#origin_type_form',{
            submitHandler: function() {
                try{
                    save_origin_type();
                    $("#origin_type_form :input").val('');
                    validator.resetForm();
                    $('#model_origin_type').modal('hide');
                }catch(e){return false;}
                return false;
            },
            rules: {
                origin_type: {
                    required : true,
                    minlength : 3,
                    remote: {
                       type: "get",
                       url: "origin-type-check-code"
                  }
                }
            },
            messages: {
                custom: {
                    required: "This is a custom error message",
                }
            }
        });



        var dataSet = get_origin_type_list();

        TABLE = $('#tbl_origin_type').DataTable({
            autoWidth: false,
             columns: [
                 { data: "origin_type_id",
                render : function(data){
                   var str = '<i class="icon-pencil" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer;margin-right:3px" data-action="EDIT" data-id="'+data+'">\n\
</i>  <i class="icon-bin" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer" data-action="DELETE" data-id="'+data+'"></i>';
                    return str;
                }
                },
            { data: "origin_type" },
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


      $('#tbl_origin_type').on('click','i',function(){
          var ele = $(this);
          if(ele.attr('data-action') === 'EDIT'){
              origin_type_edit(ele.attr('data-id'));
          }
          else if(ele.attr('data-action') === 'DELETE'){
              origin_type_change_status(ele.attr('data-id'));
          }
      });

      $('#btn_new_origin_type').click(function(){
		  $('#model_origin_type').modal('show');
          $("#origin_type_form :input").val('');
          $("#origin_type").prop('disabled', false);
          validator.resetForm();
          $('#btn_save').html('<b><i class="icon-floppy-disk"></i></b> Save');
      });


    });


    function save_origin_type()
	{
        var data = app_serialize_form_to_json('#origin_type_form');
        data['_token'] = X_CSRF_TOKEN;
        data['origin_type'] = $('#origin_type').val();

        $.ajax({
            url : 'origin-type-save',
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


	function get_origin_type_list(){
		var data = [];
		$.ajax({
			url : 'origin-type-get-list',
			async : false,
			type : 'get',
			data : {},
			success : function(res){
				data = JSON.parse(res);
			},
			error : function(){

			}
		});
		return data;
	}


	function reload_table()
	{
		var dataset = get_origin_type_list();
		  var tbl = $('#tbl_origin_type').dataTable();
		  tbl.fnClearTable();
		  tbl.fnDraw();
		  if(dataset != null && dataset.length != 0)
			  tbl.fnAddData(dataset);
	}


	function origin_type_edit(_id)
	{
			$.ajax({
				url : 'origin-type-get',
				type : 'get',
				data : {'origin_type_id' : _id},
				success : function(res){
						var data = JSON.parse(res);
						$('#origin_type_id').val(data['origin_type_id']);
						$('#origin_type').val(data['origin_type']).prop('disabled', true);
            $('#btn_save').html('<b><i class="icon-floppy-disk"></i></b> Update');
            $('#model_origin_type').modal('show');
				}
			});
	}


	function origin_type_change_status(_id){
    app_alert('warning','Do you want to deactivate selected origin type?',function(isConfirm){
			if (isConfirm) { // yes button
				$.ajax({
					url : 'origin-type-change-status',
					type : 'get',
					data : {'origin_type_id' : _id , 'status' : 0},
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
