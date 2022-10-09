@extends('adminlte::page')

@section('title', 'Setting - Store')

@section('content_header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                @empty($store)
                    Add New Store
                @else
                    Edit Store: {{$store->store_name}}
                @endif
            </h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('stores') }}">Store</a></li>
                <li class="breadcrumb-item active">
                    @empty($store)
                        Add New Store
                    @else
                        Edit Store: {{$store->store_name}}
                    @endif
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
                            @empty($store)
                                Form Store
                            @else
                                Edit Store: {{$store->store_name}}
                            @endif
                        </h3>
                    </div>
                    <!-- /.card-header -->
                    <form method="POST" class="needs-validation form-store" novalidate action="@empty($store) {{ url('stores') }} @else {{ url('stores/'.$store->id) }} @endempty" enctype="multipart/form-data">
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
                            @if(!empty($store))
                                <input type="hidden" name="_method" value="PUT">
                            @endif
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="Store">Store Name *</label>
                                        <input type="text" class="form-control" id="Store" name="store_name" @php if(!empty(old('store_name'))) echo 'value="'.old('store_name').'"'; elseif(!empty($store->store_name)) echo 'value="'.$store->store_name.'"'; else echo 'autocomplete="off"'; @endphp placeholder="Masukan Store Name" required>
                                        <div class="invalid-feedback">
                                            {{__('Mohon isi Store Name')}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if(!empty($companies))
                                <div class="form-group">
                                    <label for="company_id">Company</label>
                                    <div class="col-sm-12">
                                        <select name="company_id" id="company_id" class="select2" style="width: 100%">
                                            <option value="">--- Select Company ---</option>
                                            @if (!empty($companies))
                                                @foreach ($companies as $comp)
                                                    <option value="{{$comp->id}}" @if (!empty($store) && $comp->id == $store->company_id) selected @endif>{{$comp->display_name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            @endif
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="telp">No Telp</label>
                                        <input class="form-control" id="telp" name="no_telepone" placeholder="Masukan No Telp" value="@if(!empty(old('no_telepone'))){{old('no_telepone')}}@elseif(!empty($store->no_telepone)){{$store->no_telepone}}@endif" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <textarea class="form-control" rows="5" id="address" name="store_address" placeholder="Masukan Address">@php if(!empty(old('store_address'))) echo old('store_address'); elseif(!empty($store->store_address)) echo $store->store_address;@endphp</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="store_description">Description</label>
                                        <textarea class="form-control" rows="5" id="store_description" name="store_description" placeholder="Masukan Address">@php if(!empty(old('store_description'))) echo old('store_description'); elseif(!empty($store->store_description)) echo $store->store_description;@endphp</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="longitude">Longitude</label>
                                        <input class="form-control" id="longitude" name="meta[longitude]" placeholder="Masukan Longitude" value="@if(!empty(old('meta')['longitude'])){{old('meta')['longitude']}}@elseif(!empty($store->meta['longitude'])){{$store->meta['longitude']}}@endif" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="latitude">Latitude</label>
                                        <input class="form-control" id="latitude" name="meta[latitude]" placeholder="Masukan Latitude" value="@if(!empty(old('meta')['latitude'])){{old('meta')['latitude']}}@elseif(!empty($store->meta['latitude'])){{$store->meta['latitude']}}@endif" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="description">Logo</label>
                                <div class="card">
                                    <div class="card-body text-center">
                                        <input type="file" class="gambar" style="display: none" accept="image/*" name="file" data-preview="#previewImg" data-max-width="600" data-max-height="200" data-max-size="100000">
                                        <img id="previewImg" @if(!empty($store->meta['image'])) src="{{ $store->meta['image']['media_path'] }}"  @else src="{{ asset('assets/img/box.jpg') }}" @endif width="225px" class="img-size-125 mb-2">
                                        <div class="text-center">
                                            <a type="button text-center" class="btn upload-btn btn-default btx-sx">
                                            <i class="fas fa-upload"></i> Upload
                                            </a>
                                        </div>
                                        <span class="text-muted">* Masukan gambar dengan ukuran width x height 600 x 200px</span>
                                    </div>
                                </div>
                            </div>
                            @if (!empty($store))
                                <label for="status">Status</label>
                                <div class="col-sm-6">
                                    <input type="checkbox" data-toggle="toggle" name="status" data-on="Active" data-size="xs" data-off="Inactive" data-onstyle="success" data-offstyle="info" value="1" @if (!empty($store->status) && $store->status == 1) checked @endif>
                                </div>
                            @endif
                        </div>
                    <!-- /.card-body -->
                    
                        <div class="card-footer">
                            <a href="{{url('stores')}}" class="btn btn-default btn-sx">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sx btnSubmit">Submit</button>
                        </div>
                    </form>
                </div>
                <!-- /.card -->
            </div>
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
@stop

@section('css')
    <link rel="stylesheet" href="{{asset('css/custom.css')}}">
@stop

@section('js')
    <script type="text/javascript">
        var base_url = {!! json_encode(url('/')) !!};
    </script>
    {{-- <script src="//cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.3/jquery.inputmask.min.js"></script> --}}
    {{-- <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('vendor/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js') }}"></script> --}}
    <script src="{{ asset('js/pos.js') }}"></script>
    <script src="{{ asset('js/upload.js') }}"></script>
    <script src="{{ asset('js/store.js') }}"></script>
@stop