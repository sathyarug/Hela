(function() {

   $(function(){        

   //  X_CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');      


     var validator = app_form_validator('#country-form',{
        submitHandler: function() { 
            try{
                save_country();
                $("#country-form :input").val('');
                validator.resetForm();
            }catch(e){return false;}
            return false; 
        },
        rules: {
            country_code: {
                required : true                
            },
            country_description: {
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



     var dataSet = [];//get_currency_list();

     /*TABLE = $('#tbl').DataTable({
            //autoWidth: false,
            columns: [
            { data: "country_id",
            render : function(data){
             var str = '<i class="icon-pencil" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer;margin-right:3px" data-action="EDIT" data-id="'+data+'">\n\
             </i>  <i class="icon-bin" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer" data-action="DELETE" data-id="'+data+'"></i>';
             return str;
         }
     },
     { data: "country_code" },
     { data: "country_description" },

     ],
     columnDefs: [{ 
        orderable: false,
        width: '100px',
        targets: [ 0 ]
    }],
    data: dataSet,
    dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
});*/


     $('#tbl').on('click','i',function(){
      var ele = $(this);
      if(ele.attr('data-action') === 'EDIT'){
          cur_edit(ele.attr('data-id'));
      }
      else if(ele.attr('data-action') === 'DELETE'){

      }
  });

     $('#btn-new').click(function(){          
      $("#country-form :input").val('');
      validator.resetForm();
      $('#btn-save').html('<b><i class="icon-floppy-disk"></i></b> Save');
  });

 });

   function save_country(){
        /*var data = app_serialize_form_to_json('#currency-form');
        data['_token'] = X_CSRF_TOKEN;*/

        $.ajax({
            url : 'insertCountry',
            async : false,
            type : 'post',
            data : {
                '_token': $('input[name=_token]').val(),
                'country_code': $('input[name=country_code]').val(),
                'country_description': $('input[name=country_description]').val(),
            },
            success : function(response){
                if(COUNTRY_ID == 0){
                    $('#title').html('Update country');
                }
                
                app_alert('Country details saved successfully.');
                reload_table();
            },
            error : function(){

            }
        });
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

    /*function get_currency_list(){
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
    


  function cur_edit(_id){    
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


function cur_delete(){

}*/

/*
    $(document).ready(function () {
        $('#btn-save').click(function () {
            $.ajax({
                type: 'post',
                url :'insertCountry',
                data: {
                    '_token': $('input[name=_token]').val(),
                    'country_code': $('input[name=country_code]').val(),
                    'country_description': $('input[name=country_description]').val(),
                },
                success: function (data) {
                    $("#country-form")[0].reset();
                },
            });
        });
    });*/
})();

