$(document).ready(function () {
    $('#tbl').dataTable( {
        "scrollX": true,
        "scrollY": true,
        "paging":   false,
        "ordering": false,
        "info":     false,
        "searching":false,
    } );
    getAll();

    $('#btn-save').click(function (event) {
        event.preventDefault();
        save_country();

    });

    $('#btn-update').click(function (event) {
        event.preventDefault();
        id=$('#country_id').val();
        update_country(id);

    });
    $('#data').on('click', '.icon-bin', function () {
        id = $(this).data('id');
        destroy(id);
    });

    $('#data').on('click', '.icon-pencil', function () {
        id = $(this).data('id');
        edit(id);
    });

});

$('#btn-new').click(function (event) {
    event.preventDefault();
    $("#country_id,#country_code,#country_description").val('');
    $('#btn-save').show();
    $('#btn-update').hide();
    validator.resetForm();
     
    $('#btn-save').html('<b><i class="icon-floppy-disk"></i></b> Save');
});

function save_country() {
//formData=$('#formData').serializeArray();
    $.ajax({
        url: 'insertCountry',
        type: 'POST',
        dataType: 'JSON',
        data:{
           '_token': $('input[name=_token]').val(),
            'country_id': $('input[name=country_id]').val(),
            'country_code': $('input[name=country_code]').val(),
            'country_description': $('input[name=country_description]').val(),
        },
    })
        .done(function () {
            $("#country_code,#country_description,#country_id").val('');
            getAll();
        })
        .fail(function () {
            $('alert').show();
        })
}

function getAll() {
    $('#data').empty();
    $.ajax({
        url: 'get_all_country',
        type: 'GET',
    })
        .done(function (data) {
            $.each(data, function (index, val) {
                $('#data').append('<tr>')
                $('#data').append('<td>' + val.country_id + '</td>')
                $('#data').append('<td>' + val.country_code + '</td>')
                $('#data').append('<td>' + val.country_description + '</td>')
                //$('#data').append('<td><button class="btn btn-xs btn-danger" data-id="' + val.country_id + '">Delete</button><button class="btn btn-xs btn-info" data-id="' + val.country_id + '">Edit</button></td>')
                $('#data').append('<td><i class="icon-pencil" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer;margin-right:3px" data-action="EDIT" data-id="'+val.country_id+'">\n\
        </i><i class="icon-bin" style="border-style:solid; border-width: 1px;padding:2px;cursor:pointer" data-action="DELETE" data-id="'+val.country_id+'"></i></td>')
                $('#data').append('</tr>')
            });

        }).fail(function () {
            console.log('error');

        })
}

function destroy(id) {
    $.ajax({
        url: 'delete_country/' + id,
        type: 'DELETE',
        dataType: 'JSON',
        data: {'_token': $('input[name=_token]').val()},
    })
        .done(function () {
            getAll();
        })
        .fail(function () {
            $('alert').show();
        });
}

function edit(id) {
    $.ajax({
        url: 'edit_country/' + id,
        type: 'GET',
    })
        .done(function (data) {
           $('#country_code').val(data.country_code);
           $('#country_description').val(data.country_description);
           $('#country_id').val(data.country_id);
           /* $('#btn-save').hide();
            $('#btn-update').show();*/
            console.log(data.country_id);
        })
        .fail(function () {
            $('alert').show();
        });
}

function update_country(id){
    formData=$('#country-form').serializeArray();
    $.ajax({
        url: 'update_country/' + id,
        type: 'POST',
        dataType: 'JSON',
        data: formData,
    })
        .done(function () {
            getAll();
            $("#country_code,#country_description,#country_id").val('');
           /* $('#btn-save').show();
            $('#btn-update').hide();*/
            
        })
        .fail(function () {
            $('alert').show();
        });   
}