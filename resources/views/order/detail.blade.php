@extends('adminlte::page')

@section('title', 'Transaction - Order')

@section('content_header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                @empty($order)
                    {{__('Add New Order')}}
                @else
                    {{__('Edit Order')}}: {{$order->order_code.' #'.$order->customer_name}}
                @endif
            </h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/">{{__('Home')}}</a></li>
            <li class="breadcrumb-item"><a href="{{ url('orders') }}">{{__('Order')}}</a></li>
            <li class="breadcrumb-item active">
                {{__('Detail Order')}}: {{$order->order_code.' #'.$order->customer_name}}
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
                        {{__('Detail Order')}}: {{$order->order_code.' #'.$order->customer_name}}
                    </h3>
                </div>
            <!-- /.card-header -->
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Order">Order Code</label>
                                @if (!empty($order))
                                    <p>{{$order->order_code}}</p>
                                @else
                                    <p>-</p>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Order">Customer Name</label>
                                @if (!empty($order->customer_name))
                                    <p>{{$order->customer_name}}</p>
                                @else
                                    <p>-</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Order">Store</label>
                                @if (!empty($order->store))
                                    <p>{{$order->store->store_name}}</p>
                                @else
                                    <p>-</p>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Order">Order Price</label>
                                @if (!empty($order))
                                    <p>Rp.{{number_format($order->total_order)}}</p>
                                @else
                                    <p>-</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="Order">Order Date</label>
                                @if (!empty($order))
                                    <p>{{date('M, d-Y H:i:s', strtotime($order->created_at))}}</p>
                                @else
                                    <p>-</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="Order">Order Item</label>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Qty</th>
                                            <th>Price/1</th>
                                            <th>Subprice</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($order->mapping)
                                            @php
                                                $grandtotal = 0;
                                            @endphp
                                            @foreach ($order->mapping as $item)
                                                <tr>
                                                    <td>{{$item->product->product_display_name}}</td>
                                                    <td>{{$item->order_qty}}</td>
                                                    <td>Rp. {{number_format($item->default_price)}}</td>
                                                    <td>Rp. {{number_format($item->order_subtotal)}}</td>
                                                </tr>
                                                @php
                                                    $grandtotal += $item->default_price * $item->order_qty;
                                                @endphp
                                            @endforeach
                                            <tr>
                                                <th class="text-right" colspan="3">Total:</th>
                                                {{-- <th colspan="2"></th> --}}
                                                <th>Rp. {{number_format($grandtotal)}}</th>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="status" class="d-block">Status</label>
                        @switch($order->status)
                            @case(2)
                                <span class="badge badge-info">{{$order->status_label}}</span> 
                                @break
                            @case(0)
                                <span class="badge badge-danger">{{$order->status_label}}</span> 
                                @break
                            @default
                                <span class="badge badge-success">{{$order->status_label}}</span>  
                        @endswitch
                    </div>
                </div>
            <!-- /.card-body -->
            
                <div class="card-footer">
                    <a href="{{url('orders/')}}" class="btn btn-default btn-sx">Back</a>
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
    <script src="{{ asset('js/order.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.4/daterangepicker.min.js" type="text/javascript"></script>
@stop