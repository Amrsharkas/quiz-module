<?php

namespace mennaAbouelsaadat\quizGenerator\Models;

use Illuminate\Database\Eloquent\Model;

class GeneratedQuiz extends Model
{
    public function questions()
    {
        return $this->hasMany('App\GeneratedQuizQuestion');
    }
}
