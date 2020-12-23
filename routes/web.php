<?php

use Illuminate\Support\Facades\Route;

    Route::group(['prefix' => 'admin/questions', 'as' => 'admin.questions.'], function () {
        Route::get('/', ['uses' => 'CGPQuestionController@index']);//index Quizzes
    Route::get('/init/', ['uses' => 'CGPQuestionController@init'])->name('init');//init question
    Route::get('/edit/{question_id}', ['uses' => 'CGPQuestionController@edit'])->name('edit');//edit question

    Route::get('/init_answer', ['uses' => 'CGPQuestionController@initAnswer'])->name('init_answer');
        Route::get('/init_text_correct_answer', ['uses' => 'CGPQuestionController@initTextCorrectAnswer'])->name('init_text_correct_answer');
        Route::post('/update', ['uses' => 'CGPQuestionController@update'])->name('update');//update question
    Route::post('/update_after_user_response', ['uses' => 'CGPQuestionController@updateAfterUserResponse'])->name('update_after_user_response');//update question
//    Route::get('{id}/edit', 'QuestionController@edit')->name('edit');
    Route::delete('{id}/delete', ['uses' => 'CGPQuestionController@delete'])->name('delete');//delete question
    Route::delete('{id}/{model}/delete_file', ['uses' => 'CGPQuestionController@deleteFile'])->name('delete_file');
        Route::get('{id}/get_question_content', ['uses' => 'CGPQuestionController@getQuestionContent']) ->name('get_question_content') ;
        Route::post('{id}/answers/{answer_id}/remove', ['uses' => 'CGPQuestionController@removeAnswer']) ->name('get_question_content') ;
        Route::get('rollback_questions', ['uses' => 'CGPQuestionController@rollbackQuestions']) ->name('rollback_questions') ;
        Route::post('{id}/possible_answers/{answer_id}/remove', ['uses' => 'CGPQuestionController@removeTextCorrectAnswer']) ->name('get_question_content') ;
        Route::post('/init_topic', ['uses' => 'CGPQuestionController@initTopic']) ->name('init_topic') ;
        Route::get('{id}/clone', ['uses' => 'CGPQuestionController@clone'])->name('clone') ;
    });

Route::group(['prefix' => 'admin/quiz', 'as' => 'admin.quiz.'], function () {
    Route::get('/', ['uses' => 'CGPQuizController@index'])->name('index');
    Route::get('/add_quiz_template', ['uses' => 'CGPQuizController@addQuizTemplate'])->name('add_quiz_template');
    Route::get('/edit_quiz_template/{quiz_id}', ['uses' => 'CGPQuizController@editQuizTemplate'])->name('edit_quiz_template');
    Route::get('/add_quiz_section/{quiz_id}', ['uses' => 'CGPQuizController@addQuizSection'])->name('add_quiz_section');
    Route::post('/update', ['uses' => 'CGPQuizController@update'])->name('update');
    Route::get('/delete_quiz_section/{quiz_section_id}', ['uses' => 'CGPQuizController@deleteQuizSection'])->name('delete_quiz_section');
    Route::get('/add_quiz_section_question_detail/{quiz_section_id}', ['uses' => 'CGPQuizController@addQuizSectionQuestionDetail'])->name('add_quiz_section_question_detail');
    Route::get('/delete_quiz_section_detail/{quiz_section_detail_id}', ['uses' => 'CGPQuizController@deleteQuizSectionDetail'])->name('delete_quiz_section_detail');

    Route::get('/generate_quiz/{quiz_id}', ['uses' => 'CGPQuizController@generateQuiz'])->name('generate_quiz');
});
