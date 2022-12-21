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
                <p class="mt-3 ml-3"><b>#rifki CO/22/12/0017</b></p>
            </div>
            <div class="" style="height: 73.2vh">
                <table class="table text-dark" style="margin-bottom: 13px;">
                    <tr>
                        <td class="text-center">1</td>
                        <td width="50%">Soto</td>
                        <td>Rp. 10,000</td>
                    </tr>
                    <tr>
                        <td class="text-center">1</td>
                        <td width="50%">Soto</td>
                        <td>Rp. 10,000</td>
                    </tr>
                    <tr>
                        <td class="text-center">1</td>
                        <td width="50%">Soto</td>
                        <td>Rp. 10,000</td>
                    </tr>
                </table>
            </div>
            <div class="row m-0" style="background-color: rgb(0, 0, 0)">
                <div class="col text-center pt-2" style="color: white">
                    <h4><b><i class="fas fa-fw fa-credit-card"></i> TOTAL</b></h4>
                </div>
                <div class="col text-center pt-2" style="color: white">
                    <h4><b>Rp. 10,000,000</b></h4>
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
                        <b>Rp. 10,000,000</b>
                    </div>
                </div>
                <div class="row pt-2 pl-3">
                    <div class="col-7">
                        <b>BAYAR TUNAI</b>
                    </div>
                    <div class="col-5">
                        <b>Rp. 9,000,000</b>
                    </div>
                </div>
                <hr class="mb-0" style="width:95%;height:2px;color:rgb(0, 0, 0);background-color:rgb(0, 0, 0)">
                <div class="row pt-2 pl-3" style="color: red">
                    <div class="col-7">
                        <b>SISA TAGIHAN</b>
                    </div>
                    <div class="col-5">
                        <b>Rp. 1,000,000</b>
                    </div>
                </div>
                <div class="row pt-2 pl-3">
                    <div class="col-7">
                        <b>Kembali</b>
                    </div>
                    <div class="col-5">
                        <b>Rp. 0</b>
                    </div>
                </div>
            </div>
            <div class="row m-0" style="background-color: rgb(160, 160, 160)">
                <div class="col text-center pt-2" style="color: rgb(0, 0, 0)">
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
                    <button class="btn btn-block" style="background-color: rgb(0, 0, 0); color:white"><b>UANG PAS</b></button>
                </div>
                <div class="form-group text-center p-2">
                    <input type="number" class="form-control" id="" autofocus placeholder="Enter Bayar">
                </div>
            </div>
            <div class="row m-0" style="background-color: rgb(0, 0, 0)">
                <div class="col text-center pt-2" style="color: white">
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