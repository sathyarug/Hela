(function(){

    var PAYMENT_ID = 0;
    var X_CSRF_TOKEN = '';
    var TABLE = null;

    $(function(){

       X_CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');


        var validator = app_form_validator('#payment_form',{
            submitHandler: function() {
                try{
                    save_payment_term();
                    $("#payment_form :input").val('');
                    validator.resetForm();
                    $('#model_payment_term').modal('hide');
                }catch(e){return false;}
                return false;
            },
            rules: {
                 payment_code: {
                    required : true,
                    minlength: 3,
                    remote: {
                       type: "get",
                       url: "payment-term-check-code"
                  }
                },
                  payment_description: {
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



        var dataSet = get_payment_term_list();

        TABLE = $('#tbl_payment_term').DataTable({
            //autoWidth: false,
        columns: [
             {
               data: "payment_term_id",
               render : function(data,arg,full){
                 var str = '<i class="icon-pencil" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer;margin-right:3px" data-action="EDIT" data-id="'+data+'"></i>';
                 if(full['status'] == 1){
                   str += '<i class="icon-bin" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer" data-action="DELETE" data-id="'+data+'"></i>';
                 }
                  return str;
              }
            },
            { data: "payment_code" },
            { data: "payment_description" },
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


      $('#tbl_payment_term').on('click','i',function(){
          var ele = $(this);
          if(ele.attr('data-action') === 'EDIT'){
              payment_term_edit(ele.attr('data-id'));
          }
          else if(ele.attr('data-action') === 'DELETE'){
              payment_term_change_status(ele.attr('data-id'));
          }
      });

      $('#btn_new_payment_term').click(function(){
          $("#payment_form :input").val('');
          validator.resetForm();
          $('#btn_save').html('<b><i class="icon-floppy-disk"></i></b> Save');
          $("#payment_code").prop('disabled', false);
          $('#model_payment_term').modal('show');
      });


    });





    function save_payment_term(){
        var data = app_serialize_form_to_json('#payment_form');
        data['_token'] = X_CSRF_TOKEN;
        data['payment_code'] = $('#payment_code').val();
        //var cur_code = $('#cur-code').val();
        //var cur_description = $('#cur-description').val();
        $.ajax({
            url : 'payment-term.save',
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


function get_payment_term_list(){
    var data = [];
    $.ajax({
        url : 'payment-term.get_payment_term_list',
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
	var dataset = get_payment_term_list();
	  var tbl = $('#tbl_payment_term').dataTable();
	  tbl.fnClearTable();
	  tbl.fnDraw();
	  if(dataset != null && dataset.length != 0)
	      tbl.fnAddData(dataset);

}


function payment_term_edit(_id){
    $.ajax({
        url : 'payment-term.get',
        type : 'get',
        data : {'payment_term_id' : _id},
        success : function(res){
            var data = JSON.parse(res);
            $('#payment_id').val(data['payment_term_id']);
            $('#payment_code').val(data['payment_code']).prop('disabled', true);;
            $('#payment_description').val(data['payment_description']);
            $('#btn_save').html('<b><i class="icon-floppy-disk"></i></b> Update');
            $('#model_payment_term').modal('show');
        }
    });
}


function payment_term_change_status(_id){
  app_alert('warning','Do you want to deactivate selected payment term?',function(isConfirm){
      if (isConfirm) { // yes button
          $.ajax({
              url : 'payment-term-change-status',
              type : 'get',
              data : {'payment_term_id' : _id , 'status' : 0},
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
