@extends('adminlte::page')

@section('title', 'Master Data Management - Company')

@section('content_header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                Detail Company: {{$company->display_name}}
            </h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('companies') }}">Company</a></li>
                <li class="breadcrumb-item active">
                    Detail Company: {{$company->display_name}}
                </li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@stop

@section('content')
<section class="content">
    <div class="container">
        <!-- Small boxes (Stat box) -->
        <div class="row justify-content-center">
            <div class="col-10">
                <!-- general form elements disabled -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            Detail Company: {{$company->display_name}}
                        </h3>
                    </div>
                <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="Company">Company Name</label>
                                    @if (!empty($company->display_name))
                                        <p>{{$company->display_name}}</p>
                                    @else
                                        <p>-</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="Parent">Parent Company</label>
                                    @if (!empty($company->parent->display_name))
                                        <p>{{$company->parent->display_name}}</p>
                                    @else
                                        <p>-</p>
                                    @endif
                                </div>
                            </div>
                        </div> --}}
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="billing_address">Billing Address</label>
                                    @if (!empty($company->meta['billing_address']))
                                        <p>{{$company->meta['billing_address']}}</p>
                                    @else
                                        <p>-</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="shipping_address">Shipping Address</label>
                                    @if (!empty($company->meta['shipping_address']))
                                        <p>{{$company->meta['shipping_address']}}</p>
                                    @else
                                        <p>-</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description">Logo</label>
                            <div class="card">
                                <div class="card-body text-center">
                                    <img id="previewImg" @if(!empty($company->meta['image'])) src="{{ $company->meta['image']['media_path'] }}"  @else src="{{ asset('assets/img/box.jpg') }}" @endif width="225px" class="img-size-125 mb-2">
                                </div>
                            </div>
                        </div>
                        @if (!empty($company))
                            @switch($company->status)
                                @case(2)
                                    <span class="badge badge-info">{{$company->status_label}}</span>
                                    @break
                                @case(0)
                                    <span class="badge badge-danger">{{$company->status_label}}</span>
                                    @break
                                @default
                                    <span class="badge badge-success">{{$company->status_label}}</span>
                            @endswitch
                        @endif
                    </div>
                <!-- /.card-body -->
                
                    <div class="card-footer">
                        <a href="{{url('companies')}}" class="btn btn-default btn-sx">Back</a>
                        @if (GlobalHelper::userCan($request, 'update-company'))
                            <a href="{{url('companies/'.$company->id.'/edit')}}" class="btn btn-success btn-sx">Edit</a>
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
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css') }}">
@stop

@section('js')
    <script type="text/javascript">
        var base_url = {!! json_encode(url('/')) !!};
    </script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js') }}"></script>
    <script src="{{ asset('js/pos.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.4/daterangepicker.min.js" type="text/javascript"></script>
@stop