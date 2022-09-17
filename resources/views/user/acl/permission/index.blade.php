@extends('adminlte::page')

@section('title', 'User Management - Permissions')

@section('content_header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Permissions</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active">Permissions</li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@stop

@section('content')
    <section class="content">
        <div class="container">
        <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                    <!-- /.card-header -->
                        <div class="card-body">
                            @if (GlobalHelper::userRole($request,'superadmin'))
                                <a class="btn btn-primary btn-sx" href="{{ url('users/acl/permissions/create') }}" style="margin-bottom: 10px">New Permission</a>
                            @endif
                            @if(Session::has('flash_error'))
                                <div class="alert alert-danger text-center">{!! session('flash_error') !!}</div>
                            @endif
                            @if(Session::has('flash_success'))
                                <div class="alert alert-success text-center">{!! session('flash_success') !!}</div>
                            @endif
                            <div class="table-responsive">
                                <table id="permissionList" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Created Date</th>
                                            <th>Modified Date</th>
                                            <th>Status</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                            <tr>
                                                <td colspan="6">
                                                    <center class="image-loading"><img src="{{ asset('assets/img/loading.gif') }}" style="width: 64px;" /></center>
                                                </td>
                                            </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <!-- /.card-body -->
                    </div>
                </div>
                <!-- /.card -->
                
            </div>
        <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@stop

@section('css')
  <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.4/daterangepicker.min.css">
@stop

@section('js')
    <script type="text/javascript">
        var base_url = {!! json_encode(url('/')) !!};
    </script>
    <script src="{{ asset('js/renvee.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.4/daterangepicker.min.js" type="text/javascript"></script>
    <script src="{{ asset('js/user.js') }}"></script>

@stop