@extends('adminlte::page')

@section('title', 'Master Data Management - Product')

@section('content_header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                @empty($product)
                    {{__('Add New Product')}}
                @else
                    {{__('Edit Product')}}: {{$product->product_display_name}}
                @endif
            </h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="/">{{__('Home')}}</a></li>
                <li class="breadcrumb-item"><a href="{{ url('products') }}">{{__('Product')}}</a></li>
                <li class="breadcrumb-item active">
                    @empty($product)
                        {{__('Add New Product')}}
                    @else
                        {{__('Edit Product')}}: {{$product->product_display_name}}
                    @endif
                </li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@stop

@section('content')
<section class="content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-10">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            @empty($product)
                                {{__('Form Product')}}
                            @else
                                {{__('Edit Product')}}: {{$product->product_display_name}}
                            @endif
                        </h3>
                    </div>
                    <!-- /.card-header -->
                    <form method="POST" class="needs-validation" novalidate id="form-user" action="@empty($product) {{ url('/products') }} @else {{ url('/products/'.$product->id) }} @endempty" enctype="multipart/form-data" >
                        <div class="card-body">
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
                            
                            {{ csrf_field() }}
                            @if(!empty($product))
                                <input type="hidden" name="_method" value="PUT">
                            @endif
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="Product">{{__('Category Product')}} *</label>
                                        <select name="category_id" id="" class="select2" style="width: 100%" required>
                                            <option value="">{{__('Pilih Category Product')}} ..</option>
                                            @if (!empty($categories))
                                                @foreach ($categories as $cat)
                                                    <option value="{{$cat->id}}" @if(!empty($product->category_id) && $cat->id == $product->category_id) selected @endif>{{$cat->category_display_name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div class="invalid-feedback">
                                            {{__('Mohon isi kategori')}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="name">{{__('Product Name')}} *</label>
                                        <input type="text" class="form-control" required id="name" name="name" @php if(!empty(old('name'))) echo 'value="'.old('name').'"'; elseif(!empty($product->product_display_name)) echo 'value="'.$product->product_display_name.'"'; else echo 'autocomplete="off"'; @endphp placeholder="Masukan Nama Product">
                                        <div class="invalid-feedback">
                                            {{__('Mohon isi nama produk')}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="price">{{__('Product Price')}} *</label>
                                        <input type="text" class="form-control numbering" required id="price" name="price" @php if(!empty(old('price'))) echo 'value="'.old('price').'"'; elseif(!empty($product->product_price)) echo 'value="'.$product->product_price.'"'; else echo 'autocomplete="off"'; @endphp placeholder="Masukan Harga Product">
                                        <div class="invalid-feedback">
                                            {{__('Mohon isi harga produk')}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm">
                                    <div class="form-group">
                                        <label for="description">{{__('Product Description')}}</label>
                                        <textarea class="form-control deskripsi" name="product_description" id="description" rows="5" placeholder="Product Description">@if(!empty(old('product_description'))){{old('product_description')}}@elseif(!empty($product->product_description)){{$product->product_description}}@endif</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                {{-- @if(!empty($stores)) 
                                    <label for="store">Store *</label>
                                    <div class="sepH_a">
                                        <a href="#" class="btn btn-link btn-xs" id="perm_select_all">Select All</a>
                                        @if(!empty($role->stores) && !in_array($role->id,$restricted_ids))
                                            <a href="#" class="btn btn-link btn-xs" id="perm_deselect_all">Deselect All</a>
                                        @endif
                                    </div>
                                    @if(!empty($role->stores) && in_array($role->id,($restricted_ids)))
                                        @foreach($role->stores as $permission)
                                            <input type="hidden" name="store[]" value="{{$permission}}">
                                        @endforeach
                                    @endif
                                    <div class="col-sm-12">
                                        <select multiple="multiple" id="store" name="store[]" class="multi-select multi-select-store" data-label="Store" required="">
                                                @foreach($stores as $store)
                                                    <option value="{{$store->id}}" @php if(!empty($product->stores) && in_array($store->id,$product->stores)) { echo 'selected'; if(in_array($product->id,($restricted_ids))) echo ' disabled'; } @endphp>{{$store->store_name}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                    <div class="invalid-feedback">
                                        {{__('Mohon isi store')}}
                                    </div>
                                @endif --}}
                            </div>
                            <div class="form-group">
                                <label for="description">Product Image *</label>
                                <div class="card">
                                    <div class="card-body text-center">
                                    <input type="file" class="required gambar" style="display: none" @if(empty($product->meta['image'])) required @endif accept="image/*" name="image" data-preview="#previewImg" data-preview-detail="#previewDetailImg" data-min-width="250" data-min-height="250" data-wid="250" data-cropper="1">
                                    <figure class="figure">
                                        <img id="previewImg" @if(!empty($product->meta['image'])) src="{{ $product->meta['image']['media_path'] }}"  @else src="{{ asset('assets/img/box.jpg') }}" @endif width="350" height="350" class="img-fluid mb-2 img-thumbnail figure-img rounded">
                                        <figcaption class="figure-caption text-center">Image.</figcaption>
                                    </figure>
                                    <div class="invalid-feedback">
                                        {{__('Mohon upload gambar produk')}}
                                    </div>
                                    <div class="text-center">
                                        <a type="button text-center" class="btn upload-btn btn-default btx-sx " data-target=".gambar">
                                            <i class="fas fa-upload"></i> Upload
                                        </a>
                                    </div>
                                    <span class="text-muted">* Masukan gambar dengan ukuran minimun width x height 250 x 250px</span>
                                    </div>
                                </div>
                            </div>

                            @if(!empty($product))
                                <label for="status">{{__('Status')}}</label>
                                <div class="col-sm-6">
                                    <input type="checkbox" data-toggle="toggle" name="status" data-on="Active" data-size="xs" data-off="Inactive" data-onstyle="success" data-offstyle="info" value="1" @if (!empty($product->status) && $product->status == 1) checked @endif>
                                </div>
                            @endif
                        </div>
                        <!-- /.card-body -->
                    
                        <div class="card-footer">
                            <a href="{{url('products')}}" class="btn btn-default btn-sx">{{__('Cancel')}}</a>
                            <button type="submit" class="btn btn-primary btn-sx btnSubmit">{{__('Submit')}}</button>
                        </div>
                    </form>
                    <div class="overlay d-none spinner">
                        <i class="fa fa-fw fa-spinner fa-spin"></i>
                    </div>
                    
                    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" data-backdrop="static">
                        <div class="modal-dialog modal-md" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalLabel">Crop Image</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="img-container">
                                        <div class="row">
                                            <img id="image" src="https://avatars0.githubusercontent.com/u/3456749" class="img-fluid">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" id="croplist" data-image="list" data-form="#form-user" data-input=".gambar">Crop</button>
                                </div>
                            </div>
                        </div>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" integrity="sha256-jKV9n9bkk/CTP8zbtEtnKaKf+ehRovOYeKoyfthwbC8=" crossorigin="anonymous" />
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.4/daterangepicker.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/multi-select/0.9.12/css/multi-select.min.css">
    <link rel="stylesheet" href="{{asset('css/custom.css')}}">
@stop

@section('js')
    <script type="text/javascript">
        var base_url = {!! json_encode(url('/')) !!};
    </script>
    <script src="{{asset('vendor/jquery-ui/jquery-ui.min.js')}}"></script>    
    <script type="text/javascript" src="{{ asset('vendor/multi-select/js/jquery.multi-select.js') }}"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.quicksearch/2.4.0/jquery.quicksearch.min.js"></script>
    {{-- <script src="{{ asset('js/cropper.js') }}" integrity="sha256-CgvH7sz3tHhkiVKh05kSUgG97YtzYNnWt6OXcmYzqHY=" crossorigin="anonymous"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js" integrity="sha512-ooSWpxJsiXe6t4+PPjCgYmVfr1NS5QXJACcR/FPpsdm6kqG1FmQ2SVyg2RXeVuCRBLr0lWHnWJP6Zs1Efvxzww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.js" integrity="sha512-ZK6m9vADamSl5fxBPtXw6ho6A4TuX89HUbcfvxa2v2NYNT/7l8yFGJ3JlXyMN4hlNbz0il4k6DvqbIW5CCwqkw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('js/pos.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.4/daterangepicker.min.js" type="text/javascript"></script>
    <script src="{{asset('js/upload.js')}}"></script>
    <script src="{{ asset('js/product.js') }}"></script>
@stop