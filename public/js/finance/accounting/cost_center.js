(function(){

    var CC_ID = 0;
    var X_CSRF_TOKEN = '';
    var TABLE = null;

    $(function(){

       X_CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

        var validator = app_form_validator('#cost_center_form',{
            submitHandler: function() {
                try{
                    save_cost_center();
                    $("#cost_center_form :input").val('');
                    validator.resetForm();
                    $('#model_cost_center').modal('hide');
                }catch(e){return false;}
                return false;
            },
            rules: {
                cost_center_code: {
                    required : true,
                    minlength : 3,
                    remote: {
                       type: "get",
                       url: "cost-center-check-code"
                  }
                },
                cost_center_name: {
                    required : true
                }
            },
            messages: {
                custom: {
                    required: "This is a custom error message",
                }
            }
        });



        var dataSet = get_cost_center_list();

        TABLE = $('#tbl_cost_center').DataTable({
            autoWidth: false,
             columns: [
                 { data: "cost_center_id",
                render : function(data){
                   var str = '<i class="icon-pencil" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer;margin-right:3px" data-action="EDIT" data-id="'+data+'">\n\
</i>  <i class="icon-bin" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer" data-action="DELETE" data-id="'+data+'"></i>';
                    return str;
                }
                },
            { data: "cost_center_code" },
            { data: "cost_center_name" },
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


      $('#tbl_cost_center').on('click','i',function(){
          var ele = $(this);
          if(ele.attr('data-action') === 'EDIT'){
              cost_center_edit(ele.attr('data-id'));
          }
          else if(ele.attr('data-action') === 'DELETE'){
              cost_center_change_status(ele.attr('data-id'));
          }
      });

      $('#btn_new_cost_center').click(function(){
		  $('#model_cost_center').modal('show');
          $("#cost_center_form :input").val('');
          $("#cost_center_code").prop('disabled', false);
          validator.resetForm();
          $('#btn_save').html('<b><i class="icon-floppy-disk"></i></b> Save');
      });


    });


    function save_cost_center()
	{
        var data = app_serialize_form_to_json('#cost_center_form');
        data['_token'] = X_CSRF_TOKEN;
        data['cost_center_code'] = $('#cost_center_code').val();

        $.ajax({
            url : 'cost-center.save',
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


	function get_cost_center_list(){
		var data = [];
		$.ajax({
			url : 'cost-center.get_list',
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
		var dataset = get_cost_center_list();
		  var tbl = $('#tbl_cost_center').dataTable();
		  tbl.fnClearTable();
		  tbl.fnDraw();
		  if(dataset != null && dataset.length != 0)
			  tbl.fnAddData(dataset);
	}


	function cost_center_edit(_id)
	{
			$.ajax({
				url : 'cost-center.get',
				type : 'get',
				data : {'cost_center_id' : _id},
				success : function(res){
						var data = JSON.parse(res);
						$('#cost_center_id').val(data['cost_center_id']);
						$('#cost_center_code').val(data['cost_center_code']).prop('disabled', true);
						$('#cost_center_name').val(data['cost_center_name']);
            $('#btn_save').html('<b><i class="icon-floppy-disk"></i></b> Update');
            $('#model_cost_center').modal('show');
				}
			});
	}


	function cost_center_change_status(_id){
    app_alert('warning','Do you want to deactivate selected cost center?',function(isConfirm){
			if (isConfirm) { // yes button
				$.ajax({
					url : 'cost-center-change-status',
					type : 'get',
					data : {'cost_center_id' : _id , 'status' : 0},
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
