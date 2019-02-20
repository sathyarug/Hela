/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var myApp = angular.module("myModule",[]);

myApp.controller("myControler", function($scope){
    $scope.message = "Well Come to Angular ";
});

myApp.controller("myControl1", function($scope){
    
    $scope.message = " This is My Controler 1";
});


