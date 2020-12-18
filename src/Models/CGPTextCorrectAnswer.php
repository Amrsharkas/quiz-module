<?php

namespace mennaAbouelsaadat\quizGenerator\Models;

use Illuminate\Database\Eloquent\Model;

class CGPTextCorrectAnswer extends Model
{
    //

    protected $fillable = [
        'question_answer_id',
        'text'
    ] ;
    public function answer()
    {
        return $this ->belongsTo('App\QuestionAnswer', 'question_answer_id') ;
    }
}
