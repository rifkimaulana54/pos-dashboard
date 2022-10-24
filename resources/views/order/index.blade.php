@extends('adminlte::page')

@section('title', 'Transaction - Order')

@section('content_header')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0 text-dark">{{__('Order')}}</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/">{{__('Home')}}</a></li>
            <li class="breadcrumb-item active">{{__('Order')}}</li>
        </ol>
    </div><!-- /.col -->
</div><!-- /.row -->
@stop

@section('content')
<section class="content">
    <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex">
                        <div class="d-flex justify-content-between" style="width: 85%">
                            <div class="d-flex">
                                <label class="py-2 mx-2">Filter</label>
                                @if(GlobalHelper::userRole($request,'superadmin'))
                                    <div class="mr-2">
                                        <select class="form-control filter-table select2 mr-2" name="store" id="store" style="max-width: 150px">
                                            <option value="">-- Store --</option>
                                            @if (!empty($stores))
                                                @foreach ($stores as $store)
                                                    <option value="{{$store->id}}">{{$store->store_name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                @endif
                                <div class="mr-2">
                                    <select class="form-control filter-table select2 mr-2" name="status" style="max-width: 150px" id="status">
                                        <option value="">-- Status --</option>
                                        <option value="2">Waiting List</option>
                                        <option value="4">Completed</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <form class="form-inline export" action="{{ url('/orders/export')}}" method="post">
                            <input type="hidden" name="keyword" id="keyword">
                            <input type="hidden" name="filter_status" id="filter_status">
                            <input type="hidden" name="filter_store" id="filter_store">
                            {{ csrf_field() }}
                            <div class="d-flex">
                                <label style="margin-right:10px">Export to</label>
                                <button class="dt-button buttons-excel buttons-html5 btn btn-default" tabindex="0" aria-controls="SchedulerList" type="submit"><span>Excel</span></button>
                            </div>
                        </form>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        @if(Session::has('flash_error'))
                        <div class="alert alert-danger text-center">{!! session('flash_error') !!}</div>
                        @endif
                        @if(Session::has('flash_success'))
                        <div class="alert alert-success text-center">{!! session('flash_success') !!}</div>
                        @endif
                        <div class="table-responsive">
                            <table id="orderList" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>{{__('ID')}}</th>
                                        <th>{{__('Order Code')}}</th>
                                        <th>{{__('Customer Name')}}</th>
                                        <th>{{__('Total')}}</th>
                                        <th>{{__('Store')}}</th>
                                        <th>{{__('Created Date')}}</th>
                                        <th>{{__('Modified Date')}}</th>
                                        <th>{{__('Status')}}</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="9">
                                            <center class="image-loading"><img src="{{ asset('assets/img/loading.gif') }}" style="width: 64px;" /></center>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot  align="right">
                                    <tr>
                                        <td colspan="2"></td>
                                        <td colspan="7" class="text-center grandtotal"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
            <!-- /.card -->

        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
@stop

@section('css')
<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.4/daterangepicker.min.css">
@stop

@section('js')
<script type="text/javascript">
    var base_url = {!!json_encode(url('/')) !!};
</script>
<script src="{{ asset('js/pos.js') }}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.js" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.4/daterangepicker.min.js" type="text/javascript"></script>
<script src="{{ asset('js/order/order.js') }}"></script>

@stop