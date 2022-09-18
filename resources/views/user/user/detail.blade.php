@extends('adminlte::page')

@section('title', 'User Management - Detail User')

@section('content_header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                {{__('Detail User')}}: @if (!empty($user->fullname)) {{$user->fullname}} @endif
            </h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('users') }}">User</a></li>
                <li class="breadcrumb-item active">
                    {{__('Detail User')}}: @if (!empty($user->fullname)) {{$user->fullname}} @endif
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
                            {{__('Detail User')}}: @if (!empty($user->fullname)) {{$user->fullname}} @endif
                        </h3>
                    </div>
                <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group mb-3">
                                    <label>{{__('Name')}}</label>
                                    <p>
                                        @if (!empty($user->fullname))
                                            {{ $user->fullname }}
                                        @endif
                                    </p>
                                </div>
                                <div class="form-group mb-3">
                                    <label>{{__('Email')}}</label>
                                    <p>
                                        @if (!empty($user->email))
                                            {{ $user->email }}
                                        @endif
                                    </p>
                                </div>
                                <div class="form-group mb-3">
                                    <label>{{__('Phone')}}</label>
                                    <p>
                                        @if (!empty($user->phone))
                                            {{ $user->phone }}
                                        @endif
                                    </p>
                                </div>
                                <div class="form-group mb-3">
                                    <label>{{__('Role')}}</label>
                                    <p>
                                        @if (!empty($user->roles[0]))
                                            {{ $user->roles[0] }}
                                        @endif
                                    </p>
                                </div>
                                {{-- <div class="form-group mb-3">
                                    <label>{{__('Tanggal Lahir')}}</label>
                                    <p>
                                        @if (!empty($metas['tanggal_lahir']))
                                            {{ $metas['tanggal_lahir'] }}
                                        @endif
                                    </p>
                                </div>
                                <div class="form-group mb-3">
                                    <label>{{__('Address')}}</label>
                                    <p>
                                        @if (!empty($metas['address']))
                                            {{ $metas['address'] }}
                                        @endif
                                    </p>
                                </div> --}}
                                {{-- <div class="form-group mb-3">
                                    <label>{{__('Company')}}</label>
                                    <p>
                                        @if (!empty($user->company->company_name))
                                            {{ $user->company->company_name }}
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div> --}}
                            </div>
                            <div class="col-sm-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <img id="previewImg" @if(!empty($metas['profile_pic'])) src="{{ $metas['profile_pic']['media_path'] }}" width="50%"  @else src="{{ asset('assets/img/pr.jpg') }}" width="50%" @endif class="img-size-250 img-circle mb-2">
                                </div>
                            </div>
                            </div>
                            <div class="card-body">
                                @if (!empty($user))
                                    <h6>Status</h6>
                                    <div class="row">
                                        <div class="col-6">
                                            @switch($user->status)
                                                @case(2)
                                                    <span class="badge badge-info">{{$user->status_label}}</span>
                                                    @break
                                                @case(0)
                                                    <span class="badge badge-danger">{{$user->status_label}}</span>
                                                    @break
                                                @default
                                                <span class="badge badge-success">{{$user->status_label}}</span>
                                            @endswitch
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    
                    <div class="card-footer">
                        <a href="{{url('users')}}" class="btn btn-default btn-sx">{{__('Back')}}</a>
                        @if(GlobalHelper::userCan($request, 'update-users'))
                            <a href="{{url('users/'.$user->id.'/edit')}}" class="btn btn-success btn-sx" id="btnSubmit">{{__('Edit')}}</a>
                        @endif
                    </div>
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
@stop

@section('js')
    <script type="text/javascript">
        var base_url = {!! json_encode(url('/')) !!};
    </script>
    <script src="{{ asset('js/kiosk.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.4/daterangepicker.min.js" type="text/javascript"></script>
    <script src="{{ asset('js/user.js') }}"></script>
    <script src="{{ asset('js/upload.js') }}"></script>
@stop