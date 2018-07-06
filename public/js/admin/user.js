/**
 * Created by sankap on 7/4/2018.
 */
$(document).ready(function(){
    // User regisration date pickers
    $('#date_of_birth').pickadate({
        max: true,
        selectMonths: true,
        selectYears: true
    });

    $('#date_of_birth').pickadate({
        max: true,
        selectMonths: true,
        selectYears: true
    });

    // User regisration form validation
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

    // User regisration Report Level dropdowns
    $('#immediate').select2({
        ajax: {
            url: "load-report-levels",
            dataType: 'json',
            delay: 250,
            dropdownParent: $('#alternative'),
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function(obj) {
                        return { id: obj.user_id, text: obj.first_name+' '+obj.last_name };
                    })
                };
            },
            cache: true
        },
    });


    // Initialize
    $("#alternative").select2({
        ajax: {
            url: "load-report-levels",
            dataType: 'json',
            delay: 250,
            dropdownParent: $('#alternative'),
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function(obj) {
                        return { id: obj.user_id, text: obj.first_name+' '+obj.last_name };
                    })
                };
            },
            cache: true
        },

        /*escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: 1,
        templateResult: formatRepo, // omitted for brevity, see the source of this page
        templateSelection: formatRepoSelection // omitted for brevity, see the source of this page*/
    });

    function formatRepoSelection (repo) {
        //console.log(repo);
        return repo.user_id || repo.emp_number;
    }


    // Format displayed data
    function formatRepo (repo) {
        if (repo.loading) return repo.first_name;

        var markup = "<div class='select2-result-repository clearfix'>" +
            //"<div class='select2-result-repository__avatar'><img src='" + repo.owner.avatar_url + "' /></div>" +
            "<div class='select2-result-repository__meta'>" +
            "<div class='select2-result-repository__title'>" + repo.first_name +' '+ repo.last_name+  "</div></div></div>";

        /*if (repo.description) {
            markup += "<div class='select2-result-repository__description'>" + repo.description + "</div>";
        }*/

        /*markup += "<div class='select2-result-repository__statistics'>" +
            "<div class='select2-result-repository__forks'>" + repo.forks_count + " Forks</div>" +
            "<div class='select2-result-repository__stargazers'>" + repo.stargazers_count + " Stars</div>" +
            "<div class='select2-result-repository__watchers'>" + repo.watchers_count + " Watchers</div>" +
            "</div>" +
            "</div></div>";*/

        return markup;
    }

});
