@extends('adminlte::page-kasir')

@section('title', 'POS - Kasir')

@section('content_header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop
@section('body')
    <nav class="navbar-top p-3">
        <div class="row">
            <div class="col">
            {{-- <i class="fas fa-fw fa-arrow-left"></i>  --}}
                <h5 class="m-0"><b>POS</b></h5>
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
        <div class="col-md-4 border-right border-dark">
            
        </div>
        <div class="col-md-8">
            <form action="{{url('kasir/process-bayar')}}" method="POST" id="form-order">
                {{ csrf_field() }}
                <div class="detail-order-2 scrollbar-detail-order bord-detail-order thin-detail-order">

                </div>
                <div class="row bg-secondary border-left border-dark">
                    <div class="col-md-9 p-2" style="color: black">
                        <h4 class="ml-2"><b>TOTAL</b></h4>
                    </div>
                    <div class="col-md-3 p-2 text-right" style="color: black">
                        <h4 class="mr-4"><b>Rp. 100,000</b></h4>
                    </div>
                </div>
                <div class="row border-left border-dark">
                    <div class="col-md-9 p-2">
                        <a href="" class="btn btn-bayar-order-list text-light" style="background-color: black">
                            <b><i class="fas fa-fw fa-print"></i>PRINT</b>
                        </a>
                    </div>
                    <div class="col-md-3 p-2 d-flex">
                        <a href="" class="btn btn-light text-dark mr-2">
                            <b><i class="fas fa-fw fa-save"></i>ORDER</b>
                        </a>
                        <a href="" class="btn btn-bayar-order-list text-light" style="background-color: black">
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
    <script src="{{ asset('js/kasir.js') }}"></script>
@stop