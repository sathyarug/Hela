/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var X_CSRF_TOKEN = '';

var urlPath = "{{'/'}}";
$(document).ready(function(){
    
    X_CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    
    var validator = app_form_validator('#customesize_form',{
        
        submitHandler:function(){
            try{
                save_customesize();
                $("#customesize_form :input").val('');
                validator.resetForm();
            }catch(e){
                return false;
            }
            return false;
        },

        rules: {
            
            customers:{
                required:true,
            },
            
            divisions:{
               required:true, 
            },
            
            sizenames:{
               required:true,  
            }
            
        }
    });
    
    $("#customers").change(function(){
        $("#divisions").empty();
        
        var customercode = $("#customers").val();
        
        LoadDivisions(customercode,-1);
        
    });
    
    $("#divisions").change(function(){
        
    });
    
    $('#add_data').click(function () {
        $('#show_customesizes').modal('show');
        $('#customesize_form')[0].reset();        
        validator.resetForm();
        $('#btn-save').html('<b><i class="icon-floppy-disk"></i></b> save');
       
    });
    
    function LoadDivisions(customercode, divisionId){
        $("#divisions").empty();
        $.ajax({
            url: "/customesizes/getdivision",
            type:"GET",
            data:{'customerCode':customercode, _token:X_CSRF_TOKEN},
            dataType:"json",
            success:function(response){
                
                $.each(response, function(key, value){
                    
                    $("#divisions").append(new Option(key,value));
                   
                    if(value == divisionId){                        
                        $("#divisions").val(divisionId).attr('selected','selected');
                    }
                });
            },
            error:function(response){
                
                
                alert('Error ' + response);
            }
        });
        
    }
    
    function save_customesize(){
        
        var data = app_serialize_form_to_json('#customesize_form');
        
        data['_token'] = X_CSRF_TOKEN;
        data['customer_code'] = $("#customers").val();
        data['division_code'] = $("#divisions").val();
        data['size_name'] = $("#sizenames").val();
        
       $.ajax({
           url:'/customesizes/save_sizes',
           async:false,
           type:"POST",
           data:data,
           dataType:"json",
           success:function(response){
               
                var result = JSON.parse(JSON.stringify(response));
               
                app_alert('success', result['message']);
                reload_table();
                $('#customesize_form')[0].reset();
                $('#show_customesizes').modal('toggle');
                validator.resetForm();
               
           },
           error:function(response){
               //alert('Error ' + response[0]);
               //alert(JSON.parse(JSON.stringify(response)));
               jQuery.each(response,function(i,val){
                   alert("Error " + "Key " + i+ "Value " + val);
               })
               
               
           }
           
       });
    }
    
    $('#customsize_tbl').on('click', 'i', function () {
        var ele = $(this);
        if (ele.attr('data-action') === 'EDIT') {
            customesize_edit(ele.attr('data-id'));
        } else if (ele.attr('data-action') === 'DELETE') {
            customesize_delete(ele.attr('data-id'));
        }
    });
    
    function customesize_edit(_id) {

        $('#show_customesizes').modal('show');
        $('#customesize_form')[0].reset();
        validator.resetForm();

        $.ajax({
            url: '/customesizes/edit_customesizes',
            type: 'get',
            data: {'size_id': _id},
            success: function (res) {
                var data = JSON.parse(res);
                
                LoadDivisions(data['customer_id'],data['division_id']);
               
                $('#size_hid').val(data['size_id']);
                $('#customers').val(data['customer_id']);                
                $('#sizenames').val(data['size_name']);
                $('#btn-save').html('<b><i class="icon-pencil"></i></b> Update');
            }
        });
    }
    
    function customesize_delete(_id) {

        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this custome size file!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#EF5350",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel pls!",
            closeOnConfirm: false,
            closeOnCancel: false
        },
                function (isConfirm) {
                    if (isConfirm) {

                        $.ajax({
                            url: '/customesizes/delete_customesizes',
                            type: 'get',
                            data: {'size_id': _id},
                            success: function (res) {
                                var data = JSON.parse(res);
                                swal({
                                    title: "Deleted!",
                                    text: "Season has been deleted.",
                                    confirmButtonColor: "#66BB6A",
                                    type: "success"
                                });

                                reload_table();

                            }
                        });

                    } else {
                        swal({
                            title: "Cancelled",
                            text: "Your imaginary file is safe :)",
                            confirmButtonColor: "#2196F3",
                            type: "error"
                        });
                    }
                });
    }
    
    function reload_table()
    {
        var dataset = loadCustomeSizes();
        var tbl = $('#customsize_tbl').dataTable();
        tbl.fnClearTable();
        tbl.fnDraw();
        if (dataset != null && dataset.length != 0)
            tbl.fnAddData(dataset);

    }
    
    function loadCustomeSizes(){
        
        var data = [];
        
        $.ajax({
            url:"/customesizes/list_customesizes",
            async:false,
            type:"GET",
            data:{},
            dataType:"json",
            success:function(result){
                
                data = JSON.parse(JSON.stringify(result));
            },
            error:function(){
                alert("Error in loading");
            }
        });
        return data;
    }
    
    var dataset = loadCustomeSizes();
    
    TABLE = $("#customsize_tbl").DataTable({
        
        autoWidth:false,        
        columns: [
            {data: "size_id",
                render: function (data) {
                    var str = '<i class="icon-pencil" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer;margin-right:3px" data-action="EDIT" data-id="' + data + '">\n\
       </i>  <i class="icon-bin" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer" data-action="DELETE" data-id="' + data + '"></i>';
                    return str;
                }
            },            
            {data: "customer_name"},
            {data: "division_description"},
            {data: "size_name"},

            
        ],
        columnDefs: [{
                orderable: false,
                width: '100px',
                targets: [0]
            }],
        data: dataset,
        dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
        
    });
});


    



