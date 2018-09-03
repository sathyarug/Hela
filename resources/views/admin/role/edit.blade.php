<script>

    $(function () {

        $('#permission-field').select2();


        var validator = app_form_validator('#role_form', {

            submitHandler: function () {
                try {
                   // edit_role();
                   add_edit_role();
                } catch (e) {
                    console.log(e);
                    return false;
                }
                return false;
            },

        });

    });

 </script>

{!! Form::model($role, [
'method' => 'POST',
'url' => ['/admin/role', $role->id],
'class' => 'form-horizontal',
'id'=>'role_form'
]) !!}

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Edit Role</h5>
</div>

@include ('admin.role.form', ['submitButtonText' => 'Update'])

{!! Form::close() !!}
