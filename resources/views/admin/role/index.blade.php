@extends('layout.main')
@section('title') Role @endsection
@section('m_role') class = 'active' @endsection
@section('body')

<!-- Page header -->

<div class="page-header page-header-default ">


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
<div class="content">
    <div class="row">
        @include('admin.sidebar') 

        <div class="col-md-12">

            <div class="panel panel-flat">

                <div class="panel-heading">
                    <h5 class="panel-title">Role</h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>

                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="text-right">
                                <button type="button" class="btn bg-teal-400 btn-labeled btn-primary btn-xs" id="add_data"><b><i class="icon-plus3"></i></b>Add New</button>
                            </div> 

                            <table class="table datatable-basic" id="role_tbl">
                                <thead>
                                    <tr>
                                        <th class="text-center">Action</th>
                                        <th>Role Name</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>

<!--                    <div class="row">
                        <div class="col-md-12 text-right">
                            <a href="{{ url('/admin/role/create') }}" class="btn bg-teal-400 btn-labeled btn-primary btn-xs" title="Add New Role">
                                <b> <i class="icon-plus3"></i></b> Add New
                            </a>
                        </div>-->
                        <!--                        <div class="col-md-9">
                                                    {!! Form::open(['method' => 'GET', 'url' => '/admin/role', 'class' => 'form-inline text-right', 'role' => 'search'])  !!}
                                                    <div class="input-group">
                        
                                                        <span class="input-group-append">
                        
                                                            <div class="input-group">
                                                                <input type="text" class="form-control input-xs" style="height: 28px;" name="search" placeholder="Search..." value="{{ request('search') }}">
                                                                <span class="input-group-btn">
                                                                    <button class="btn bg-teal btn-xs" type="submit">Search</button>
                                                                </span>
                                                            </div>
                                                        </span>
                                                    </div>
                                                    {!! Form::close() !!}
                                                </div>-->
                    <!--</div>-->


                    <!--<br/>-->
<!--                    <div class="table-responsive">
                        <table class="table table-borderless" id="role_table">
                            <thead>
                                <tr>
                                    <th>#</th><th>Name</th><th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                               {{-- @foreach($role as $item) --}}
                                <tr>
                                  {{--  <td>{{ $loop->iteration or $item->id }}</td>--}}
                                   {{-- <td>{{ $item->name }}</td>--}}

                                    <td>

                                   {{--     <a href="{{ url('/admin/role/' . $item->id) }}" title="View Role"><button class="btn bg-teal-400 btn-labeled btn-success btn-xs"><b><i class="icon-search4"></i></b> View</button></a>--}}
                                    {{--    <a href="{{ url('/admin/role/' . $item->id . '/edit') }}" title="Edit Role">--}}
                                    {{--        <button class="btn bg-teal-400 btn-labeled btn-success btn-xs"><b><i class="icon-pencil"></i></b> Edit</button></a>--}}
                                    {{--    {!! Form::open([--}}
                                    {{--    'method'=>'DELETE',--}}
                                    {{--    'url' => ['/admin/role', $item->id],--}}
                                     {{--   'style' => 'display:inline'--}}
                                    {{--    ]) !!}--}}
                                    {{--    {!! Form::button('<b><i class="icon-bin"></i></b> Delete', array(--}}
                                   {{--     'type' => 'submit',--}}
                                    {{--    'class' => 'btn bg-teal-400 btn-labeled btn-danger btn-xs',--}}
                                   {{--     'title' => 'Delete Role',--}}
                                   {{--     'onclick'=>'return confirm("Confirm delete?")'--}}
                                   {{--     )) !!}--}}
                                  {{--      {!! Form::close() !!}--}}
                                    </td>
                                </tr>
                               {{--  @endforeach--}}
                            </tbody>
                        </table>
                        {{-- <div class="pagination-wrapper"> {!! $role->appends(['search' => Request::get('search')])->render() !!} </div> --}}
                    </div>-->

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascripy') 
<script type="text/javascript" src="{{ URL::asset('assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/selects/select2.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/admin/role.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('assets/js/pages/datatables_basic.js') }}"></script>

@endsection