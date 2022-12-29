@extends('adminlte::page-kasir')

@section('title', 'POS Kasir - Pay Order')

@section('content_header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop
@section('body')
    <nav class="navbar-top p-2">
        <div class="row">
            <div class="col d-flex align-items-center">
                <h5 class="m-0"><a href="{{url('/kasir/order-list')}}" class="text-light"><i class="fas fa-fw fa-arrow-left"></i></a> <b>POS RESTO</b></h5>
            </div>
            <div class="col">
                <div class="row">
                    <div class="col text-right">
                        <img src="{{ asset(config('adminlte.logo_img')) }}" height="50" class="img-circle m-0">
                    </div>
                    <div class="col text-left">
                        <p class="m-0"><b>Warung</b></p>
                        <b>Buyut Semar</b>
                    </div>
                </div>
            </div>
            <div class="col text-right dropdown.flex-*-row-reverse mt-2">
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
        <div class="col-md-4 pl-0 pt-0 pr-0">
            <div class="border-bottom border-dark ">
                <p class="mt-3 ml-3"><b>@if(!empty($order->customer_name)) #{{$order->customer_name}} @endif @if(!empty($order->order_code)) {{$order->order_code}} @endif</b></p>
            </div>
            <div class="pay-order scrollbar-detail-order bord-detail-order thin-detail-order">
                <table class="table text-dark" style="margin-bottom: 13px;">
                    @if(!empty($order->mapping))
                        @foreach ($order->mapping as $item)
                            <tr>
                                <td class="text-center">{{$item->order_qty}}</td>
                                <td width="50%">{{$item->product->product_display_name}}</td>
                                <td>Rp. {{number_format($item->order_subtotal)}}</td>
                            </tr>
                        @endforeach
                    @endif
                </table>
            </div>
            <div class="row m-0" style="background-color: rgb(0, 0, 0)">
                <div class="col text-center pt-2" style="color: white">
                    <h4><b><i class="fas fa-fw fa-credit-card"></i> TOTAL</b></h4>
                </div>
                <div class="col text-center pt-2" style="color: white">
                    <h4><b>@if(!empty($order))Rp. {{number_format($order->total_order)}}@endif</b></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 pl-0 pt-0 pr-0" style="background-color: rgb(203, 203, 203)">
            <div class="border-bottom border-dark">
                <p class="mt-3 ml-2">TOTAL TAGIHAN</p>
            </div>
            <div class="" style="height: 73.2vh">
                <div class="row pt-2 pl-3">
                    <div class="col-7">
                        <b>TAGIHAN</b>
                    </div>
                    <div class="col-5">
                        @if(!empty($order) && $order->status == 4)
                            <b>Rp. 0</b>
                        @else
                            <b>@if(!empty($order))Rp. {{number_format($order->total_order)}}@endif</b>
                        @endif
                    </div>
                </div>
                <div class="row pt-2 pl-3">
                    <div class="col-7">
                        <b>BAYAR TUNAI</b>
                    </div>
                    <div class="col-5">
                        <b id="pay">Rp. 0</b>
                    </div>
                </div>
                <hr class="mb-0" style="width:95%;height:2px;color:rgb(0, 0, 0);background-color:rgb(0, 0, 0)">
                <div class="row pt-2 pl-3" style="color: red">
                    <div class="col-7">
                        <b>SISA TAGIHAN</b>
                    </div>
                    <div class="col-5">
                        @if(!empty($order) && $order->status == 4)
                            <b>Rp. 0</b>
                        @else
                            <b id="bill">@if(!empty($order))Rp. {{number_format($order->total_order)}}@endif</b>
                        @endif
                    </div>
                </div>
                <div class="row pt-2 pl-3">
                    <div class="col-7">
                        <b>Kembali</b>
                    </div>
                    <div class="col-5">
                        <b id="change">Rp. 0</b>
                    </div>
                </div>
            </div>
            <div class="row m-0 btn-print-bayar" data-id="{{$order->id}}">
                <div class="col text-center pt-2">
                    <h4><b><i class="fas fa-fw fa-print"></i> PRINT</b></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 pl-0 pt-0 pb-0">
            <div class="border-bottom border-dark">
                <p class="mt-3 ml-2">PEMBAYARAN TUNAI</p>
            </div>
            
            <div class="" style="height: 73.2vh">
                <div class="text-center p-2">
                    <div class="alert alert-success alert-dismissible fade show @if(!empty($order) && $order->status == 2) d-none @endif" role="alert">
                        <i class="fas fa-fw fa-check"></i> <b>Sudah Bayar</b>
                    </div>
                    <button class="btn btn-block" id="pay-pas" @if(!empty($order) && $order->status == 4) disabled @endif data-pay="@if(!empty($order)){{$order->total_order}}@endif" style="background-color: rgb(0, 0, 0); color:white"><b>UANG PAS</b></button>
                </div>
                <div class="form-group text-center p-2">
                    <input type="text" class="form-control numbering enter-pay" @if(!empty($order) && $order->status == 4) disabled @endif autofocus placeholder="Enter Bayar">
                    <input type="hidden" id="total" value="@if(!empty($order)){{$order->total_order}}@endif">
                    <input type="hidden" id="tunai" value="0">
                </div>
            </div>
            <div class="row m-0 btn-prosess" style="opacity:0.5" data-id-order="{{$order->id}}">
                <div class="col text-center pt-2">
                    <h4><b><i class="fas fa-fw fa-credit-card"></i> PROSES BAYAR</b></h4>
                </div>
            </div>
        </div>
    </div>
    <div class="overlay d-none spinner">
        <i class="fa fa-fw fa-spinner fa-spin"></i>
    </div>
    
    <!-- Grid row -->
@stop

@section('css')
    {{-- <link rel="stylesheet" href="{{asset('css/custom.css')}}"> --}}
@stop

@section('js')
    <script type="text/javascript">
        var base_url = {!!json_encode(url('/')) !!};
    </script>
    <script src="{{ asset('js/pos.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.4/daterangepicker.min.js" type="text/javascript"></script>
    <script src="{{ asset('js/pay-order.js') }}"></script>
@stop