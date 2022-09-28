@extends('adminlte::page')

@section('title', 'Master Data Management - Struktur Company')

@section('content_header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                Structure Company
            </h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('companies') }}">Company</a></li>
            <li class="breadcrumb-item active">
                Structure Company
            </li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@stop

@section('content')
<section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="container">
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
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <!-- general form elements disabled -->
                    <form action="{{url('companies/structure')}}" id="form-struktur" method="post">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    Structure Company
                                </h3>
                            </div>
                            <input type="hidden" name="company_json" id="company_json">
                            <div class="card-body">
                                <div class="col-md-12 boxes-hierarchy" id="boxes-hierarchy">
                                    <div class="dd">
                                        <ol class="dd-list" id="rootList">
                                            
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        
                            <div class="card-footer">
                                <a href="{{url('companies')}}" class="btn btn-default btn-sx">Back</a>
                                <button type="submit" class="btn btn-success btn-sx">Save</button>
                            </div>
                        </div>
                    </form>
                    <!-- /.card -->
                </div>
            </div>
        </div>
        <!-- /.row -->
</section>
@stop

@section('css')
  <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.4/daterangepicker.min.css">
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/nestable2/1.6.0/jquery.nestable.min.css">
  <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@stop

@section('js')
    <script src="{{ asset('js/integriya.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.4/daterangepicker.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/nestable2/1.6.0/jquery.nestable.min.js"></script>
    <script src="{{ asset('js/setting/company.js') }}"></script>
    <script src="{{ asset('js/upload.js') }}"></script>
@stop