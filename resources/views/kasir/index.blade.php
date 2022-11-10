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
                <h5 class="m-0"><b>POS RESTO</b></h5>
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
    <div class="row" id="navbar row-body">
        <div class="col-md-8 border-right border-dark">
            <div class="row">
                <div class="d-flex mt-3 col-sm-6">
                    <label class="py-2 mx-2"><i class="fas fa-fw fa-filter"></i>Filter</label>
                    <div class="mr-2">
                        <select class="form-control filter-table select2 mr-2" name="category_id" id="category_id" style="max-width: 250px">
                            <option value="" id="all">-- All Category --</option>
                            @if (!empty($categories))
                                @foreach ($categories as $cat)
                                    <option value="{{$cat->id}}" @if(!empty($product->category_id) && $cat->id == $product->category_id) selected @endif>{{$cat->category_display_name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="mt-3 col-sm-6">
                    <div class="mr-2 float-right d-flex">
                        <input type="text" class="form-control" id="search" placeholder="Search product">
                        <div class="icon" style="margin: 8px 0 0 -25px">
                            <i class="fas fa-fw fa-search"></i>
                        </div>
                        {{-- <i class="fas fa-fw fa-times"></i> --}}
                    </div>
                </div>
                <!-- Grid column -->
                <div class="col-md-12 pt-2 example-1 scrollbar-deep-purple thin border-top border-dark bg-light" style="margin-top: 15px ">
                    <div class="row pt-2 product-wrapper">
                        @foreach ($products as $product)
                            <div class="col-md-2.5 ml-3">
                                <div class="product-show btn-product-card" data-name="{{$product->product_display_name}}" data-price="{{$product->product_price}}" data-id="{{$product->id}}" title="{{$product->product_display_name}}">
                                    <div class="card card-kasir" id="card-product" style="width: 133px">
                                        @php
                                            if(!empty($product->metas))
                                            {
                                                $metas = array();
                                                foreach($product->metas as $meta)
                                                    $metas[$meta->meta_key] = GlobalHelper::maybe_unserialize($meta->meta_value);

                                                if(!empty($metas['image']))
                                                    $image_html = $metas['image']['media_path'];
                                            }
                                        @endphp
                                        <img class="card-img-top" src="@if(!empty($product->metas)) {{$image_html}} @else {{asset('/assets/img/box.jpg')}} @endif" height="100" alt="{{$product->product_display_name}}">
                                        <p class="text-center mb-0 text-light text-price"><b>Rp. {{number_format($product->product_price)}}</b></p>
                                        <p class="card-text text-center text-title"><b>{{substr($product->product_display_name, 0 ,28)}}</b></p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <!-- Grid column -->
            </div>
        </div>
        <div class="col-md-4">
            <form action="{{url('kasir/store')}}" method="POST" id="form-order">
                {{ csrf_field() }}
                <div id="update-order"></div>
                <div class="row border-bottom border-dark">
                    <div class="col-6 border-right border-dark">
                        <div class="border-bottom mt-1 btn-list-order">
                            <b><i class="fas fa-fw fa-list"></i>ORDER LIST</b><small class="bg-danger rounded-circle pl-1 pr-1 count-order">@if(!empty($count_orders)){{$count_orders}}@else{{0}}@endif</small>
                        </div>
                        <div class="mt-2 mb-1">
                            <select class="form-control filter-table select2 mr-2" name="order_id" id="order_id">
                                <option value="" id="order_code">-- Order Code --</option>
                                @if(!empty($orders))
                                    @foreach ($orders as $order)
                                        <option value="{{$order->id}}" @if(!empty($order_list->order_id) && $order_list == $order->id) selected @elseif(!empty($_GET['order']) && $_GET['order'] == $order->id) selected @endif>{{$order->order_code}}@if(!empty($order->customer_name)) #{{$order->customer_name}}@endif</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-6 mt-1">
                        <b><i class="fas fa-fw fa-user"></i>CUSTOMER NAME</b>
                        <div class="mb-1">
                            <input type="text" class="form-control" name="customer" id="customer-name" placeholder="Enter Name"  style="max-width: 200px" required autocomplete="off">
                        </div>
                    </div>
                </div>
                
                {{-- muncul ketika sudah memilih produk --}}
                {{-- <p class="m-2">Order Code: CO/06/000001</p> --}}
                <div class="example-2 scrollbar-deep-order bord-orderered-deep-order thin-order">
                    <table>
                        <tbody id="list_order">
                            @if(Session::has('flash_error'))
                                <div class="alert alert-success alert-dismissible fade show mt-2 mr-2 not-product" role="alert">
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
                            <p class="m-3 not-product">Belum ada product yang dipilih!</p>
                            <tr class="d-none tr_clone_items">
                                <td>
                                    <div class="input-group input-spinner">
                                        <div class="input-group-prepend">
                                            <button class="btn btn-light rounded-left btn-update-qty button-minus layer-0" type="button" id="button-minus" data-operator="-"> <i class="fa fa-minus"></i> </button>
                                        </div>
                                        <input type="text" class="form-control claim_qty px-0 " value="1" min="1" name="items[##n##][column][claim_qty]" id="claim_qty" max="45" readonly>
                                        <div class="input-group-append">
                                            <button class="btn btn-light rounded-right btn-update-qty button-plus layer-0" type="button" id="button-plus" data-operator="+" data-max="10"> <i class="fa fa-plus"></i> </button>
                                        </div>
                                    </div>
                                </td>
                                <td width="210">
                                    <p class="mt-3" id="product_name"></p>
                                </td>
                                <td>
                                    <p class="mt-3" id="price"></p>
                                    <input type="hidden" name="items[##n##][column][default_price]" value="0" class="default_price">
                                    <input type="hidden" name="items[##n##][column][subtotal]" class="sub_price">
                                    <input type="hidden" name="items[##n##][column][product_id]" class="product_id">
                                </td>
                            </tr>
                            <input type="hidden" value="0" id="total_add_qty">
                        </tbody>
                    </table>
                    <div id="append_order">
                    </div>
                    <input type="hidden" name="grandtotal" value="0" id="grandtotal">
                </div>
                <div class="row bg-light form-btn">
                    <div class="col text-center p-2 btn-hapus add-btn-hapus">
                        <b><i class="fas fa-fw fa-trash"></i>HAPUS</b>
                    </div>
                    <div class="col text-center p-2 border-left border-dark btn-simpan add-btn-simpan">
                        <b><i class="fas fa-fw fa-save"></i>SIMPAN</b>
                    </div>
                    <button type="submit" class="btnSubmit d-none"></button>
                </div>
                <div class="row order-price btn-bayar add-btn-bayar form-btn">
                    <div class="col text-center pt-2">
                        <h4><b><i class="fas fa-fw fa-credit-card"></i> BAYAR</b></h4>
                    </div>
                    <div class="col text-center pt-2 grandtotal-bayar">
                        <h4><b>Rp. 0</b></h4>
                    </div>
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