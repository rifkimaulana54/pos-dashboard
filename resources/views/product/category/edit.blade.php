@extends('adminlte::page')

@section('title', 'Master Data Management - Category')

@section('content_header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                @empty($category)
                    Add New Category
                @else
                    Edit Category: {{$category->category_display_name}}
                @endif
            </h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('categories') }}">Category</a></li>
                <li class="breadcrumb-item active">
                    @empty($category)
                        Add New Category
                    @else
                        Edit Category: {{$category->category_display_name}}
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
                            @empty($category)
                                Form Category
                            @else
                                Edit Category: {{$category->category_display_name}}
                            @endif
                        </h3>
                    </div>
                    <!-- /.card-header -->
                    <form method="POST" class="needs-validation" novalidate id="form-user" action="@empty($category) {{ url('/categories') }} @else {{ url('/categories/'.$category->id) }} @endempty">
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
                            @if(!empty($category))
                                <input type="hidden" name="_method" value="PUT">
                            @endif
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="Category">Parent Category</label>
                                        <select name="parent_id" id="" class="select2" style="width: 100%">
                                            <option value="">Pilih Parent Category ..</option>
                                            @if (!empty($categories))
                                                @foreach ($categories as $cat)
                                                @php
                                                    if(!empty($category) && $category->id == $cat->id)
                                                        continue;
                                                @endphp
                                                    <option value="{{$cat->id}}" @if(!empty($category->parent_id) && $cat->id == $category->parent_id) selected @endif>{{$cat->category_display_name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="name">{{__('Category Name')}} *</label>
                                        <input type="text" class="form-control" required id="name" name="name" @php if(!empty(old('name'))) echo 'value="'.old('name').'"'; elseif(!empty($category->category_display_name)) echo 'value="'.$category->category_display_name.'"'; else echo 'autocomplete="off"'; @endphp placeholder="Masukan Nama Category">
                                        <div class="invalid-feedback">
                                            {{__('Mohon isi role kategori')}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm">
                                    <div class="form-group">
                                        <label for="description">{{__('Category Description')}}</label>
                                        <textarea class="form-control deskripsi" name="category_description" id="description" rows="5" placeholder="Category Description">@if(!empty(old('category_description'))){{old('category_description')}}@elseif(!empty($category->category_description)){{$category->category_description}}@endif</textarea>
                                    </div>
                                </div>
                            </div>
                            
                            @if (!empty($companies))
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="company">Company *</label>
                                            <div class="sepH_a">
                                                <a href="#" class="btn btn-link btn-xs" id="act_select_all">Select All</a>
                                                <a href="#" class="btn btn-link btn-xs" id="act_deselect_all">Deselect All</a>
                                            </div>
                                            <div class="col-sm-12">
                                                <select multiple="multiple" id="company" name="company_id[]" class="multi-select multi-select-company" data-label="Company" required>
                                                    @foreach ($companies as $company)
                                                        <option value="{!! ucwords($company->company_name) !!}" >{!! ucwords($company->company_name) !!}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if(!empty($category))
                                <label for="status">{{__('Status')}}</label>
                                <div class="col-sm-6">
                                    <input type="checkbox" data-toggle="toggle" name="status" data-on="Active" data-size="xs" data-off="Inactive" data-onstyle="success" data-offstyle="info" value="1" @if (!empty($category->status) && $category->status == 1) checked @endif>
                                </div>
                            @endif
                        </div>
                        <!-- /.card-body -->
                    
                        <div class="card-footer">
                            <a href="{{url('users/acl/roles')}}" class="btn btn-default btn-sx">{{__('Cancel')}}</a>
                            <button type="submit" class="btn btn-primary btn-sx btnSubmit">{{__('Submit')}}</button>
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
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.4/daterangepicker.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/multi-select/0.9.12/css/multi-select.min.css">
    <link rel="stylesheet" href="{{asset('css/custom.css')}}">
@stop

@section('js')
    <script type="text/javascript">
        var base_url = {!! json_encode(url('/')) !!};
    </script>
    <script type="text/javascript" src="{{ asset('vendor/multi-select/js/jquery.multi-select.js') }}"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.quicksearch/2.4.0/jquery.quicksearch.min.js"></script>
    <script src="{{ asset('js/pos.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.4/daterangepicker.min.js" type="text/javascript"></script>
    <script src="{{ asset('js/category.js') }}"></script>
@stop