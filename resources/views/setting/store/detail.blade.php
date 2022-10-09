@extends('adminlte::page')

@section('title', 'Master Data Management - Store')

@section('content_header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                Detail Store: {{$store->store_name}}
            </h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('stores') }}">Store</a></li>
                <li class="breadcrumb-item active">
                    Detail Store: {{$store->store_name}}
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
                            Detail Store: {{$store->store_name}}
                        </h3>
                    </div>
                <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="Store">Store Name</label>
                                    @if (!empty($store->store_name))
                                        <p>{{$store->store_name}}</p>
                                    @else
                                        <p>-</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="Parent">Parent Store</label>
                                    @if (!empty($store->parent->store_name))
                                        <p>{{$store->parent->store_name}}</p>
                                    @else
                                        <p>-</p>
                                    @endif
                                </div>
                            </div>
                        </div> --}}
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="store_description">Store dESCRIPTION</label>
                                    @if (!empty($store->store_description))
                                        <p>{{$store->store_description}}</p>
                                    @else
                                        <p>-</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="store_address">Store Address</label>
                                    @if (!empty($store->store_address))
                                        <p>{{$store->store_address}}</p>
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
                                    <img id="previewImg" @if(!empty($store->meta['image'])) src="{{ $store->meta['image']['media_path'] }}"  @else src="{{ asset('assets/img/box.jpg') }}" @endif width="225px" class="img-size-125 mb-2">
                                </div>
                            </div>
                        </div>
                        @if (!empty($store))
                            @switch($store->status)
                                @case(2)
                                    <span class="badge badge-info">{{$store->status_label}}</span>
                                    @break
                                @case(0)
                                    <span class="badge badge-danger">{{$store->status_label}}</span>
                                    @break
                                @default
                                    <span class="badge badge-success">{{$store->status_label}}</span>
                            @endswitch
                        @endif
                    </div>
                <!-- /.card-body -->
                
                    <div class="card-footer">
                        <a href="{{url('stores')}}" class="btn btn-default btn-sx">Back</a>
                        @if (GlobalHelper::userCan($request, 'update-store'))
                            <a href="{{url('stores/'.$store->id.'/edit')}}" class="btn btn-success btn-sx">Edit</a>
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
@stop

@section('js')
    <script type="text/javascript">
        var base_url = {!! json_encode(url('/')) !!};
    </script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/pos.js') }}"></script>
@stop