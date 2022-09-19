@extends('adminlte::page')

@section('title', 'User Management - Permissions')

@section('content_header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                @empty($permission)
                    Add New Permission
                @else
                    Edit Permission: {{$permission->display_name}}
                @endif
            </h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('users/acl/permissions') }}">Permissions</a></li>
                <li class="breadcrumb-item active">
                    @empty($permission)
                        Add New Permission
                    @else
                        Edit Permission: {{$permission->display_name}}
                    @endif
                </li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@stop

@section('content')
<section class="content">
    <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row justify-content-center">
            <div class="col-12">
                <!-- general form elements disabled -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            @empty($permission)
                                Form Permission
                            @else
                                Edit Permission: {{$permission->display_name}}
                            @endif
                        </h3>
                    </div>
                    <!-- /.card-header -->
                    <form role="form" id="form-user" class="needs-validation" novalidate method="POST" action="@empty($permission) {{ url('/users/acl/permissions') }} @else {{ url('/users/acl/permissions/'.$permission->id) }} @endempty">
                        <div class="card-body">
                            @if(Session::has('flash_error'))
                                <div class="alert alert-danger text-center">{!! session('flash_error') !!}</div>
                            @endif
                            @if(Session::has('flash_success'))
                                <div class="alert alert-success text-center">{!! session('flash_success') !!}</div>
                            @endif
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                                {{ csrf_field() }}
                                @if(!empty($permission))
                                    <input type="hidden" name="_method" value="PUT">
                                @endif
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="name">Permission Name *</label>
                                            <input type="text" class="form-control" required id="name" name="name" @php if(!empty(old('name'))) echo 'value="'.old('name').'"'; elseif(!empty($permission->display_name)) { echo 'value="'.$permission->display_name.'"'; if(!empty($permission->protected)) echo ' readonly '; }  else echo 'autocomplete="off"'; @endphp placeholder="Masukkan Nama Permission">
                                            <div class="invalid-feedback">
                                                Mohon isi permission name
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        @php
                                            $actions = array('create', 'read', 'update', 'delete')
                                        @endphp
                                        @if(!empty($permission))
                                            @if(empty($permission->protected))
                                                <label for="status">Status</label>
                                                <div class="col-sm-6">
                                                    <input type="checkbox" id="status" name="status" class="checkbox-toggle" data-size="xs" data-on="Active" data-off="Inactive" value="1" @if(!empty($permission->status) && $permission->status == 1)) checked @endif>
                                                </div>
                                            @endif
                                        @else
                                            <div class="form-group">
                                                @if(!empty($actions)) 
                                                    <label for="action">Action *</label>
                                                    <div class="sepH_a">
                                                        <a href="#" class="btn btn-link btn-xs" id="act_select_all">Select All</a>
                                                        <a href="#" class="btn btn-link btn-xs" id="act_deselect_all">Deselect All</a>
                                                    </div>
                                                    {{-- @if(!empty($role->permissions) && in_array($role->id,($restricted_ids)))
                                                        @foreach($role->permissions as $permission)
                                                            <input type="hidden" name="permission[]" value="{{$permission}}">
                                                        @endforeach
                                                    @endif --}}
                                                    <div class="col-sm-12">
                                                        <select multiple="multiple" id="action" name="action[]" class="multi-select multi-select-action" data-label="Action" required>
                                                            @foreach ($actions as $action)
                                                                <option value="{!! ucwords($action) !!}" >{!! ucwords($action) !!}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                        </div>
                    <!-- /.card-body -->
                    
                        <div class="card-footer">
                            <a href="{{url('users/acl/permissions')}}" class="btn btn-default btn-sx">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sx btnSubmit">Submit</button>
                        </div>
                    </form>
                </div>
                <!-- /.card -->
            </div>
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
@stop

@section('css')
  <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.4/daterangepicker.min.css">
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/multi-select/0.9.12/css/multi-select.min.css">
  <link rel="stylesheet" href="{{asset('css/custom.css')}}">
@stop

@section('js')
    <script type="text/javascript">
        var base_url = {!! json_encode(url('/')) !!};
    </script>
    <script src="{{ asset('js/pos.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/multi-select/js/jquery.multi-select.js') }}"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.quicksearch/2.4.0/jquery.quicksearch.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.4/daterangepicker.min.js" type="text/javascript"></script>
    <script src="{{ asset('js/user.js') }}"></script>
@stop