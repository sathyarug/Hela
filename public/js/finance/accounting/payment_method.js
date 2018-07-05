(function(){

    var PAYMENT_METHOD_ID = 0;
    var X_CSRF_TOKEN = '';
    var TABLE = null;

    $(function(){

       X_CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');


        var validator = app_form_validator('#payment_method_form',{
            submitHandler: function() {
                try{
                    save_payment_method();
                    $("#payment_method_form :input").val('');
                    validator.resetForm();
                    $('#model_payment_method').modal('hide');
                }catch(e){return false;}
                return false;
            },
            rules: {
                 payment_method_code: {
                    required : true,
                    minlength: 3,
                    remote: {
                       type: "get",
                       url: "payment-method-check-code"
                  }
                },
                  payment_method_description: {
                      required : true,
                      minlength: 4
                  }
            },
            messages: {
                custom: {
                    required: "This is a custom error message",
                }
            }
        });



        var dataSet = get_payment_method_list();

        TABLE = $('#tbl_payment_method').DataTable({
            autoWidth: false,
        columns: [
             {
               data: "payment_method_id",
               render : function(data,arg,full){
                 var str = '<i class="icon-pencil" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer;margin-right:3px" data-action="EDIT" data-id="'+data+'"></i>';
                 if(full['status'] == 1){
                   str += '<i class="icon-bin" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer" data-action="DELETE" data-id="'+data+'"></i>';
                 }
                  return str;
              }
            },
            { data: "payment_method_code" },
            { data: "payment_method_description" },
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
           },

        ],
            columnDefs: [{
            orderable: false,
            width: '100px',
            targets: [ 0 ]
        }],
     data: dataSet,
        dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"pi>',
        });


      $('#tbl_payment_method').on('click','i',function(){
          var ele = $(this);
          if(ele.attr('data-action') === 'EDIT'){
              payment_method_edit(ele.attr('data-id'));
          }
          else if(ele.attr('data-action') === 'DELETE'){
              payment_method_change_status(ele.attr('data-id'));
          }
      });

      $('#btn_new_payment_method').click(function(){
          $("#payment_method_form :input").val('');
          validator.resetForm();
          $('#btn_save_payment_method').html('<b><i class="icon-floppy-disk"></i></b> Save');
          $("#payment_method_code").prop('disabled', false);
          $('#model_payment_method').modal('show');
      });


    });





    function save_payment_method(){
        var data = app_serialize_form_to_json('#payment_method_form');
        data['_token'] = X_CSRF_TOKEN;
        data['payment_method_code'] = $('#payment_method_code').val();
        //var cur_code = $('#cur-code').val();
        //var cur_description = $('#cur-description').val();
        $.ajax({
            url : 'payment-method.save',
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


function get_payment_method_list(){
    var data = [];
    $.ajax({
        url : 'payment-method.get_payment_method_list',
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
	var dataset = get_payment_method_list();
	  var tbl = $('#tbl_payment_method').dataTable();
	  tbl.fnClearTable();
	  tbl.fnDraw();
	  if(dataset != null && dataset.length != 0)
	      tbl.fnAddData(dataset);

}


function payment_method_edit(_id){
    $.ajax({
        url : 'payment-method.get',
        type : 'get',
        data : {'payment_method_id' : _id},
        success : function(res){
            var data = JSON.parse(res);
            $('#payment_method_id').val(data['payment_method_id']);
            $('#payment_method_code').val(data['payment_method_code']).prop('disabled', true);;
            $('#payment_method_description').val(data['payment_method_description']);
            $('#btn_save_payment_method').html('<b><i class="icon-floppy-disk"></i></b> Update');
            $('#model_payment_method').modal('show');
        }
    });
}


function payment_method_change_status(_id){
  app_alert('warning','Do you want to deactivate selected payment method?',function(isConfirm){
      if (isConfirm) { // yes button
          $.ajax({
              url : 'payment-method-change-status',
              type : 'get',
              data : {'payment_method_id' : _id , 'status' : 0},
              success : function(res){
                  var data = JSON.parse(res);
                  if(data['status'] == 'success'){
                    reload_table();
                  }
                  else{
                    app_alert('error','Error');
                  }
              }
          });
      }
  });
}

})();
