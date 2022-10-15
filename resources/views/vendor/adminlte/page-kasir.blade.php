@extends('adminlte::master')

@section('adminlte_css')
    @stack('css')
    @yield('css')
@stop

@section('body')
    @yield('content_header')
@stop

@section('adminlte_js')
    @stack('js')
    @yield('js')
@stop
