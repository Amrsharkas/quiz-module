<?php

namespace mennaAbouelsaadat\quizGenerator\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionCorrectAnswer extends Model
{
    protected $table = "correct_question_answers";
    protected $fillable = ['content', 'file_id', 'question_answer_id'];

    public function files()
    {
        return $this->belongsTo('App\File', 'file_id');
    }
    public function questionAnswer()
    {
        return $this->belongsTo('App\QuestionAnswer', 'question_answer_id');
    }
}
