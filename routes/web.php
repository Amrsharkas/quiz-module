<?php

use Illuminate\Support\Facades\Route;
use mennaAbouelsaadat\quizGenerator\Http\Controllers\PostController;

    Route::group(['prefix' => 'admin/questions', 'as' => 'admin.questions.'], function () {
        Route::get('/', ['uses' => 'QuestionController@index']);//index Quizzes
        Route::get('/init/', ['uses' => 'QuestionController@init'])->name('init');//init question
        Route::get('/edit/{question_id}', ['uses' => 'QuestionController@edit'])->name('edit');//edit question

        Route::get('/init_answer', ['uses' => 'QuestionController@initAnswer'])->name('init_answer');
        Route::get('/init_text_correct_answer', ['uses' => 'QuestionController@initTextCorrectAnswer'])->name('init_text_correct_answer');
        Route::post('/update', ['uses' => 'QuestionController@update'])->name('update');//update question
        Route::post('/update_after_user_response', ['uses' => 'QuestionController@updateAfterUserResponse'])->name('update_after_user_response');//update question
        Route::delete('{id}/delete', ['uses' => 'QuestionController@delete'])->name('delete');//delete question
        Route::delete('{id}/{model}/delete_file', ['uses' => 'QuestionController@deleteFile'])->name('delete_file');
        Route::get('{id}/get_question_content', ['uses' => 'QuestionController@getQuestionContent']) ->name('get_question_content') ;
        Route::post('{id}/answers/{answer_id}/remove', ['uses' => 'QuestionController@removeAnswer']) ->name('get_question_content') ;
        Route::get('rollback_questions', ['uses' => 'QuestionController@rollbackQuestions']) ->name('rollback_questions') ;
        Route::post('{id}/possible_answers/{answer_id}/remove', ['uses' => 'QuestionController@removeTextCorrectAnswer']) ->name('get_question_content') ;
    });

    Route::group(['prefix' => 'admin/quiz', 'as' => 'admin.quiz.'], function () {
        Route::get('/', ['uses' => 'QuizController@index'])->name('index');
        Route::get('/add_quiz_template', ['uses' => 'QuizController@addQuizTemplate'])->name('add_quiz_template');
        Route::get('/edit_quiz_template/{quiz_id}', ['uses' => 'QuizController@editQuizTemplate'])->name('edit_quiz_template');
        Route::get('/add_quiz_section/{quiz_id}', ['uses' => 'QuizController@addQuizSection'])->name('add_quiz_section');
        Route::post('/update', ['uses' => 'QuizController@update'])->name('update');
        Route::get('/delete_quiz_section/{quiz_section_id}', ['uses' => 'QuizController@deleteQuizSection'])->name('delete_quiz_section');
        Route::get('/add_quiz_section_question_detail/{quiz_section_id}', ['uses' => 'QuizController@addQuizSectionQuestionDetail'])->name('add_quiz_section_question_detail');
        Route::get('/delete_quiz_section_detail/{quiz_section_detail_id}', ['uses' => 'QuizController@deleteQuizSectionDetail'])->name('delete_quiz_section_detail');

        Route::get('/generate_quiz/{quiz_id}', ['uses' => 'QuizController@generateQuiz'])->name('generate_quiz');
    });
