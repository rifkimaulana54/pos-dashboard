@extends('adminlte::page')

@section('title', 'Product Management - Import Product')

@section('content_header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                Import Products
            </h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('products') }}">Products</a></li>
            <li class="breadcrumb-item active">
                Import Products
            </li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@stop

@section('content')
<section class="content">
    <div class="container">
        <div class="card card-info">
            <div class="card-header with-border">
              <h3 class="card-title">Confirm Upload Product</h3>
            </div>
            <form class="form-horizontal" action="{{ url('/products/upload/finish') }}" method="POST" name="importform" enctype="multipart/form-data">
              {{ csrf_field() }}
            <input type="hidden" name="cancel" id="cancelHidden" value="0">
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
                  <div class="table-responsive">
                    <table id="importList" class="table table-bordered table-hover" style="width: 100%;">
                      <thead> 
                        <tr>
                          @if(count($rows) > 0)
                            @php 
                              $total_column = 0; 
                              $head_column = array();
                            @endphp
                            @foreach($rows[0] as $h => $head)
                              @if($h <=  4)
                                <th data-tr="{{$h}}">{{$head}}</th>
                              @endif
                            @endforeach
                          @endif
                        </tr>
                      </thead>

                      @if(!empty($rows)) 
                        @php  $style =''; @endphp
                        <tbody>
                          @php $error = empty($rows) ? true : false; @endphp
                          @foreach($rows as $k => $datas)
                            @if($k > 0)
                                @php
                                  $style = (!empty($datas[4])) ? 'table-danger' : '';     
                                  if(!empty($style)) $error = true;                  
                                @endphp
                                <tr class="{{$style}}">
                                  @foreach($datas as $row => $value)
                                    @if($row <= 4)
                                      <td>{!! $value !!}</td>
                                    @endif
                                  @endforeach
                                </tr>
                            @endif
                          @endforeach
                        </tbody>
                      @endif
                    </table>
                  </div>
              </div>
              <!-- /.card-body -->
              <div class="card-footer text-center">
                @if($error)
                  <div class="alert alert-danger text-center">Please Fix The Error Berfore Continue.</div>
                @endif
                <button class="btn btn-danger" type="submit" id="btnCancel">
                    <i class="fa fa-fw fa-times"></i> Cancel Upload
                </button>
                @if(!$error)
                  <button class="btn btn-primary" type="submit" id="btnConfirm">
                      <i class="fa fa-fw fa-check"></i> Confirm Upload
                  </button>
                @endif
            </div>
              <!-- /.card-footer -->
            </form>
        </div>
        {!! csrf_field() !!}
    </div>
</section>
@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.4/daterangepicker.min.css">
@stop

@section('js')
    <script type="text/javascript">
        var base_url = {!! json_encode(url('/')) !!};
    </script>
    <script src="{{ asset('js/pos.js') }}"></script>
    <script src="{{ asset('js/upload.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.4/daterangepicker.min.js" type="text/javascript"></script>
@stop