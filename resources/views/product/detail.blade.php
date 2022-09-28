@extends('adminlte::page')

@section('title', 'Master Data Management - Product')

@section('content_header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                @empty($product)
                    {{__('Add New Product')}}
                @else
                    {{__('Edit Product')}}: {{$product->product_display_name}}
                @endif
            </h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/">{{__('Home')}}</a></li>
            <li class="breadcrumb-item"><a href="{{ url('products') }}">{{__('Product')}}</a></li>
            <li class="breadcrumb-item active">
                {{__('Detail Product')}}: {{$product->product_display_name}}
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
                        {{__('Detail Product')}}: {{$product->product_display_name}}
                    </h3>
                </div>
            <!-- /.card-header -->
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="Product">{{__('Category Product')}}</label>
                                @if (!empty($product->category))
                                    <p>{{$product->category->category_display_name}}</p>
                                @else
                                    <p>-</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="Product">Product Name</label>
                                @if (!empty($product))
                                    <p>{{$product->product_display_name}}</p>
                                @else
                                    <p>-</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="Product">Product Price</label>
                                @if (!empty($product))
                                    <p>Rp.{{number_format($product->product_price)}}</p>
                                @else
                                    <p>-</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="Product">Product Description</label>
                                @if (!empty($product))
                                    <p>{{$product->product_description}}</p>
                                @else
                                    <p>-</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Product Image</label>
                        <div class="card">
                            <div class="card-body text-center">
                            <img id="previewImg" @if(!empty($product->meta['image'])) src="{{ $product->meta['image']['media_path'] }}"  @else src="{{ asset('assets/img/box.jpg') }}" @endif width="250px" class="img-size-125 mb-2">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="status" class="d-block">Status</label>
                        @switch($product->status)
                            @case(2)
                                <span class="badge badge-info">{{$product->status_label}}</span> 
                                @break
                            @case(0)
                                <span class="badge badge-danger">{{$product->status_label}}</span> 
                                @break
                            @default
                                <span class="badge badge-success">{{$product->status_label}}</span>  
                        @endswitch
                    </div>
                </div>
            <!-- /.card-body -->
            
                <div class="card-footer">
                    <a href="{{url('products/')}}" class="btn btn-default btn-sx">Back</a>
                    @if (GlobalHelper::userCan($request, 'update-product'))
                        <a href="{{url('products/' . $product->id .'/edit')}}" class="btn btn-success btn-sx">Edit</a>
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
    <script src="{{ asset('js/pos.js') }}"></script>
    <script src="{{ asset('js/product.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.4/daterangepicker.min.js" type="text/javascript"></script>
@stop