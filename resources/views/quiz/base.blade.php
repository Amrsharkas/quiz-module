<?php
$i = '9';
$j = '';
?>
@extends('admin.master')
@section('plugins_css')

@endsection
@section('plugins_js')

@endsection

@section('page_js')
    {{--datatable_visibility--}}
<script type="text/javascript">
	$(document).on('click','.add_quiz_section',function(){
		url = $(this).attr('data-form');
		target = $(this).attr('data-target');
		$.ajax({
			url:url,
			method:'get',
			success:function(view)
			{
				$(target).append(view);
				$("select.select2").select2();
			}
		})
	})
	$(document).on('click','.add_quiz_section_question_detail',function(){
		url = $(this).attr('data-form');
		target = $(this).attr('data-target');
		$.ajax({
			url:url,
			method:'get',
			success:function(view)
			{
				$(target).append(view);
				$("select.select2").select2();
			}
		})
	})
	$(document).on('click','.remove_quiz_section',function(){
		url = $(this).attr('data-action');
		target = $(this).closest('.quiz-section');
		$.ajax({
			url:url,
			method:'get',
			success:function(view)
			{
				target.remove();

			}
		})
	})

	$(document).on('click','.remove_quiz_section_detail',function(){
		url = $(this).attr('data-action');
		target = $(this).closest('.question_details');
		$.ajax({
			url:url,
			method:'get',
			success:function(view)
			{
				target.remove();

			}
		})
	})
</script>
@endsection


@section('add_inits')

@stop


@section('page_title_small')

@stop

@section('content')
    @include($partialView)
@stop