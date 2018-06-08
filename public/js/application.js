function app_serialize_form_to_json(_form_selector){
        var o = {};
        var a = $(_form_selector).serializeArray();
        $.each(a, function () {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    }

/**
 * 
 * @param {string or object} _config
 * @param {string} _type
 * @returns {void}
 */
function app_alert(_config,_type){
    var options = {
        title: "Success",
        text: "",
        confirmButtonColor: "#66BB6A",
        type: "success"
    };
    var data_type = typeof _config;
    if(data_type === 'string'){
        if(_type === 'error'){
            options['title'] = 'Error';
            options['type'] = 'error';
            options['confirmButtonColor'] = '#EF5350';
        }
        options['text'] = _config;
    }
    else{
        $.extend( options, _config );
    }    
    swal(options);
}


function app_form_validator(_form,_config){
    var options = {        
        //ignore: 'input[type=hidden]', // ignore hidden fields
        errorClass: 'validation-error-label',
        successClass: 'validation-valid-label',
        highlight: function(element, errorClass) {
            $(element).removeClass(errorClass);
        },
        unhighlight: function(element, errorClass) {
            $(element).removeClass(errorClass);
        },
        // Different components require proper error label placement
        errorPlacement: function(error, element) {

            // Styled checkboxes, radios, bootstrap switch
            if (element.parents('div').hasClass("checker") || element.parents('div').hasClass("choice") || element.parent().hasClass('bootstrap-switch-container') ) {
                if(element.parents('label').hasClass('checkbox-inline') || element.parents('label').hasClass('radio-inline')) {
                    error.appendTo( element.parent().parent().parent().parent() );
                }
                 else {
                    error.appendTo( element.parent().parent().parent().parent().parent() );
                }
            }

            // Unstyled checkboxes, radios
            else if (element.parents('div').hasClass('checkbox') || element.parents('div').hasClass('radio')) {
                error.appendTo( element.parent().parent().parent() );
            }

            // Input with icons and Select2
            else if (element.parents('div').hasClass('has-feedback') || element.hasClass('select2-hidden-accessible')) {
                error.appendTo( element.parent() );
            }

            // Inline checkboxes, radios
            else if (element.parents('label').hasClass('checkbox-inline') || element.parents('label').hasClass('radio-inline')) {
                error.appendTo( element.parent().parent() );
            }

            // Input group, styled file input
            else if (element.parent().hasClass('uploader') || element.parents().hasClass('input-group')) {
                error.appendTo( element.parent().parent() );
            }

            else {
                error.insertAfter(element);
            }
        },
        validClass: "validation-valid-label",
        success: function(label) {
            label.addClass("validation-valid-label").text("Success.");              
        },
        submitHandler: function() {              
        }
    };
    $.extend(true , options, _config );
    var validator_obj = $(_form).validate(options);
    return validator_obj;
}