@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="card card-outline no-need-confirm">
        <div class="card-header">
            <h3 class="card-title text-bold mt-2" style="font-size: 20px;">{{__('Welcome')}}, {{request()->user_name}}</h3>
            <div class="card-tools pull-right">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="dt-buttons col-xs-12 text-center">
                            <div class="form-inline pull-right" style="margin-right:10px;">
                                <label for="daterange-dashboard" style="margin-right:10px">{{__('Periode')}}</label>
                                <input type="text" name="daterange" class="form-control" id="order_time_range" style="width: 230px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    @if(Session::has('flash_error'))
        <div class="alert alert-danger alert-dismissible fade show mt-2 mr-2 not-product" role="alert">
            {!! session('flash_error') !!}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if(Session::has('flash_success'))
        <div class="alert alert-success alert-dismissible fade show mt-2 mr-2 not-product" role="alert">
            {!! session('flash_success') !!}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <div class="row">
        <div class="col-md col-xs-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="order_alls">0</h3>
                    <p>Orders</p>
                </div>
                <div class="icon">
                    <i class="fas fa-fw fa-shopping-cart"></i>
                </div>
                <a href="{{url('orders')}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        
        <div class="col-md col-xs-6">

            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3 id="order_waiting_lists">0</h3>
                    <p>Order Waiting List</p>
                </div>
                <div class="icon">
                    <i class="fas fa-fw fa-list"></i>
                </div>
                <a href="{{url('orders')}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-md col-xs-6">

            <div class="small-box bg-green">
                <div class="inner">
                    <h3 id="income">0</h3>
                    <p>Income</p>
                </div>
                <div class="icon">
                    <i class="fas fa-fw fa-file-invoice-dollar"></i>
                </div>
                <a href="{{url('orders')}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        {{-- <div class="col-md col-xs-6">

            <div class="small-box bg-red">
                <div class="inner">
                    <h3>2</h3>
                    <p>Total Store</p>
                </div>
                <div class="icon">
                    <i class="fas fa-fw fa-home "></i>
                </div>
                <a href="{{url('orders')}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div> --}}
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <strong>{{__('Total Orders Date')}}</strong>
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="orderTime" style="max-height:340px; min-height:340px;width: 100%;"></canvas>
                        <div class="overlay" id="loadingChart">
                        <i class="fa fa-fw fa-spinner fa-spin"></i>
                        <p>Memuat...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/daterangepicker.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/custom.css')}}">
@stop

@section('js')
    {{-- <script src="{{asset('vendor/chart.js/Chart.js')}}"></script>
    <script src="{{asset('vendor/chart.js/Chart.min.js')}}"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.3.2/chart.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-annotation/1.4.0/chartjs-plugin-annotation.min.js"></script>
    <script>
        // Chart.register('chartjs-plugin-annotation');
    </script>
    <script src="{{ asset('js/daterangepicker.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pos.js') }}"></script>
    <script src="{{ asset('js/home.js') }}"></script>
@stop