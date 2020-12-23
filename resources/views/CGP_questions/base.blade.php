<?php
$i = '9';
$j = '';
?>
@extends('admin.master_no_navbar')
@section('plugins_css')

@endsection
@section('plugins_js')

@endsection

@section('page_js')


@endsection


@section('add_inits')

@stop


@section('page_title_small')

@stop

@section('content')
    @include($partialView)
@stop
