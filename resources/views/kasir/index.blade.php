@extends('adminlte::page-kasir')

@section('body')
    <nav class="navbar-top p-3">
        <div class="row">
            <div class="col">
            {{-- <i class="fas fa-fw fa-arrow-left"></i>  --}}
                <h5 class="m-0"><b>POINT OF SALES</b></h5>
            </div>
            <div class="col text-center">
            {{-- <i class="fas fa-fw fa-arrow-left"></i>  --}}
                <h5 class="m-0"><b>Logo</b></h5>
            </div>
            <div class="col text-right">
            {{-- <i class="fas fa-fw fa-arrow-left"></i>  --}}
                <h5 class="m-0"><b>Username</b></h5>
            </div>
        </div>
    </nav>
    <div class="row" id="navbar">
        <div class="col-sm-8 border-right border-dark">
            <div class="row">
                <div class="d-flex mt-3 col-sm-6">
                    <label class="py-2 mx-2">Filter</label>
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
                <div class="col-md-12 pt-2 example-1 scrollbar-deep-purple bordered-deep-purple thin border-top border-dark bg-light" style="margin-top: 15px ">
                    <div class="row pt-2 product-wrapper">
                        {{-- @foreach ($products as $product)
                            <div class="col-md-2.5 ml-3">
                                <a href="#" class="product-show" title="{{$product->product_display_name}}">
                                    <div class="card card-kasir" style="width: 133px">
                                        @php
                                            if(!empty($product->metas))
                                            {
                                                $metas = array();
                                                foreach($product->metas as $meta)
                                                    $metas[$meta->meta_key] = GlobalHelper::maybe_unserialize($meta->meta_value);

                                                if(!empty($metas['image']))
                                                    $product->image_html = $metas['image']['media_path'];
                                            }
                                        @endphp
                                        <img class="card-img-top" src="{{$product->image_html}}" height="100" alt="Card image cap">
                                        <p class="text-center mb-0 text-light text-price"><b>Rp. {{number_format($product->product_price)}}</b></p>
                                        <p class="card-text text-center text-title"><b>{{substr($product->product_display_name, 0 ,28)}}</b></p>
                                    </div>
                                </a>
                            </div>
                        @endforeach --}}
                    </div>
                    <div class="overlay d-none spinner">
                        <i class="fa fa-fw fa-spinner fa-spin"></i>
                    </div>
                </div>
                <!-- Grid column -->
            </div>
        </div>
        <div class="col-sm-4">
            <form action="">
                <div class="row border-bottom border-dark">
                    <div class="col-md-6 border-right border-dark">
                        <div class="border-bottom mt-1">
                            <b>ORDER LIST</b>
                        </div>
                        <div class="mt-2 mb-1">
                            <select class="form-control filter-table select2 mr-2" disabled name="category_id" id="category_id">
                                <option value="">-- Select Order Code --</option>
                                @if (!empty(session('companies')))
                                    @foreach (session('companies') as $comp)
                                        <option value="{{$comp['id']}}" @if(!empty(session('company')['id']) && session('company')['id'] == $comp['id']) selected @endif>{{$comp['company_name']}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 mt-1">
                        <b>CUSTOMER NAME</b>
                        <div class="mb-1">
                            <input type="text" class="form-control" placeholder="Enter Name"  style="max-width: 200px" required>
                        </div>
                    </div>
                </div>
                
                {{-- muncul ketika sudah memilih produk --}}
                <p class="m-2">Order Code: KO/06/000001</p>
                <div class="example-2 scrollbar-deep-order bord-orderered-deep-order thin-order">
                    <table>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="input-group input-spinner">
                                        <div class="input-group-prepend">
                                            <button class="btn btn-light rounded-left btn-update-qty button-minus layer-0" type="button" id="button-minus" data-operator="-"> <i class="fa fa-minus"></i> </button>
                                        </div>
                                        <input type="text" class="form-control claim_qty px-0" value="1" min="1" name="claim_qty" id="claim_qty" max="45">
                                        <div class="input-group-append">
                                            <button class="btn btn-light rounded-right btn-update-qty button-plus layer-0" type="button" id="button-plus" data-operator="+" data-max="10"> <i class="fa fa-plus"></i> </button>
                                        </div>
                                    </div>
                                </td>
                                <td width="210" class="">
                                    <p class="mt-3">ini adalah nama product </p>
                                </td>
                                <td>
                                    <p class="mt-3" id="price">Rp. 10.000</p>
                                    <input type="hidden" value="10000" class="default_price">
                                    <input type="hidden" value="10000" class="sub_price">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="input-group input-spinner">
                                        <div class="input-group-prepend">
                                            <button class="btn btn-light rounded-left btn-update-qty button-minus layer-0" type="button" id="button-minus" data-operator="-"> <i class="fa fa-minus"></i> </button>
                                        </div>
                                        <input type="text" class="form-control claim_qty px-0" value="2" min="1" name="claim_qty" id="claim_qty" max="45">
                                        <div class="input-group-append">
                                            <button class="btn btn-light rounded-right btn-update-qty button-plus layer-0" type="button" id="button-plus" data-operator="+" data-max="45"> <i class="fa fa-plus"></i> </button>
                                        </div>
                                    </div>
                                </td>
                                <td width="210" class="">
                                    <p class="mt-3">ini adalah nama product </p>
                                </td>
                                <td>
                                    <p class="mt-3" id="price">Rp. 30.000</p>
                                    <input type="hidden" value="30000" class="default_price">
                                    <input type="hidden" value="30000" class="sub_price">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="input-group input-spinner">
                                        <div class="input-group-prepend">
                                            <button class="btn btn-light rounded-left btn-update-qty button-minus layer-0" type="button" id="button-minus" data-operator="-"> <i class="fa fa-minus"></i> </button>
                                        </div>
                                        <input type="text" class="form-control claim_qty px-0" value="1" min="1" name="claim_qty" id="claim_qty" max="45">
                                        <div class="input-group-append">
                                            <button class="btn btn-light rounded-right btn-update-qty button-plus layer-0" type="button" id="button-plus" data-operator="+" data-max="45"> <i class="fa fa-plus"></i> </button>
                                        </div>
                                    </div>
                                </td>
                                <td width="210" class="">
                                    <p class="mt-3">ini adalah nama product </p>
                                </td>
                                <td>
                                    <p class="mt-3" id="price">Rp. 1.750.000</p>
                                    <input type="hidden" value="1750000" class="default_price">
                                    <input type="hidden" value="1750000" class="sub_price">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="row bg-light">
                    <div class="col text-center p-2">
                        <a href="" class="btn-order">
                            <b>HAPUS</b>
                        </a>
                    </div>
                    <div class="col text-center p-2 border-left border-dark">
                        <a href="" class="btn-order">
                            <b>SIMPAN</b>
                        </a>
                    </div>
                </div>
                <div class="row order-price">
                    <div class="col text-center pt-2">
                        <a href="" class="btn-order-price">
                            <h4><b>BAYAR</b></h4>
                        </a>
                    </div>
                    <div class="col text-center pt-2">
                        <a href="" class="btn-order-price">
                            <h4><b>Rp. 5.000.000</b></h4>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        
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