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
<script>
    $(() => {
        // Select2 js
        $("select.select2").select2();

        // Uploader js
        $('.uploader').each(function() {
            var uploader = $(this);
            initFineUploader(uploader);
        });
    })

    function serializeInfos() {

        var infos
        var data;
        var question_type = $("input[name='question_type']:checked").val();
        infos = $("input[name='question_type']:checked").closest('div.row').find("input[name='infos[]']:checked").map(function() {
            // console.log($(this).val()) ;
            return $(this).val();

        }).get();

        data = {
            question_type: question_type,
            infos: infos
        };

        return data;
    }

    function render() {

        var data = serializeInfos();
        console.log(data);

        var question_id = $('#question_id').val();

        $.ajax({
            url: "/admin/questions/" + question_id + "/get_question_content",
            method: "GET",
            data: data,
            success: function(response) {
                if (response.status == 'success') {

                    $('#question_content').empty();
                    $('#question_content').append(response.content);
                    console.log($('#content'));
                    $('#question_content').find('.uploader').each(function() {
                        var uploader = $(this);
                        initFineUploader(uploader);
                    });

                }
            }
        });
    }
    $('.type_change').change(function(e) {
        // console.log($(this).attr('id') == 'multiple_choice') ;
        if ($(this).attr('id') == 'multiple_choice') {

            $('#text_input_div').find('.type_info').each(function() {
                $(this).prop('checked', false);
                $(this).prop('disabled', true);
                $(this).closest('label').addClass('disabled-input-style')
            })
            $('#multiple_choice_div').find('.type_info').each(function() {
                $(this).prop('checked', false);
                $(this).prop('disabled', false);
                $(this).closest('label').removeClass('disabled-input-style')
            })

        } else if ($(this).attr('id') == 'text_input') {
            $('#multiple_choice_div').find('.type_info').each(function() {
                $(this).prop('checked', false);
                $(this).prop('disabled', true);
                $(this).closest('label').addClass('disabled-input-style')
            })
            $('#text_input_div').find('.type_info').each(function() {
                $(this).prop('checked', true);
                $(this).prop('disabled', false);
                $(this).closest('label').removeClass('disabled-input-style')
            })
        }

        render();
    });

    $(document).on('click', '#add_new_option', function() {
        addAnswer('Question Input') ;
    })


    function addAnswer(type) {

        var data = serializeInfos();
        data.question_id = $('#question_id').val();
        if(type == 'Text Input')
            data.answer_text = $('#next_text_input').val();

        $.ajax({
            url: '/admin/questions/init_answer',
            method: 'GET',
            data: data,
            success: function(response) {
                if (response.status == 'success') {
                    if(type == 'Question Input'){

                        $('#div_options').append(response.content);
                        console.log(response);
                        initFineUploader($('#file_id_' + response.id));
                    }else if(type == 'Text Input'){

                        $('#text_inputs_div').find('.qu-text-inputs').append(response.content);
                        $('#next_text_input').val('');
                    }


                }
            }
        });
    }

    function removeAnswer(button, type) {

        console.log('ss') ;
        var s_answer = $(button);
        url = $(button).data('action');
        swal({
            title: "Are you sure?",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: false
        }, function() {
            $.ajax({
                url: url,
                method: 'post',
                data: {
                    _token: "{!! csrf_token() !!}"
                },
                success: function(data) {
                    if (data.status == 'success') {

                        swal("Deleted!", data.message, "success");
                        if(type == 'Text Input')
                            console.log($(button).closest('.s_answer').remove());
                        else if(type == 'Question Input')
                            console.log($(button).closest('.single_option').remove());


                        return false;
                    }
                    if (data.status == 'error') {
                        swal("Error", data.message, "error");
                        return false;
                    }
                }
            });
        });
    }

    function addTextCorrectAnswer(button) {

        var data = serializeInfos();
        data.question_id = $('#question_id').val();
        data.answer_text = $('#next_possible_answer').val();
        $.ajax({
            url: '/admin/questions/init_text_correct_answer',
            method: 'GET',
            data: data,
            success: function(response) {
                if (response.status == 'success') {
                    $('#possible_answers_div').find('.qu-possible-answers').append(response.content);
                    $('#next_possible_answer').val('');
                }
            },
            // error: function (jqXHR, exception) {
            //     console.log(jqXHR.status)
            // }
        });
    }


    function removeTextCorrectAnswer(button) {

        var s_answer = $(button);
        url = $(button).data('action');
        swal({
            title: "Are you sure?",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: false
        }, function() {
            $.ajax({
                url: url,
                method: 'post',
                data: {
                    _token: "{!! csrf_token() !!}"
                },
                success: function(data) {
                    if (data.status == 'success') {

                        swal("Deleted!", data.message, "success");
                        console.log($(button).closest('.s_answer').remove());
                        return false;
                    }
                    if (data.status == 'error') {
                        swal("Error", data.message, "error");
                        return false;
                    }
                }
            });
        });


    }

    saveCritriaEffectQuiz();
    $(document).on('change','.validate_quiz',function(){
        saveCritriaEffectQuiz()
    })
    function saveCritriaEffectQuiz()
    {
        var criteria_effect_quiz = '';
        $('.validate_quiz').each(function(){
            if($(this).is(':checkbox') || $(this).is(':radio'))
            {
                if ($(this).is(':checked')) {
                   criteria_effect_quiz += $(this).val();

                }
            }
            else
            {
                criteria_effect_quiz += $(this).val();
            }

        })
        $('[name=criteria_effect_quiz]').val(criteria_effect_quiz);
    }
    function insufficient_quizzes(parameters)
    {
        swal({
        title: "",
        text: '('+parameters['msg'] + ') will be insufficient quizzes, Are you sure you want to procceed? <br><button class="edit_question" data-response-type="yes" data-question-id="'+parameters['question_id']+'">Yes</button><button class="edit_question" data-response-type="no" data-question-id="'+parameters['question_id']+'">NO</button>',
        type: "warning",
        html: true,
        showCancelButton: false,
        showConfirmButton: false,
        closeOnConfirm: false
    })
    }
     $(document).on('click','.edit_question',function(){
        question_id = $(this).attr('data-question-id');
        response = $(this).attr('data-response-type');
        $.ajax({
            url:'/admin/questions/update_after_user_response' ,
            method: 'post',
            data:{question_id:question_id,response:response},
            success: function (data) {

            }
        });
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
