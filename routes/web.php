<?php

use Illuminate\Support\Facades\Route;

    Route::group(['prefix' => 'admin/questions', 'as' => 'admin.questions.'], function () {
        Route::get('/', ['uses' => 'mennaAbouelsaadat\quizGenerator\Http\Controllers\CGPQuestionController@index']);//index Quizzes
    Route::get('/init/', ['uses' => 'mennaAbouelsaadat\quizGenerator\Http\Controllers\CGPQuestionController@init'])->name('init');//init question
    Route::get('/edit/{question_id}', ['uses' => 'mennaAbouelsaadat\quizGenerator\Http\Controllers\CGPQuestionController@edit'])->name('edit');//edit question

    Route::get('/init_answer', ['uses' => 'mennaAbouelsaadat\quizGenerator\Http\Controllers\CGPQuestionController@initAnswer'])->name('init_answer');
    Route::get('/init_text_correct_answer', ['uses' => 'mennaAbouelsaadat\quizGenerator\Http\Controllers\CGPQuestionController@initTextCorrectAnswer'])->name('init_text_correct_answer');
    Route::post('/update', ['uses' => 'mennaAbouelsaadat\quizGenerator\Http\Controllers\CGPQuestionController@update'])->name('update');//update question
    Route::post('/update_after_user_response', ['uses' => 'mennaAbouelsaadat\quizGenerator\Http\Controllers\CGPQuestionController@updateAfterUserResponse'])->name('update_after_user_response');//update question
//    Route::get('{id}/edit', 'QuestionController@edit')->name('edit');
    Route::delete('{id}/delete', ['uses' => 'mennaAbouelsaadat\quizGenerator\Http\Controllers\CGPQuestionController@delete'])->name('delete');//delete question
    Route::delete('{id}/{model}/delete_file', ['uses' => 'CGPQuestionController@deleteFile'])->name('delete_file');
        Route::get('{id}/get_question_content', ['uses' => 'mennaAbouelsaadat\quizGenerator\Http\Controllers\CGPQuestionController@getQuestionContent']) ->name('get_question_content') ;
        Route::post('{id}/answers/{answer_id}/remove', ['uses' => 'mennaAbouelsaadat\quizGenerator\Http\Controllers\CGPQuestionController@removeAnswer']) ->name('get_question_content') ;
        Route::get('rollback_questions', ['uses' => 'mennaAbouelsaadat\quizGenerator\Http\Controllers\CGPQuestionController@rollbackQuestions']) ->name('rollback_questions') ;
        Route::post('{id}/possible_answers/{answer_id}/remove', ['uses' => 'mennaAbouelsaadat\quizGenerator\Http\Controllers\CGPQuestionController@removeTextCorrectAnswer']) ->name('get_question_content') ;
        Route::post('/topic/init', ['uses' => 'mennaAbouelsaadat\quizGenerator\Http\Controllers\CGPQuestionController@initTopic', 'middleware' => ['authorize', 'hasPermission'], 'roles' => ["Teacher", "Assistant"]])->name('storeTopic');
        Route::get('{id}/clone', ['uses' => 'mennaAbouelsaadat\quizGenerator\Http\Controllers\CGPQuestionController@clone'])->name('clone') ;
    });

Route::group(['prefix' => 'admin/quiz', 'as' => 'admin.quiz.'], function () {
    Route::get('/', ['uses' => 'mennaAbouelsaadat\quizGenerator\Http\Controllers\CGPQuizController@index'])->name('index');
    Route::get('/add_quiz_template', ['uses' => 'mennaAbouelsaadat\quizGenerator\Http\Controllers\CGPQuizController@addQuizTemplate'])->name('add_quiz_template');
    Route::post('/update_after_user_response', ['uses' => 'mennaAbouelsaadat\quizGenerator\Http\Controllers\CGPQuizController@updateAfterUserResponse'])->name('update_after_user_response');
    Route::get('/edit_quiz_template/{quiz_id}', ['uses' => 'mennaAbouelsaadat\quizGenerator\Http\Controllers\CGPQuizController@editQuizTemplate'])->name('edit_quiz_template');
    Route::get('/add_quiz_section/{quiz_id}', ['uses' => 'mennaAbouelsaadat\quizGenerator\Http\Controllers\CGPQuizController@addQuizSection'])->name('add_quiz_section');
    Route::post('/update', ['uses' => 'mennaAbouelsaadat\quizGenerator\Http\Controllers\CGPQuizController@update'])->name('update');
    Route::get('/delete_quiz_section/{quiz_section_id}', ['uses' => 'mennaAbouelsaadat\quizGenerator\Http\Controllers\CGPQuizController@deleteQuizSection'])->name('delete_quiz_section');
    Route::get('/add_quiz_section_question_detail/{quiz_section_id}', ['uses' => 'mennaAbouelsaadat\quizGenerator\Http\Controllers\CGPQuizController@addQuizSectionQuestionDetail'])->name('add_quiz_section_question_detail');
    Route::get('/delete_quiz_section_detail/{quiz_section_detail_id}', ['uses' => 'mennaAbouelsaadat\quizGenerator\Http\Controllers\CGPQuizController@deleteQuizSectionDetail'])->name('delete_quiz_section_detail');

    Route::get('/generate_quiz/{quiz_id}', ['uses' => 'mennaAbouelsaadat\quizGenerator\Http\Controllers\CGPQuizController@generateQuiz'])->name('generate_quiz');
});
