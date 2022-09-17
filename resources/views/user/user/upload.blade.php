@extends('adminlte::page')

@section('title', 'User Management - Import Users')

@section('content_header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                Import Users
            </h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('users') }}">Users</a></li>
            <li class="breadcrumb-item active">
                Import Users
            </li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@stop

@section('content')
<section class="content">
    <div class="container">
        <div class="card">
            <div class="card-header with-border">
              <h3 class="card-title">Upload User (.xls / .xlsx only)</h3>
            </div>
            <form class="form-horizontal" action="{{ url('/users/upload/confirm') }}" method="POST" name="importform" enctype="multipart/form-data">
              {{ csrf_field() }}
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
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <a class="btn btn-info" style="display: inline-block;" id="filebutton" href="javascript:void(0)">
                                <i class="fa fa-fw fa-upload"></i> Pilih File
                            </a>
                            <br>
                            <a href="{{asset('excel/Template_Upload_User.xlsx')}}"><small>download template</small></a>
                            <input type="file" id="file" name="file" style="display: none;"  accept=".xls,.xlsx" />
                            <h3 id='nameFile' class="d-none">Tidak ada file yang dipilih</h3>
                        </div>
                    </div>
              </div>
              <!-- /.box-body -->
              {{-- <div class="box-footer">
                  <button class="btn btn-primary hidden" type="submit" id="btnSubmit" style="width: 100px;">
                      <i class="fa fa-fw fa-upload"></i> Upload
                  </button>
              </div> --}}
              <div class="card-footer">
                <a href="{{ url('/users') }}" class="btn btn-default" title="back to calculator list">Back</a>
                <button class="btn btn-primary d-none" type="submit" id="btnSubmit" style="width: 100px;">
                    <i class="fa fa-fw fa-upload"></i> Upload
                </button>
              </div>
              <!-- /.box-footer -->
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
    <script src="{{ asset('js/integriya.js') }}"></script>
    <script src="{{ asset('js/upload.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.4/daterangepicker.min.js" type="text/javascript"></script>
@stop