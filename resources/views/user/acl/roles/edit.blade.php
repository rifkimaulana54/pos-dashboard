@extends('adminlte::page')

@section('title', 'User Management - Roles')

@section('content_header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                @empty($role)
                    Add New Role
                @else
                    Edit Role: {{$role->display_name}}
                @endif
            </h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('users/acl/roles') }}">Roles</a></li>
                <li class="breadcrumb-item active">
                    @empty($role)
                        Add New Role
                    @else
                        Edit Role: {{$role->display_name}}
                    @endif
                </li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@stop

@section('content')
@php
  $restricted_ids = [1,2,3,4];
@endphp
<section class="content">
    <div class="container-fuild">
        <!-- Small boxes (Stat box) -->
        <div class="row justify-content-center">
            <div class="col-12">
                <!-- general form elements disabled -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            @empty($role)
                                Form Role
                            @else
                                Edit Role: {{$role->display_name}}
                            @endif
                        </h3>
                    </div>
                    <!-- /.card-header -->
                    <form method="POST" class="needs-validation" novalidate id="form-user" action="@empty($role) {{ url('/users/acl/roles') }} @else {{ url('/users/acl/roles/'.$role->id) }} @endempty">
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
                            @if(!empty($role))
                                <input type="hidden" name="_method" value="PUT">
                            @endif
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="name">Role Name *</label>
                                    <input type="text" class="form-control" required id="name" name="name" @php if(!empty(old('name'))) echo 'value="'.old('name').'"'; elseif(!empty($role->display_name)) echo 'value="'.$role->display_name.'"'; else echo 'autocomplete="off"'; @endphp placeholder="Masukan Nama Role">
                                    <div class="invalid-feedback">
                                        Mohon isi role name
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                @if(!empty($permissions)) 
                                    <label for="permission">Permission *</label>
                                    <div class="sepH_a">
                                        <a href="#" class="btn btn-link btn-xs" id="perm_select_all">Select All</a>
                                        @if(!empty($role->permissions) && !in_array($role->id,$restricted_ids))
                                            <a href="#" class="btn btn-link btn-xs" id="perm_deselect_all">Deselect All</a>
                                        @endif
                                    </div>
                                    @if(!empty($role->permissions) && in_array($role->id,($restricted_ids)))
                                        @foreach($role->permissions as $permission)
                                            <input type="hidden" name="permission[]" value="{{$permission}}">
                                        @endforeach
                                    @endif
                                    <div class="col-sm-12">
                                        <select multiple="multiple" id="permission" name="permission[]" class="multi-select multi-select-permission" data-label="Permission" required="">
                                            @foreach($permissions as $group => $permission)
                                            <optgroup label="{!! strtoupper($group) !!}">
                                                @foreach($permission as $perm)
                                                <option value="{{$perm->id}}" @php if(!empty($role->permissions) && in_array($perm->id,$role->permissions)) { echo 'selected'; if(in_array($role->id,($restricted_ids))) echo ' disabled'; } @endphp>{{$perm->name}}</option>
                                                @endforeach
                                            </optgroup>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>
                            @if(!empty($role) && !in_array($role->id,$restricted_ids))
                                <label for="status">Status</label>
                                <div class="col-sm-6">
                                    <input type="checkbox" data-toggle="toggle" name="status" data-on="Active" data-size="xs" data-off="Inactive" data-onstyle="success" data-offstyle="info" value="1" @if (!empty($role->status) && $role->status == 1) checked @endif>
                                </div>
                            @else
                                <input type="hidden" name="status" value="1">
                            @endif
                        </div>
                        <!-- /.card-body -->
                    
                        <div class="card-footer">
                            <a href="{{url('users/acl/roles')}}" class="btn btn-default btn-sx">Cancel</a>
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
    <script type="text/javascript" src="{{ asset('vendor/multi-select/js/jquery.multi-select.js') }}"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.quicksearch/2.4.0/jquery.quicksearch.min.js"></script>
    <script src="{{ asset('js/renvee.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.4/daterangepicker.min.js" type="text/javascript"></script>
    <script src="{{ asset('js/user.js') }}"></script>
@stop