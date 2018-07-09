(function(){

    var X_CSRF_TOKEN = '';
    var TABLE = null;

    $(function(){

       X_CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

        var validator = app_form_validator('#color_form',{
            submitHandler: function() {
                try{
                    save_color();
                    $("#color_form :input").val('');
                    validator.resetForm();
                    $('#model_color').modal('hide');
                }catch(e){return false;}
                return false;
            },
            rules: {
                color_code: {
                    required : true,
                    minlength : 3,
                    remote: {
                       type: "get",
                       url: "color-check-code"
                  }
                },
                color_name: {
                    required : true,
                    minlength : 3,
                    remote: {
                       type: "get",
                       url: "color-check-name"
                  }
                }
            }
        });



        var dataSet = get_color_list();

        TABLE = $('#tbl_color').DataTable({
            autoWidth: false,
             columns: [
                 { data: "color_id",
                render : function(data){
                   var str = '<i class="icon-pencil" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer;margin-right:3px" data-action="EDIT" data-id="'+data+'">\n\
</i>  <i class="icon-bin" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer" data-action="DELETE" data-id="'+data+'"></i>';
                    return str;
                }
                },
            { data: "color_code" },
            { data: "color_name" },
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


      $('#tbl_color').on('click','i',function(){
          var ele = $(this);
          if(ele.attr('data-action') === 'EDIT'){
              color_edit(ele.attr('data-id'));
          }
          else if(ele.attr('data-action') === 'DELETE'){
              color_change_status(ele.attr('data-id'));
          }
      });

      $('#btn_new_color').click(function(){
		  $('#model_color').modal('show');
          $("#color_form :input").val('');
          $("#color_code").prop('disabled', false);
          validator.resetForm();
          $('#btn_save').html('<b><i class="icon-floppy-disk"></i></b> Save');
      });


    });


    function save_color()
	{
        var data = app_serialize_form_to_json('#color_form');
        data['_token'] = X_CSRF_TOKEN;
        //data['origin_type'] = $('#origin_type').val();

        $.ajax({
            url : 'color-save',
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


	function get_color_list(){
		var data = [];
		$.ajax({
			url : 'color-get-list',
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
		var dataset = get_color_list();
		  var tbl = $('#tbl_color').dataTable();
		  tbl.fnClearTable();
		  tbl.fnDraw();
		  if(dataset != null && dataset.length != 0)
			  tbl.fnAddData(dataset);
	}


	function color_edit(_id)
	{
			/*$.ajax({
				url : 'color-get',
				type : 'get',
				data : {'color_id' : _id},
				success : function(res){
						var data = JSON.parse(res);
						$('#color_id').val(data['color_id']);
						$('#color_code').val(data['color_code']).prop('disabled', true);
            $('#color_name').val(data['color_name']).prop('disabled', true);
            $('#btn_save').html('<b><i class="icon-floppy-disk"></i></b> Update');
            $('#model_color').modal('show');
				}
			});*/
	}


	function color_change_status(_id){
    app_alert('warning','Do you want to deactivate selected color?',function(isConfirm){
			if (isConfirm) { // yes button
				$.ajax({
					url : 'color-change-status',
					type : 'get',
					data : {'color_id' : _id , 'status' : 0},
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
