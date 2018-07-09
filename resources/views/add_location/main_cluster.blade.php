<div class="col-md-12">

    <div class="text-right">
        <button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs" 
        id="add_cluster"><b><i class="icon-plus3"></i></b>Add New</button>
    </div> 



    <table class="table datatable-basic" id="cluster_tbl">
        <thead>
            <tr>
                <th class="text-center">Action</th>
                <th>Cluster Code</th>
                <th>Source Code</th>      
                <th>Cluster Name</th>                                              
                <th>Status</th>
                <!-- <th class="text-center">Actions</th> -->
            </tr>
        </thead>


        <tbody>

        </tbody>


    </table>

</div>

<!-- popup -->

<div id="show_cluster" class="modal fade">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <form class="form-horizontal form-validate-jquery" action="#" id="cluster_form">


                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h5 class="modal-title">Add Main Cluster</h5>
                </div>

                <div class="modal-body">
                    {{csrf_field()}}


                    <fieldset class="content-group">
                     <div class=" col-md-12">

                        <label>Select Main Sourse <span class="text-danger">*</span>:</label>
                        <select class="select-search input-xxs" name="main_source[]" id="main_source">
                           <option value="">Select One ...</option>
                       </select>

                   </div>

                   <div class=" col-md-12">
                    <label>Cluster code <span class="text-danger">*</span>:</label>
                    <input type="text" class="form-control input-xxs" name="cluster-code" id="cluster_code">
                </div>

                <div class=" col-md-12">
                    <label>Cluster Name <span class="text-danger">*</span>:</label>
                    <input type="text" class="form-control input-xxs" name="cluster-name" id="cluster_name">
                </div>


            </fieldset>


        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-link" data-dismiss="modal" id="">Close</button>                       
            <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs" id="btn-save-2">
                <b><i class="icon-floppy-disk"></i></b> Save</button>

            </div>
        </form>
    </div>
</div>
</div> 