@extends('adminlte::page')

@section('title', 'Master Data Management - Category')

@section('content_header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                @empty($category)
                    Add New Category
                @else
                    Edit Category: {{$category->category_display_name}}
                @endif
            </h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('categories') }}">Category</a></li>
            <li class="breadcrumb-item active">
                Detail Category: {{$category->category_display_name}}
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
                        Detail Category: {{$category->category_display_name}}
                    </h3>
                </div>
            <!-- /.card-header -->
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="Category">Parent Category</label>
                                @if (!empty($category->parent))
                                    <p>{{$category->parent->category_display_name}}</p>
                                @else
                                    <p>-</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="Category">Category Name</label>
                                @if (!empty($category))
                                    <p>{{$category->category_display_name}}</p>
                                @else
                                    <p>-</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="Category">Category Description</label>
                                @if (!empty($category))
                                    <p>{{$category->category_description}}</p>
                                @else
                                    <p>-</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="Category">Company</label>
                                @if (!empty($category))
                                    <p>{{$category->company->name}}</p>
                                @else
                                    <p>-</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="status" class="d-block">Status</label>
                        @switch($category->status)
                            @case(2)
                                <span class="badge badge-info">{{$category->status_label}}</span> 
                                @break
                            @case(0)
                                <span class="badge badge-danger">{{$category->status_label}}</span> 
                                @break
                            @default
                                <span class="badge badge-success">{{$category->status_label}}</span>  
                        @endswitch
                    </div>
                </div>
            <!-- /.card-body -->
            
                <div class="card-footer">
                    <a href="{{url('categories/')}}" class="btn btn-default btn-sx">Back</a>
                    @if (GlobalHelper::userCan($request, 'update-category'))
                        <a href="{{url('categories/' . $category->id .'/edit')}}" class="btn btn-success btn-sx">Edit</a>
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
    <script src="{{ asset('js/category.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.4/daterangepicker.min.js" type="text/javascript"></script>
@stop