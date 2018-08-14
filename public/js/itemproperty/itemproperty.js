/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 */
var X_CSRF_TOKEN = '';
var urlPath =  "{{'/'}}";

$(document).ready(function(){
    X_CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    
    var validator = app_form_validator('#itemproperty_form',{
        
        submitHandler:function(){
          try{
              SaveItemProperties();
              $("#itemproperty_form : input").val('');
              validator.resetForm();
          }catch(e){
            return false;
          }  
          return false;
        },
        
    });
    
    // Load Properties
    // ======================================
    LoadUnssignProrperties();
    
    $('#multiselect1').multiselect();
    $('#multiselect2').multiselect();
    
    $("#category_code").change(function(){
        
        var mainCategoryCode = $("#category_code").val();
        
        LoadSubCategories(mainCategoryCode);
    });
    
    $("#subcategory").change(function(){
        
       var subCatCode = $("#subcategory").val();
       
        if(subCatCode != "-1"){
            LoadAssignProperties();
            LoadUnassignPropertiesBySubCat();
        }else{
            LoadUnssignProrperties();
            $("#multiselect1_to").empty();
        }
       
    });
    
    $("#add_data").click(function(){
       $("#show_itemproperty").modal('show');
       $("#itemproperty_form")[0].reset();
       
    });
    
    $("#btn-save-property-assign").click(function(){
        
        var iSequenceNo = 1;
        var mainCategoryCode = $("#category_code").val();
        var subCategoryCode = $("#subcategory").val();
        
        if(mainCategoryCode == "-1"){
            alert("Please select main category from list");return;
        }
        
        if(subCategoryCode == "-1"){
            alert("Please select required sub category from list");return;
        }
        
        var data = app_serialize_form_to_json('#itemproperty_assign_form');
        
        data['_token'] = X_CSRF_TOKEN;
        data['subcategory_code'] = subCategoryCode;
        
        $("#multiselect1_to option").each(function(i){
            
            data['property_id'] = $(this).val();
            data['sequence_no'] = iSequenceNo;
            
            $.ajax({
            url:'itemproperty/save-assign',
            async:false,
            type:"POST",
            data:data,
            dataType:"json",
            success:function(response){
                
                iSequenceNo++;
                
            }
            
        });
            
        })
        
        /*
        
        */
    });
    
    $("#multiselect_up").click(move_up);
    $("#multiselect_down").click(move_down);
    
    function move_up(){
       
       $("#multiselect1_to :selected").each(function(i, selected){
          if(!$(this).prev().length) return false;
          $(this).insertBefore($(this).prev());
       });
       $("#multiselect1_to").focus().blur();       
    }
    
    function move_down(){
       $($("#multiselect1_to :selected").get().reverse()).each(function(i, selected){
           if (!$(this).next().length) return false;
            $(this).insertAfter($(this).next());
       });
       $("#multiselect1_to").focus().blur(); 
    }
    
    
    
    function SaveItemProperties(){
        
        var data = app_serialize_form_to_json('#itemproperty_form');
        
        data['_token'] = X_CSRF_TOKEN;
        data['property_name'] = $("#propertyname").val();
        
        $.ajax({
           url:'itemproperty/save_itemproperty',
           async:false,
           type:"POST",
           data:data,
           dataType:"json",
           success:function(response){
               
                var result = JSON.parse(JSON.stringify(response));
                LoadUnssignProrperties();
                app_alert('success');
                $('#itemproperty_form')[0].reset();
                $('#show_itemproperty').modal('toggle');
                validator.resetForm();
               
           },
           error:function(response){
              
           }
           
        });
    }
    
    function LoadUnssignProrperties(){
        $("#multiselect1").empty();
        $.ajax({
        url:'itemproperty/load-properties',
        type:'get',
        data:'',
        dataType:"json",
        success:function(response){            
            $.each(response, function(key, value){
               $("#multiselect1").append(new Option(key, value));
            });
            
        }
        
    });
        
    }
    
    function LoadAssignProperties(){
        
        $("#multiselect1_to").empty();
        
        var subCategoryCode = $("#subcategory").val();
        
        var data = app_serialize_form_to_json('#itemproperty_assign_form');
        
        data['subcategory_code'] = subCategoryCode;
        
        $.ajax({
           url:'itemproperty/load-assign-properties',
           type:'get',
           data:data,
           dataType:"json",
           success:function(response){
               
              var res = JSON.parse(JSON.stringify(response));
              
              $.each(res, function(key, value){
                  
                  $("#multiselect1_to").append(new Option(res[key]["property_name"],res[key]["property_id"]));
              })
              
           }
        });
    }
    
    function  LoadUnassignPropertiesBySubCat(){
        
        $("#multiselect1").empty();
        
        $.ajax({
            url:'itemproperty/load-unassign-bysubcat',
            type:'get',
            data:'',
            dataType:"json",
            success:function(response){
                var res = JSON.parse(JSON.stringify(response));
              
              $.each(res, function(key, value){
                  
                  $("#multiselect1").append(new Option(res[key]["property_name"],res[key]["property_id"]));
              })
            }
        });
        
        
    }
});





