@extends('adminlte::page-kasir')

@section('title', 'POS - Kasir')

@section('content_header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop
@section('body')
    <nav class="navbar-top p-3">
        <div class="row">
            <div class="col">
                <h5 class="m-0"><a href="{{url('/kasir')}}" class="text-light"><i class="fas fa-fw fa-arrow-left"></i></a> <b>POS RESTO</b></h5>
            </div>
            <div class="col text-center">
                <h5 class="m-0"><b>Logo</b></h5>
            </div>
            <div class="col text-right dropdown">
                <a href="#" class="m-0 dropdown-toggle text-light" id="kasirDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><b>{{$request->user_name}} </b><img src="@if(!empty($metas['profile_pic'])){{$metas['profile_pic']['media_path']}}@else{{asset('assets/static/pp.png')}}@endif" class="float-rigth rounded-circle" alt="" width="30" height="30"></a>
                <div class="dropdown-menu" aria-labelledby="kasirDropdown">
                    <a class="dropdown-item" href="/" target="_blank"><i class="fas fa-fw fa-home"></i> Dashboard</a>
                    @php
                        $logout_url = View::getSection('logout_url') ?? config('adminlte.logout_url', 'logout') 
                    @endphp

                    @if (config('adminlte.use_route_url', false))
                        @php $logout_url = $logout_url ? route($logout_url) : '' @endphp
                    @else
                        @php $logout_url = $logout_url ? url($logout_url) : '' @endphp
                    @endif
                    <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-fw fa-power-off text-danger"></i> Logout</a>
                    <form id="logout-form" action="{{ $logout_url }}" method="POST" style="display: none;">
                        @if(config('adminlte.logout_method'))
                            {{ method_field(config('adminlte.logout_method')) }}
                        @endif
                        {{ csrf_field() }}
                    </form>
                </div>
            </div>
        </div>
    </nav>
    <div class="row" id="navbar">
        <div class="col-md-4 pr-0 border-right border-dark list-order-2 scrollbar-list-order bord-list-order thin-list-order">
            <div class="pl-5 pr-5 pt-2 pb-3 sticky-top bg-white">
                <div class="row d-flex">
                    <input type="text" class="form-control" id="search_order" placeholder="Search order">
                    <div class="icon" style="margin: 8px 0 0 -25px">
                        <i class="fas fa-fw fa-search"></i>
                    </div>
                </div>
            </div>
            <div class="row pl-2 text-dark" style="background-color: rgb(203, 203, 203);">
                <div class="col">
                    <small><b>{{date('d-m-Y')}}</b></small>
                </div>
                <div class="col text-right">
                    <small class="mr-2"><b>@if(!empty($orders->total_records)){{$orders->total_records}}@else 0 @endif Transaksi</b></small>
                </div>
            </div>

            <div class="order_list">
                @if(!empty($orders->orders))
                    @foreach ($orders->orders as $key => $order)
                        <div class="pl-2 pt-4 pr-2 order_list_detail" data-order="{{json_encode($order)}}">
                            <div class="row border-bottom border-dark pb-2">
                                <div class="col">
                                    <span class="m-0">#@if(!empty($order->customer_name)){{$order->customer_name}}@endif</span><br>
                                    <b>{{$order->order_code}}</b>
                                </div>
                                <div class="col text-right">
                                    <small>{{date('H:i',strtotime($order->created_at))}}</small><br>
                                    <b>Rp. {{number_format($order->total_order)}}</b>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center"><h4>Belum ada order</h4></div>
                @endif
            </div>
            
        </div>
        <div class="col-md-8 p-0">
            <form action="{{url('kasir/process-bayar')}}" method="POST" id="form-order">
                {{ csrf_field() }}
                <div class="detail-order-2 scrollbar-detail-order bord-detail-order thin-detail-order">
                    <div class="row text-light" style="background-color: rgb(203, 203, 203)">
                        <table class="table text-dark" style="margin-bottom: 13px;">
                            <tr>
                                <td class="pl-4">Kode Order<br><b id="order_code">-</b></td>
                                <td>Nama Pemesan<br><b id="customer_name">-</b></td>
                                <td>Waktu Pemesanan<br><b id="order_date">-</b></td>
                                <td>Kasir<br><b id="store">-</b></td>
                            </tr>
                        </table>
                    </div>

                    <div class="table-order-list">
                        <div class="text-center mt-3"><h4>Tidak ada order yang dipilih!</h4></div>
                    </div>
                </div>
                <div class="row m-0 pt-2" style="background-color: rgb(203, 203, 203)">
                    <div class="col-md-9 p-2" style="color: black">
                        <h4 class="ml-2"><b>TOTAL</b></h4>
                    </div>
                    <div class="col-md-3 p-2 text-right" style="color: black">
                        <h4 class="mr-4"><b id="total_bayar">Rp. 0</b></h4>
                    </div>
                </div>
                <div class="row m-0">
                    <div class="col-md-8 p-2">
                        <a href="#" class="btn btn-print-order-list text-light ml-4 disabled" style="background-color: black">
                            <b><i class="fas fa-fw fa-print"></i>PRINT</b>
                        </a>
                    </div>
                    <div class="col-md-4 p-2 d-flex">
                        <a href="#" class="btn btn-light btn-order-order-list text-dark mr-2 disabled">
                            <b><i class="fas fa-fw fa-save"></i>ORDER</b>
                        </a>
                        <a href="#" class="btn btn-bayar-order-list text-light disabled" style="background-color: black">
                            <b><i class="fas fa-fw fa-save"></i>BAYAR</b>
                        </a>
                    </div>
                    <button type="submit" class="btnSubmit d-none"></button>
                </div>
            </form>
        </div>
        <div class="overlay d-none spinner">
            <i class="fa fa-fw fa-spinner fa-spin"></i>
        </div>
    </div>
    
    <!-- Grid row -->
@stop

@section('css')
    <link rel="stylesheet" href="{{asset('css/custom.css')}}">
@stop

@section('js')
    <script type="text/javascript">
        var base_url = {!!json_encode(url('/')) !!};
    </script>
    <script src="{{ asset('js/pos.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.4/daterangepicker.min.js" type="text/javascript"></script>
    <script src="{{ asset('js/order-list.js') }}"></script>
@stop