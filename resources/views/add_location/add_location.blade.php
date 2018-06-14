
@extends('layout.main')

@section('title') Form Details @endsection
@section('m_add_location') class = 'active' @endsection

@section('body')
<!-- Page header -->
<div class="page-header page-header-default ">
    <!-- <div class="page-header-content">
            <div class="page-title">
                    <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold">Home</span> - Dashboard</h4>
            </div>

            <div class="heading-elements">
                    <div class="heading-btn-group">
                            <a href="#" class="btn btn-link btn-float has-text"><i class="icon-bars-alt text-primary"></i><span>Statistics</span></a>
                            <a href="#" class="btn btn-link btn-float has-text"><i class="icon-calculator text-primary"></i> <span>Invoices</span></a>
                            <a href="#" class="btn btn-link btn-float has-text"><i class="icon-calendar5 text-primary"></i> <span>Schedule</span></a>
                    </div>
            </div>
    </div> -->

    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="index.html"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active">@yield('title')</li>
        </ul>

        <ul class="breadcrumb-elements">
            <li><a href="#"><i class="icon-comment-discussion position-left"></i> Support</a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="icon-gear position-left"></i>
                    Settings
                    <span class="caret"></span>
                </a>

                <ul class="dropdown-menu dropdown-menu-right">
                    <li><a href="#"><i class="icon-user-lock"></i> Account security</a></li>
                    <li><a href="#"><i class="icon-statistics"></i> Analytics</a></li>
                    <li><a href="#"><i class="icon-accessibility"></i> Accessibility</a></li>
                    <li class="divider"></li>
                    <li><a href="#"><i class="icon-gear"></i> All settings</a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>
<!-- /page header -->


<!-- Content area -->
<div class="content">


    <div class="col-md-12">

        <!-- Basic layout-->





        <div class="col-md-12">
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h6 class="panel-title">Add Location</h6>

                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>

                <div class="panel-body">
                    <div class="tabbable">
                        <ul class="nav nav-tabs nav-tabs-highlight nav-justified">
                            <li class="active"><a href="#highlighted-justified-tab1" data-toggle="tab">Main Sourse</a></li>
                            <li><a href="#highlighted-justified-tab2" data-toggle="tab">Main Cluster</a></li>
                            <li><a href="#highlighted-justified-tab3" data-toggle="tab">Main Company</a></li>
                            <li><a href="#highlighted-justified-tab4" data-toggle="tab">Location</a></li>

                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane active" id="highlighted-justified-tab1">

                                <!-- <div class=" col-md-12">
                                        <fieldset class="content-group">
                                                <div class=" col-md-3">

                                                        <label>Main Sourse Name * :</label>
                                                        <input type="text" class="form-control input-xxs" >
                                                </div>
                                        </fieldset>
                                </div>-->


                                <div class="col-md-12">


                                    <div class="text-right">
                                        <button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs"  data-toggle="modal" data-target="#modal_mini"><b><i class="icon-plus3"></i></b>Add New</button>
                                    </div> 



                                    <table class="table datatable-basic">
                                        <thead>
                                            <tr>
                                                <th>First Name</th>
                                                <th>Last Name</th>
                                                <th>Job Title</th>
                                                <th>DOB</th>
                                                <th>Status</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Marth</td>
                                                <td>Enright</td>
                                                <td>Traffic Court Referee</td>
                                                <td>22 Jun 1972</td>
                                                <td><span class="label label-success">Active</span></td>
                                                <td class="text-center">
                                                    <ul class="icons-list">
                                                        <li class="dropdown">
                                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                                <i class="icon-menu9"></i>
                                                            </a>

                                                            <ul class="dropdown-menu dropdown-menu-right">
                                                                <li><a href="#"><i class="icon-pencil"></i> Edit</a></li>
                                                                <li><a href="#"><i class="icon-bin"></i> Inactive</a></li>
                                                            </ul>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Jackelyn</td>
                                                <td>Weible</td>
                                                <td>Airline Transport Pilot</td>
                                                <td>3 Oct 1981</td>
                                                <td><span class="label label-default">Inactive</span></td>
                                                <td class="text-center">
                                                    <ul class="icons-list">
                                                        <li class="dropdown">
                                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                                <i class="icon-menu9"></i>
                                                            </a>

                                                            <ul class="dropdown-menu dropdown-menu-right">
                                                                <li><a href="#"><i class="icon-pencil"></i> Edit</a></li>
                                                                <li><a href="#"><i class="icon-bin"></i> Inactive</a></li>
                                                            </ul>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>


                                        </tbody></table>





                                </div>




                            </div>

                            <div class="tab-pane" id="highlighted-justified-tab2">
                                <!-- <div class=" col-md-12">
                                        <fieldset class="content-group">

                                                <div class=" col-md-3">

                                                        <label>Select Main Sourse *:</label>
                                                        <select class="select-search input-xxs" >

                                                                <option value="">Select One ...</option>

                                                        </select>
                                                </div>

                                                <div class=" col-md-3">

                                                        <label>Cluster Name * :</label>
                                                        <input type="text" class="form-control input-xxs" >
                                                </div>
                                        </fieldset>
                                </div>


                                <div class="text-right">
                                        <button type="button" class="btn bg-teal-400 btn-labeled btn-success btn-xs"><b><i class="icon-floppy-disk"></i></b> Save</button>
                                </div> -->
                            </div>

                            <div class="tab-pane" id="highlighted-justified-tab3">
                                DIY synth PBR banksy irony. Leggings gentrify squid 8-bit cred pitchfork. Williamsburg whatever.
                            </div>

                            <div class="tab-pane" id="highlighted-justified-tab4">
                                Aliquip jean shorts ullamco ad vinyl cillum PBR. Homo nostrud organic, assumenda labore aesthet.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- /basic layout -->

    </div>






</div>
<!-- /latest posts -->

<!-- Mini modal -->
<div id="modal_mini" class="modal fade">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Add Main Sourse</h5>
            </div>

            <div class="modal-body">
                <div class=" col-md-12">
                    <form class="form-horizontal form-validate-jquery" action="#">
                        <fieldset class="content-group">


                            <label>Main Sourse Name <span class="text-danger">*</span> :</label>

                            <input type="text" name="basic" class="form-control input-xxs" required="required" placeholder="Text input validation">

                        </fieldset>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>								
                <button type="submit" class="btn bg-teal-400 btn-labeled btn-success btn-xs"><b><i class="icon-floppy-disk"></i></b> Save</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /mini modal -->


@endsection




@section('javascripy') 

<script type="text/javascript" src="assets/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/js/pages/datatables_basic.js"></script>

<script type="text/javascript" src="assets/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/js/pages/form_validation.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/bootstrap_multiselect.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/inputs/touchspin.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/styling/switch.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/styling/switchery.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/styling/uniform.min.js"></script>

@endsection
