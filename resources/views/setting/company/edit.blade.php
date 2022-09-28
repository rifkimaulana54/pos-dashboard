@extends('adminlte::page')

@section('title', 'Setting - Company')

@section('content_header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                @empty($company)
                    Add New Company
                @else
                    Edit Company: {{$company->display_name}}
                @endif
            </h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('companies') }}">Company</a></li>
                <li class="breadcrumb-item active">
                    @empty($company)
                        Add New Company
                    @else
                        Edit Company: {{$company->display_name}}
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
                            @empty($company)
                                Form Company
                            @else
                                Edit Company: {{$company->display_name}}
                            @endif
                        </h3>
                    </div>
                    <!-- /.card-header -->
                    <form method="POST" class="form-company" action="@empty($company) {{ url('companies') }} @else {{ url('companies/'.$company->id) }} @endempty" enctype="multipart/form-data">
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
                            @if(!empty($company))
                                <input type="hidden" name="_method" value="PUT">
                            @endif
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="Company">Company Name *</label>
                                        <input type="text" class="form-control" id="Company" name="name" @php if(!empty(old('name'))) echo 'value="'.old('name').'"'; elseif(!empty($company->display_name)) echo 'value="'.$company->display_name.'"'; else echo 'autocomplete="off"'; @endphp placeholder="Masukan Company Name">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="company_id" value="@if(!empty($company)){{$company->id}}@endif">
                            {{-- @if(!empty($companies))
                                <div class="form-group">
                                    <label for="parent_id">Parent Company</label>
                                    <div class="col-sm-12">
                                        <select name="parent_id" id="parent_id" class="select2" style="width: 100%">
                                            <option value="">--- Select Parent Company ---</option>
                                            @if (!empty($companies))
                                                @foreach ($companies as $comp)
                                                    @php
                                                        if(!empty($company->id) && in_array($comp->id, array_column($company->childs, 'id')) || !empty($company->id) && $company->id == $comp->id)
                                                            continue;
                                                    @endphp
                                                    <option value="{{$comp->id}}" @if (!empty($company) && $comp->id == $company->parent_id) selected @endif>{{$comp->display_name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            @endif --}}
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="telp">No Telp</label>
                                        <input class="form-control" id="telp" name="meta[telp]" placeholder="Masukan No Telp" value="@if(!empty(old('meta')['telp'])){{old('meta')['telp']}}@elseif(!empty($company->meta['telp'])){{$company->meta['telp']}}@endif" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="billing_address">Billing Address</label>
                                        <textarea class="form-control" rows="5" id="billing_address" name="meta[billing_address]" placeholder="Masukan Billing Address">@php if(!empty(old('meta')['billing_address'])) echo old('meta')['billing_address']; elseif(!empty($company->meta['billing_address'])) echo $company->meta['billing_address'];@endphp</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="billing_address">Shipping Address</label>
                                        <textarea class="form-control" rows="5" id="shipping_address" name="meta[shipping_address]" placeholder="Masukan Shipping Address">@php if(!empty(old('meta')['shipping_address'])) echo old('meta')['shipping_address']; elseif(!empty($company->meta['shipping_address'])) echo $company->meta['shipping_address'];@endphp</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="description">Logo</label>
                                <div class="card">
                                    <div class="card-body text-center">
                                        <input type="file" class="gambar" style="display: none" accept="image/*" name="file" data-preview="#previewImg" data-max-width="600" data-max-height="200" data-max-size="100000">
                                        <img id="previewImg" @if(!empty($company->meta['image'])) src="{{ $company->meta['image']['media_path'] }}"  @else src="{{ asset('assets/img/box.jpg') }}" @endif width="225px" class="img-size-125 mb-2">
                                        <div class="text-center">
                                            <a type="button text-center" class="btn upload-btn btn-default btx-sx">
                                            <i class="fas fa-upload"></i> Upload
                                            </a>
                                        </div>
                                        <span class="text-muted">* Masukan gambar dengan ukuran width x height 600 x 200px</span>
                                    </div>
                                </div>
                            </div>
                            @if (!empty($company))
                                <label for="status">Status</label>
                                <div class="col-sm-6">
                                    <input type="checkbox" data-toggle="toggle" name="status" data-on="Active" data-size="xs" data-off="Inactive" data-onstyle="success" data-offstyle="info" value="1" @if (!empty($company->status) && $company->status == 1) checked @endif>
                                </div>
                            @endif
                        </div>
                    <!-- /.card-body -->
                    
                        <div class="card-footer">
                            <a href="{{url('companies')}}" class="btn btn-default btn-sx">Cancel</a>
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
    <link rel="stylesheet" href="{{asset('vendor/datepicker/css/bootstrap-datepicker3.min.css')}}">
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
    <script src="{{ asset('js/company.js') }}"></script>
    {{-- <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.js" type="text/javascript"></script> --}}
    {{-- <script src="{{asset('vendor/datepicker/js/bootstrap-datepicker.min.js')}}"></script> --}}
    {{-- <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.4/daterangepicker.min.js" type="text/javascript"></script> --}}
@stop