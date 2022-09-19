@extends('adminlte::page')

@section('title', 'User Management - User')

@section('content_header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                @empty($user)
                    Add New User
                @else
                    Edit User: {{$user->fullname}}
                @endif
            </h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('users') }}">User</a></li>
                <li class="breadcrumb-item active">
                    @empty($user)
                        Add New User
                    @else
                        Edit User: {{$user->fullname}}
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
                            @empty($user)
                                Form User
                            @else
                                Edit User: {{$user->fullname}}
                            @endif
                        </h3>
                    </div>
                <!-- /.card-header -->
                    <form role="form" method="POST" class="needs-validation form-detail" novalidate id="form-user" action="@empty($user) {{ url('/users') }} @else {{ url('/users/'.$user->id) }} @endempty" enctype="multipart/form-data">
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
                            @if(!empty($user))
                                <input type="hidden" name="_method" value="PUT">
                                <input type="hidden" name="id_user" value={{$user->id}}>
                            @endif
                            <div class="card card-primary card-outline card-outline-tabs card-orange">
                                <div class="card-body">
                                    <div class="row">
                            <div class="col-sm-6">
                                <label for="">{{__('Full Name')}} *</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" name="name" required placeholder="Full name" @php if(!empty(old('name'))) echo 'value="'.old('name').'"'; elseif(!empty($user->fullname)) echo 'value="'.$user->fullname.'"'; else echo 'autocomplete="off"'; @endphp>
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <span class="fas fa-user"></span>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">
                                        {{__('Mohon isi nama')}}
                                    </div>
                                </div>
                                <label for="">{{__('Email')}} *</label>
                                <div class="input-group mb-3">
                                    <input type="email" class="form-control" required name="email" placeholder="Email" @php if(!empty(old('email'))) echo 'value="'.old('email').'"'; elseif(!empty($user->email)) echo 'value="'.$user->email.'"'; else echo 'autocomplete="off"'; @endphp>
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <span class="fas fa-envelope"></span>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">
                                        {{__('Mohon isi email')}}
                                    </div>
                                </div>
                                <label for="">{{__('Phone')}}</label>
                                <div class="input-group mb-3">
                                    <input type="phone" class="form-control" name="phone" placeholder="081XXXXXX" maxlength="13" @php if(!empty(old('phone'))) echo 'value="'.old('phone').'"'; elseif(!empty($user->phone)) echo 'value="'.$user->phone.'"'; else echo 'autocomplete="off"'; @endphp>
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <span class="fas fa-phone"></span>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">
                                        {{__('Mohon isi nomor telepon')}}
                                    </div>
                                </div>
                                <label for="">{{__('Password')}} *</label>
                                <div class="input-group mb-3">
                                    <input type="password" class="form-control" name="password" placeholder="Password" @if(empty($user)) required @endif>
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <span class="fas fa-lock"></span>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">
                                        {{__('Mohon isi password')}}
                                    </div>
                                </div>
                                <label for="">{{__('Confirm Password')}} *</label>
                                <div class="input-group mb-3">
                                    <input type="password" class="form-control" name="password_confirmation" placeholder="Retype password" @if(empty($user)) required @endif>
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <span class="fas fa-lock"></span>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">
                                        {{__('Mohon isi confirmasi password')}}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">{{__('Role')}} *</label>
                                    <select class="form-control select2" required name="role" style="width: 100%">
                                        <option value="">--- {{__('Select Role')}} ---</option>
                                        @if (!empty($roles))
                                            @foreach ($roles as $role)
                                                @php
                                                    if(empty($user->roles[0]) && $role->id == 1 || !empty($user->roles[0]) && $user->roles[0] !== 'superadmin' && $role->id == 1)
                                                        continue;
                                                @endphp
                                                <option value="{{ $role->id }}" @if (!empty($user) && $role->name == $user->roles[0]) {{ 'selected' }} @endif>{{ $role->display_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <input type="hidden" name="role_name" @if(!empty($user->roles[0]) && $user->roles[0]) value="{{$user->roles[0]}}" @endif>
                                    <div class="invalid-feedback">
                                        {{__('Mohon isi role')}}
                                    </div>
                                </div>
                                {{-- @if (!empty($companies))
                                    <div class="form-group">
                                        <label for="">{{__('Company')}} *</label>
                                        <select class="form-control select2" required name="company_id" style="width: 100%">
                                            <option value="">--- {{__('Select Company')}} ---</option>
                                            @foreach ($companies as $company)
                                                <option value="{{ $company->id }}" @if (!empty($user) && $company->id == $user->company_id) {{ 'selected' }} @elseif(!empty(old('company_id')) && old('company_id') == $company->id)  @endif>{{ $company->company_name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            {{__('Mohon isi company')}}
                                        </div>
                                    </div>
                                @endif --}}
                                {{-- <label for="">Jurnal Access Token</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Access Token" id="myInput" name="meta[jurnal_access_token]" value="@if(!empty($metas['jurnal_access_token'])){{$metas['jurnal_access_token']}}@endif">
                                    <div class="input-group-append">
                                        <a class="btn btn-info" href="@if(!empty($settings['jurnal_url'])){{$settings['jurnal_url']}}@endif/authorize_apps/new?client_id=@if(!empty($settings['jurnal_client_id'])){{$settings['jurnal_client_id']}}@endif" target="_blank">Connect Jurnal</a>
                                    </div>
                                </div> --}}
                            </div>
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <input type="file" class="gambar" data-preview="#previewImg" style="display: none" accept="image/*" name="file">
                                        <img id="previewImg" @if(!empty($metas['profile_pic'])) src="{{ $metas['profile_pic']['media_path'] }}"  @else src="{{ asset('assets/static/pp.png') }}" @endif width="225px" class="img-size-125 mb-2">
                                        <div class="text-center">
                                            <a type="button text-center" class="btn upload-btn btn-default btx-sx">
                                            <i class="fas fa-upload"></i> {{__('Upload')}}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr/>
                            <!--<div class="col-sm-12 mt-3">
                                <div class="form-group">
                                    <h6>Store</h6>
                                    <div class="form-group">
                                        {{-- <label>Product Type *</label> --}}
                                        <div class="sepH_a">
                                            <a href="#" class="btn btn-link btn-xs str_select_all">Select All</a>
                                            <a href="#" class="btn btn-link btn-xs str_deselect_all">Deselect All</a>
                                        </div>
                                        <div>
                                            {{-- <select multiple="multiple" id="store" name="warehouses[]" @if(!empty($shiftActive) && $shiftActive) disabled @endif class="multi-select multi-select-store store" data-label="Store">
                                            @if (!empty($warehouses['Store Warehouse']))
                                                @foreach ($warehouses['Store Warehouse'] as $store)
                                                    <option value="{{ $store->id }}" @if(!empty($warehouses_selected) && in_array($store->id, $warehouses_selected)) selected @endif>{{ $store->warehouse_name }}</option>
                                                @endforeach
                                            @endif --}}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>-->
                            
                            @if (!empty($user))
                                <div class="col-sm-6">
                                    <label for="">Status</label>
                                    <div>
                                        <input type="checkbox" data-toggle="toggle" name="status" data-on="Active" data-size="xs" data-off="Archive" data-onstyle="success" data-offstyle="danger" value="1" @if (!empty($user->status) && $user->status == 1) checked @endif>
                                    </div>
                                </div>
                            @endif
                        </div>
                                </div>
                            </div>
                        </div>
                    <!-- /.card-body -->
                    
                        <div class="card-footer">
                            <a href="{{url('users')}}" class="btn btn-default btn-sx">Cancel</a>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" integrity="sha256-yMjaV542P+q1RnH6XByCPDfUFhmOafWbeLPmqKh11zo=" crossorigin="anonymous" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/multi-select/0.9.12/css/multi-select.min.css">
    <link rel="stylesheet" href="{{asset('css/custom.css')}}">  
@stop

@section('js')
    <script type="text/javascript">
        var base_url = {!! json_encode(url('/')) !!};
    </script>
    <script src="{{ asset('js/pos.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.3/jquery.inputmask.min.js"></script>
    <script type="text/javascript" src="{{ asset('vendor/multi-select/js/jquery.multi-select.js') }}"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.quicksearch/2.4.0/jquery.quicksearch.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js" integrity="sha256-5YmaxAwMjIpMrVlK84Y/+NjCpKnFYa8bWWBbUHSBGfU=" crossorigin="anonymous"></script>
    <script src="{{ asset('js/user.js') }}"></script>
    <script src="{{ asset('js/upload.js') }}"></script>
@stop