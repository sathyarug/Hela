/**
 * Created by sankap on 7/4/2018.
 */
$(document).ready(function(){

    X_CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

    var validator = app_form_validator('#signup-form',{
        submitHandler: function() {
            try{
               $( "#signup-form" ).submit();
            }catch(e){return false;}
            return false;
        },
        rules: {

            first_name: {
                required: true,
                minlength: 1
            },

            last_name: {
                required: true,
                minlength: 1
            },

            email: {
                required: true,
                email: true,
                maxlength: 100
            },

            emp_number: {
                required: true,
            },

            loc_id: {
                required: true,
            },

            dept_id: {
                required: true,
            },

            cost_center_id: {
                required: true,
            },

            desig_id: {
                required: true,
            },

            password: {
                required: true,
            },

            user_name: {
                required: true,
                remote: "validate-empno"
            }

        },
        messages: {
            user_name:{
             remote: "This Username is already avilable."
             },

        }
    });


});
