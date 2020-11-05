<?php

use Illuminate\Support\Facades\Route;
use mennaAbouelsaadat\quizGenerator\Http\Controllers\PostController;

Route::get('/', ['uses' => 'QuizGeneratorController@index'])->name('index');
