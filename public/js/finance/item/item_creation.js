/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var X_CSRF_TOKEN = '';
var urlPath =  "{{'/'}}";

var app_itemCreation = angular.module('appItemCreation',[]);

app_itemCreation.controller('ctrlCategory',['$scope','$http','$log', function($scope, $http){
    
    $http.get('/item-creation/get-maincategory').then(function(response){
        $scope.category = response.data;
        //alert(response.data);
    });
    
     
     function up(){alert("OK");}
   
}]);

/*
app_itemCreation.controller('ctrlCategory', function($scope){
    
    $scope.up = function(){
        alert("OK");
    }
    
});

 */

    

$(document).ready(function(){
    
    X_CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
   
   
    /*
    $("#category_code").change(function(){
        
        var mainCategoryCode = $("#category_code").val();
        
        alert(mainCategoryCode);
        
        
    });
    
   */
  
});


